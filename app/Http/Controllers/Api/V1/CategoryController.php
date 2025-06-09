<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get Categories
     */
    public function index(){
        $categories = Category::paginate(10);
        return  CategoryResource::collection($categories);
    }
}
