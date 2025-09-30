@php
    $sessionKey = auth()->check() ? 'recently_viewed_user_' . auth()->id() : 'recently_viewed_guest';
    $recentProducts = session($sessionKey, []);
@endphp

@if($recentProducts && count($recentProducts) > 0)
<div class="recently-viewed my-4">
     <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Recently Viewed Products</h4>
         {{-- Clear History Button --}}
        <form action="{{ route('recently-viewed.clear') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-danger">
                Clear History
            </button>
        </form>
    </div>
    <div class="row">
        @foreach($recentProducts as $product)
            <div class="col-6 col-sm-4 col-md-2 mb-3">
                <div class="card h-100 shadow-sm">
                    <a href="{{ route('products.show', $product['slug']) }}">
                        <img src="{{ isset($product['image']) && !Str::startsWith($product['image'], ['http', '/']) 
                                    ? asset('storage/'.$product['image']) 
                                    : $product['image'] }}" 
                             class="card-img-top" 
                             alt="{{ $product['title'] }}" 
                             style="height:150px; object-fit:cover;">
                    </a>
                    <div class="card-body p-2 text-center">
                        <a href="{{ route('products.show', $product['slug']) }}" class="text-decoration-none text-dark">
                            <small>{{ Str::limit($product['title'], 20) }}</small>
                        </a>
                        <div><strong>₹{{ $product['price'] }}</strong></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
