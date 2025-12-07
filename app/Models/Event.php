<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'category',
        'location',
        'organizer',
        'event_date',
        'date',
        'status',
        'cover_image',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'event_date' => 'date',
        'date' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    // Relation avec les participants (membres)
    public function participants()
    {
        return $this->belongsToMany(User::class, 'event_participants')
                    ->withTimestamps()
                    ->withPivot(['status', 'registered_at']);
    }

    // Scope pour événements publiés
    public function scopePublished($query)
    {
        return $query->where('status', 'publié');
    }

    // Scope pour événements à venir
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now())
                     ->orWhere('event_date', '>=', now()->toDateString());
    }

    // Scope pour événements passés
    public function scopePast($query)
    {
        return $query->where('date', '<', now())
                     ->orWhere('event_date', '<', now()->toDateString());
    }
}
