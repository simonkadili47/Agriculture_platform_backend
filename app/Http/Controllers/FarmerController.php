<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

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
}
