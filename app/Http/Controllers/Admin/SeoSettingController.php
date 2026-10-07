<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\Request;

class SeoSettingController extends Controller
{
    /**
     * The 6 pages managed by this module.
     * key   = route_name stored in DB
     * label = display name in view
     */
    private array $pages = [
        'home'           => 'Home',
        'about-us'       => 'About Us',
        'our-projects'   => 'Our Projects',
        'resources'      => 'Resources',
        'blogs-listing'  => 'Blogs Listing',
        'contact-us'     => 'Contact Us',
    ];

    /**
     * Display the SEO settings page with all 6 page panels.
     */
    public function index()
    {
        // Load existing settings keyed by route_name
        $settings = SeoSetting::whereIn('route_name', array_keys($this->pages))
            ->get()
            ->keyBy('route_name');

        return view('admin.seo_settings.index', [
            'pages'    => $this->pages,
            'settings' => $settings,
        ]);
    }

    /**
     * Save SEO settings for a single page via AJAX.
     */
    public function save(Request $request)
    {
        $request->validate([
            'route_name'      => 'required|string|in:' . implode(',', array_keys($this->pages)),
            'meta_title'      => 'nullable|string|max:255',
            'meta_description'=> 'nullable|string|max:500',
            'meta_keywords'   => 'nullable|string|max:500',
            'og_title'        => 'nullable|string|max:255',
            'og_description'  => 'nullable|string|max:500',
            'og_image'        => 'nullable|string|max:500',
            'canonical_url'   => 'nullable|url|max:500',
        ], [
            'route_name.in'    => 'Invalid page selected.',
            'canonical_url.url'=> 'Canonical URL must be a valid URL.',
        ]);

        $routeName = $request->route_name;
        $page      = $this->pages[$routeName];

        SeoSetting::savePage($routeName, $page, [
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords'    => $request->meta_keywords,
            'og_title'         => $request->og_title,
            'og_description'   => $request->og_description,
            'og_image'         => $request->og_image,
            'canonical_url'    => $request->canonical_url,
        ]);

        return response()->json([
            'success' => true,
            'message' => "SEO settings for \"{$page}\" saved successfully.",
        ]);
    }
}
