<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Business;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Keygen;
use Auth;
use DB;

class WarehouseController extends Controller
{
    public function index()
    {
        if(auth()->user()->hasRole('Superadmin')){
            $lims_warehouse_all = Warehouse::where('is_active', true)->get();
            $business = Business::get();
        } elseif(auth()->user()->hasRole('Admin Bisnis')){
            $business = Business::where('id', auth()->user()->business_id)->get();
            $lims_warehouse_all = Warehouse::where('is_active', true)->where('business_id', auth()->user()->business_id)->get();
        } else{
            $business = Business::where('id', auth()->user()->warehouse->business_id)->get();
            $lims_warehouse_all = Warehouse::where('is_active', true)->where('warehouse_id', auth()->user()->warehouse_id)->get();
        }
        $numberOfWarehouse = Warehouse::where('is_active', true)->count();
        return view('backend.warehouse.create', compact('lims_warehouse_all', 'numberOfWarehouse','business'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'business_id' => 'required',
            'address' => 'required',
            'service' => 'required'
        ]);
        $input['is_active'] = true;
        // $image = $request->image;
        // $imageName = Str::slug($request->name) . '-' . Str::random(10).'.'.$image->extension();
        // $uploadImage = $image->storeAs('public/outlet_logo', $imageName);
        if(auth()->user()->hasRole('Superadmin')){
            $business_id = $request->business_id;
        } else{
            $business_id = auth()->user()->business_id;
        }
        $warehouse = Warehouse::create([
            'name' => $request->name,
            'is_active' => 1,
            'address' => $request->address,
            'business_id' => $request->business_id,
            'is_self_service' => $request->service
        ]);

        if($warehouse) {
            return redirect()->back()->with('message', 'Data Berhasil Ditambahkan');
        } else {
            return redirect()->back()->with('message', 'Data Gagal Ditambahkan');
        }

    }

    public function edit($id)
    {
        $lims_warehouse_data = Warehouse::findOrFail($id);
        return $lims_warehouse_data;
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'max:255',
            'business_id' => 'required',
            'address' => 'required',
            'service' => 'required'
        ]);
        $input = $request->all();
        $lims_warehouse_data = Warehouse::find($id);
        $image = $request->image;
        if($image){
            $this->fileDelete('storage/outlet_logo/', $lims_warehouse_data->logo);
            $imageName = Str::slug($request->name) . '-' . Str::random(10).'.'.$image->extension();
            $uploadImage = $image->storeAs('public/outlet_logo', $imageName);
        } else {
            $imageName = $lims_warehouse_data->logo;
        }
        $lims_warehouse_data->update([
            'name' => $request->name,
            'address' => $request->address,
            'business_id' => $request->business_id,
            'is_self_service' => $request->service
            // 'logo' => $imageName,
        ]);
        return redirect()->back()->with('message', 'Data Berhasil Diubah');
    }

    public function importWarehouse(Request $request)
    {
        //get file
        $upload=$request->file('file');
        $ext = pathinfo($upload->getClientOriginalName(), PATHINFO_EXTENSION);
        if($ext != 'csv')
            return redirect()->back()->with('not_permitted', 'Please upload a CSV file');
        $filename =  $upload->getClientOriginalName();
        $upload=$request->file('file');
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

           $warehouse = Warehouse::firstOrNew([ 'name'=>$data['name'], 'is_active'=>true ]);
           $warehouse->name = $data['name'];
           $warehouse->phone = $data['phone'];
           $warehouse->email = $data['email'];
           $warehouse->address = $data['address'];
           $warehouse->is_active = true;
           $warehouse->save();
        }
        return redirect()->back()->with('message', 'Warehouse imported successfully');
    }

    public function deleteBySelection(Request $request)
    {
        $warehouse_id = $request['warehouseIdArray'];
        foreach ($warehouse_id as $id) {
            $lims_warehouse_data = Warehouse::find($id);
            $lims_warehouse_data->is_active = false;
            $lims_warehouse_data->save();
        }
        return 'Warehouse deleted successfully!';
    }

    public function destroy($id)
    {
        $lims_warehouse_data = Warehouse::find($id);
        $warehouse_users = User::where('warehouse_id', $lims_warehouse_data->id)->get();
        foreach ($warehouse_users as $user) {
            $user->delete();
        }
        $lims_warehouse_data->delete();
        return redirect()->back()->with('not_permitted', 'Data berhasil dihapus');
    }

    public function warehouseAll()
    {
        if(Auth::user()->role_id > 2)
            $lims_warehouse_list = DB::table('warehouses')->where([
            ['is_active', true],
            ['id', Auth::user()->warehouse_id]
        ])->get();
        else
            $lims_warehouse_list = DB::table('warehouses')->where('is_active', true)->get();

        $html = '';
        foreach($lims_warehouse_list as $warehouse){
            $html .='<option value="'.$warehouse->id.'">'.$warehouse->name.'</option>';
        }

        return response()->json($html);
    }
}
