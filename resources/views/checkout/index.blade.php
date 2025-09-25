@extends('lay_app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <h3 class="mb-4 text-decoration-underline">Checkout Page</h3>

    {{-- Cart Summary --}}
<div class="d-flex flex-wrap">
    {{-- Left Side: Order Summary Table --}}
    <div class="flex-grow-1 me-4" style="min-width: 60%;">
        @if (!empty($items) && count($items) > 0)
        <h4>Order Summary</h4>
        <div class="table-responsive mb-4">
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
            </table>
        </div>
        @else
        <div class="alert alert-info mb-4">Your cart is empty.</div>
        @endif
    </div>

    {{-- Right Side: Total Summary Box --}}
    <div style="min-width: 250px;">
        <div class="card p-3 shadow-sm">
            <h5 class="mb-3">Total Summary</h5>

            <div class="d-flex justify-content-between">
                <span>Subtotal:</span>
                <span>₹<span id="subtotal">{{ number_format($totals['total'], 2) }}</span></span>
            </div>

            <div class="d-flex justify-content-between">
                <span>GST (18%):</span>
                @php
                    $gst = $totals['total'] * 0.18;
                @endphp
                <span>₹<span id="gst">{{ number_format($gst, 2) }}</span></span>
            </div>

            <div class="d-flex justify-content-between" id="discount-row" style="display:none; color: green;">
                <span>Discount:</span>
                <span>- ₹<span id="discountAmount">0.00</span></span>
            </div>

               {{-- Optional Shipping Row --}}
        <div class="d-flex justify-content-between" id="shipping-row">
            <span>Shipping:</span>
            <span>₹<span id="shippingAmount">0.00</span></span>
        </div>

            <hr>

            <div class="d-flex justify-content-between fw-bold">
                <span>Final Total:</span>
                <span>₹<span id="finalTotal">{{ number_format($totals['total'] + $gst, 2) }}</span></span>
            </div>
        </div>
    </div>
</div>

<div class="mb-3" style="max-width: 350px;">
    <label for="coupon_code" class="form-label">Apply Coupon</label>
    <div class="input-group">
        <input type="text" id="coupon_code" name="coupon_code" class="form-control" placeholder="Enter coupon code">
        <button type="button" id="apply-coupon" class="btn btn-primary">Apply</button>
    </div>
    <div id="coupon-message" class="mt-2"></div>
</div>

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

          <div class="mb-3">
            <label>Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" required maxlength="10" 
           pattern="\d{10}" 
           title="Please enter exactly 10 digits">
        </div>

        {{-- Country --}}
<div class="mb-3">
    <label for="country" class="form-label">Country</label>
    <select id="country" name="country" class="form-control" required>
        <option value="">Select Country</option>
    </select>
</div>

{{-- State --}}
<div class="mb-3">
    <label for="state" class="form-label">State</label>
    <select id="state" name="state" class="form-control" required disabled>
        <option value="">Select State</option>
    </select>
</div>

{{-- City --}}
<div class="mb-3">
    <label for="city" class="form-label">City</label>
    <select id="city" name="city" class="form-control" required disabled>
        <option value="">Select City</option>
    </select>
</div>

{{-- Pincode --}}
<div class="mb-3">
    <label for="pincode" class="form-label">Pincode</label>
    <input type="text" id="pincode" name="pincode" class="form-control" 
           placeholder="Enter Pincode" required 
           maxlength="6" pattern="\d{6}" 
           title="Please enter a valid 6-digit pincode">
    @error('pincode') <div class="text-danger small">{{ $message }}</div> @enderror
</div>


{{-- Street Address --}}
<div class="mb-3">
    <label for="street" class="form-label">Street Address</label>
    <input type="text" id="street" name="street" class="form-control" placeholder="Flat / House No, Road, Area" required>
</div>
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
    const countrySelect = document.getElementById("country");
    const stateSelect   = document.getElementById("state");
    const citySelect    = document.getElementById("city");
    const pincodeSelect = document.getElementById("pincode");

    // 1. Load Countries
    fetch("https://countriesnow.space/api/v0.1/countries/positions")
        .then(res => res.json())
        .then(data => {
            data.data.forEach(c => {
                const opt = document.createElement("option");
                opt.value = c.name;
                opt.textContent = c.name;
                countrySelect.appendChild(opt);
            });
        });

    // 2. On Country Change → Load States
    countrySelect.addEventListener("change", function () {
        stateSelect.innerHTML = '<option value="">Select State</option>';
        citySelect.innerHTML = '<option value="">Select City</option>';
        pincodeSelect.innerHTML = '<option value="">Select Pincode</option>';
        stateSelect.disabled = true;
        citySelect.disabled = true;
        pincodeSelect.disabled = true;

        if (this.value) {
            fetch("https://countriesnow.space/api/v0.1/countries/states", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ country: this.value })
            })
            .then(res => res.json())
            .then(data => {
                data.data.states.forEach(s => {
                    const opt = document.createElement("option");
                    opt.value = s.name;
                    opt.textContent = s.name;
                    stateSelect.appendChild(opt);
                });
                stateSelect.disabled = false;
            });
        }
    });

    // 3. On State Change → Load Cities
    stateSelect.addEventListener("change", function () {
        citySelect.innerHTML = '<option value="">Select City</option>';
        pincodeSelect.innerHTML = '<option value="">Select Pincode</option>';
        citySelect.disabled = true;
        pincodeSelect.disabled = true;

        if (this.value) {
            fetch("https://countriesnow.space/api/v0.1/countries/state/cities", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ country: countrySelect.value, state: this.value })
            })
            .then(res => res.json())
            .then(data => {
                data.data.forEach(city => {
                    const opt = document.createElement("option");
                    opt.value = city;
                    opt.textContent = city;
                    citySelect.appendChild(opt);
                });
                citySelect.disabled = false;
            });
        }
    });

    // 4. On City Change → Load Pincodes from public API
    citySelect.addEventListener("change", function () {
        pincodeSelect.innerHTML = '<option value="">Select Pincode</option>';
        pincodeSelect.disabled = true;

        const city = this.value;
        const state = stateSelect.value;

        if (city && state) {
            fetch(`https://api.postalpincode.in/postoffice/${city}`)
                .then(res => res.json())
                .then(data => {
                    if (data[0].Status === "Success") {
                        data[0].PostOffice.forEach(p => {
                            if (p.State.toLowerCase() === state.toLowerCase()) {
                                const opt = document.createElement("option");
                                opt.value = p.Pincode;
                                opt.textContent = p.Pincode;
                                pincodeSelect.appendChild(opt);
                            }
                        });
                        pincodeSelect.disabled = false;
                    } else {
                        console.warn("No pincodes found for this city.");
                    }
                })
                .catch(err => console.error("Error fetching pincodes:", err));
        }
    });
});
</script>

