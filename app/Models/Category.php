<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Sluggable, HasFactory;
    protected $fillable = [
        'name',
        'slug'
    ];

    public function sluggable(): array{
        return [
            'slug'=>[
                'source'=>'name'
            ]
        ];
    }
}
