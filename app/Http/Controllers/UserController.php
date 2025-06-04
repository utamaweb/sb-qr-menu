<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Roles;
use App\Models\Warehouse;
use App\Models\Business;
use App\Models\Shift;
use App\Models\MailSetting;
use App\Mail\UserDetails;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Auth;
use Hash;
use Keygen;
use Mail;

class UserController extends Controller
{
    /**
     * Display a listing of users
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (auth()->user()->hasRole('Superadmin')) {
            $lims_user_list = User::with('business', 'warehouse')->get();
        } elseif (auth()->user()->hasRole('Admin Bisnis')) {
            $outlet = Warehouse::where('business_id', auth()->user()->business_id)->pluck('id');
            $lims_user_list = User::where('business_id', auth()->user()->business_id)
                ->orWhereIn('warehouse_id', $outlet)
                ->get();
        } else {
            $lims_user_list = User::with('business', 'warehouse')
                ->where('is_active', true)
                ->where('warehouse_id', auth()->user()->warehouse_id)
                ->get();
        }

        $numberOfUserAccount = $lims_user_list->count();

        return view('backend.user.index', compact('lims_user_list', 'numberOfUserAccount'));
    }

    /**
     * Show form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        $lims_role_list = Roles::where('id', '>=', auth()->user()->role_id)->get();
        $business = Business::get();

        $lims_warehouse_list = Warehouse::where('is_active', true);

        if (!auth()->user()->hasRole('Superadmin')) {
            $lims_warehouse_list->where('business_id', auth()->user()->business_id);
        }

        $lims_warehouse_list = $lims_warehouse_list->get();
        $numberOfUserAccount = User::where('is_active', true)->count();

        return view('backend.user.create', compact(
            'lims_role_list',
            'lims_warehouse_list',
            'numberOfUserAccount',
            'business'
        ));
    }

    /**
     * Generate a random password
     *
     * @return string
     */
    public function generatePassword()
    {
        return Keygen::numeric(6)->generate();
    }

    /**
     * Store a newly created user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => ['max:255'],
            'email' => ['email', 'max:255'],
        ]);

        $data = $request->all();
        $message = 'Data berhasil ditambah';
        $roleName = Role::find($request->role_id)->name;

        $user = new User;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->role_id = $request->role_id;
        $user->phone = $request->phone_number;

        $this->setBusinessAndWarehouse($user, $request->role_id, $request->business_id, $request->warehouse_id);

        $user->save();
        $user->assignRole($roleName);

        return redirect('admin/user')->with('message', $message);
    }

    /**
     * Show form for editing a user
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        $business = Business::get();
        $user = User::find($id);
        $lims_role_list = Roles::where('id', '>=', auth()->user()->role_id)->get();

        $lims_warehouse_list = Warehouse::where('is_active', true);

        if (!auth()->user()->hasRole('Superadmin')) {
            $lims_warehouse_list->where('business_id', auth()->user()->business_id);
        }

        $lims_warehouse_list = $lims_warehouse_list->get();

        return view('backend.user.edit', compact('user', 'lims_role_list', 'lims_warehouse_list', 'business'));
    }

    /**
     * Update the specified user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => ['max:255'],
            'email' => ['email', 'max:255'],
        ]);

        $user = User::find($id);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;

        if ($request->password) {
            $user->password = bcrypt($request->password);
        }

        $user->is_active = $request->has('is_active');
        $user->role_id = $request->role_id;
        $user->phone = $request->phone;

        $this->setBusinessAndWarehouse($user, $request->role_id, $request->business_id, $request->warehouse_id);

        $user->save();
        $user->syncRoles($request->role_id);

        return redirect('admin/user')->with('message', 'Data Berhasil Diubah');
    }

    /**
     * Set business and warehouse IDs based on role
     *
     * @param  \App\Models\User  $user
     * @param  int  $roleId
     * @param  int|null  $businessId
     * @param  int|null  $warehouseId
     * @return void
     */
    private function setBusinessAndWarehouse($user, $roleId, $businessId = null, $warehouseId = null)
    {
        if ($roleId == 1) {
            $user->warehouse_id = null;
            $user->business_id = null;
        } elseif ($roleId == 2 || $roleId == 6) {
            $user->warehouse_id = null;
            $user->business_id = $businessId;
        } else {
            $user->warehouse_id = $warehouseId;
            $user->business_id = null;
        }
    }

    /**
     * Show user profile
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function profile($id)
    {
        $lims_user_data = User::find($id);
        return view('backend.user.profile', compact('lims_user_data'));
    }

    /**
     * Update user profile
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function profileUpdate(Request $request, $id)
    {
        $lims_user_data = User::find($id);
        $lims_user_data->update($request->all());

        return redirect()->back()->with('message', 'Data Berhasil Diubah');
    }

    /**
     * Change user password
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(Request $request, $id)
    {
        $input = $request->all();
        $lims_user_data = User::find($id);

        if ($input['new_pass'] != $input['confirm_pass']) {
            return redirect("user/profile/{$id}")->with('message', "Please Confirm your new password");
        }

        if (Hash::check($input['current_pass'], $lims_user_data->password)) {
            $lims_user_data->password = bcrypt($input['new_pass']);
            $lims_user_data->save();
        } else {
            return redirect("user/profile/{$id}")->with('not_permitted', "Current Password doesn't match");
        }

        auth()->logout();
        return redirect('/');
    }

    /**
     * Delete multiple users by selection
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public function deleteBySelection(Request $request)
    {
        $user_id = $request['userIdArray'];

        foreach ($user_id as $id) {
            $lims_user_data = User::find($id);
            $lims_user_data->is_active = false;
            $lims_user_data->save();
        }

        return 'User deleted successfully!';
    }

    /**
     * Delete a user
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $checkShift = Shift::where('user_id', $user->id)->where('is_closed', 0)->count();

        if ($checkShift > 0) {
            return redirect('admin/user')->with(
                'not_permitted',
                'User tidak bisa dihapus karena terdapat shift yang dibuka oleh user tersebut. Tutup kasir terlebih dahulu menggunakan user tersebut.'
            );
        }

        $user->delete();

        if (Auth::id() == $id) {
            auth()->logout();
            return redirect('/login');
        }

        return redirect('admin/user')->with('not_permitted', 'Data berhasil dihapus');
    }

    /**
     * Get list of users for notifications
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notificationUsers()
    {
        $notification_users = DB::table('users')
            ->where('is_active', true)
            ->where('id', '!=', Auth::user()->id)
            ->where('role_id', '!=', '5')
            ->get();

        $html = '';
        foreach ($notification_users as $user) {
            $html .= '<option value="' . $user->id . '">' . $user->name . ' (' . $user->email . ')' . '</option>';
        }

        return response()->json($html);
    }

    /**
     * Get list of all active users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function allUsers()
    {
        $lims_user_list = DB::table('users')->where('is_active', true)->get();

        $html = '';
        foreach ($lims_user_list as $user) {
            $html .= '<option value="' . $user->id . '">' . $user->name . ' (' . $user->phone . ')' . '</option>';
        }

        return response()->json($html);
    }
}
