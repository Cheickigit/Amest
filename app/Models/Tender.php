<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
     protected $fillable = [
        'reference','organization','contact_name','email','phone','deadline',
        'scope','documents','status',
    ];
    protected $casts = ['deadline'=>'date','documents'=>'array'];
}
