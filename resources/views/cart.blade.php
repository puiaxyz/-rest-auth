@extends('layouts.app')

@section('content')
<h1>Your Cart</h1>

@if (count($cartItems) > 0)
    <ul>
        @foreach ($cartItems as $item)
            <li>
                <strong>{{ $item->menuItem->name }}</strong>
                - ${{ $item->menuItem->price }} (x{{ $item->quantity }})
                <input type="number" id="quantity-{{ $item->id }}" value="{{ $item->quantity }}" min="1">
                <button onclick="updateQuantity({{ $item->id }})">Update</button>

                <form action="{{ route('cart.remove', $item->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Remove</button>
                </form>
            </li>
        @endforeach
    </ul>

    <p><strong>Total: ${{ $totalPrice }}</strong></p>
    <a href="{{ route('checkout') }}">Proceed to Checkout</a>
@else
    <p>Your cart is empty.</p>
@endif

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function updateQuantity(cartItemId) {
        const quantity = document.getElementById(`quantity-${cartItemId}`).value;

        // If the quantity is 0, we remove the item
        if (quantity == 0) {
            removeFromCart(cartItemId);
            return;
        }

        axios.patch(`/api/cart/${cartItemId}`, {
            cart_item_id: cartItemId,
            quantity: quantity
        })
        .then(response => {
            alert(response.data.message);
            location.reload(); // Refresh the page to reflect the changes
        })
        .catch(error => {
            console.error('Error updating quantity:', error);
            alert('Failed to update quantity. Please try again.');
        });
    }

    function removeFromCart(cartItemId) {
        axios.delete(`/api/cart/${cartItemId}`)
            .then(response => {
                alert(response.data.message);
                location.reload(); // Refresh the page to reflect the changes
            })
            .catch(error => {
                console.error('Error removing item:', error);
                alert('Failed to remove item. Please try again.');
            });
    }
</script>
@endsection
