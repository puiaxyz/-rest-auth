@extends('layouts.app')

@section('content')
<h1>Order #{{ $order->id }} Details</h1>

<div class="order-details">
    <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
    <p><strong>Total Price:</strong> ${{ number_format($order->total_price, 2) }}</p>

    <h3>Items in Order:</h3>
    <ul>
        @foreach ($order->cartItems as $cartItem)
            <li>
                <strong>{{ $cartItem->menuItem->name }}</strong> - ${{ number_format($cartItem->menuItem->price, 2) }} (x{{ $cartItem->quantity }})
            </li>
        @endforeach
    </ul>

    <p><strong>Placed On:</strong> {{ $order->created_at->format('M d, Y') }}</p>

    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
</div>
@endsection
