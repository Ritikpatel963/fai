<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'subtitle',
        'excerpt',
        'content',
        'client_name',
        'location',
        'crop',
        'project_category_id',
        'project_type',
        'area',
        'project_status',
        'completed_at',
        'featured_image',
        'banner_image',
        'gallery_images',
        'contact_phone',
        'contact_whatsapp',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_featured',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'completed_at'   => 'date',
        'gallery_images' => 'array',
        'is_featured'    => 'boolean',
        'status'         => 'boolean',
        'sort_order'     => 'integer',
    ];

    /**
     * Category / Structure Type relationship.
     */
    public function category()
    {
        return $this->belongsTo(ProjectCategory::class, 'project_category_id');
    }

    /**
     * Ensure gallery_images always returns an array.
     */
    public function getGalleryImagesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
        return is_array($value) ? $value : (json_decode($value, true) ?: []);
    }
}

