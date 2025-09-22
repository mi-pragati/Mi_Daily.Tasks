@extends('admin.layouts.app')

@section('title', 'All Orders')

@php
use App\Models\Order;
@endphp

@section('content')
<div class="container py-4">
    <h1>All Orders</h1>

    {{-- 🔍 Search & Status Filters --}}
    <div class="card mb-3 p-3 shadow-sm">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-2 align-items-center">
            {{-- Search --}}
            <div class="col-md-4">
                <input type="text" id="search" name="search" class="form-control form-control-sm"
                       value="{{ request('search') }}"
                       placeholder="Order ID, Name, Email, Phone">
            </div>

            {{-- Status --}}
            <div class="col-md-3">
                <select id="status" name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="{{ Order::STATUS_PENDING }}" {{ request('status') == Order::STATUS_PENDING ? 'selected' : '' }}>
                        Pending
                    </option>
                    <option value="{{ Order::STATUS_COMPLETED }}" {{ request('status') == Order::STATUS_COMPLETED ? 'selected' : '' }}>
                        Completed
                    </option>
                </select>
            </div>

            {{-- Submit --}}
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Total (₹)</th>
                <th>Order Status</th> {{-- Renamed --}}
                <th>Order Date</th>
                <th>Delivery Status</th> {{-- New Column --}}
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td>{{ $order->id }}</td>
                <td>{{ $order->user->name ?? 'Guest' }}</td>
                <td>{{ $order->email }}</td>
                <td>{{ $order->phone }}</td>
                <td>{{ $order->address }}</td>
                <td>₹{{ number_format($order->total, 2) }}</td>

                {{-- Editable Order Status --}}
                <td>
                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                            <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </form>
                </td>

                {{-- Order Date --}}
                <td>{{ $order->created_at->format('d-m-Y h:i A') }}</td>

                {{-- Delivery Status --}}
                <td>
                    @php
                        $return = $order->latestReturn;
                    @endphp

                    @if($return)
                        @if($return->status === 'Return Pending')
                            <span class="badge border-warning text-dark">Return Pending</span>
                        @elseif($return->status === 'Returned')
                            <span class="badge border border-success text-success">Returned</span>
                        @elseif($return->status === 'Rejected')
                            <span class="badge border border-danger text-danger">Return Rejected</span>
                        @endif
                    @else
                        <span class="badge border border-warning text-dark">{{ $order->delivery_status ?? 'Pending' }}</span>
                    @endif
                </td>


                {{-- Action --}}
                <td class="text-center">
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn p-0 border-0 bg-transparent focus-ring-0" title="View Order">
                        <i class="bi bi-eye"></i>
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

{{-- 🔔 Success Popup --}}
@push('scripts')
    @if(session('success'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 3000
            });
        </script>
    @endif
@endpush
