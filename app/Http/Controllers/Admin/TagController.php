<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tags = Tag::latest()->get();
            return response()->json(['data' => $tags]);
        }
        return view('admin.blogs.tags');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags',
        ]);

        $slug = Str::slug($request->slug ?? $request->name);
        
        $originalSlug = $slug;
        $counter = 1;
        while(Tag::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $tag = Tag::create([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return response()->json(['success' => true, 'tag' => $tag]);
    }

    public function update(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags,slug,' . $tag->id,
        ]);

        $slug = Str::slug($request->slug ?? $request->name);
        
        $originalSlug = $slug;
        $counter = 1;
        while(Tag::where('slug', $slug)->where('id', '!=', $tag->id)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $tag->update([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy(Tag $tag)
    {
        // Detach from all blogs automatically thanks to cascadeOnDelete on the pivot
        $tag->delete();
        return response()->json(['success' => true]);
    }
}
