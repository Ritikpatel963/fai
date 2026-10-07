<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Display a listing of resources.
     */
    public function index()
    {
        $resources = Resource::latest()->paginate(10);
        return view('admin.resources.index', compact('resources'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.resources.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'file_upload' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,mp4,mp3',
        ]);

        // Auto generate slug from title
        $slug = \Illuminate\Support\Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (\App\Models\Resource::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $filename = uniqid('resource_') . '.' . $file->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('resources')) {
                Storage::disk('public')->makeDirectory('resources');
            }
            $file->storeAs('public/resources', $filename);
            $filePath = '/storage/resources/' . $filename;
        }

        Resource::create([
            'title'          => $request->title,
            'slug'           => $slug,
            'featured_image' => $request->featured_image,
            'file'           => $filePath,
            'file_type'      => $filePath ? strtolower(pathinfo($filePath, PATHINFO_EXTENSION)) : null,
            'status'         => 1,
            'published_at'   => now(),
        ]);

        return response()->json([
            'success'  => true,
            'redirect' => route('admin.resources.index'),
        ]);
    }

    /**
     * Display the specified resource (not used in admin panel).
     */
    public function show(Resource $resource)
    {
        return redirect()->route('admin.resources.edit', $resource);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        return view('admin.resources.edit', compact('resource'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'file_upload' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,mp4,mp3',
        ]);

        // Handle file upload — keep existing if no new file uploaded
        $filePath = $resource->file;
        $fileType = $resource->file_type;
        if ($request->hasFile('file_upload')) {
            // Delete old file from disk if it exists
            if ($resource->file) {
                $oldPath = ltrim(str_replace('/storage/', '', $resource->file), '/');
                if (Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $file = $request->file('file_upload');
            $filename = uniqid('resource_') . '.' . $file->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('resources')) {
                Storage::disk('public')->makeDirectory('resources');
            }
            $file->storeAs('public/resources', $filename);
            $filePath = '/storage/resources/' . $filename;
            $fileType = strtolower($file->getClientOriginalExtension());
        }

        // Regenerate slug from title — ensure uniqueness excluding current record
        $slug = \Illuminate\Support\Str::slug($request->title);
        $originalSlug = $slug;
        $count = 1;
        while (\App\Models\Resource::where('slug', $slug)->where('id', '!=', $resource->id)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $resource->update([
            'title'          => $request->title,
            'slug'           => $slug,
            'featured_image' => $request->featured_image,
            'file'           => $filePath,
            'file_type'      => $fileType,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Soft delete the specified resource.
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();
        return response()->json(['success' => true]);
    }
}
