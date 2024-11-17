<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;

class CheckoutController extends Controller
{
    /**
     * Initiate the checkout process by creating an order in Razorpay.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    // In CheckoutController.php
// In CheckoutController.php

public function processCheckout(Request $request)
{
    \Log::info('Checkout process initiated.');

    $user = Auth::user();

    if (!$user) {
        \Log::error('User is not authenticated.');
        return redirect()->route('login')->with('error', 'Please log in before proceeding.');
    }

    $request->validate([
        'address' => 'required|string|max:255',
    ]);

    $cartItems = CartItem::with('menuItem')->where('user_id', $user->id)->get();

    if ($cartItems->isEmpty()) {
        \Log::error('Cart is empty.');
        return redirect()->route('cart.show')->with('error', 'Your cart is empty. Please add items before proceeding.');
    }

    \Log::info('Checkout process reached. Total items:', ['count' => $cartItems->count()]);

    // Continue with the checkout process
    return redirect()->route('checkout.initiate')->with('success', 'Checkout process initiated.');
}


public function showCheckout()
{
    // Get the authenticated user
    $user = Auth::user();

    // Fetch the user's cart items
    $cartItems = CartItem::with('menuItem')->where('user_id', $user->id)->get();

    // Check if the cart is empty
    if ($cartItems->isEmpty()) {
        return redirect()->route('cart.show')->with('error', 'Your cart is empty. Please add items before proceeding.');
    }

    // Calculate the total price
    $totalPrice = $cartItems->sum(function ($cartItem) {
        return $cartItem->menuItem->price * $cartItem->quantity;
    });

    // Return the checkout view with cart items and total price
    return view('checkout', compact('cartItems', 'totalPrice'));
}

public function initiateCheckout(Request $request)
{
    $user = Auth::user();
    $cartItems = CartItem::where('user_id', $user->id)->get();

    if ($cartItems->isEmpty()) {
        return response()->json(['error' => 'Your cart is empty.'], 400);
    }

    $totalPrice = $cartItems->sum(function ($item) {
        return $item->menuItem->price * $item->quantity;
    });

    // Create a Razorpay order
    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    $razorpayOrder = $api->order->create([
        'amount' => $totalPrice * 100,
        'currency' => 'INR',
        'payment_capture' => 1,
    ]);

    // Create a new order in the database
    $order = Order::create([
        'user_id' => $user->id,
        'total_price' => $totalPrice,
        'status' => 'pending',
        'razorpay_order_id' => $razorpayOrder->id,
    ]);

    return response()->json([
        'razorpay_order_id' => $razorpayOrder->id,
        'razorpay_amount' => $totalPrice * 100,
        'razorpay_currency' => 'INR',
    ]);
}

    /**
     * Verify the payment after user has completed the transaction.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function verifyPayment(Request $request)
{
    $user = Auth::user();
    $razorpayOrderId = $request->razorpay_order_id;
    $razorpayPaymentId = $request->razorpay_payment_id;
    $razorpaySignature = $request->razorpay_signature;

    $order = Order::where('razorpay_order_id', $razorpayOrderId)->where('user_id', $user->id)->first();

    if (!$order) {
        return response()->json(['error' => 'Order not found.'], 404);
    }

    try {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $attributes = [
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'razorpay_signature' => $razorpaySignature,
        ];

        $api->utility->verifyPaymentSignature($attributes);

        // Update order details
        $order->status = 'completed';
        $order->razorpay_payment_id = $razorpayPaymentId;
        $order->save();

        CartItem::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Payment verified successfully.']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Payment verification failed.'], 400);
    }
}
}
