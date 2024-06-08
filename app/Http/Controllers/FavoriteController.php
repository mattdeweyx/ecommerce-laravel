<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();

        // Retrieve the user's favorite products
        $favorites = $user->favoriteProducts()->get();

        return response()->json(['favorites' => $favorites], 200);
    }

    public function addToFavorites(Request $request, $id): Response
    {
        $user = $request->user();
        $product = Product::findOrFail($id);

        // Check if the product is already in the user's favorites
        $existingFavorite = DB::table('favorite_product')->where('user_id', $user->id)->where('product_id', $product->id)->exists();
        if ($existingFavorite) {
            return response()->json(['message' => 'This product is already in your favorites'], 400);
        }

        // Add the product to the user's favorites
        $user->favoriteProducts()->attach($product->id);

        return response()->json(['message' => 'Product added to favorites successfully'], 200);
    }

    public function removeFromFavorites(Request $request, $id): Response
    {
        $user = $request->user();
        $product = Product::findOrFail($id);

        // Check if the product is in the user's favorites
        $existingFavorite = DB::table('favorite_product')->where('user_id', $user->id)->where('product_id', $product->id)->exists();
        if (!$existingFavorite) {
            return response()->json(['message' => 'This product is not in your favorites'], 400);
        }

        // Remove the product from the user's favorites
        $user->favoriteProducts()->detach($product->id);

        return response()->json(['message' => 'Product removed from favorites successfully'], 200);
    }
}
