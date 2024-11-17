@extends('layouts.app')

@section('content')
<h1>Checkout</h1>

@if ($cartItems->isEmpty())
    <p>Your cart is empty. Please add items before proceeding.</p>
@else
    <h3>Cart Summary</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cartItems as $cartItem)
                <tr>
                    <td>{{ $cartItem->menuItem->name }}</td>
                    <td>{{ $cartItem->quantity }}</td>
                    <td>${{ number_format($cartItem->menuItem->price, 2) }}</td>
                    <td>${{ number_format($cartItem->menuItem->price * $cartItem->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Total Price: ${{ number_format($totalPrice, 2) }}</h4>

    <button id="proceedToPayment" class="btn btn-primary">Proceed to Payment</button>
@endif

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('proceedToPayment').onclick = function() {
        axios.post('/api/checkout')
            .then(response => {
                const orderData = response.data;

                const options = {
                    "key": "{{ env('RAZORPAY_KEY') }}",
                    "amount": orderData.razorpay_amount,
                    "currency": orderData.razorpay_currency,
                    "name": "Your Restaurant",
                    "description": "Order Payment",
                    "order_id": orderData.razorpay_order_id,
                    "handler": function(paymentResponse) {
                        // Payment successful, send data to verify
                        axios.post('/api/checkout/verify', {
                            razorpay_order_id: paymentResponse.razorpay_order_id,
                            razorpay_payment_id: paymentResponse.razorpay_payment_id,
                            razorpay_signature: paymentResponse.razorpay_signature,
                        }).then(response => {
                            alert('Payment successful! Redirecting to receipt page.');
                            window.location.href = '/orders/receipt';
                        }).catch(error => {
                            alert('Payment verification failed. Please try again.');
                        });
                    },
                    "prefill": {
                        "name": "{{ Auth::user()->name }}",
                        "email": "{{ Auth::user()->email }}"
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();
            })
            .catch(error => {
                alert('Failed to initiate payment. Please try again.');
                console.error(error);
            });
    };
</script>
@endsection
