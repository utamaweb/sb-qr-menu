<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Product_Warehouse;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            // name, code, type, barcode_symbology, brand_id, category_id, unit_id, purchase_unit_id, sale_unit_id, cost, price, qty, alert_quantity, promotion, promotion_price, starting_date, last_date, tax_id, tax_method, image, featured, product_details, is_active
            ['AYAM PAKET REGULER', 81204818, 'standard', 1, 2, 15000, 'ayam-paket-reguler.jpg', 'Ayam potongan kecil + sambal + ganja + nasi + es teh'],
            ['AYAM PAKET JUMBO', 91941029, 'standard', 1, 2, 20000, 'ayam-paket-jumbo.jpg', 'Ayam potongan jumbo + sambal + ganja + nasi + es teh'],
            ['AYAM GANJA REGULER', 10294918, 'standard', 1, 2, 10000, 'ayam-ganja-reguler.jpg', 'Ayam potongan kecil + sambal + ganja'],
            ['AYAM GANJA JUMBO', 23040183, 'standard', 1, 2, 12000, 'ayam-ganja-jumbo.jpg', 'Ayam potongan jumbo + sambal + ganja'],
            ['BEBEK PAKET', 48919239, 'standard', 1, 2, 25000, 'bebek-paket.jpg', 'Bebek + sambal + ganja + nasi + es teh'],
            ['BEBEK REGULER', 87123881, 'standard', 1, 2, 22000, 'bebek-reguler.jpg', 'Bebek + sambal + ganja'],
        ];

        foreach ($products as $product) {
            Product::create([
                'name'  =>  $product[0],
                'slug' => Str::slug($product[0]),
                'code'  =>  $product[1],
                'type'  =>  $product[2],
                'category_id'  =>  $product[3],
                'unit_id'  =>  $product[4],
                'price'  =>  $product[5],
                'image'  =>  $product[6],
                'product_details'  =>  $product[7],
            ]);
        }
        foreach (Product::get() as $product) {
            Product_Warehouse::create([
                'product_id'  =>  $product->id,
                'warehouse_id' => $product->id,
                'price'  =>  12000
            ]);
        }
    }
}
