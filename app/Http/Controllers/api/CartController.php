<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Models\CartItem;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    /**
     * Show the user's cart.
     *
     * @return \Illuminate\View\View
     */
    public function showCart()
    {
        // Get the authenticated user
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');  // If no user is authenticated, redirect to login page
        }

        // Get the cart items for the user
        $cartItems = CartItem::with('menuItem')->where('user_id', $user->id)->get();

        // Calculate total price
        $totalPrice = $cartItems->sum(function ($cartItem) {
            return $cartItem->menuItem->price * $cartItem->quantity;
        });

        // Return the view with cart items and total price
        return view('cart', compact('cartItems', 'totalPrice'));
    }

    /**
     * Add an item to the cart.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        \Log::info('User:', ['user' => Auth::user()]);
    
        if (!Auth::check()) {
            \Log::info('User is not authenticated');
            return redirect()->route('login');
        }
        // Validate the request
        $request->validate([
            'menu_item_id' => 'required|exists:menu_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Get the authenticated user
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');  // If no user is authenticated, redirect to login page
        }

        $menuItem = MenuItem::find($request->menu_item_id);

        // Check if the item is already in the cart
        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('menu_item_id', $menuItem->id)
                            ->first();

        if ($cartItem) {
            // If the item is already in the cart, update the quantity
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // If the item is not in the cart, create a new cart item
            CartItem::create([
                'user_id' => $user->id,
                'menu_item_id' => $menuItem->id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['message' => 'Item added to cart successfully']);
    }

    /**
     * Update the quantity of a cart item.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateQuantity(Request $request)
{
    // Validate the request
    $request->validate([
        'cart_item_id' => 'required|exists:cart_items,id',
        'quantity' => 'required|integer|min:1'
    ]);

    // Get the cart item
    $cartItem = CartItem::find($request->cart_item_id);
    $cartItem->quantity = $request->quantity;
    $cartItem->save();

    return response()->json(['message' => 'Cart item updated successfully']);
}


    /**
     * Remove an item from the cart.
     *
     * @param int $cartItemId
     * @return \Illuminate\Http\Response
     */
    public function removeFromCart($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cartItem->delete();

        return response()->json(['message' => 'Item removed from cart']);
    }
}
