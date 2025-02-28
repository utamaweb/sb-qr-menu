<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoryParent;
use App\Models\CustomCategory;
use Illuminate\Http\Request;

class CustomCategoryController extends Controller
{
    // Get all cateogry
    public function index() {
        $categories = CategoryParent::all();

        foreach($categories as $category) {
            $customCategory = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category->id)->whereNull('deleted_at')->first();

            $category['name'] = $customCategory ? $customCategory->name : $category->name;
        }

        return response()->json([
            'status' => 'Ok',
            'categories' => $categories
        ], 200);
    }
}
