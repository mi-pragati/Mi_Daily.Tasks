<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class AdminProductController extends Controller
{
    private function makeUniqueSlug(string $text, ?int $ignoreId = null): string
{
    $base = Str::slug($text);
    if ($base === '') {
        $base = 'item';
    }
    $slug = $base;
    $i = 2;

    $query = Product::query();
    if ($ignoreId) {
        $query->where('id', '<>', $ignoreId);
    }

    while ((clone $query)->where('slug', $slug)->exists()) {
        $slug = $base.'-'.$i++;
    }
    return $slug;
}

    public function index(Request $request)
    {
        $productCategories = ProductCategory::all();

        $products = Product::with('category')
            ->when($request->filled('search'), function ($query) use ($request) {
                $q = $request->input('search');
                $query->where(function ($sub) use ($q) {
                    $sub->where('title', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->paginate(20);

              // Recently viewed products
            $sessionKey = auth()->check() ? 'recently_viewed_user_' . auth()->id() : 'recently_viewed_guest';
            $recentProducts = session($sessionKey, []);

        return view('admin.products.index', compact('products', 'productCategories', 'recentProducts'));
    }

    // Clear recently viewed
public function clearRecentlyViewed()
{
    $sessionKey = auth()->check()
        ? 'recently_viewed_user_' . auth()->id()
        : 'recently_viewed_guest';

    session()->forget($sessionKey);

    return redirect()->route('admin.products.index')->with('status', 'Recently viewed history cleared.');
}

    // Show single product details
    public function show(Product $product)
{
    $sessionKey = auth()->check() ? 'recently_viewed_user_' . auth()->id() : 'recently_viewed_guest';
    $recentProducts = session($sessionKey, []);

    // Use product ID as key to prevent duplicates
    $recentProducts[$product->id] = [
        'id'    => $product->id,
        'title' => $product->title,
        'image' => $product->image,
        'price' => $product->price,
        'slug'  => $product->slug,
    ];

    // Keep only last 10 viewed products
    $recentProducts = array_slice($recentProducts, -10, 10, true);

    session([$sessionKey => $recentProducts]);

    return view('admin.products.show', compact('product'));
}

   public function showCategoryProducts(ProductCategory $category, Request $request)
{
    $productCategories = ProductCategory::all();

    $q = $request->query('search');
    $products = Product::with('category')
        ->where('product_category_id', $category->id)
        ->when($q, function ($query) use ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        })
        ->latest()
        ->paginate(20)
        ->withQueryString();

    return view('admin.products.index', compact('products', 'category', 'productCategories'));
}

    // Show create form
    public function create()
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    // Store a new product

public function store(Request $request)
{
    // 1) Pre-validate: detect low-level PHP upload errors (temp dir, size, etc.)
    $phpErrorMap = [
        UPLOAD_ERR_INI_SIZE   => 'Exceeds upload_max_filesize.',
        UPLOAD_ERR_FORM_SIZE  => 'Exceeds MAX_FILE_SIZE in form.',
        UPLOAD_ERR_PARTIAL    => 'File only partially uploaded.',
        UPLOAD_ERR_NO_FILE    => 'No file uploaded.',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
        UPLOAD_ERR_EXTENSION  => 'File upload stopped by extension.',
    ];

    $checkIsValid = function (?UploadedFile $f, string $field) use ($phpErrorMap) {
        if ($f && !$f->isValid()) {
            $code = $f->getError();
            $msg  = $phpErrorMap[$code] ?? ('Unknown error code '.$code);
            Log::error('Upload invalid', [
                'field'      => $field,
                'error_code' => $code,
                'message'    => $msg,
                'clientName' => $f->getClientOriginalName(),
                'size'       => $f->getSize(),
                'mime'       => $f->getMimeType(),
            ]);
            abort(
                back()->withErrors([$field => "Image upload failed: $msg"])->withInput()->getStatusCode(),
                "Image upload failed: $msg"
            );
        }
    };

    if ($request->hasFile('image')) {
        $checkIsValid($request->file('image'), 'image');
    }
    if ($request->hasFile('media')) {
        foreach ((array) $request->file('media') as $mf) {
            $checkIsValid($mf, 'media');
        }
    }

    // 2) Laravel validation
    $validated = $request->validate([
        'user_id'              => ['nullable', 'exists:users,id'],
        'product_category_id'  => ['required', 'exists:product_categories,id'],
        'title'                => ['required', 'string', 'max:255'],
        'slug'                 => ['nullable', 'string', 'max:255'],
        'description'          => ['nullable', 'string'],
        'price'                => ['required', 'numeric', 'min:0'],
        'stock'                => ['required', 'integer', 'min:0'],
        'status'               => ['required', 'in:draft,published'],

        // files
        'image'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        'media'                => ['nullable', 'array'],
        'media.*'              => ['nullable', 'mimes:jpeg,jpg,png,webp,gif,mp4,avi,mov', 'max:20480'],
    ]);

    $mediaFiles = (array) $request->file('media', []);
    $data = Arr::except($validated, ['media']);

    // user + slug
    $data['user_id'] = auth()->id();
    $data['slug'] = !empty($data['slug'])
        ? Str::slug($data['slug'])
        : $this->makeUniqueSlug($data['title']);

    $mainImagePath = null;
    $mediaPaths = [];

    DB::beginTransaction();
    try {
        // 3) Store main image (or set default)
        if ($request->hasFile('image')) {
            $mainImagePath = $request->file('image')->store('products', 'public');
            if ($mainImagePath === false || $mainImagePath === null) {
                throw new \RuntimeException('Failed to store main image.');
            }
            $data['image'] = $mainImagePath;
        } else {
            $data['image'] = 'products/default-image.jpg'; // ensure this file exists on public disk
        }

        // 4) Create product
        /** @var \App\Models\Product $product */
        $product = \App\Models\Product::create($data);

        // 5) Store additional media
        foreach ($mediaFiles as $file) {
            if ($file instanceof UploadedFile) {
                $p = $file->store('products/media', 'public');
                if ($p === false || $p === null) {
                    throw new \RuntimeException('Failed to store a media file.');
                }
                $mediaPaths[] = $p;
            }
        }

        // If product is using default image and we uploaded images, promote first image to main
        if (!empty($mediaPaths) && ($product->image === 'products/default-image.jpg' || empty($product->image))) {
            $firstImage = collect($mediaPaths)->first(function ($p) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                return in_array($ext, ['jpg','jpeg','png','webp','gif']);
            });
            if ($firstImage) {
                $product->image = $firstImage;
            }
        }

        if (!empty($mediaPaths)) {
            $product->media = $mediaPaths; // ensure Product::$casts has 'media' => 'array'
        }

        $product->save();

        DB::commit();

        return redirect()->route('admin.products.index')->with('status', 'Product created successfully!');

    } catch (\Throwable $e) {
        // 6) Cleanup any files that were saved before failure
        DB::rollBack();
        try {
            if ($mainImagePath && Storage::disk('public')->exists($mainImagePath)) {
                Storage::disk('public')->delete($mainImagePath);
            }
            foreach ($mediaPaths as $mp) {
                if (Storage::disk('public')->exists($mp)) {
                    Storage::disk('public')->delete($mp);
                }
            }
        } catch (\Throwable $cleanupEx) {
            Log::warning('Failed to cleanup uploaded files after exception', [
                'error' => $cleanupEx->getMessage(),
            ]);
        }

        report($e);
        return back()->withErrors(['image' => 'Image upload failed: '.$e->getMessage()])->withInput();
    }
}


    // Show edit form
    public function edit(Product $product)
    {
        $categories = ProductCategory::orderBy('name')->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // Update product
   public function update(Request $request, Product $product)
{
    $validated = $request->validate([
        'product_category_id'  => ['required', 'exists:product_categories,id'],
        'title'                => ['required', 'string', 'max:255'],
        'slug'                 => ['nullable', 'string', 'max:255'],
        'description'          => ['nullable', 'string'],
        'price'                => ['required', 'numeric', 'min:0'],
        'stock'                => ['required', 'integer', 'min:0'],
        'status'               => ['required', 'in:draft,published'],

        'image'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        'media.*'              => ['nullable', 'mimes:jpeg,jpg,png,webp,gif,mp4,avi,mov', 'max:20480'],
    ]);

    $mediaFiles = $request->file('media', []);
    $data = Arr::except($validated, ['media']);

    // slug
    if (!empty($data['slug'])) {
        $data['slug'] = $this->makeUniqueSlug($data['slug'], $product->id);
    } else {
        unset($data['slug']); // keep existing slug
    }

    // main image
    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    $product->update($data);

    // append new media files
    if (!empty($mediaFiles)) {
        $existing = $product->media ?? [];
        foreach ($mediaFiles as $file) {
            $existing[] = $file->store('products/media', 'public');
        }
        $product->media = array_values($existing);
        $product->save();
    }

    return redirect()->route('admin.products.index')->with('status', 'Product updated successfully!');
}



    // Delete product
    public function destroy(Product $product)
    {
        if ($product->user_id != auth()->id()) {
            return redirect()->route('admin.products.index')->with('status', 'You do not have permission to delete this product.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('status', 'Product deleted successfully!');
    }
}
