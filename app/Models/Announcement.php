<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject',
        'content',
        'type',
        'important',
        'expiration_date',
        'author',
        'target_roles',
    ];

    protected $casts = [
        'important' => 'boolean',
        'expiration_date' => 'datetime',
        'target_roles' => 'array',
    ];

    // Scope pour annonces importantes
    public function scopeImportant($query)
    {
        return $query->where('important', true);
    }

    // Scope pour annonces non expirées
    public function scopeActive($query)
    {
        return $query->whereNull('expiration_date')
                     ->orWhere('expiration_date', '>', now());
    }
}
