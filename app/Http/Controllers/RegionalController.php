<?php

namespace App\Http\Controllers;

use App\Models\Regional;
use Illuminate\Http\Request;

class RegionalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $regionals = Regional::where('business_id', auth()->user()->business_id)->get();
        return view('backend.regional.index', compact('regionals'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'max:255',
        ]);
        $regional = Regional::create([
            'name'        => $request->name,
            'business_id' => auth()->user()->business_id
        ]);
        return redirect()->back()->with('message', 'Regional Berhasil Ditambah');
    }

    // public function update(Request $request, $id)
    // {
    //     $this->validate($request,[
    //         'name' => 'max:255',
    //     ]);

    //     $regional = Regional::find($id);
    //     $regional->update([
    //         'name' => $request->name,
    //     ]);

    //     return redirect()->back()->with('message', 'Regional Berhasil Diubah');
    // }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        try {
            $regional = Regional::findOrFail($id);
            $regional->name = $request->name;
            $regional->save();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        $regional = Regional::findOrFail($id);
        $regional->delete();
        return redirect()->back()->with('not_permitted', 'Regional Berhasil Dihapus');
    }
}
