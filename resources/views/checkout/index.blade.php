@extends('lay_app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-decoration-underline">Checkout Page</h3>

    {{-- Cart Summary --}}
    @if (!empty($items) && count($items) > 0)
        <h4>Order Summary</h4>
        <div class="table-responsive mb-4" style="max-width: 70%;">
            <table class="table align-left">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price (₹)</th>
                        <th>Qty</th>
                        <th>Total (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                    <tr>
                        <td>{{ $item['title'] }}</td>
                        <td>
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" style="max-height:50px;" class="img-fluid rounded">
                            @else
                                <img src="https://placehold.co/120x80" alt="No image" class="img-fluid rounded">
                            @endif
                        </td>
                        <td>₹{{ number_format($item['price'], 2) }}</td>
                        <td>{{ $item['qty'] }}</td>
                        <td>₹{{ number_format($item['price'] * $item['qty'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total</th>
                        <th>₹{{ number_format($totals['total'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="alert alert-info mb-4">Your cart is empty.</div>
    @endif

    {{-- Checkout Form --}}
    <h4>Checkout Form</h4>

    <form id="checkout-form" method="POST" action="{{ route('checkout.placeOrder') }}">
        @csrf

        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email Address</label>
            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" class="form-control" required>
        </div>

        <div class="mb-3 position-relative">
            <label for="address" class="form-label">Delivery Address</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Type your address..." autocomplete="off" required>
            <ul id="address-suggestions" class="list-group position-absolute w-100" style="z-index:1000; display:none; max-height:200px; overflow-y:auto;"></ul>
        </div>

        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">

        <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required>
        </div>

        {{-- Payment Method --}}
        <h4>Payment Method</h4>
        <div class="mb-3">
            <label><input type="radio" name="payment_method" id="cod" value="cod"> Cash on Delivery</label><br>
            <label><input type="radio" name="payment_method" id="stripe" value="stripe"> Pay Online (Card)</label>
        </div>

        {{-- Stripe/Card Form --}}
        <div id="stripe-form" style="display:none; margin-top:15px; max-width:400px;">
            <input type="hidden" name="stripe_method" value="card">
            <div id="card-element" style="border:1px solid #ddd; padding:10px; border-radius:6px;"></div>
            <div id="card-errors" style="color:red; margin-top:10px;"></div>
        </div>

        {{-- Buttons --}}
        <button type="submit" id="cod-submit" class="btn btn-primary mt-3" style="display:none;">Place Order</button>
        <button type="button" id="pay-now" class="btn btn-success mt-3" style="display:none;">Pay & Place Order</button>
    </form>
</div>
@endsection

@push('scripts')

{{-- ================== Address Autocomplete ================== --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const addressInput = document.getElementById("address");
    const suggestionsList = document.getElementById("address-suggestions");
    const latInput = document.getElementById("latitude");
    const lonInput = document.getElementById("longitude");
    let timeout = null;

    addressInput.addEventListener("input", function () {
        clearTimeout(timeout);
        const query = this.value.trim();
        if (query.length < 3) { suggestionsList.style.display = "none"; return; }

        timeout = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&limit=8&q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    suggestionsList.innerHTML = "";
                    if (data.length > 0) {
                        suggestionsList.style.display = "block";
                        data.forEach(place => {
                            const li = document.createElement("li");
                            li.classList.add("list-group-item", "list-group-item-action");
                            li.textContent = place.display_name;
                            li.addEventListener("click", function () {
                                addressInput.value = place.display_name;
                                latInput.value = place.lat;
                                lonInput.value = place.lon;
                                suggestionsList.style.display = "none";
                            });
                            suggestionsList.appendChild(li);
                        });
                    } else suggestionsList.style.display = "none";
                })
                .catch(() => suggestionsList.style.display = "none");
        }, 400);
    });

    document.addEventListener("click", function (event) {
        if (!addressInput.contains(event.target) && !suggestionsList.contains(event.target)) {
            suggestionsList.style.display = "none";
        }
    });
});
</script>

<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const cardElement = elements.create("card");
    cardElement.mount("#card-element");

    const codRadio = document.querySelector('input[value="cod"]');
    const stripeRadio = document.querySelector('input[value="stripe"]');
    const codSubmit = document.getElementById("cod-submit");
    const payNow = document.getElementById("pay-now");
    const stripeForm = document.getElementById("stripe-form");

    function togglePaymentMethod() {
        if (codRadio.checked) {
            codSubmit.style.display = "inline-block";
            payNow.style.display = "none";
            stripeForm.style.display = "none";
        } else if (stripeRadio.checked) {
            codSubmit.style.display = "none";
            payNow.style.display = "inline-block";
            stripeForm.style.display = "block";
        }
    }

    codRadio.addEventListener("change", togglePaymentMethod);
    stripeRadio.addEventListener("change", togglePaymentMethod);
    togglePaymentMethod();

    let isProcessing = false;

    // COD submit
    codSubmit.addEventListener("click", async () => {
        if (isProcessing) return;
        isProcessing = true;
        codSubmit.disabled = true;

        const data = {
            _token: "{{ csrf_token() }}",
            payment_method: "cod",
            name: document.querySelector('input[name="name"]').value,
            email: document.querySelector('input[name="email"]').value,
            address: document.querySelector('input[name="address"]').value,
            phone: document.querySelector('input[name="phone"]').value,
        };

        const res = await fetch("{{ route('checkout.placeOrder') }}", {
            method: "POST",
            headers: { "Content-Type": "application/json", "Accept": "application/json" },
            body: JSON.stringify(data)
        });
        const result = await res.json();
        if (result.success) window.location.href = result.redirect;
        else alert(result.error || "Error processing order");

        isProcessing = false;
        codSubmit.disabled = false;
    });

    // Stripe submit
    payNow.addEventListener("click", async () => {
        if (isProcessing) return;
        isProcessing = true;
        payNow.disabled = true;

        const data = {
            _token: "{{ csrf_token() }}",
            payment_method: "stripe",
            stripe_method: "card",
            name: document.querySelector('input[name="name"]').value,
            email: document.querySelector('input[name="email"]').value,
            address: document.querySelector('input[name="address"]').value,
            phone: document.querySelector('input[name="phone"]').value,
        };

        try {
            const response = await fetch("{{ route('checkout.placeOrder') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "Accept": "application/json" },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.clientSecret && result.orderId) {
                const paymentResult = await stripe.confirmCardPayment(result.clientSecret, {
                    payment_method: { card: cardElement, billing_details: { name: data.name, email: data.email } }
                });

                if (paymentResult.error) alert(paymentResult.error.message);
                else if (paymentResult.paymentIntent.status === "succeeded") {
                    await fetch("{{ route('checkout.storePayment') }}", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: JSON.stringify({
                            order_id: result.orderId,
                            payment_id: paymentResult.paymentIntent.id,
                            amount: paymentResult.paymentIntent.amount / 100,
                            currency: paymentResult.paymentIntent.currency,
                            status: paymentResult.paymentIntent.status,
                            payment_method: "card"
                        })
                    });

                    window.location.href = "{{ route('customer.orders.confirmation', ':id') }}".replace(':id', result.orderId);
                }
            } else alert(result.error || "Unknown error");
        } catch (err) {
            console.error(err);
            alert("Payment request failed: " + err.message);
        } finally {
            isProcessing = false;
            payNow.disabled = false;
        }
    });
});
</script>
@endpush
