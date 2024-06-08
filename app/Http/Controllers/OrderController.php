<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class OrderController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Retrieve cart items for the specific user
            $cartItems = Cart::where('user_id', $request->user()->id)->get();

            // Create a new order
            $order = Order::create([
                'order_id' => 'A' . strtoupper(Str::random(20)), // Generate a unique order ID
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'address' => $request->input('address'),
                'city' => $request->input('city'),
                'zip_code' => $request->input('zip_code'),
                'country' => $request->input('country'),
                'phone' => $request->input('phone'),
                'total_amount' => 0, // Initial total amount
                'user_id' => $request->user()->id,
                'status' => 'pending',
            ]);

            $totalAmount = 0;

            // Create order items for each cart item and update total amount
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;

                // Calculate total for the order item
                $total = $cartItem->quantity * $product->price;

                // Create order item
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'quantity' => $cartItem->quantity,
                    'total' => $total,
                ]);

                // Update total amount for the order
                $totalAmount += $total;
            }

            // Update total amount for the order
            $order->total_amount = $totalAmount;
            $order->save();

            // Destroy the cart items after conversion
            $this->destroyCartItems($request);

            return response()->json(['message' => 'Order created successfully', 'order_id' => $order->order_id], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create order', 'message' => $e->getMessage()], 500);
        }
    }

    // Method to destroy cart items after conversion to order items
    private function destroyCartItems(Request $request)
    {
        try {
            // Retrieve cart items for the specific user
            $cartItems = Cart::where('user_id', $request->user()->id)->get();

            // Destroy each cart item
            foreach ($cartItems as $cartItem) {
                $cartItem->delete();
            }
        } catch (\Exception $e) {
            // Log or handle the error accordingly
        }
    }
}
