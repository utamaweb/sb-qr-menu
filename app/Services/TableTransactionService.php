<?php

namespace App\Services;

use App\Models\CustomCategory;
use App\Models\Category;
use App\Models\CategoryParent;
use App\Models\Product_Warehouse;
use App\Models\Warehouse;
use App\Models\TableTransaction;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Services\OutletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableTransactionService
{
    private $outletService;

    /**
     * Constructor -> __construct (Public method)
     *
     * Class constructor.
     */
    public function __construct()
    {
        $this->outletService = new OutletService();
    }

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

    /**
     * Get table transaction details -> getTableTransactionDetails (Public method)
     */
    public function getTableTransactionDetails($tableTransactionCode)
    {
        // Get table transaction details
        return TableTransaction::where('code', $tableTransactionCode)->with('table', 'table.outlet')->first();
    }

    /**
     * Create new transaction -> createNewTransaction (Public method)
     */
    public function createNewTransaction($tableTransactionCode, $data, $shift)
    {
        // Get table transaction details
        $tableTransaction = $this->getTableTransactionDetails($tableTransactionCode);

        try {
            DB::beginTransaction();

            // Check table transaction
            if (!$tableTransaction) {
                throw new \RuntimeException('Table transaction not found for code: ' . $tableTransactionCode);
            }

            // Create new transaction
            $transaction = Transaction::create([
                'shift_id' => $shift->id,
                'warehouse_id' => $tableTransaction->table->outlet_id,
                'sequence_number' => $this->outletService->getLatestTransactionQueueNumber($tableTransaction->table->outlet_id) + 1,
                'order_type_id' => 1,
                'category_order' => $tableTransaction->table->name,
                'date' => now()->format('Y-m-d'),
                'notes' => $tableTransaction->table->name,
                'total_amount' => $data['total'],
                'total_qty' => array_sum(array_column($data['items'], 'qty')),
                'status' => 'pending',
            ]);

            // Create transaction details
            foreach ($data['items'] as $item) {
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'qty' => $item['qty'],
                    'product_price' => $item['price'],
                    'subtotal' => $item['qty'] * $item['price'],
                ]);
            }

            // Update table transaction status to ordered
            $tableTransaction->status = 'ordered';
            $tableTransaction->transaction_id = $transaction->id;
            $tableTransaction->save();

            DB::commit();
            return true;

        } catch (\Throwable $th) {
            DB::rollBack();
            return $th->getMessage();
        }
    }
}
?>
