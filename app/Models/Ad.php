<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Ad extends Model
{
    /** @use HasFactory<\Database\Factories\AdFactory> */
    use HasFactory, Sluggable;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category_id',
        'location',
        'price',
        'contact_email',
        'contact_phone',
        'is_active'
    ];

    public function getRouteKeyName(){
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(AdImage::class);
    }

    public function thumbnail(): HasOne
    {
        return $this->hasOne(AdImage::class)->where('type', 'thumbnail');
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(AdImage::class)->where('type', 'gallery');
    }
}
