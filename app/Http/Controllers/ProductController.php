<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Method to add a product
    public function addproduct(Request $request)
    {
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'product_description' => 'required|string|max:255',
            'product_image' => 'required|file|image|mimes:jpg,png,webp,jpeg,gif,svg|max:2048',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            // Handle image upload
            if ($request->hasFile('product_image')) {
                $imagePath = $request->file('product_image')->store('images/products', 'public');
                $imageName = basename($imagePath);
            }

            // Create a new product with validated data and image name
            $product = Product::create([
                'product_name' => $validatedData['product_name'],
                'selling_price' => $validatedData['selling_price'],
                'quantity' => $validatedData['quantity'],
                'product_description' => $validatedData['product_description'],
                'product_image' => $imageName,
                'user_id' => $validatedData['user_id'],
                'category_id' => $validatedData['category_id'],
            ]);

            return response()->json([
                'message' => 'Product added successfully.',
                'product' => $product,
            ], 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add product: ' . $e->getMessage()], 500);
        }
    }

    // View all products
    public function viewproduct()
    {
        $products = Product::all();
        return response()->json($products);
    }

    // Update a product
    public function updateproduct(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Updated validation with nullable for product_image
        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'product_description' => 'required|string|max:255',
            'product_image' => 'nullable|file|image|mimes:jpg,png,webp,jpeg,gif,svg|max:2048',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Handle image upload only if a new image is uploaded
        if ($request->hasFile('product_image')) {
            $imagePath = $request->file('product_image')->store('images/products', 'public');
            $validatedData['product_image'] = basename($imagePath);
        }

        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $product,
        ], 200);
    }

    // Delete a product
    public function deleteproduct($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.'], 200);
    }
}
