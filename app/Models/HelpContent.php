<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
Use App\Traits\Loggable;

class HelpContent extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'coordinators_content',
        'interns_content',
        'machines_usage_content',
    ];
}