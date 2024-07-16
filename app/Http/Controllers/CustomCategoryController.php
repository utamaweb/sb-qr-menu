<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomCategory;
use App\Models\CategoryParent;

class CustomCategoryController extends Controller
{
    // Index
    public function index() {
        $categories = CategoryParent::all();

        foreach ($categories as $category) {
            $checkCustom = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category->id)->whereNull('deleted_at')->exists();
            $customCategory = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category->id)->whereNull('deleted_at')->first();

            if($checkCustom) {
                $category['custom'] = $customCategory->name;
            } else {
                $category['custom'] = "-";
            }
        }

        return view('backend.custom_category.index', compact('categories'));
    }

    // Form
    public function form($category) {
        $checkCustom = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category)->whereNull('deleted_at')->exists();
        $customCategory = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category)->whereNull('deleted_at')->first();

        $category = CategoryParent::where('id', $category)->first();

        if($checkCustom) {
            $category['custom'] = $customCategory->name;
        } else {
            $category['custom'] = "-";
        }

        return view('backend.custom_category.form', compact('category'));

    }

    // Store
    public function store(Request $request, $category) {
        $this->validate($request, [
            'custom' => 'required',
        ]);

        $checkCustom = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category)->whereNull('deleted_at')->exists();
        $customCategory = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category)->whereNull('deleted_at')->first();

        if($checkCustom) {
            $customCategory->update([
                'name' => $request->custom
            ]);
        } else {
            CustomCategory::create([
                'warehouse_id' => auth()->user()->warehouse_id,
                'category_id' => $category,
                'name' => $request->custom
            ]);
        }

        return redirect()->route('custom-category.index')->with('success', 'Data berhasil disimpan');
    }

    // Destroy
    public function destroy($category) {
        $checkCustom = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category)->whereNull('deleted_at')->exists();
        $customCategory = CustomCategory::where('warehouse_id', auth()->user()->warehouse_id)->where('category_id', $category)->whereNull('deleted_at')->first();

        if($checkCustom) {
            $customCategory->delete();
            return redirect()->route('custom-category.index')->with('success', 'Data berhasil dihapus');
        } else {
            return redirect()->route('custom-category.index')->with('error', 'Data kategori belum diubah sebelumnya');
        }
    }
}
