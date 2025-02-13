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
            $lims_warehouse_all = Warehouse::with('business')->where('is_active', true)->get();
            $business = Business::get();
        } elseif(auth()->user()->hasRole('Admin Bisnis')){
            $business = Business::with('warehouse', 'warehouse.business')->where('id', auth()->user()->business_id)->firstOrFail();
            $lims_warehouse_all = $business->warehouse;
        } else{
            $business = Business::with('warehouse', 'warehouse.business')->where('id', auth()->user()->warehouse->business_id)->first();
            $lims_warehouse_all = $business->warehouse;
        }
        $numberOfWarehouse = $lims_warehouse_all->count();
        return view('backend.warehouse.index', compact('lims_warehouse_all', 'numberOfWarehouse','business'));
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
            'is_self_service' => $request->service,
            'tagihan' => intVal(str_replace(',', '', $request->tagihan)),
            'expired_at' => $request->expired_at,
            'whatsapp' => $request->whatsapp
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
            'is_self_service' => $request->service,
            'tagihan' => intVal(str_replace(',', '', $request->tagihan)),
            'expired_at' => $request->expired_at,
            'whatsapp' => $request->whatsapp
        ]);
        return redirect()->back()->with('message', 'Data Berhasil Diubah');
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

    // Warehouse Max Shifts Count
    public function maxShiftPage() {
        return view('backend.warehouse.maxShift');
    }

    // Warehouse Max Shifts Count Update
    public function maxShiftUpdate(Request $request) {
        $validate = $request->validate([
            'max_shift' => 'numeric|required',
        ]);

        // Update proceess
        $update = auth()->user()->warehouse->update([
            'max_shift_count' => $validate['max_shift'],
        ]);

        if($update) {
            return redirect()->route('maxShiftPage')->with('message', 'Jumlah Shift Maksimal Berhasil diubah!');
        } else {
            return redirect()->route('maxShiftPage')->with('message', 'Jumlah Shift Maksimal Gagal diubah!');
        }
    }

    /**
     * Get outlet by id
     */
    public function getOutletById($id) {
        $outlet = Warehouse::with('business')->find($id);

        return response()->json($outlet);
    }

    /**
     * Renew outlet subscription
     */
    public function renewal(Request $request, $id) {
        // Find outlet by id
        $outlet = Warehouse::find($id);

        try {
            DB::beginTransaction();

            // Update outlet subscription
            $outlet->update([
                'expired_at' => $request->expired_at,
            ]);

            DB::commit();
            return redirect()->route('outlet.index')->with('message', 'Berhasil menyelesaikan pembayaran tagihan!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('outlet.index')->with('message', 'Gagal menyelesaikan pembayaran tagihan!');
        }
    }
}
