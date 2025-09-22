@extends('layouts.customer')

@section('title', 'My Orders')

@php
use App\Models\Order;
@endphp


@section('content')
<div class="container py-4">
    <h3 class="mb-4">My Orders</h3>

    {{-- 🔍 Search & Status Filters --}}
    <div class="card mb-3 p-3 shadow-sm">
        <form method="GET" action="{{ route('customer.orders.index') }}" class="row g-2 align-items-center">
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

    {{-- Orders Table --}}
    @if(!$orders || $orders->isEmpty())
        <div class="alert alert-info text-center">You have no orders yet.</div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Order Id</th>
                                <th>Date & Time</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Action</th>
                                <th>Re-Order</th>
                                <th>Return</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                                <tr>
                                    {{-- Order ID --}}
                                    <td>{{ $order->id }}</td>

                                    {{-- Order Date --}}
                                    <td>{{ $order->created_at->format('d-m-Y h:i A') }}</td>
                                    {{-- Total --}}
                                    <td>₹{{ number_format($order->total, 2) }}</td>

                                    {{-- Status --}}
                                  <td>
                                    <span class="badge 
                                         @if(Str::startsWith(strtolower($order->status), 'completed')) bg-success
                                            @elseif(Str::startsWith(strtolower($order->status), 'pending')) bg-warning text-dark
                                            @endif">
                                    {{ ucfirst($order->status) }}
                                    </span>
                                </td>


                                    {{-- Action --}}
                                    <td>
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                            View
                                        </a>
                                    </td>

                                     {{-- Reorder --}}
            <td>
                <form action="{{ route('customer.orders.reorder', $order->id) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-success px-2 py-1">
                    Re-Order
                </button>
                </form>
            </td>

           {{-- Return --}}
<td>
    @php
        $latestReturn = $order->returns()->latest()->first();
    @endphp

    @if($latestReturn)
        @php $returnStatus = $latestReturn->status; @endphp
        <span class="btn btn-sm 
            @if($returnStatus === 'Return Pending') btn-warning text-dark
            @elseif($returnStatus === 'Returned') btn-outline-success
            @elseif($returnStatus === 'Rejected') btn-danger
            @endif" disabled>
            @if($returnStatus === 'Returned')
                Returned
            @elseif($returnStatus === 'Rejected')
                Return Rejected
            @else
                {{ $returnStatus }}
            @endif
        </span>
    @else
        <a href="{{ route('customer.orders.return', $order->id) }}" 
           class="btn btn-sm btn-outline-danger">
           Return
        </a>
    @endif
</td>


                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $orders->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
