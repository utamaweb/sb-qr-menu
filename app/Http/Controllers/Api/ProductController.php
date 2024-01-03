<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index() {
        $products = Product::get()->map(function ($item) {
                                $item->image = $item->image ? url('public/storage/product_images/'.$item->image) : "";
                                return $item;
                            });
        return response()->json($products, 200);
    }
}
