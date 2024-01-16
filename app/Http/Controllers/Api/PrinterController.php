<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Printer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Str;
use Storage;

class PrinterController extends Controller
{
    public function index() {
        $printers = Printer::get();
        return response()->json($printers, 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'device_type' => 'required',
            'name' => 'required',
            'paper_type' => 'required',
            'connection' => 'required',
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $printer = Printer::create([
                'device_type' => $request->device_type,
                'name' => $request->name,
                'paper_type' => $request->paper_type,
                'connection' => $request->connection,
                'driver_type' => $request->driver_type,
                'mac_address' => $request->mac_address,
                'warehouse_id' => auth()->user()->warehouse_id,
            ]);
            DB::commit();
            return response()->json($printer, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function detail($id)
    {
        $printer = Printer::where('id', $id)->first();
        if ($printer == NULL){
            return response()->json(['message' => 'Data Tidak Ditemukan.'], 404);
        }
        return response()->json($printer, 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'device_type' => 'required',
            'name' => 'required',
            'paper_type' => 'required',
            'connection' => 'required',
            'mac_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 400);
        }

        DB::beginTransaction();

        try {
            $printer = Printer::findOrFail($id);
            $printer->update([
                'device_type' => $request->device_type,
                'name' => $request->name,
                'paper_type' => $request->paper_type,
                'connection' => $request->connection,
                'driver_type' => $request->driver_type,
                'mac_address' => $request->mac_address,
            ]);
            DB::commit();
            return response()->json($printer, 200);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function destroy(Request $request, $id)
    {
        $printer = Printer::find($id);
        $printer->delete();
        return response()->json(['message' => 'Printer Succesfully Deleted.'], 200);
    }
}
