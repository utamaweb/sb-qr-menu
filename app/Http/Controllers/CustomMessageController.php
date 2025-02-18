<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomMessage;
use Illuminate\Support\Facades\DB;

class CustomMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all custom messages
        $custom_messages = CustomMessage::all();

        return view('backend.custom_message.index', compact('custom_messages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            CustomMessage::create([
                'key'   => strtoupper(str_replace(' ', '_', $request->key)),
                'value' => $request->value,
            ]);

            DB::commit();
            return redirect()->route('custom-message.index')->with('message', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('custom-message.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Get custom message
        $custom_message = CustomMessage::findOrFail($id);
        return $custom_message;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Get custom message
        $custom_message = CustomMessage::findOrFail($id);
        try {
            DB::beginTransaction();

            $custom_message->key = strtoupper(str_replace(' ', '_', $request->key));
            $custom_message->value = $request->value;

            $custom_message->save();

            DB::commit();
            return redirect()->route('custom-message.index')->with('message', 'Data berhasil disimpan');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('custom-message.index')->with('error', 'Data gagal disimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            CustomMessage::destroy($id);

            DB::commit();
            return redirect()->route('custom-message.index')->with('message', 'Data berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('custom-message.index')->with('error', 'Data gagal dihapus');
        }
    }
}
