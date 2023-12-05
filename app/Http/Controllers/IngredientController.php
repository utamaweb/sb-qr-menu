<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ingredient;
use Illuminate\Validation\Rule;
use Keygen;
use Auth;
use DB;
use App\Traits\CacheForget;

class IngredientController extends Controller
{
    use CacheForget;
    public function index()
    {
        // $lims_order_type_all = Ingredient::where('is_active', true)->get();
        // $numberOfIngredient = Ingredient::where('is_active', true)->count();
        return view('backend.ingredient.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
                Rule::unique('order_types')->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);
        $input = $request->all();
        $input['is_active'] = true;
        Ingredient::create($input);
        $this->cacheForget('order_type_list');
        return redirect('order_type')->with('message', 'Data inserted successfully');
    }

    public function edit($id)
    {
        $lims_order_type_data = Ingredient::findOrFail($id);
        return $lims_order_type_data;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
                Rule::unique('order_types')->ignore($request->order_type_id)->where(function ($query) {
                    return $query->where('is_active', 1);
                }),
            ],
        ]);
        $input = $request->all();
        $lims_order_type_data = Ingredient::find($input['order_type_id']);
        $lims_order_type_data->update($input);
        $this->cacheForget('order_type_list');
        return redirect('order_type')->with('message', 'Data updated successfully');
    }

    public function importIngredient(Request $request)
    {
        //get file
        $upload = $request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if ($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');
        $filename =  $upload->getClientOriginalName();
        $upload = $request->file('file');
        $filePath = $upload->getRealPath();
        //open and read
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);
        $escapedHeader = [];
        //validate
        foreach ($header as $key => $value) {
            $lheader = strtolower($value);
            $escapedItem = preg_replace('/[^a-z]/', '', $lheader);
            array_push($escapedHeader, $escapedItem);
        }
        //looping through othe columns
        while ($columns = fgetcsv($file)) {
            if ($columns[0] == "")
                continue;
            foreach ($columns as $key => $value) {
                $value = preg_replace('/\D/', '', $value);
            }
            $data = array_combine($escapedHeader, $columns);

            $order_type = Ingredient::firstOrNew(['name' => $data['name'], 'is_active' => true]);
            $order_type->name = $data['name'];
            $order_type->phone = $data['phone'];
            $order_type->email = $data['email'];
            $order_type->address = $data['address'];
            $order_type->is_active = true;
            $order_type->save();
        }
        $this->cacheForget('order_type_list');
        return redirect('order_type')->with('message', 'Ingredient imported successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $order_type_id = $request['order_typeIdArray'];
        foreach ($order_type_id as $id) {
            $lims_order_type_data = Ingredient::find($id);
            $lims_order_type_data->is_active = false;
            $lims_order_type_data->save();
        }
        $this->cacheForget('order_type_list');
        return 'Ingredient deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_order_type_data = Ingredient::find($id);
        $lims_order_type_data->is_active = false;
        $lims_order_type_data->save();
        $this->cacheForget('order_type_list');
        return redirect('order_type')->with('not_permitted', 'Data deleted successfully');
    }

    public function order_typeAll()
    {
        if (Auth::user()->role_id > 2)
            $lims_order_type_list = DB::table('order_types')->where([
                ['is_active', true],
                ['id', Auth::user()->order_type_id]
            ])->get();
        else
            $lims_order_type_list = DB::table('order_types')->where('is_active', true)->get();

        $html = '';
        foreach ($lims_order_type_list as $order_type) {
            $html .= '<option value="' . $order_type->id . '">' . $order_type->name . '</option>';
        }

        return response()->json($html);
    }
}
