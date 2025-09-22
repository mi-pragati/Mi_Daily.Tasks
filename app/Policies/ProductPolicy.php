<?php
namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    // Admin can do anything
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true; // anyone authenticated can list, used mainly for editor dashboard
    }

    public function view(?User $user, Product $product): bool
    {
        // public view handled by controller; policy mostly for editor/admin
        return true;
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['editor', 'admin'], true);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->role === 'editor' && $product->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->role === 'editor' && $product->user_id === $user->id;
    }
}
