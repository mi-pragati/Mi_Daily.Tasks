@extends('lay_app')

@section('title', 'Home')

@section('content')
  <div class="container" style="padding-left: 1%; padding-right: 1%;">
      {{-- Hero Banner --}}
      <div class="my-3 text-center">
          <img src="https://static.vecteezy.com/system/resources/previews/002/294/859/non_2x/flash-sale-web-banner-design-e-commerce-online-shopping-header-or-footer-banner-free-vector.jpg"
               alt="Hero Banner" class="img-fluid">
      </div>

      {{-- Featured Products --}}
      <h3 class="my-4">Featured Products</h3>
      <div class="row">
          @foreach($featuredProducts as $product)
              <div class="col-md-2 col-sm-4 mb-4">
                  <div class="card shadow-sm">
                      <img src="https://picsum.photos/300/200?random={{ rand(1, 1000) }}"
                           class="card-img-top" alt="{{ $product->name }}"
                           style="height: 200px; object-fit: cover;">
                      <div class="card-body">
                          <h5 class="card-title">{{ $product->name }}</h5>
                          <p class="card-text"><strong>Price:</strong> ₹{{ $product->price }}</p>
                          <a href="{{ route('products.show', $product) }}" class="btn btn-primary">
                            View Product
                          </a>
                      </div>
                  </div>
              </div>
          @endforeach
      </div>

      {{-- Categories --}}
      <h3 class="my-4">Shop by Categories</h3>
      <div class="row">
          @foreach($categories as $category)
              <div class="col-md-3 col-sm-6 mb-4">
                  <div class="card shadow-sm">
                      <img src="https://picsum.photos/300/200?random={{ rand(1, 1000) }}"
                           class="card-img-top" alt="{{ $category->name }}"
                           style="height: 200px; object-fit: cover;">
                      <div class="card-body">
                          <h5 class="card-title text-center">{{ $category->name }}</h5>
                          <a href="{{ route('products.index', ['category' => $category->id]) }}"
                             class="btn btn-primary w-100">Shop Now</a>
                      </div>
                  </div>
              </div>
          @endforeach
      </div>
  </div>
@endsection
