@extends('layouts.app')

@section('content')
<h1>Order Confirmation</h1>
<p>Your order #{{ $order->id }} has been successfully placed!</p>
<p>Total Amount: ${{ number_format($order->total_price, 2) }}</p>
<p>Status: {{ ucfirst($order->status) }}</p>
@endsection
