<?php

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function rate(Request $request, $id)
    {
        $user = $request->user();
        $product = Product::findOrFail($id);

        // Check if the user has already rated the product
        $existingRating = DB::table('product_user')->where('user_id', $user->id)->where('product_id', $product->id)->exists();
        if ($existingRating) {
            return response()->json(['message' => 'You have already rated this product'], 400);
        }

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|between:1,5', // Assuming rating is between 1 and 5
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $rating = $request->input('rating');

        // Store the rating in the pivot table
        $product->users()->attach($user->id, ['rating' => $rating]);

        // Update product's overall rating (pseudo code)
        // $product->updateRating();

        return response()->json(['message' => 'Product rated successfully'], 200);
    }

    public function unrate(Request $request, $id)
    {
        $user = $request->user();
        $product = Product::findOrFail($id);

        // Check if the user has already rated the product
        $existingRating = DB::table('product_user')->where('user_id', $user->id)->where('product_id', $product->id)->exists();
        if (!$existingRating) {
            return response()->json(['message' => 'You have not rated this product yet'], 400);
        }

        // Remove the rating from the pivot table
        DB::table('product_user')->where('user_id', $user->id)->where('product_id', $product->id)->delete();

        // Update product's overall rating (pseudo code)
        // $product->updateRating();

        return response()->json(['message' => 'Rating removed successfully'], 200);
    }
}
