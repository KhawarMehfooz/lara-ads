<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get Categories
     * @param \Illuminate\Http\Request $request
     * @return CategoryResource
     */
    public function index(Request $request){
        $categories = Category::paginate(10);
        return  CategoryResource::collection($categories);
    }
}
