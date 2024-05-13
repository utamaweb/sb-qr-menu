<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Shift;
use App\Models\Product_Warehouse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Str;
use Storage;

class ProductController extends Controller
{
    // public function index() {
    //     $products = Product::get()->map(function ($item) {
    //                             $item->image = $item->image ? url('storage/product_images/'.$item->image) : "";
    //                             return $item;
    //                         });
    //     return response()->json($products, 200);
    // }
    public function index()
    {
        $warehouseId = auth()->user()->warehouse_id;
        // $products = Product::get()->map(function ($product) use($warehouseId) {
        $products = Product::join('product_warehouse', 'products.id', '=', 'product_warehouse.product_id')
            ->where('product_warehouse.warehouse_id', $warehouseId)
            ->get(['products.*', 'product_warehouse.price AS warehouse_harga'])
            ->map(function ($product) use ($warehouseId) {
                $ingredients = $product->ingredient()->get();

                // check shift
                $shift = Shift::where('warehouse_id', auth()->user()->warehouse_id)
                    ->where('is_closed', 0)
                    ->orderBy('id', 'DESC')
                    ->first();

                // Ambil stok terakhir untuk setiap bahan baku di gudang tertentu
                $ingredientStocks = [];
                foreach ($ingredients as $ingredient) {
                    $lastStock = Stock::where('ingredient_id', $ingredient->id)
                        ->where('shift_id', $shift->id)
                        ->where('warehouse_id', $warehouseId)
                        ->first();

                    if ($lastStock) {
                        $ingredientStocks[$ingredient->id] = $lastStock->last_stock;
                    } else {
                        $ingredientStocks[$ingredient->id] = 0; // Jika tidak ada stok, set qty menjadi 0
                    }
                }

                // Cek jika $ingredientStocks tidak kosong sebelum menggunakan min()
                if (!empty($ingredientStocks)) {
                    // Ambil stok terkecil dari semua bahan baku
                    $smallestStock = min($ingredientStocks);
                } else {
                    // Jika $ingredientStocks kosong, set qty terkecil menjadi 0
                    $smallestStock = 0;
                }
                if($product->warehouse_harga != null){
                    $product->price = $product->warehouse_harga;
                }

                unset($product['qty']);
                // Tambahkan qty terkecil ke dalam produk
                $product->qty = $smallestStock;

                $product->image = $product->image ? url('storage/product_images/' . $product->image) : "";
                $product->category_parent_id = $product->category->category_parent->id;
                $product->category_parent_name = $product->category->category_parent->name;

                return $product;
            });

        return response()->json($products, 200);
    }


    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'type' => 'required|string',
            'name' => 'required|string',
            'code' => 'required',
            'category_id' => 'required',
            'unit_id' => 'required',
            'image' => 'file|image|mimes:jpeg,png,jpg|max:8012',
            'price' => 'required',
            'product_details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $image = $request->image;
            $imageName = 'default-img.png';
            if ($request->image) {
                $imageName = Str::slug($request->name) . '-' . Str::random(10) . '.' . $image->extension();
                $uploadImage = $image->storeAs('public/product_images', $imageName);
            }
            $product = Product::create([
                'type' => $request->type,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'code' => $request->code,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'image' => $imageName,
                'price' => $request->price,
                'product_details' => $request->product_details,
            ]);
            if (isset($request->ingredients)) {
                $product->ingredient()->sync($request->ingredients);
            }
            DB::commit();
            return response()->json($product, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function detail($id)
    {
        $product = Product::where('id', $id)->first();
        if ($product == NULL) {
            return response()->json(['message' => 'Data Tidak Ditemukan.'], 404);
        }
        $product->image = $product->image ? url('storage/product_images/' . $product->image) : "";
        return response()->json($product, 200);
    }

    public function getPostByTitle(Request $request, $title)
    {
        $posts = Post::where('title', 'LIKE', '%' . $title . '%')->with('user')->get();
        $posts->map(function ($item) {
            $item['user']['avatar_url'] = $item['user']['avatar'] ? "https://storage.googleapis.com/ecocrafters_bucket/" . $item['user']['avatar'] : "https://storage.googleapis.com/ecocrafters-api.appspot.com/avatar.png";

            return $item;
        });
        return response()->json($posts, 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'type' => 'required|string',
            'name' => 'required|string',
            'code' => 'required',
            'category_id' => 'required',
            'unit_id' => 'required',
            'image' => 'file|image|mimes:jpeg,png,jpg|max:8012',
            'price' => 'required',
            'product_details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $image = $request->image;
            $productFind = Product::findOrFail($id);
            $imageName = $productFind->image;
            if ($image) {
                Storage::delete('public/product_images/' . $productFind->image);
                $imageName = Str::slug($request->name) . '-' . Str::random(10) . '.' . $image->extension();
                $uploadImage = $image->storeAs('public/product_images', $imageName);
            }
            $product = Product::findOrFail($id);
            $product->update([
                'type' => $request->type,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'code' => $request->code,
                'category_id' => $request->category_id,
                'unit_id' => $request->unit_id,
                'image' => $imageName,
                'price' => $request->price,
                'product_details' => $request->product_details,
            ]);
            if (isset($request->ingredients)) {
                $product->ingredient()->sync($request->ingredients);
            }
            DB::commit();
            return response()->json($product, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function deleteFile($path = null)
    {
        Storage::disk('gcs')->delete($path);
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product->image != "default-image.png") {
            Storage::delete('public/product_images/' . $product->image);
        }
        $product->delete();
        return response()->json(['message' => 'Product Succesfully Deleted.'], 200);
    }

    // public function handleIngredients(Request $request, Product $product){
    //     // $tagsNames = $request->get('tags');
    //     $ingredientsNames = explode(',', $request->get('ingredient'));
    //     foreach($ingredientsNames as $ingredientName){
    //         Ingredient::firstOrCreate(['name' => $ingredientName])->save();
    //     }
    //     $ingredients = Ingredient::whereIn('name', $ingredientsNames)->get();
    //     $post->ingredient()->sync($ingredients);
    // }
}