<script src="https://js.stripe.com/v3/"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const codRadio = document.querySelector('input[value="cod"]');
    const stripeRadio = document.querySelector('input[value="stripe"]');
    const codSubmit = document.getElementById("cod-submit");
    const payNow = document.getElementById("pay-now");
    const stripeForm = document.getElementById("stripe-form");

    const subtotalElem = document.getElementById('subtotal');
    const gstElem = document.getElementById('gst');
    const shippingElem = document.getElementById('shippingAmount');
    const discountRow = document.getElementById('discount-row');
    const discountAmountElem = document.getElementById('discountAmount');
    const finalTotalElem = document.getElementById('finalTotal');
    const couponInput = document.getElementById('coupon_code');
    const couponMessage = document.getElementById('coupon-message');

    let isProcessing = false;

    // ---------------------- STRIPE INIT ----------------------
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const cardElement = elements.create("card");
    cardElement.mount("#card-element");

    // ---------------------- PAYMENT TOGGLE ----------------------
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

    // ---------------------- HELPER: CALCULATE TOTAL ----------------------
    function parseNumber(str){
    return parseFloat(str.replace(/,/g, '')) || 0;
}

function updateSummary(discount = 0){
    const subtotal = parseNumber(subtotalElem.innerText);
    const gst = subtotal * 0.18;
    const shipping = parseNumber(shippingElem.innerText);

    gstElem.innerText = gst.toFixed(2);

    if(discount > 0){
        discountRow.style.display = 'flex';
        discountAmountElem.innerText = discount.toFixed(2);
    } else {
        discountRow.style.display = 'none';
    }

    let finalTotal = subtotal + gst + shipping - discount;
    if(finalTotal < 0) finalTotal = 0;

    finalTotalElem.innerText = finalTotal.toFixed(2);
}


    // Initial calculation
    updateSummary(0);

    // ---------------------- APPLY COUPON ----------------------
    document.getElementById('apply-coupon')?.addEventListener('click', async () => {
        const code = couponInput.value.trim();
        if(!code){
            couponMessage.innerHTML = '<span class="text-danger">⚠ Please enter a coupon code.</span>';
            return;
        }

        try {
            const res = await fetch("{{ route('checkout.applyCoupon') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ coupon_code: code })
            });

            const data = await res.json();

            if(data.success){
                updateSummary(data.discount);
                couponMessage.innerHTML = '<span class="text-success">✅ Coupon ' + data.coupon + ' applied. Discount: ₹' + data.discount.toFixed(2) + '</span>';
            } else {
                updateSummary(0);
                couponMessage.innerHTML = '<span class="text-danger">' + data.message + '</span>';
            }
        } catch(err){
            console.error("Coupon Error:", err);
            couponMessage.innerHTML = '<span class="text-danger">⚠ Error applying coupon.</span>';
        }
    });

    // ---------------------- PLACE ORDER FUNCTION ----------------------
    async function getFormData(paymentMethod = "cod"){
        return {
            _token: "{{ csrf_token() }}",
            payment_method: paymentMethod,
            stripe_method: paymentMethod === "stripe" ? "card" : null,
            coupon_code: couponInput.value,
            name: document.querySelector('input[name="name"]').value || '',
            email: document.querySelector('input[name="email"]').value || '',
            phone: document.querySelector('input[name="phone"]').value || '',
            country: document.getElementById("country").value,
            state: document.getElementById("state").value,
            city: document.getElementById("city").value,
            pincode: document.getElementById("pincode").value,
            street: document.getElementById("street").value
        };
    }

    async function placeOrder(paymentMethod){
        if(isProcessing) return;
        isProcessing = true;

        const data = await getFormData(paymentMethod);

        try{
            // COD
            if(paymentMethod === "cod"){
                codSubmit.disabled = true;
                const res = await fetch("{{ route('checkout.placeOrder') }}", {
                    method:"POST",
                    headers:{ "Content-Type":"application/json","Accept":"application/json"},
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                if(result.success && result.orderId) window.location.href = result.redirect;
                else alert(result.error || "Error processing COD order.");
                codSubmit.disabled = false;
            }

            // STRIPE
            if(paymentMethod === "stripe"){
                payNow.disabled = true;
                const res = await fetch("{{ route('checkout.placeOrder') }}", {
                    method:"POST",
                    headers:{ "Content-Type":"application/json","Accept":"application/json"},
                    body: JSON.stringify(data)
                });
                const result = await res.json();

                if(result.clientSecret && result.orderId){
                    const paymentResult = await stripe.confirmCardPayment(result.clientSecret, {
                        payment_method: { card: cardElement, billing_details:{ name: data.name, email: data.email } }
                    });

                    if(paymentResult.error) alert(paymentResult.error.message);
                    else if(paymentResult.paymentIntent.status === "succeeded"){
                        await fetch("{{ route('checkout.storePayment') }}", {
                            method:"POST",
                            headers:{ "Content-Type":"application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                            body: JSON.stringify({
                                order_id: result.orderId,
                                payment_id: paymentResult.paymentIntent.id,
                                amount: paymentResult.paymentIntent.amount / 100,
                                currency: paymentResult.paymentIntent.currency,
                                status: paymentResult.paymentIntent.status,
                                payment_method: "card"
                            })
                        });
                        const confirmationRoute = "{{ route('customer.orders.confirmation', ':id') }}";
                        window.location.href = confirmationRoute.replace(':id', result.orderId);
                    }
                } else alert(result.error || "Stripe payment failed.");
                payNow.disabled = false;
            }
        }catch(err){ console.error(err); alert("An error occurred: "+err.message); }
        finally{ isProcessing = false; }
    }

    codSubmit.addEventListener("click", e=>{ e.preventDefault(); placeOrder("cod"); });
    payNow.addEventListener("click", e=>{ e.preventDefault(); placeOrder("stripe"); });
});
</script>

@endpush
