@extends('layouts.app')

@section('content')
<h1>Your Orders</h1>

@if ($orders->isEmpty())
    <p>You have no orders yet.</p>
@else
    <table class="table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Status</th>
                <th>Total Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ ucfirst($order->status) }}</td>
                    <td>${{ number_format($order->total_price, 2) }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info">View Details</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
@endsection
