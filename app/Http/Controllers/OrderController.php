<?php

namespace App\Http\Controllers;

use App\Actions\CreateOrderAction;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
{
    try {
        // Fetch all orders with their items, ordered by most recent first
        $orders = Order::with('items')->orderBy('created_at', 'DESC')->get();

        // Group orders by provider_name
        $groupedOrders = $orders->groupBy('provider_name');

        // Sort the grouped orders by provider name (alphabetical order)
        $sortedGroupedOrders = $groupedOrders->sortKeys();

        return response()->json([
            'success' => true,
            'data' => $orders // Grouped and sorted data by provider_name
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error fetching order items'
        ], 500);
    }
}


    public function store(Request $request)
    {
        $validated = $request->validate([
            'hmo_code' => 'required|string|exists:hmos,code',
            'provider_name' => 'required|string',
            'encounter_date' => 'required|date',
            'items' => 'required|array',
            'items.*.name' => 'required|string',
            'items.*.unit_price' => 'required|numeric',
            'items.*.quantity' => 'required|integer',
            'total_amount' => 'required|numeric',
        ]);

        // Call the action with the validated data
        $order = CreateOrderAction::run($validated);

        return response()->json(['success' => true, 'message' => 'Order submitted successfully!', 'order' => $order], 201);

    }

}
