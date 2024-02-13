<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use App\Models\User;
use App\Models\Menu;
use Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        if(Auth::user()->role_id <= 2) {
            $lims_role_all = Roles::get();
            return view('backend.role.create', compact('lims_role_all'));
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }


    public function create()
    {

    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
            ],
        ]);
        Roles::create([
            'name'  => $request->name,
            'description'  => $request->description,
            'guard_name'  => $request->guard_name,
        ]);
        return redirect('admin/role')->with('message', 'Data inserted successfully');
    }

    public function edit($id)
    {
        if(Auth::user()->role_id <= 2) {
            $lims_role_data = Roles::find($id);
            return $lims_role_data;
        }
        else
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
            ],
        ]);

        $input = $request->all();
        $lims_role_data = Roles::where('id', $input['role_id'])->first();
        $lims_role_data->update($input);
        return redirect('admin/role')->with('message', 'Data updated successfully');
    }

    public function permission($id)
    {
        $role = Role::find($id);
        $izin = $role->permissions->pluck('name')->toArray();
        $menus = Menu::all();
        return view('backend.role.permission', compact('role', 'izin', 'menus'));
    }

    public function setPermission(Request $request)
    {
        $role = Role::firstOrCreate(['id' => $request['role_id']]);

        $role->syncPermissions($request->izin_akses);

        cache()->forget('permissions');

        return redirect('admin/role')->with('message', 'Permission updated successfully');
    }

    public function destroy($id)
    {
        $lims_role_data = Roles::find($id);
        $lims_role_data->delete();
        return redirect('admin/role')->with('not_permitted', 'Data deleted successfully');
    }
}
