<?php

namespace App\Services;

use App\Models\CustomCategory;
use App\Models\Category;
use App\Models\CategoryParent;
use App\Models\Product_Warehouse;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class TableTransactionService
{
    /**
     * Get mapped outlet products -> getMappedProducts (Public method)
     *
     * method used to get mapped outlet products.
     */
    public function getMappedProducts(Warehouse $warehouse)
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
?>
