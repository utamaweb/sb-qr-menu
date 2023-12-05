<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // name, code, type, barcode_symbology, brand_id, category_id, unit_id, purchase_unit_id, sale_unit_id, cost, price, qty, alert_quantity, promotion, promotion_price, starting_date, last_date, tax_id, tax_method, image, featured, product_details, is_active
            ['AYAM GANJA', 12345678, 'standard', 'C128', 1, 23, 13, 13, 13, 10000, 12000, 5, 1, NULL, NULL, NULL, NULL, NULL, 1, ".jpg", 1, "", 1],
        ];

        foreach ($products as $product) {
            Product::create([
                'name'  =>  $product[0],
                'code'  =>  $product[1],
                'type'  =>  $product[2],
                'barcode_symbology'  =>  $product[3],
                'brand_id'  =>  $product[4],
                'category_id'  =>  $product[5],
                'unit_id'  =>  $product[6],
                'purchase_unit_id'  =>  $product[7],
                'sale_unit_id'  =>  $product[8],
                'cost'  =>  $product[9],
                'price'  =>  $product[10],
                'qty'  =>  $product[11],
                'alert_quantity'  =>  $product[12],
                'promotion'  =>  $product[13],
                'promotion_price'  =>  $product[14],
                'starting_date'  =>  $product[15],
                'last_date'  =>  $product[16],
                'tax_id'  =>  $product[17],
                'tax_method'  =>  $product[18],
                'image'  =>  $product[19],
                'featured'  =>  $product[20],
                'product_details'  =>  $product[21],
                'is_active'  =>  $product[22],
            ]);
        }
    }
}
