<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;

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
    try {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $cartItems = CartItem::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['error' => 'Your cart is empty.'], 400);
        }

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->menuItem->price * $item->quantity;
        });

        // Log the total price for debugging
        \Log::info("Total price: $totalPrice");

        // Create a Razorpay order
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $razorpayOrder = $api->order->create([
            'amount' => $totalPrice * 100,
            'currency' => 'INR',
            'payment_capture' => 1,
        ]);

        // Log the Razorpay order for debugging
        \Log::info("Razorpay Order: " . json_encode($razorpayOrder));

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
    } catch (\Exception $e) {
        \Log::error("Error in initiateCheckout: " . $e->getMessage());
        return response()->json(['error' => 'Server error. Please try again later.'], 500);
    }
}


    /**
     * Verify the payment after user has completed the transaction.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function verifyPayment(Request $request)
    {
        $razorpayOrderId = $request->input('razorpay_order_id');
        $razorpayPaymentId = $request->input('razorpay_payment_id');
        $razorpaySignature = $request->input('razorpay_signature');
    
        try {
            $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    
            // Create the expected signature
            $generatedSignature = hash_hmac(
                'sha256',
                $razorpayOrderId . '|' . $razorpayPaymentId,
                env('RAZORPAY_SECRET')
            );
    
            // Verify the signature
            if ($generatedSignature !== $razorpaySignature) {
                Log::error("Signature verification failed");
                return response()->json(['error' => 'Payment verification failed'], 400);
            }
    
            // Update the order status in the database
            $order = Order::where('razorpay_order_id', $razorpayOrderId)->first();
    
            if ($order) {
                $order->status = 'completed';
                $order->razorpay_payment_id = $razorpayPaymentId;
                $order->razorpay_signature = $razorpaySignature;
                $order->save();
            }
    
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error("Error in verifyPayment: " . $e->getMessage());
            return response()->json(['error' => 'Payment verification failed'], 500);
        }
    }
}
