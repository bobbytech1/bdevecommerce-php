<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct()
    {
        // Apply JWT authentication to all methods except getProducts and getSingleProduct
        $this->middleware('auth:jwt')->except(['getProducts', 'getSingleProduct']);
    }

    // Get all products
    public function getProducts()
    {
        return response()->json(Product::all());
    }

    // Store a new product (Admin only)
    public function saveProduct(Request $request)
    {
        Log::info('Authenticated user:', auth()->user()->toArray()); // Log the authenticated user

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
        ]);

        try {
            $product = Product::create($request->all());
            return response()->json($product, 201);
        } catch (\Exception $e) {
            Log::error('Error creating product:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create product'], 500);
        }
    }

    // Show a single product
    public function getSingleProduct(Product $product)
    {
        return response()->json($product);
    }

    // Update a product (Admin only)
    public function updateProduct(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'string|max:255',
            'description' => 'string',
            'price' => 'numeric|min:0',
            'stock_quantity' => 'integer|min:0',
        ]);

        try {
            $product->update($request->all());
            return response()->json($product);
        } catch (\Exception $e) {
            Log::error('Error updating product:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to update product'], 500);
        }
    }

    // Delete a product (Admin only)
    public function deleteProduct(Product $product)
    {
        try {
            $product->delete();
            return response()->json(['message' => 'Product deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Error deleting product:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to delete product'], 500);
        }
    }
}