<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->category->name ?? null,
            'created_by' => $this->user->name ?? null,
            'location' => $this->location,
            'price' => $this->price,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'is_active' => $this->is_active,
            'thumbnail' => $this->images->where('type', 'thumbnail')->first()?->path
                ? asset('storage/' . $this->images->where('type', 'thumbnail')->first()->path)
                : null,
            'gallery' => $this->images->where('type', 'gallery')->map(function ($image) {
                return asset('storage/' . $image->path);
            })->values(),
            'created_at' => $this->created_at?->toDateTimeString(),
        ];

    }
}
