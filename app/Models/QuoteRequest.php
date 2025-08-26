<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
     protected $fillable = [
        'name','company','email','phone','city','project_type','budget',
        'desired_date','message','files','status',
    ];

    protected $casts = ['files'=>'array','desired_date'=>'date'];
}
