<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectCategory;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $query = Project::with('category')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('crop', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('project_type', 'like', "%{$search}%")
                  ->orWhereHas('category', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'data' => $projects->items(),
                'last_page' => $projects->lastPage(),
            ]);
        }

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $categories = ProjectCategory::where('status', true)->orderBy('name')->get();
        return view('admin.projects.create', compact('categories'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255|unique:projects,slug',
            'subtitle'            => 'nullable|string|max:255',
            'excerpt'             => 'nullable|string',
            'content'             => 'nullable|string',
            'client_name'         => 'nullable|string|max:255',
            'location'            => 'nullable|string|max:255',
            'crop'                => 'nullable|string|max:255',
            'project_category_id' => 'nullable|exists:project_categories,id',
            'project_type'        => 'nullable|string|max:255',
            'area'                => 'nullable|string|max:255',
            'project_status'      => 'nullable|string|max:100',
            'completed_at'        => 'nullable|date',
            'featured_image'      => 'nullable|string|max:500',
            'banner_image'        => 'nullable|string|max:500',
            'gallery_images'      => 'nullable|array',
            'gallery_images.*'    => 'nullable|string',
            'contact_phone'       => 'nullable|string|max:50',
            'contact_whatsapp'    => 'nullable|string|max:50',
            'meta_title'          => 'nullable|string|max:255',
            'meta_description'    => 'nullable|string',
            'meta_keywords'       => 'nullable|string',
            'is_featured'         => 'nullable|boolean',
            'status'              => 'nullable|boolean',
            'sort_order'          => 'nullable|integer',
        ]);

        // Generate and ensure unique slug
        $baseSlug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;
        while (Project::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        // Sanitize rich text content if HTMLPurifier is available
        $cleanContent = $request->content;
        if (!empty($cleanContent) && class_exists(HTMLPurifier::class)) {
            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifierConfig->set('HTML.Allowed', 'p,br,strong,em,u,s,ul,ol,li,blockquote,h1,h2,h3,h4,h5,h6,a[href|title|target],img[src|alt|width|height],table,thead,tbody,tr,th,td,figure,figcaption,oembed[url]');
            $purifierConfig->set('AutoFormat.AutoParagraph', false);
            $purifierConfig->set('AutoFormat.RemoveEmpty', true);
            $purifierConfig->set('URI.AllowedSchemes', ['http' => true, 'https' => true]);
            $purifier = new HTMLPurifier($purifierConfig);
            $cleanContent = $purifier->purify($cleanContent);
        }

        // Clean gallery images array
        $galleryImages = [];
        if ($request->has('gallery_images') && is_array($request->gallery_images)) {
            $galleryImages = array_values(array_filter($request->gallery_images, fn($img) => !empty($img)));
        }

        // Sync category name to project_type for backward compatibility & frontend display
        $projectType = $request->project_type;
        if ($request->filled('project_category_id')) {
            $category = ProjectCategory::find($request->project_category_id);
            if ($category) {
                $projectType = $category->name;
            }
        }

        $project = Project::create([
            'title'               => $request->title,
            'slug'                => $slug,
            'subtitle'            => $request->subtitle,
            'excerpt'             => $request->excerpt,
            'content'             => $cleanContent,
            'client_name'         => $request->client_name,
            'location'            => $request->location,
            'crop'                => $request->crop,
            'project_category_id' => $request->project_category_id,
            'project_type'        => $projectType,
            'area'                => $request->area,
            'project_status'      => $request->project_status ?? 'Completed',
            'completed_at'        => $request->completed_at,
            'featured_image'      => $request->featured_image,
            'banner_image'        => $request->banner_image,
            'gallery_images'      => $galleryImages,
            'contact_phone'       => $request->contact_phone,
            'contact_whatsapp'    => $request->contact_whatsapp,
            'meta_title'          => $request->meta_title,
            'meta_description'    => $request->meta_description,
            'meta_keywords'       => $request->meta_keywords,
            'is_featured'         => $request->boolean('is_featured'),
            'status'              => $request->has('status') ? $request->boolean('status') : true,
            'sort_order'          => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Project created successfully!',
            'redirect' => route('admin.projects.index'),
        ]);
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $categories = ProjectCategory::where('status', true)->orderBy('name')->get();
        return view('admin.projects.edit', compact('project', 'categories'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title'               => 'required|string|max:255',
            'slug'                => 'nullable|string|max:255|unique:projects,slug,' . $project->id,
            'subtitle'            => 'nullable|string|max:255',
            'excerpt'             => 'nullable|string',
            'content'             => 'nullable|string',
            'client_name'         => 'nullable|string|max:255',
            'location'            => 'nullable|string|max:255',
            'crop'                => 'nullable|string|max:255',
            'project_category_id' => 'nullable|exists:project_categories,id',
            'project_type'        => 'nullable|string|max:255',
            'area'                => 'nullable|string|max:255',
            'project_status'      => 'nullable|string|max:100',
            'completed_at'        => 'nullable|date',
            'featured_image'      => 'nullable|string|max:500',
            'banner_image'        => 'nullable|string|max:500',
            'gallery_images'      => 'nullable|array',
            'gallery_images.*'    => 'nullable|string',
            'contact_phone'       => 'nullable|string|max:50',
            'contact_whatsapp'    => 'nullable|string|max:50',
            'meta_title'          => 'nullable|string|max:255',
            'meta_description'    => 'nullable|string',
            'meta_keywords'       => 'nullable|string',
            'is_featured'         => 'nullable|boolean',
            'status'              => 'nullable|boolean',
            'sort_order'          => 'nullable|integer',
        ]);

        // Generate unique slug
        $baseSlug = !empty($request->slug) ? Str::slug($request->slug) : Str::slug($request->title);
        $slug = $baseSlug;
        $counter = 1;
        while (Project::where('slug', $slug)->where('id', '!=', $project->id)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        // Sanitize rich text content if HTMLPurifier is available
        $cleanContent = $request->content;
        if (!empty($cleanContent) && class_exists(HTMLPurifier::class)) {
            $purifierConfig = HTMLPurifier_Config::createDefault();
            $purifierConfig->set('HTML.Allowed', 'p,br,strong,em,u,s,ul,ol,li,blockquote,h1,h2,h3,h4,h5,h6,a[href|title|target],img[src|alt|width|height],table,thead,tbody,tr,th,td,figure,figcaption,oembed[url]');
            $purifierConfig->set('AutoFormat.AutoParagraph', false);
            $purifierConfig->set('AutoFormat.RemoveEmpty', true);
            $purifierConfig->set('URI.AllowedSchemes', ['http' => true, 'https' => true]);
            $purifier = new HTMLPurifier($purifierConfig);
            $cleanContent = $purifier->purify($cleanContent);
        }

        // Clean gallery images array
        $galleryImages = [];
        if ($request->has('gallery_images') && is_array($request->gallery_images)) {
            $galleryImages = array_values(array_filter($request->gallery_images, fn($img) => !empty($img)));
        }

        // Sync category name to project_type
        $projectType = $request->project_type;
        if ($request->filled('project_category_id')) {
            $category = ProjectCategory::find($request->project_category_id);
            if ($category) {
                $projectType = $category->name;
            }
        }

        $project->update([
            'title'               => $request->title,
            'slug'                => $slug,
            'subtitle'            => $request->subtitle,
            'excerpt'             => $request->excerpt,
            'content'             => $cleanContent,
            'client_name'         => $request->client_name,
            'location'            => $request->location,
            'crop'                => $request->crop,
            'project_category_id' => $request->project_category_id,
            'project_type'        => $projectType,
            'area'                => $request->area,
            'project_status'      => $request->project_status ?? 'Completed',
            'completed_at'        => $request->completed_at,
            'featured_image'      => $request->featured_image,
            'banner_image'        => $request->banner_image,
            'gallery_images'      => $galleryImages,
            'contact_phone'       => $request->contact_phone,
            'contact_whatsapp'    => $request->contact_whatsapp,
            'meta_title'          => $request->meta_title,
            'meta_description'    => $request->meta_description,
            'meta_keywords'       => $request->meta_keywords,
            'is_featured'         => $request->boolean('is_featured'),
            'status'              => $request->has('status') ? $request->boolean('status') : true,
            'sort_order'          => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Project updated successfully!',
            'redirect' => route('admin.projects.index'),
        ]);
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully.',
        ]);
    }
}
