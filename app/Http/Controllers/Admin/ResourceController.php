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
            'slug'        => 'required|string|max:255|unique:resources',
            'status'      => 'required|in:0,1',
            'description' => 'nullable|string',
            'file_type'   => 'nullable|string|max:50',
            'file_upload' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,mp4,mp3',
        ]);

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
            'title'             => $request->title,
            'slug'              => $request->slug,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'featured_image'    => $request->featured_image,
            'file'              => $filePath,
            'file_type'         => $request->file_type,
            'status'            => $request->status,
            'published_at'      => $request->status ? now() : null,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'meta_keywords'     => $request->meta_keywords,
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
            'slug'        => 'required|string|max:255|unique:resources,slug,' . $resource->id,
            'status'      => 'required|in:0,1',
            'description' => 'nullable|string',
            'file_type'   => 'nullable|string|max:50',
            'file_upload' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,zip,mp4,mp3',
        ]);

        // Handle file upload — keep existing if no new file uploaded
        $filePath = $resource->file;
        if ($request->hasFile('file_upload')) {
            $file = $request->file('file_upload');
            $filename = uniqid('resource_') . '.' . $file->getClientOriginalExtension();
            if (!Storage::disk('public')->exists('resources')) {
                Storage::disk('public')->makeDirectory('resources');
            }
            $file->storeAs('public/resources', $filename);
            $filePath = '/storage/resources/' . $filename;
        }

        $resource->update([
            'title'             => $request->title,
            'slug'              => $request->slug,
            'short_description' => $request->short_description,
            'description'       => $request->description,
            'featured_image'    => $request->featured_image,
            'file'              => $filePath,
            'file_type'         => $request->file_type,
            'status'            => $request->status,
            'published_at'      => $request->status ? ($resource->published_at ?? now()) : null,
            'meta_title'        => $request->meta_title,
            'meta_description'  => $request->meta_description,
            'meta_keywords'     => $request->meta_keywords,
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
