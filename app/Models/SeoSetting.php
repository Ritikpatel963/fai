<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page',
        'route_name',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
    ];

    /**
     * Get SEO settings for a specific page by route name.
     */
    public static function forPage(string $routeName): ?self
    {
        return static::where('route_name', $routeName)->first();
    }

    /**
     * Save or update SEO settings for a page.
     */
    public static function savePage(string $routeName, string $page, array $data): void
    {
        static::updateOrCreate(
            ['route_name' => $routeName],
            array_merge(['page' => $page], $data)
        );
    }
}
