<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    protected $table = 'quotes';

    protected $fillable = [
        'name','company','email','phone','city',
        'project_type','budget','desired_date','message',
        'files','status','read_at','source_ip','user_agent',
    ];

    protected $casts = [
        'files'   => 'array',     // JSON (PostgreSQL -> jsonb)
        'read_at' => 'datetime',
    ];
}
