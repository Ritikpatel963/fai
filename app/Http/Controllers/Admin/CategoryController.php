<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $categories = Category::with('parent')->latest()->get();
            return response()->json(['data' => $categories]);
        }
        $categories = Category::with('parent')->latest()->get();
        return view('admin.blogs.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $slug = Str::slug($request->slug ?? $request->name);
        
        // Ensure unique slug
        $originalSlug = $slug;
        $counter = 1;
        while(Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
        }

        $category = Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'image' => $imagePath,
            'status' => $request->status ?? true,
        ]);

        return response()->json(['success' => true, 'category' => $category]);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $slug = Str::slug($request->slug ?? $request->name);
        
        $originalSlug = $slug;
        $counter = 1;
        while(Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data = [
            'name' => $request->name,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'status' => $request->status ?? true,
        ];

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);

        return response()->json(['success' => true]);
    }

    public function destroy(Category $category)
    {
        if ($category->blogs()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete category because it has blogs attached.'], 403);
        }
        
        if ($category->children()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Cannot delete category because it has subcategories.'], 403);
        }

        $category->delete();
        return response()->json(['success' => true]);
    }
}
