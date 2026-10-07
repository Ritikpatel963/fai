<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends Controller
{
    /**
     * Display all testimonials ordered by the order column.
     */
    public function index()
    {
        $testimonials = Testimonial::orderBy('order')->orderBy('created_at', 'desc')->get();
        return view('admin.testimonials.index', compact('testimonials'));
    }

    /**
     * Store a new testimonial via AJAX.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'rating'  => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:2000',
            'image'   => 'nullable|string|max:500',
            'order'   => 'nullable|integer|min:0',
            'status'  => 'required|in:0,1',
        ], [
            'name.required'    => 'Name is required.',
            'rating.required'  => 'Please select a rating.',
            'rating.min'       => 'Rating must be between 1 and 5.',
            'rating.max'       => 'Rating must be between 1 and 5.',
            'message.required' => 'Testimonial message is required.',
        ]);

        Testimonial::create([
            'name'    => $request->name,
            'rating'  => $request->rating,
            'message' => $request->message,
            'image'   => $request->image,
            'order'   => $request->order ?? 0,
            'status'  => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial added successfully.',
        ]);
    }

    /**
     * Return a single testimonial as JSON for editing.
     */
    public function show(Testimonial $testimonial)
    {
        return response()->json([
            'success'      => true,
            'testimonial'  => $testimonial->only(['id', 'name', 'rating', 'message', 'order', 'status', 'image']),
        ]);
    }

    /**
     * Update an existing testimonial via AJAX.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'rating'  => 'required|integer|min:1|max:5',
            'message' => 'required|string|max:2000',
            'image'   => 'nullable|string|max:500',
            'order'   => 'nullable|integer|min:0',
            'status'  => 'required|in:0,1',
        ], [
            'name.required'    => 'Name is required.',
            'rating.required'  => 'Please select a rating.',
            'message.required' => 'Testimonial message is required.',
        ]);

        $testimonial->update([
            'name'    => $request->name,
            'rating'  => $request->rating,
            'message' => $request->message,
            'image'   => $request->image,
            'order'   => $request->order ?? $testimonial->order,
            'status'  => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Testimonial updated successfully.',
        ]);
    }

    /**
     * Soft delete a testimonial via AJAX.
     */
    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        return response()->json(['success' => true]);
    }
}
