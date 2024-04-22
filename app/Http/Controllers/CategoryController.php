<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\CategoryParent;
use App\Models\Product;
use DB;
use Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;
use App\Traits\TenantInfo;
use App\Traits\CacheForget;
use Intervention\Image\Facades\Image;

class CategoryController extends Controller
{
    use CacheForget;
    use TenantInfo;

    public function index()
    {
        $categories = Category::get();
        $categoryParents = CategoryParent::get();
        return view('backend.category.create', compact('categories','categoryParents'));
    }


    public function store(Request $request)
    {
        $request->name = preg_replace('/\s+/', ' ', $request->name);
        $this->validate($request, [
            'name' => 'max:255',
        ]);
        $image = $request->image;
        if ($image) {
            $ext = pathinfo($image->getClientOriginalName(), PATHINFO_EXTENSION);
            $imageName = date("Ymdhis");
            if(!config('database.connections.saleprosaas_landlord')) {
                $imageName = $imageName . '.' . $ext;
                $image->move('public/images/category', $imageName);
            }
            else {
                $imageName = $this->getTenantId() . '_' . $imageName . '.' . $ext;
                $image->move('public/images/category', $imageName);
            }
            Image::make('public/images/category/'. $imageName)->fit(300, 300)->save();
            $lims_category_data['image'] = $imageName;
        }
        $icon = $request->icon;
        $lims_category_data['name'] = $request->name;
        $lims_category_data['category_parent_id'] = $request->category_parent_id;
        $lims_category_data['is_active'] = true;

        DB::table('categories')->insert($lims_category_data);
        $this->cacheForget('category_list');
        return redirect()->back()->with('message', 'Category inserted successfully');
    }

    public function edit($id)
    {
        $lims_category_data = DB::table('categories')->where('id', $id)->first();
        $lims_parent_data = DB::table('categories')->where('id', $lims_category_data->parent_id)->first();
        if($lims_parent_data)
            $lims_category_data['parent'] = $lims_parent_data['name'];
        return $lims_category_data;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'name' => 'max:255',
        ]);

        $lims_category_data = Category::find($id);
        $lims_category_data->update([
            'name' => $request->name,
            'category_parent_id' => $request->category_parent_id
        ]);

        return redirect()->back()->with('message', 'Category updated successfully');
    }

    public function import(Request $request)
    {
        //get file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');
        $filename =  $upload->getClientOriginalName();
        $filePath=$upload->getRealPath();
        //open and read
        $file=fopen($filePath, 'r');
        $header= fgetcsv($file);
        $escapedHeader=[];
        //validate
        foreach ($header as $key => $value) {
            $lheader=strtolower($value);
            $escapedItem=preg_replace('/[^a-z]/', '', $lheader);
            array_push($escapedHeader, $escapedItem);
        }
        //looping through othe columns
        while($columns=fgetcsv($file))
        {
            if($columns[0]=="")
                continue;
            foreach ($columns as $key => $value) {
                $value=preg_replace('/\D/','',$value);
            }
            $data= array_combine($escapedHeader, $columns);
            $category = Category::firstOrNew(['name' => $data['name'], 'is_active' => true ]);
            if($data['parentcategory']){
                $parent_category = Category::firstOrNew(['name' => $data['parentcategory'], 'is_active' => true ]);
                $parent_id = $parent_category->id;
            }
            else
                $parent_id = null;

            $category->parent_id = $parent_id;
            $category->is_active = true;
            $category->save();
        }
        $this->cacheForget('category_list');
        return redirect()->back()->with('message', 'Category imported successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $category_id = $request['categoryIdArray'];
        foreach ($category_id as $id) {
            $lims_product_data = Product::where('category_id', $id)->get();
            foreach ($lims_product_data as $product_data) {
                $product_data->is_active = false;
                $product_data->save();
            }
            $lims_category_data = Category::findOrFail($id);
            $lims_category_data->is_active = false;
            $lims_category_data->save();

            $this->fileDelete('images/category/', $lims_category_data->image);
            $this->fileDelete('images/category/icons', $lims_category_data->icon);
        }
        $this->cacheForget('category_list');
        return 'Category deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_category_data = Category::findOrFail($id);
        $lims_product_data = Product::where('category_id', $id)->get();
        foreach ($lims_product_data as $product_data) {
            $product_data->is_active = false;
            $product_data->delete();
        }

        $this->fileDelete('images/category/', $lims_category_data->image);
        $this->fileDelete('images/category/icons', $lims_category_data->icon);

        $lims_category_data->delete();
        $this->cacheForget('category_list');
        return redirect()->back()->with('not_permitted', 'Category deleted successfully');
    }
}
