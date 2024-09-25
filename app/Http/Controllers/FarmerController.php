<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use Symfony\Contracts\Service\Attribute\Required;

class FarmerController extends Controller
{
    // Method to store a new category
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255',
            'category_description' => 'nullable|string',
        ]);

        try {
            $category = Category::create($validatedData);

            return response()->json([
                'message' => 'Category added successfully.',
                'category' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to add category: ' . $e->getMessage()], 500);
        }
    }

    // List all categories
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Method to show a specific category
    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        return response()->json($category);
    }

    // Method to update a category
    public function update(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $validatedData = $request->validate([
            'category_name' => 'required|string|max:255',
            'category_description' => 'nullable|string',
        ]);

        $category->update($validatedData);

        return response()->json([
            'message' => 'Category updated successfully.',
            'category' => $category,
        ], 200);
    }

    // Method to delete a category
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found.'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 204);
    }



    //product
    public function addproduct(Request $request)
    {

        $validatedData = $request->validate([
            'product_name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'product_description' => 'required|string|max:255',
            'product_image' => 'required|string',
            'farmer_id' => 'required|exists:farmers,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        try {
            // Create a new product with validated data
            $product = Product::create([
                'product_name' => $validatedData['product_name'],
                'selling_price' => $validatedData['selling_price'],
                'quantity' => $validatedData['quantity'],
                'product_description' => $validatedData['product_description'],
                'product_image' => $validatedData['product_image'],
                'farmer_id' => $validatedData['farmer_id'],
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

    public function viewproduct()
    {
        $product = Product::all();
        return response()->json($product);
    }
    public function updateproduct(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $validatedData = $request->validate([
           'product_name' => 'required|string|max:255',
            'selling_price' => 'nullable|numeric',
            'quantity' => 'required|integer',
            'product_description' => 'required|string|max:255',
            'product_image' => 'required|string',
            'farmer_id' => 'required|exists:farmers,id',
            'category_id' => 'required|exists:categories,id',
        ]);

        $product->update($validatedData);

        return response()->json([
            'message' => 'Product updated successfully.',
            'product' => $product,
        ], 200);
    }
    public function deleteproduct($id)
    {
        $product =Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.'], 204);
    }

}
