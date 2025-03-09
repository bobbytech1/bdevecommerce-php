<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Get all orders for the authenticated user
    public function getOrders(Request $request)
    {
        // Check if the authenticated user is an admin
        if ($request->user()->role === 'admin') {
            // Admin can access all orders
            $orders = Order::with('items')->get();
        } else {
            // Regular users can only access their own orders
            $orders = $request->user()->orders()->with('items')->get();
        }
    
        return response()->json($orders);
    }

    // Create a new order
    public function saveOrder(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => 0,
                'status' => 'pending',
            ]);

            $totalAmount = 0;

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                if ($product->stock_quantity < $item['quantity']) {
                    return response()->json(['message' => "Insufficient stock for {$product->name}"], 400);
                }

                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ]);

                // Reduce stock quantity
                $product->decrement('stock_quantity', $item['quantity']);
            }

            $order->update(['total_amount' => $totalAmount]);

            DB::commit();
            return response()->json($order, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // View an order
    public function showOrder(Order $order)
    {
        return response()->json($order->load('items'));
    }

    // Update an order
public function updateOrder(Order $order, Request $request)
{
    $request->validate([
        'status' => 'required|in:pending,completed,canceled',
    ]);

    $order->update(['status' => $request->status]);

    return response()->json(['message' => 'Order updated successfully', 'order' => $order]);
}

// Delete an order 
public function deleteOrder(Order $order)
{
    $order->delete();
    return response()->json(['message' => 'Order deleted successfully']);
}
}
