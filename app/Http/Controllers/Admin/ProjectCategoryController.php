<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectCategoryController extends Controller
{
    /**
     * Display a listing of project categories.
     */
    public function index(Request $request)
    {
        $categories = ProjectCategory::withCount('projects')->latest()->get();

        if ($request->ajax()) {
            return response()->json(['data' => $categories]);
        }

        return view('admin.projects.categories', compact('categories'));
    }

    /**
     * Store a newly created project category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:project_categories,slug',
            'description' => 'nullable|string',
            'status'      => 'nullable|boolean',
        ]);

        $baseSlug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (ProjectCategory::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $category = ProjectCategory::create([
            'name'        => $request->name,
            'slug'        => $slug,
            'description' => $request->description,
            'status'      => $request->has('status') ? $request->boolean('status') : true,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Category created successfully!',
            'category' => $category,
        ]);
    }

    /**
     * Update the specified project category in storage.
     */
    public function update(Request $request, ProjectCategory $projectCategory)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:project_categories,slug,' . $projectCategory->id,
            'description' => 'nullable|string',
            'status'      => 'nullable|boolean',
        ]);

        $baseSlug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->name);
        $slug = $baseSlug;
        $counter = 1;
        while (ProjectCategory::where('slug', $slug)->where('id', '!=', $projectCategory->id)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        $projectCategory->update([
            'name'        => $request->name,
            'slug'        => $slug,
            'description' => $request->description,
            'status'      => $request->has('status') ? $request->boolean('status') : true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!',
        ]);
    }

    /**
     * Remove the specified project category from storage.
     */
    public function destroy(ProjectCategory $projectCategory)
    {
        if ($projectCategory->projects()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this category because ' . $projectCategory->projects()->count() . ' project(s) are assigned to it.',
            ], 422);
        }

        $projectCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!',
        ]);
    }
}

