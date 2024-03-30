<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Keygen\Keygen;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Tax;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Product_Warehouse;
use App\Models\Product_Supplier;
use App\Models\CustomField;
use App\Models\Ingredient;
use Auth;
use DNS1D;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use DB;
use App\Models\Variant;
use App\Models\IngredientProducts;
use App\Models\ProductVariant;
use App\Models\Purchase;
use App\Models\ProductPurchase;
use App\Models\Payment;
use App\Traits\TenantInfo;
use App\Traits\CacheForget;
use Intervention\Image\Facades\Image;
use File;

class ProductController extends Controller
{
    use CacheForget;
    use TenantInfo;

    public function index()
    {
        $roleName = auth()->user()->getRoleNames()[0];
        $products = Product::get();
        if($roleName == 'Kasir'){
            $products = Product_Warehouse::where('warehouse_id', auth()->user()->warehouse_id)->get();
        }
        $numberOfProduct = DB::table('products')->where('is_active', true)->count();
        return view('backend.product.index', compact('numberOfProduct', 'products','roleName'));
    }

    public function create()
    {
        $roleName = auth()->user()->getRoleNames()[0];
       $lims_category_list = Category::where('is_active', true)->get();
       $ingredients = Ingredient::get();
       $lims_unit_list = Unit::where('is_active', true)->get();
       $lims_warehouse_list = Warehouse::where('is_active', true)->get();
       $numberOfProduct = Product::where('is_active', true)->count();
       return view('backend.product.create',compact('lims_category_list', 'lims_unit_list', 'lims_warehouse_list', 'numberOfProduct', 'ingredients','roleName'));
    }

    public function store(Request $request)
    {
        $isCodeExists = Product::where('code', $request->code)->first();
        if($isCodeExists){
            return redirect()->back()->with('not_permitted', 'Maaf, Kode Produk Tersebut Sudah Digunakan, Gunakan Kode Lain.');
        }
        $data = $request->except('image', 'file');
        $data['name'] = preg_replace('/[\n\r]/', "<br>", htmlspecialchars(trim($data['name'])));
        // if($data['type'] == 'combo') {
        //     $data['product_list'] = implode(",", $data['product_id']);
        //     $data['variant_list'] = implode(",", $data['variant_id']);
        //     $data['qty_list'] = implode(",", $data['product_qty']);
        //     $data['price_list'] = implode(",", $data['unit_price']);
        //     $data['cost'] = $data['unit_id'] = $data['purchase_unit_id'] = $data['sale_unit_id'] = 0;
        // }

        // $data['product_details'] = str_replace('"', '@', $data['product_details']);
        $data['is_active'] = true;

        $image = $request->image;
        $imageName = 'default-img.png';
        if($image){
            $imageName = Str::slug($request->name) . '-' . Str::random(10).'.'.$image->extension();
            $uploadImage = $image->storeAs('public/product_images', $imageName);
        }
        $productInsert = Product::create([
            'type' => 'standard',
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'code' => $request->code,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'product_details' => $request->product_details,
            'price' => $request->price,
            'image' => $imageName,
        ]);
        if (isset($request->ingredients)) {
            $productInsert->ingredient()->sync($request->ingredients);
        }
        $roleName = auth()->user()->getRoleNames()[0];

        if(isset($data['is_diffPrice'])) {
            $productInsert->update(['is_diffPrice' => 1]);
            foreach ($data['diff_price'] as $key => $diff_price) {
                if($diff_price) {
                    Product_Warehouse::create([
                        "product_id" => $productInsert->id,
                        "warehouse_id" => $data["warehouse_id"][$key],
                        "price" => $diff_price
                    ]);
                }
            }
        } else {
            if($roleName == 'Kasir'){
                Product_Warehouse::create([
                    'product_id' => $productInsert->id,
                    'warehouse_id' => auth()->user()->warehouse_id,
                    'price' => $request->price
                ]);
            } else {
                $warehouses = Warehouse::get();
                foreach ($warehouses as $key => $warehouse) {
                    Product_Warehouse::create([
                        "product_id" => $productInsert->id,
                        "warehouse_id" => $warehouse->id,
                        "price" => $request->price
                    ]);
                }
            }
        }
        // $this->cacheForget('product_list');
        // $this->cacheForget('product_list_with_variant');
        return redirect()->route('produk.index')->with('message', 'Data inserted successfully');
    }

    public function edit($id)
    {
        $lims_category_list = Category::where('is_active', true)->get();
        $ingredients = Ingredient::get();
        $product_warehouses = Product_Warehouse::where('product_id', $id)->get();
        $ingredientProducts = IngredientProducts::whereProductId($id)->get()->pluck('ingredient_id')->toArray();
        $lims_unit_list = Unit::where('is_active', true)->get();
        $lims_product_data = Product::where('id', $id)->first();
        if($lims_product_data->variant_option) {
            $lims_product_data->variant_option = json_decode($lims_product_data->variant_option);
            $lims_product_data->variant_value = json_decode($lims_product_data->variant_value);
        }
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $noOfVariantValue = 0;
        return view('backend.product.edit',compact('lims_category_list', 'lims_unit_list', 'lims_product_data', 'lims_warehouse_list', 'noOfVariantValue','ingredients','ingredientProducts', 'product_warehouses'));
    }

