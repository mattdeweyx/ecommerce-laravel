<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function index()
    {
        try {
            $carts = Cart::all();
            return response()->json($carts, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve carts'], 500);
        }
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
        'quantity' => 'integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 400);
    }

    try {
        // Check if the user already has the same item in their cart
        $existingCartItem = Cart::where('product_id', $request->product_id)
                                ->where('user_id', $request->user()->id)
                                ->first();

        if ($existingCartItem) {
            $existingCartItem->quantity += $request->quantity;

            // Get the product price
            $product = Product::findOrFail($request->product_id);
            $price = $product->price;

            // Calculate total based on quantity and price
            $existingCartItem->total = $existingCartItem->quantity * $price;

            $existingCartItem->save();
            return $this->getCart($request);
        }

        // Get the product price
        $product = Product::findOrFail($request->product_id);
        $price = $product->price;

        // Calculate total based on quantity and price
        $total = $price * $request->quantity;

        $cart = Cart::create([
            'product_id' => $request->product_id,
            'user_id' => $request->user()->id,
            'quantity' => $request->quantity,
            'total' => $total,
        ]);

        return $this->getCart($request);
    } catch (\Exception $e) {
        return response()->json(['error' => $e], 500);
    }
}




    public function show($id)
    {
        try {
            $cart = Cart::findOrFail($id);
            return response()->json($cart, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Cart not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'sometimes|required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        try {
            $cart = Cart::findOrFail($id);
            $cart->update($request->all());

            return response()->json($cart, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update cart item'], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        try {
            $cart = Cart::findOrFail($id);

            // Check if the authenticated user ID matches the user ID associated with the cart
            if ($request->user()->id !== $cart->user_id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $productId = $cart->product_id;
            $cart->delete();
            return $this->getCart($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete cart item' . $e], 500);
        }
    }
    
    public function getCart(Request $request)
    {
        try {
            $carts = Cart::where('user_id', $request->user()->id)->get();
            $cartItems = [];
            $total = 0;
            
            foreach ($carts as $cart) {
                $product = Product::findOrFail($cart->product_id);
                $cartItem = [
                    'cart_item_id' => $cart->id,
                    'product' => $product,
                    'quantity' => $cart->quantity,
                    'total' => $cart->total,
                ];
                $cartItems[] = $cartItem;
                $total += $cart->total;
            }
            
            return response()->json(['items' => $cartItems, 'total' => $total], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function destroyAll(Request $request)
    {
        try {
            $carts = Cart::where('user_id', $request->user()->id)->delete();
            return response()->json(['message' => 'All cart items deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete all cart items ' . $e], 500);
        }
    }
    public function decrease(Request $request, $id)
    {
        try {
            $cart = Cart::findOrFail($id);
            
            if ($cart->quantity > 1) {
                $cart->quantity--;
                $cart->total = $cart->quantity * $cart->product->price;
                $cart->save();
            } else {
                $cart->delete(); // Remove the item from the cart if quantity is 1
            }

            return response()->json(['message' => 'Cart item quantity decreased successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to decrease cart item quantity'], 500);
        }
    }

    public function increase(Request $request, $id)
    {
        try {
            $cart = Cart::findOrFail($id);
            
            $cart->quantity++;
            $cart->total = $cart->quantity * $cart->product->price;
            $cart->save();

            return response()->json(['message' => 'Cart item quantity increased successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to increase cart item quantity'], 500);
        }
    }

    
    public function getCartDetails(Request $request)
    {
        try {
            $carts = Cart::where('user_id', $request->user()->id)->get();
            $cartItems = [];
            
            foreach ($carts as $cart) {
                $product = Product::findOrFail($cart->product_id);
                $cartItem = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $cart->quantity,
                    'total' => $cart->total,
                ];
                $cartItems[] = $cartItem;
            }
            
            return response()->json($cartItems, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCartCount(Request $request)
    {
        try {
            $count = Cart::where('user_id', $request->user()->id)->count();
            return response()->json(['count' => $count], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve user cart count'], 500);
        }
    }

}
