<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the site settings page.
     */
    public function index()
    {
        $header = Setting::getGroup('header');
        $footer = Setting::getGroup('footer');

        return view('admin.settings.index', compact('header', 'footer'));
    }

    /**
     * Save header settings.
     */
    public function saveHeader(Request $request)
    {
        $request->validate([
            'header_logo' => 'nullable|string|max:500',
        ], [
            'header_logo.max' => 'Header logo path must not exceed 500 characters.',
        ]);

        Setting::set('header_logo', $request->header_logo, 'header');

        return response()->json([
            'success' => true,
            'message' => 'Header settings saved successfully.',
        ]);
    }

    /**
     * Save footer settings.
     */
    public function saveFooter(Request $request)
    {
        $request->validate([
            'footer_logo'        => 'nullable|string|max:500',
            'footer_email'       => 'nullable|email|max:255',
            'footer_phone'       => 'nullable|string|max:20',
            'footer_description' => 'nullable|string|max:500',
            'footer_facebook'    => 'nullable|url|max:500',
            'footer_instagram'   => 'nullable|url|max:500',
            'footer_twitter'     => 'nullable|url|max:500',
            'footer_youtube'     => 'nullable|url|max:500',
            'footer_linkedin'    => 'nullable|url|max:500',
        ], [
            'footer_email.email'         => 'Please enter a valid email address.',
            'footer_phone.max'           => 'Phone number must not exceed 20 characters.',
            'footer_description.max'     => 'Short description must not exceed 500 characters.',
            'footer_facebook.url'        => 'Facebook must be a valid URL (e.g. https://facebook.com/page).',
            'footer_instagram.url'       => 'Instagram must be a valid URL.',
            'footer_twitter.url'         => 'Twitter/X must be a valid URL.',
            'footer_youtube.url'         => 'YouTube must be a valid URL.',
            'footer_linkedin.url'        => 'LinkedIn must be a valid URL.',
        ]);

        $fields = [
            'footer_logo',
            'footer_email',
            'footer_phone',
            'footer_description',
            'footer_facebook',
            'footer_instagram',
            'footer_twitter',
            'footer_youtube',
            'footer_linkedin',
        ];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field), 'footer');
        }

        return response()->json([
            'success' => true,
            'message' => 'Footer settings saved successfully.',
        ]);
    }
}
