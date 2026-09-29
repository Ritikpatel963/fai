<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $blogs = Blog::with(['author', 'categories'])->latest()->paginate(10);
            return response()->json([
                'data' => $blogs->items(),
                'last_page' => $blogs->lastPage(),
            ]);
        }
        $blogs = Blog::with('author')->latest()->paginate(10);
        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();
        return view('admin.blogs.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs',
            'content' => 'required|string',
            'status' => 'required|in:draft,published,scheduled',
        ]);

        $blog = Blog::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $request->featured_image,
            'status' => $request->status,
            'visibility' => $request->visibility ?? 'public',
            'author_id' => auth()->id(),
            'published_at' => $request->status === 'published' ? now() : null,
        ]);

        if ($request->has('categories')) {
            $blog->categories()->sync($request->categories);
        }
        if ($request->has('tags')) {
            $blog->tags()->sync($request->tags);
        }

        if ($request->filled('seo_title') || $request->filled('seo_description') || $request->filled('seo_keywords')) {
            $blog->seo()->create([
                'meta_title' => $request->seo_title,
                'meta_description' => $request->seo_description,
                'focus_keyword' => $request->seo_keywords,
            ]);
        }

        return response()->json(['success' => true, 'blog' => $blog, 'redirect' => route('admin.blogs.index')]);
    }

    public function edit(Blog $blog)
    {
        $categories = \App\Models\Category::all();
        $tags = \App\Models\Tag::all();
        return view('admin.blogs.edit', compact('blog', 'categories', 'tags'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blogs,slug,' . $blog->id,
            'content' => 'required|string',
            'status' => 'required|in:draft,published,scheduled',
        ]);

        $blog->update([
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'featured_image' => $request->featured_image,
            'status' => $request->status,
            'visibility' => $request->visibility ?? 'public',
            'published_at' => $request->status === 'published' ? ($blog->published_at ?? now()) : null,
        ]);

        if ($request->has('categories')) {
            $blog->categories()->sync($request->categories);
        }
        if ($request->has('tags')) {
            $blog->tags()->sync($request->tags);
        }

        if ($request->filled('seo_title') || $request->filled('seo_description') || $request->filled('seo_keywords')) {
            $blog->seo()->updateOrCreate(
                ['blog_id' => $blog->id],
                [
                    'meta_title' => $request->seo_title,
                    'meta_description' => $request->seo_description,
                    'focus_keyword' => $request->seo_keywords,
                ]
            );
        }

        return response()->json(['success' => true]);
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json(['success' => true]);
    }
}
