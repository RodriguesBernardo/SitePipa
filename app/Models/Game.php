<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mews\Purifier\Facades\Purifier;
use App\Traits\Loggable;

class Game extends Model
{
    use HasFactory, SoftDeletes, Loggable;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'long_description',
        'how_to_play',
        'educational_objectives',
        'cover_image',
        'file_path',
        'is_featured',
    ];

    protected $with = ['tags', 'screenshots'];
    
    protected $appends = [
        'clean_long_description',
        'clean_short_description',
        'downloads_count'
    ];

    public function tags()
    {
        return $this->belongsToMany(GameTag::class, 'game_game_tag');
    }

    public function screenshots()
    {
        return $this->hasMany(GameScreenshot::class);
    }

    public function downloads()
    {
        return $this->hasMany(GameDownload::class);
    }

    public function getDownloadsCountAttribute()
    {
        return $this->downloads()->count();
    }

    public function getCleanLongDescriptionAttribute()
    {
        return Purifier::clean($this->long_description ?? '');
    }

    public function getCleanShortDescriptionAttribute()
    {
        return Purifier::clean($this->short_description ?? '');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', "%$searchTerm%")
              ->orWhere('short_description', 'like', "%$searchTerm%")
              ->orWhere('long_description', 'like', "%$searchTerm%")
              ->orWhereHas('tags', function($q) use ($searchTerm) {
                  $q->where('name', 'like', "%$searchTerm%");
              });
        });
    }
}