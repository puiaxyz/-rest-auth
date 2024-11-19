<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class StaffDashboardController extends Controller
{
    // Display the staff dashboard with pending and completed orders
    public function index()
    {
        $pendingOrders = Order::where('status', 'pending')->with('items.menuItem')->get();
        $completedOrders = Order::where('status', 'completed')
            ->whereDate('updated_at', now()->toDateString())
            ->with('items.menuItem')
            ->get();

        return view('staff.dashboard', compact('pendingOrders', 'completedOrders'));
    }

    // Update the status of an order
    public function updateOrderStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $newStatus = $request->input('status');

        if (!in_array($newStatus, ['completed', 'canceled'])) {
            return redirect()->back()->with('error', 'Invalid status update.');
        }

        $order->status = $newStatus;
        $order->save();

        return redirect()->route('staff.dashboard')->with('success', 'Order status updated successfully.');
    }
}
