<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdResource;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdController extends Controller
{
    /**
     * Get Ads
     */
    public function index(Request $request)
    {
        $query = Ad::with(['category', 'user', 'images'])->latest();

        // Search
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                    ->orWhere('description', 'like', '%' . $request->q . '%');
            });
        }

        // Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', (bool) $request->is_active);
        }

        return AdResource::collection($query->paginate(10));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Create Ad
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string|max:20',
            'price' => 'required|numeric|min:0',
            'is_active' => 'sometimes|boolean',
            'thumbnail' => 'required|image|max:4096',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|max:4096'
        ]);

        \Log::info('Ad created', $validated);
        DB::beginTransaction();

        try {

            $ad = Ad::create([
                'user_id' => $request->user()->id,
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'location' => $validated['location'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'price' => $validated['price'],
                'is_active' => $validated['is_active'] ?? true,
            ]);

            if ($request->hasFile('thumbnail')) {
                $thumbnail_path = $request->file('thumbnail')->store('ads/thumbnails', 'public');
                $ad->images()->create([
                    'path' => $thumbnail_path,
                    'type' => 'thumbnail'
                ]);
            }

            if ($request->hasFile('gallery')) {
                foreach ($request->file('gallery') as $index => $image) {
                    $gallery_path = $image->store('ads/gallery', 'public');
                    $ad->images()->create([
                        'path' => $gallery_path,
                        'type' => 'gallery'
                    ]);
                }
            }
            DB::commit();



            return response()->json([
                'message' => 'Ad created successfully.',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create ad', 'error' => $e->getMessage()], 500);
        }

    }

    /**
     * Get Single Ad
     */
    public function show(Ad $ad)
    {
        return new AdResource($ad->load(['category','user','images']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ad $ad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ad $ad)
    {
        //
    }
}
