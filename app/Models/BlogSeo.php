<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogSeo extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_id',
        'meta_title',
        'meta_description',
        'focus_keyword',
        'canonical_url',
        'robots',
        'og_title',
        'og_description',
        'og_image',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'schema_type',
        'schema_json',
    ];

    protected $casts = [
        'schema_json' => 'array',
    ];

    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
