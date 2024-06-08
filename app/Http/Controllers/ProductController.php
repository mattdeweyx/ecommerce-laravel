<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all()->toArray();
        shuffle($products);
        return response()->json($products);
    }

    public function store(Request $request)
{
    try {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'concentration' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'ratingCount' => 'nullable|integer',
            'ratingScore' => 'nullable|numeric',
        ]);

        // Handle the image upload
        $imagePath = $request->file('image')->store('products', 'public');

        // Merge the image URL with the validated data
        $productData = array_merge($validatedData, ['imageURL' => '/storage/' . $imagePath]);

        // Create a new product
        $product = Product::create($productData);

        // Return a JSON response
        return response()->json($product, 201);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to create product: ' . $e->getMessage()], 500);
    }
}


    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'nullable|string|max:255',
            'brand' => 'nullable|string|max:255',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'concentration' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:255',
            'imageURL' => 'nullable|string|max:255',
            'ratingCount' => 'nullable|integer',
            'ratingScore' => 'nullable|numeric',
        ]);

        $product = Product::findOrFail($id);
        $product->update($validatedData);
        return response()->json($product);
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete product: ' . $e->getMessage()], 500);
        }
    }
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

    public function indexCategory()
    {
        $categories = Product::select('category')->distinct()->get()->pluck('category')->toArray();

        $productsByCategory = [];

        foreach ($categories as $category) {
            $productsByCategory[$category] = Product::where('category', $category)->get();
        }

        return response()->json($productsByCategory);
    }

    public function showCategory($category)
    {
        $products = Product::where('category', $category)->get();
        return response()->json($products);
    }

    public function showPopular($numberOfProducts)
    {
        // Retrieve the specified number of popular products
        $popularProducts = Product::orderBy('ratingCount', 'desc')
            ->take($numberOfProducts)
            ->get();

        // You can customize this logic based on how you determine popularity

        return response()->json($popularProducts);
    }

    public function showNewest($numberOfProducts)
    {
        $newestProducts = Product::orderBy('created_at', 'desc')
            ->take($numberOfProducts)
            ->get();

        return response()->json($newestProducts);
    }

    public function search(Request $request)
    {
        // Validate the search query parameter
        $request->validate([
            'query' => 'required|string|min:1', // Adjust the validation rules as needed
        ]);

        // Perform the search query
        $query = $request->input('query');
        $results = Product::where('name', 'like', "%{$query}%")
                          ->orWhere('brand', 'like', "%{$query}%")
                          ->get();

        return response()->json($results);
    }

    public function showHomeApp(
        $homePopularNumberOfProducts,
        $homeNewestNumberOfProducts,
        $popularNumberOfProducts,
        $menNumberOfProducts,
        $womenNumberOfProducts,
        $favoriteNumberOfProducts
    )
    {
        $homeApp = [
            "home" => [
                "popular" => Product::orderBy('ratingCount', 'desc')->take($homePopularNumberOfProducts)->get(),
                "newest" => Product::orderBy('created_at', 'desc')->take($homeNewestNumberOfProducts)->get(),
            ],
            "popular" => Product::orderBy('ratingCount', 'desc')->take($popularNumberOfProducts)->get(),
            "men" => Product::where('category', 'men')->take($menNumberOfProducts)->get(),
            "women" => Product::where('category', 'women')->take($womenNumberOfProducts)->get(),
            "favorites" => Product::take($favoriteNumberOfProducts)->get(),
        ];

        // Shuffle each section of the homeApp array
        $homeApp['home']['popular']->shuffle();
        $homeApp['home']['newest']->shuffle();
        $homeApp['popular']->shuffle();
        $homeApp['men']->shuffle();
        $homeApp['women']->shuffle();
        $homeApp['favorites']->shuffle();

        return response()->json($homeApp);
    }
}
