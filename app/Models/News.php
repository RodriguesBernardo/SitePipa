<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;
use App\Traits\Loggable;

class News extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'featured_image', 
        'is_featured',
        'published_at',
        'user_id',
        'published'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'published' => 'boolean'
    ];

    protected $appends = ['cover_image_url', 'featured_image_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getCoverImageUrlAttribute()
    {
        if (!$this->cover_image) {
            return asset('images/default-cover.jpg');
        }
        
        // Verifica se o arquivo existe fisicamente
        $path = 'news/cover_images/' . $this->cover_image;
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }
        
        return asset('images/default-cover.jpg');
    }
    
    public function getFeaturedImageUrlAttribute()
    {
        if (!$this->featured_image) {
            return asset('images/default-featured.jpg');
        }
        
        if (Storage::disk('public')->exists($this->featured_image)) {
            return Storage::disk('public')->url($this->featured_image);
        }
        
        return asset('images/default-featured.jpg');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($news) {
            $news->slug = Str::slug($news->title);
            $news->user_id = $news->user_id ?? auth()->id();
        });

        static::updating(function ($news) {
            if ($news->isDirty('title')) {
                $news->slug = Str::slug($news->title);
            }
        });

        static::deleting(function ($news) {
            if ($news->cover_image) {
                Storage::disk('public')->delete($news->cover_image);
            }
            if ($news->featured_image) {
                Storage::disk('public')->delete($news->featured_image);
            }
        });
    }

    public function getCleanLongDescriptionAttribute()
    {
        return Purifier::clean($this->long_description);
    }

    public function getCleanShortDescriptionAttribute()
    {
        return Purifier::clean($this->short_description);
    }
}

