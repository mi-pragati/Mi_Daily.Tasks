@extends('admin.layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="container py-4">
    <h1 class="my-4">{{ $product->title ?? $product->name }}</h1>

    <div class="row g-4">
        <div class="col-md-6">
            {{-- Primary image (uses accessor so it works for image/media/default) --}}
            <img
                src="{{ $product->image_url }}"
                class="img-fluid rounded"
                alt="{{ $product->title ?? $product->name }}"
                style="width:100%;object-fit:cover;"
            >
        </div>

        <div class="col-md-6">
            {{-- Meta --}}
            <p class="mb-2"><strong>Category:</strong> {{ $product->category->name ?? 'No Category' }}</p>
            <p class="mb-2"><strong>Slug:</strong> {{ $product->slug }}</p>
            <p class="mb-2"><strong>Price:</strong> ₹{{ number_format((float) $product->price, 2) }}</p>
            <p class="mb-2"><strong>Stock:</strong> {{ $product->stock }}</p>
            <p class="mb-2">
                <strong>Status:</strong>
                <span class="badge {{ $product->status === 'published' ? 'bg-success' : 'bg-secondary' }}">
                    {{ ucfirst($product->status) }}
                </span>
            </p>
            <p class="mb-2"><strong>Created:</strong> {{ $product->created_at?->format('d M Y, H:i') }}</p>
            <p class="mb-4"><strong>Updated:</strong> {{ $product->updated_at?->format('d M Y, H:i') }}</p>

            {{-- Actions --}}
            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning me-2">Edit</a>

            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                  class="d-inline"
                  onsubmit="return confirm('Are you sure you want to delete this product?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>

            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">Back to Products</a>
        </div>
    </div>

    {{-- Description --}}
    <div class="mt-4">
        <h5>Description</h5>
        <p class="mb-0">{{ $product->description ?: 'No description available.' }}</p>
    </div>

    {{-- Media gallery (optional) --}}
    @php
        $mediaItems = [];
        if (!empty($product->media)) {
            $mediaItems = is_array($product->media)
                ? $product->media
                : (json_decode($product->media, true) ?: [$product->media]);
        }
    @endphp

    @if(!empty($mediaItems))
        <div class="mt-4">
            <h5>Additional Media</h5>
            <div class="row g-3">
                @foreach($mediaItems as $m)
                    @php
                        $isAbsolute = preg_match('#^https?://#i', $m);
                        $src = $isAbsolute ? $m : asset('storage/'.$m);
                        $ext = strtolower(pathinfo(parse_url($src, PHP_URL_PATH), PATHINFO_EXTENSION));
                        $isImage = in_array($ext, ['jpg','jpeg','png','webp','gif']);
                    @endphp
                    <div class="col-6 col-md-3">
                        <div class="border rounded p-2 h-100 d-flex align-items-center justify-content-center">
                            @if($isImage)
                                <img src="{{ $src }}" class="img-fluid rounded" alt="Media">
                            @else
                                <video src="{{ $src }}" controls style="width:100%;border-radius:.5rem;"></video>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
