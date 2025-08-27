<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title','slug','status','published_at',
        'excerpt','body','cover_image','tags',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'tags'         => 'array',
    ];
}
