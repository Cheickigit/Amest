<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title','slug','category','city','client','year','status',
        'cover_image','excerpt','body','media',
    ];

    protected $casts = [
        'media' => 'array',
        'year'  => 'integer',
    ];
}
