<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Keygen\Keygen;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\Product_Warehouse;
use App\Models\Ingredient;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use DB;
use App\Models\IngredientProducts;
use Intervention\Image\Facades\Image;
use File;
use Storage;

class ProductController extends Controller
{

    public function index()
    {
        $roleName = auth()->user()->getRoleNames()[0];
        if (auth()->user()->hasRole('Superadmin')) {
            $products = Product::with('category', 'unit', 'ingredient')->get();
        } elseif (auth()->user()->hasRole('Admin Bisnis')) {
            $products = Product::with('category', 'unit', 'ingredient')->where('business_id', auth()->user()->business_id)->get();
        }
        return view('backend.product.index', compact('products', 'roleName'));
    }

    public function create()
    {
        $roleName = auth()->user()->getRoleNames()[0];
        $lims_category_list = Category::where('business_id', auth()->user()->business_id)->where('is_active', true)->get();
        $ingredients = Ingredient::where('business_id', auth()->user()->business_id)->get();
        $lims_unit_list = Unit::where('is_active', true)->get();
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $numberOfProduct = Product::where('is_active', true)->count();
        return view('backend.product.create', compact('lims_category_list', 'lims_unit_list', 'lims_warehouse_list', 'numberOfProduct', 'ingredients', 'roleName'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        $isCodeExists = Product::where('code', $request->code)->first();
        if ($isCodeExists) {
            return redirect()->back()->with('not_permitted', 'Maaf, Kode Produk Tersebut Sudah Digunakan, Gunakan Kode Lain.');
        }

        $image = $request->image;
        $imageName = 'default-images.jpg';
        if ($image) {
            $imageName = Str::slug($request->name) . '-' . Str::random(10) . '.' . $image->extension();
            $uploadImage = $image->storeAs('public/product_images', $imageName);
        }
        // Insert to products
        $productInsert = Product::create([
            'type' => 'standard',
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'code' => $request->code,
            'category_id' => $request->category_id,
            'unit_id' => $request->unit_id,
            'business_id' => auth()->user()->business_id,
            'product_details' => $request->product_details,
            'price' => intVal(str_replace(',', '', $request->price)),
            'image' => $imageName,
        ]);
        // end insert to products

        // insert ingredient_products
        if($request->ingredients){
            $ingredientIds = $request->ingredients;
            if (count($ingredientIds) !== count(array_unique($ingredientIds))) {
                DB::rollback();
                return redirect()->route('produk.index')->with('not_permitted', 'Gagal Tambah Produk, Bahan Baku Tidak Boleh Sama Untuk Satu Produk');
            }
            foreach ($request->ingredients as $item => $ingredient) {
                $data = array(
                    'product_id' => $productInsert->id,
                    'ingredient_id' => $request->ingredients[$item],
                    'qty' => $request->qty[$item],
                );
                IngredientProducts::create($data);
            }
        }
        // end insert ingredient_products
        DB::commit();
        return redirect()->route('produk.index')->with('message', 'Produk Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $lims_category_list = Category::where('business_id', auth()->user()->business_id)->where('is_active', true)->get();
        $ingredients = Ingredient::where('business_id', auth()->user()->business_id)->get();
        $product_warehouses = Product_Warehouse::where('product_id', $id)->get();
        $ingredientProducts = IngredientProducts::where('product_id', $id)->get();
        $lims_unit_list = Unit::where('is_active', true)->get();
        $product = Product::where('id', $id)->first();
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $noOfVariantValue = 0;
        return view('backend.product.edit', compact('lims_category_list', 'lims_unit_list', 'product', 'lims_warehouse_list', 'noOfVariantValue', 'ingredients', 'ingredientProducts', 'product_warehouses'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        $image = $request->image;
        $productFind = Product::findOrFail($id);
        if ($image) {
            Storage::delete('public/product_images/' . $productFind->image);
            $imageName = Str::slug($request->name) . '-' . Str::random(10) . '.' . $image->extension();
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
            'price' => intVal(str_replace(',', '', $request->price)),
            'image' => $imageName,
        ]);

        // insert ingredient_products
        IngredientProducts::where('product_id', $editProduct->id)->forceDelete();
        if($request->ingredients){
            $ingredientIds = $request->ingredients;
            if (count($ingredientIds) !== count(array_unique($ingredientIds))) {
                DB::rollback();
                return redirect()->route('produk.index')->with('not_permitted', 'Gagal Ubah Produk, Bahan Baku Tidak Boleh Sama Untuk Satu Produk');
            }
            // Delete Previous Ingredient Products
            foreach ($request->ingredients as $item => $ingredient) {
                $data = array(
                    'product_id' => $editProduct->id,
                    'ingredient_id' => $request->ingredients[$item],
                    'qty' => $request->qty[$item],
                );
                IngredientProducts::create($data);
            }
        }
        // end insert ingredient_products
        DB::commit();
        return redirect()->route('produk.index')->with('message', 'Data updated successfully');
    }

    public function generateCode()
    {
        $id = Keygen::numeric(8)->generate();
        return $id;
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image != 'default-images.jpg') {
            Storage::delete('public/product_images/' . $product->image);
        }
        IngredientProducts::where('product_id', $product->id)->delete();
        Product_Warehouse::where('product_id', $product->id)->delete();
        $product->delete();
        return redirect()->back()->with('message', 'Product deleted successfully');
    }
}
