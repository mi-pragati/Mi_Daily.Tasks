@extends('lay_app')

@section('title', 'Your Cart')

@section('content')
@php use Illuminate\Support\Str; @endphp
<div class="container py-4">
  <h1 class="mb-3">Shopping Cart</h1>

  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @if(empty($items))
    <div class="alert alert-info">Your cart is empty.</div>
    <a href="{{ route('products.index') }}" class="btn btn-primary">Continue Shopping</a>
  @else
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Product</th>
            <th style="width:120px;">Image</th>
            <th style="width:140px;">Price (₹)</th>
            <th style="width:160px;">Quantity</th>
            <th style="width:140px;">Line Total (₹)</th>
            <th style="width:120px;">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $row)
            <tr>
              <td>{{ $row['title'] }}</td>
              <td>
                @if(!empty($row['image']))
                  <img src="{{ $row['image'] }}" alt="{{ $row['title'] }}" class="img-fluid rounded" style="max-height:80px">
                @else
                  <img src="https://placehold.co/120x80" class="img-fluid rounded" alt="No image">
                @endif
              </td>
              <td>₹{{ number_format($row['price'], 2) }}</td>
              <td>
                <form method="POST" action="{{ route('cart.update', $row['id']) }}" class="d-flex gap-2">
                  @csrf
                  @method('PATCH')
                  <input type="number" name="qty" min="1" value="{{ $row['qty'] }}" class="form-control" style="width:90px">
                  <button class="btn btn-outline-primary btn-sm">Update</button>
                </form>
              </td>
              <td>₹<span data-line-total>{{ number_format($row['price'] * $row['qty'], 2) }}</span></td>
              <td>
                <form method="POST" action="{{ route('cart.destroy', $row['id']) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Remove this item?')">Remove</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-end">Subtotal</th>
            <th colspan="2">₹<span data-cart-subtotal>{{ number_format($totals['subtotal'], 2) }}</span></th>
          </tr>
          <tr>
            <th colspan="4" class="text-end">Total</th>
            <th colspan="2">₹<span data-cart-total>{{ number_format($totals['total'], 2) }}</span></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex justify-content-between">
      <form method="POST" action="{{ route('cart.clear') }}">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-secondary">Clear Cart</button>
      </form>

      <a href="{{ route('checkout') }}" class="btn btn-success">Proceed to Checkout</a>
    </div>
  @endif
</div>

{{-- Lightweight AJAX to update totals without a full reload --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Attach only to the PATCH (quantity update) forms
  document.querySelectorAll('form').forEach(form => {
    if (!form.querySelector('input[name="_method"][value="PATCH"]')) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const url = form.action;
      const token = form.querySelector('input[name="_token"]').value;
      const formData = new FormData(form);
      formData.append('_method', 'PATCH');

      try {
        const res = await fetch(url, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
          },
          body: formData
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok || !data.ok) {
          alert((data && data.error) ? data.error : 'Could not update quantity.');
          return;
        }

        // Update line total in this row
        const row = form.closest('tr');
        if (row) {
          const lineCell = row.querySelector('[data-line-total]');
          if (lineCell) lineCell.textContent = Number(data.line_total).toFixed(2);
        }

        // Update subtotal & total
        const subCell = document.querySelector('[data-cart-subtotal]');
        if (subCell) subCell.textContent = Number(data.subtotal).toFixed(2);

        const totalCell = document.querySelector('[data-cart-total]');
        if (totalCell) totalCell.textContent = Number(data.total).toFixed(2);

        // Update the cart item count dynamically
        updateCartItemCount();

      } catch (err) {
        console.error(err);
        alert('Network error. Please try again.');
      }
    });
  });
});

// Update cart item count in the navbar or wherever you want to show it
const updateCartItemCount = async () => {
  try {
    const res = await fetch('{{ route("cart.count") }}', { method: 'GET' });
    const data = await res.json();
    if (data.ok) {
      const count = data.count || 0;
      document.getElementById('cart-item-count').textContent = count;
    }
  } catch (err) {
    console.error('Error updating cart item count:', err);
  }
};

// Call this on page load to initialize the cart item count
updateCartItemCount();
</script>
@endsection
