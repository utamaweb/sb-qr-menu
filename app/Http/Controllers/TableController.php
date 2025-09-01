<?php

namespace App\Http\Controllers;

use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TableController extends Controller
{

    /**
     * Index -> index (Public Method)
     */
    public function index() {
        // Get outlet_id
        $outletId = $this->getOutletId();

        // Get outlet tables
        $tables = Table::where('outlet_id', $outletId)->get();

        return view('backend.tables.index', compact('tables'));
    }

    /**
     * Create -> create (Public Method)
     */
    public function create() {
        return view('backend.tables.create');
    }

    /**
     * Store -> store (Public Method)
     */
    public function store(Request $request)
    {
        // Validate form data
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // Get outlet_id
        $outletId = $this->getOutletId();

        // Generate code in the format: outletId-<5 lowercase alphanumeric chars> e.g., 1-123ab
        // Ensure uniqueness against the unique index on `code`
        do {
            $suffix = strtolower(Str::random(5));
            $code = $outletId . '-' . $suffix;
        } while (Table::where('code', $code)->exists());

        // Create new table
        DB::beginTransaction();
        try {
            Table::create([
                'name' => $request->name,
                'code' => $code,
                'outlet_id' => $outletId
            ]);

            DB::commit();
            return redirect()->route('tables.index')->with('success', 'Table created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while creating the table. Please try again.')->withInput();
        }
    }

    /**
     * Edit -> edit (Public Method)
     */
    public function edit(Table $table)
    {
        return view('backend.tables.edit', compact('table'));
    }

    /**
     * Update -> update (Public Method)
     */
    public function update(Request $request, Table $table)
    {
        // Validate form data
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // Update table
        DB::beginTransaction();
        try {
            $table->update([
                'name' => $request->name
            ]);

            DB::commit();
            return redirect()->route('tables.index')->with('success', 'Table updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while updating the table. Please try again.')->withInput();
        }
    }

    /**
     * Destroy -> destroy (Public Method)
     */
    public function destroy(Table $table)
    {
        DB::beginTransaction();
        try {
            $table->delete();

            DB::commit();
            return redirect()->route('tables.index')->with('success', 'Table deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the table. Please try again.');
        }
    }

    /**
     * Get outlet id -> getOutletId (Private Method)
     *
     * This method used to get outlet id based on authenticated user role
     */
    private function getOutletId() {
        if (auth()->user()->hasRole('Admin Outlet')) {
            $outletId = auth()->user()->warehouse_id;
        } else {
            if (!request()->has('outlet')) {
                return redirect()->route('admin.dashboard');
            }

            $outletId = request('outlet');
        }

        return $outletId;
    }
}