    public function update(Request $request, $id)
    {
        $image = $request->image;
        $productFind = Product::findOrFail($id);
        if($image){
            $this->fileDelete('storage/product_images/', $productFind->image);
            $imageName = Str::slug($request->name) . '-' . Str::random(10).'.'.$image->extension();
            $uploadImage = $image->storeAs('public/product_images', $imageName);
        } else {
            $imageName = $productFind->image;
        }
        $editProduct = Product::findOrFail($id);
        $editProduct->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'code' => $request->code,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'product_details' => $request->product_details,
            'price' => $request->price,
            'image' => $imageName,
        ]);
        if (isset($request->ingredients)) {
            $editProduct->ingredient()->sync($request->ingredients);
        }

        $roleName = auth()->user()->getRoleNames()[0];

        if(isset($data['is_diffPrice'])) {
            $editProduct->update(['is_diffPrice' => 1]);
            foreach ($data['diff_price'] as $key => $diff_price) {
                if($diff_price) {
                    Product_Warehouse::where('product_id', $id)->where('warehouse_id', $data['warehouse_id'][$key])->update([
                        "product_id" => $id,
                        "warehouse_id" => $data["warehouse_id"][$key],
                        "price" => $diff_price
                    ]);
                }
            }
        } else {
            if($roleName == 'Kasir'){
                Product_Warehouse::where('product_id', $id)->where('warehouse_id', auth()->user()->warehouse_id)->update([
                    'product_id' => $editProduct->id,
                    'warehouse_id' => auth()->user()->warehouse_id,
                    'price' => $request->price
                ]);
            } else {
                $warehouses = Warehouse::get();
                foreach ($warehouses as $key => $warehouse) {
                    Product_Warehouse::where('product_id', $id)->where('warehouse_id', $warehouse->id)->update([
                        "product_id" => $editProduct->id,
                        "warehouse_id" => $warehouse->id,
                        "price" => $request->price
                    ]);
                }
            }
        }

        $this->cacheForget('product_list');
        // \Session::flash('edit_message', 'Product updated successfully');
        return redirect()->route('produk.index')->with('message', 'Data updated successfully');
    }

    public function generateCode()
    {
        $id = Keygen::numeric(8)->generate();
        return $id;
    }

    public function search(Request $request)
    {
        $product_code = explode(" ", $request['data']);
        $lims_product_data = Product::where('code', $product_code[0])->first();

        $product[] = $lims_product_data->name;
        $product[] = $lims_product_data->code;
        $product[] = $lims_product_data->qty;
        $product[] = $lims_product_data->price;
        $product[] = $lims_product_data->id;
        return $product;
    }

    public function saleUnit($id)
    {
        $unit = Unit::where("base_unit", $id)->orWhere('id', $id)->pluck('unit_name','id');
        return json_encode($unit);
    }

    public function getData($id, $variant_id)
    {
        if($variant_id) {
            $data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.name', 'product_variants.item_code')
                ->where([
                    ['products.id', $id],
                    ['product_variants.variant_id', $variant_id]
                ])->first();
            $data->code = $data->item_code;
        }
        else
            $data = Product::select('name', 'code')->find($id);
        return $data;
    }



    public function limsProductSearch(Request $request)
    {
        $product_code = explode("(", $request['data']);
        $product_code[0] = rtrim($product_code[0], " ");
        $lims_product_data = Product::where([
            ['code', $product_code[0] ],
            ['is_active', true]
        ])->first();
        if(!$lims_product_data) {
            $lims_product_data = Product::join('product_variants', 'products.id', 'product_variants.product_id')
                ->select('products.*', 'product_variants.item_code', 'product_variants.variant_id', 'product_variants.additional_price')
                ->where('product_variants.item_code', $product_code[0])
                ->first();

            $variant_id = $lims_product_data->variant_id;
            $additional_price = $lims_product_data->additional_price;
        }
        else {
            $variant_id = '';
            $additional_price = 0;
        }
        $product[] = $lims_product_data->name;
        if($lims_product_data->is_variant)
            $product[] = $lims_product_data->item_code;
        else
            $product[] = $lims_product_data->code;

        $product[] = $lims_product_data->price + $additional_price;
        $product[] = DNS1D::getBarcodePNG($lims_product_data->code, $lims_product_data->barcode_symbology);
        $product[] = $lims_product_data->promotion_price;
        $product[] = config('currency');
        $product[] = config('currency_position');
        $product[] = $lims_product_data->qty;
        $product[] = $lims_product_data->id;
        $product[] = $variant_id;
        return $product;
    }

    public function deleteBySelection(Request $request)
    {
        $product_id = $request['productIdArray'];
        foreach ($product_id as $id) {
            $lims_product_data = Product::findOrFail($id);
            $lims_product_data->is_active = false;
            $lims_product_data->save();

            if($lims_product_data->image) {
                $images = explode(",", $lims_product_data->image);
                foreach ($images as $image) {
                    $this->fileDelete('images/product/', $image);
                }
            }
        }
        $this->cacheForget('product_list');
        $this->cacheForget('product_list_with_variant');
        return 'Product deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_product_data = Product::findOrFail($id);
        if($lims_product_data->image != 'zummXD2dvAtI.png') {
            $this->fileDelete('storage/product_images/', $lims_product_data->image);
        }
        IngredientProducts::where('product_id', $lims_product_data->id)->delete();
        $lims_product_data->delete();
        $this->cacheForget('product_list');
        $this->cacheForget('product_list_with_variant');
        return redirect()->back()->with('message', 'Product deleted successfully');
    }
}
