@extends('layouts.app')

@section('content')
<h1>Staff Dashboard</h1>

<!-- Toggle Buttons -->
<div class="d-flex mb-4">
    <button id="togglePending" class="btn btn-primary">Pending Orders</button>
    <button id="toggleCompleted" class="btn btn-secondary">Completed Orders</button>
</div>

<!-- Pending Orders Table -->
<div id="pendingOrdersTable">
    <h3>Pending Orders</h3>
    @if ($pendingOrders->isEmpty())
        <p>No pending orders.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Price</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>${{ number_format($order->total_price, 2) }}</td>
                        <td>
                            <ul>
                                @foreach ($order->items as $item)
                                    <li>{{ $item->quantity }}x {{ $item->menuItem->name }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>
                            <form action="{{ route('staff.orders.update', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-success">Complete</button>
                            </form>
                            <form action="{{ route('staff.orders.update', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="canceled">
                                <button type="submit" class="btn btn-danger">Cancel</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<!-- Completed Orders Table -->
<div id="completedOrdersTable" style="display: none;">
    <h3>Completed Orders (Today)</h3>
    @if ($completedOrders->isEmpty())
        <p>No completed orders for today.</p>
    @else
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total Price</th>
                    <th>Items</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($completedOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->user->name }}</td>
                        <td>${{ number_format($order->total_price, 2) }}</td>
                        <td>
                            <ul>
                                @foreach ($order->items as $item)
                                    <li>{{ $item->quantity }}x {{ $item->menuItem->name }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ ucfirst($order->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<!-- JavaScript for toggling views -->
<script>
    document.getElementById('togglePending').addEventListener('click', () => {
        document.getElementById('pendingOrdersTable').style.display = 'block';
        document.getElementById('completedOrdersTable').style.display = 'none';
    });

    document.getElementById('toggleCompleted').addEventListener('click', () => {
        document.getElementById('pendingOrdersTable').style.display = 'none';
        document.getElementById('completedOrdersTable').style.display = 'block';
    });
</script>
@endsection
