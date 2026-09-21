<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'disk',
        'path',
        'filename',
        'original_filename',
        'mime_type',
        'extension',
        'size',
        'width',
        'height',
        'alt_text',
        'title',
        'caption',
        'keywords',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
