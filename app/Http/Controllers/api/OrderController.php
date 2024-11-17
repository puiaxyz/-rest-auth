<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Show the orders page for the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function showOrdersView()
    {
        $user = Auth::user();

        // Retrieve all orders for the authenticated user
        $orders = Order::with(['cartItems.menuItem'])->where('user_id', $user->id)->get();

        // Return the Blade view with the orders data
        return view('orders', compact('orders'));
    }

    /**
     * Create an order for the authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function createOrder(Request $request)
    {
        $user = Auth::user();

        // Get the user's cart items
        $cartItems = CartItem::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'No items in the cart'], 400);
        }

        // Calculate the total price
        $totalPrice = $cartItems->sum(fn($item) => $item->menuItem->price * $item->quantity);

        // Create the order
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // Clear the cart after creating the order
        CartItem::where('user_id', $user->id)->delete();

        return response()->json(['message' => 'Order created successfully', 'order' => $order]);
    }

    /**
     * Show the details of a specific order.
     *
     * @param int $orderId
     * @return \Illuminate\Http\Response
     */
    public function showOrderDetails($orderId)
    {
        $order = Order::with('cartItems.menuItem')->findOrFail($orderId);

        return response()->json($order);
    }

    /**
     * Update the status of an order.
     *
     * @param int $orderId
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrderStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:pending,completed,canceled',
        ]);

        $order = Order::findOrFail($orderId);
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status updated', 'order' => $order]);
    }
}
