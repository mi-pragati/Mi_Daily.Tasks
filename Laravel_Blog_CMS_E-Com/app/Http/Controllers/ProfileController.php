<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{

     //Show profile screen.
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $request->user()->update(['name' => $data['name']]);
        return back()->with('status', 'Name updated.');
    }

    public function updateName(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $request->user()->update(['name' => $data['name']]);
        return back()->with('status', 'Name updated.');
    }

//Sends OTP

    public function requestOtp(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'purpose'   => 'required|in:email,password',
            'new_email' => [
                'nullable',
                'email',
                'required_if:purpose,email',
                'unique:users,email',
                'not_in:'.$user->email,
            ],
        ]);

        // 6-digit code
        $code = (string) random_int(100000, 999999);
        $key  = "otp:{$user->id}:{$data['purpose']}";

        // store (10 min)
        Cache::put($key, [
            'code'          => Hash::make($code),
            'expires_at'    => now()->addMinutes(10),
            'pending_email' => $data['purpose'] === 'email' ? $data['new_email'] : null,
        ], now()->addMinutes(10));

        // email to CURRENT email
        Mail::raw(
            "Your OTP is: {$code}\nThis code expires in 10 minutes.\nRequested change: {$data['purpose']}.",
            function ($m) use ($user) {
                $m->to($user->email)->subject('Your OTP Code');
            }
        );

        return back()->with('status', 'OTP sent to your current email. (For dev: set MAIL_MAILER=log to see it in storage/logs/laravel.log)');
    }

    //confirm OTP

public function confirmOtp(Request $request): RedirectResponse
{
    // Build rules as ARRAYS (so Password::defaults() works)
    $rules = [
        'purpose'  => ['required', 'in:email,password'],
        'otp_code' => ['required', 'digits:6'],
    ];

    if ($request->input('purpose') === 'password') {
        $rules['current_password'] = ['required', 'current_password'];
        $rules['new_password']     = ['required', 'confirmed', Password::defaults()];

    }

    $data = $request->validate($rules);

    $user    = $request->user();
    $key     = "otp:{$user->id}:{$data['purpose']}";
    $payload = Cache::get($key);

    if (!$payload) {
        return back()->withErrors(['otp_code' => 'OTP expired or not found. Please request a new OTP.']);
    }
    if (!Hash::check($data['otp_code'], $payload['code'])) {
        return back()->withErrors(['otp_code' => 'Invalid OTP.']);
    }
    if (now()->greaterThan($payload['expires_at'])) {
        Cache::forget($key);
        return back()->withErrors(['otp_code' => 'OTP expired. Please request a new one.']);
    }

    if ($data['purpose'] === 'email') {
        $new = $payload['pending_email'] ?? null;
        if (!$new) {
            return back()->withErrors(['new_email' => 'No pending email found. Request a new OTP.']);
        }
        $user->update(['email' => $new]);
        Cache::forget($key);
        return back()->with('status', 'Email changed successfully.');
    }

    // Password flow
    $user->update(['password' => Hash::make($data['new_password'])]);
    Cache::forget($key);

    return back()->with('status', 'Password changed successfully.');
}

    //Delete acount(Optional)
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('status', 'Account Deleted.');
    }
}
