<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ojol;

class OjolController extends Controller
{
    // Index
    public function index() {
        $ojols = Ojol::where('business_id', '=', auth()->user()->business_id)->get();

        return view('backend.ojol.index', compact("ojols"));
    }

    // Create
    public function create() {
        return view('backend.ojol.create');
    }

    // Store
    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'percent' => 'nullable|numeric',
            'extra_price' => 'nullable'
        ]);

        $create = Ojol::create([
            'name' => ucfirst($request->name),
            'percent' => $request->percent,
            'extra_price' => intVal(str_replace(',', '', $request->extra_price)),
            'business_id' => auth()->user()->business_id
        ]);

        if($create) {
            return redirect()->route('ojol.index')->with('message', 'Data berhasil ditambahkan');
        } else {
            return redirect()->route('ojol.index')->with('message', 'Data gagal ditambahkan');
        }
    }

    // Edit
    public function edit(Ojol $ojol) {
        return view('backend.ojol.edit', compact('ojol'));
    }

    // Update
    public function update(Request $request, Ojol $ojol) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'percent' => 'nullable|numeric',
            'extra_price' => 'nullable'
        ]);

        $update = $ojol->update([
            'name' => ucfirst($request->name),
            'percent' => $request->percent,
            'extra_price' => intVal(str_replace(',', '', $request->extra_price))
        ]);

        if($update) {
            return redirect()->route('ojol.index')->with('message', 'Data berhasil diedit!');
        } else {
            return redirect()->route('ojol.index')->with('message', 'Data gagal diedit!');
        }
    }

    // Destroy
    public function destroy(Ojol $ojol) {
        $delete = $ojol->delete();

        if($delete) {
            return redirect()->route('ojol.index')->with('message', 'Data berhasil dihapus');
        } else {
            return redirect()->route('ojol.index')->with('message', 'Data gagal dihapus');
        }
    }

}
