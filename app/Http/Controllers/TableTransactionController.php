<?php

namespace App\Http\Controllers;

use App\Models\CustomCategory;
use App\Models\Category;
use App\Models\CategoryParent;
use App\Models\Product_Warehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class TableTransactionController extends Controller
{
    /**
     * Demo -> demo (Public method)
     *
     * method used for template test.
     */
    public function demo(Warehouse $warehouse)
    {
        // Get mapped products
        $mappedData = $this->getMappedProducts($warehouse);

        return view('backend.layout.menu', compact('mappedData'));
    }

    /**
     * Demo Mobile -> demoMobile (Public method)
     *
     * method used for template test in mobile view.
     */
    public function demoMobile(Warehouse $warehouse)
    {
        // Get mapped products
        $mappedData = $this->getMappedProducts($warehouse);

        return view('backend.layout.menu-mobile', compact('mappedData'));
    }

    /**
     * Get mapped outlet products -> getMappedProducts (Private method)
     *
     * method used to get mapped outlet products.
     */
    private function getMappedProducts(Warehouse $warehouse)
    {
        // Get outlet product parent category.
        // Check for custom category first.
        $customParentCategories = CustomCategory::where('warehouse_id', $warehouse->id);
        if ($customParentCategories->exists()) { // Set parent category using custom category if exist
            $parentCategories = $customParentCategories->select('category_id', 'name')->get();
        } else { // Get outlet product parent category
            $parentCategories = CategoryParent::where('business_id', $warehouse->business_id)->select('id AS category_id', 'name')->get();
        }

        // Get categories from parent categories
        $categories = Category::whereIn('category_parent_id', $parentCategories->pluck('category_id'))->select('id', 'name', 'category_parent_id')->get();

        // Get outlet products
        $products = Product_Warehouse::with('product')->where('warehouse_id', $warehouse->id)->get();

        // Map data
        $mappedData = [];
        foreach ($parentCategories as $parentCategory) {
            $mapped = [];
            $mapped['id'] = strtolower(str_replace(' ', '-', $parentCategory->name));
            $mapped['name'] = $parentCategory->name;
            $mapped['desc'] = '';

            // Get categories where parent category id match
            $categoryIds = $categories->where('category_parent_id', $parentCategory->category_id)->pluck('id')->toArray();

            $mappedProducts = $products->filter(function ($product) use ($categoryIds) {
                return in_array($product->product->category_id, $categoryIds);
            })->map(function ($product) {
                return [
                    'id' => $product->product->id,
                    'name' => $product->product->name,
                    'desc' => "",
                    'price' => $product->price,
                    'img' => asset('images/default-images.jpg'),
                ];
            })->values()->toArray();

            $mapped['items'] = $mappedProducts;
            $mappedData[] = $mapped;
        }

        return $mappedData;
    }
}
