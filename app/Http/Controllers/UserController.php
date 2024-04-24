<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Roles;
use App\Models\Biller;
use App\Models\Warehouse;
use App\Models\Business;
use App\Models\CustomerGroup;
use App\Models\Customer;
use DB;
use Auth;
use Hash;
use Keygen;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Mail\UserDetails;
use Mail;
use App\Models\MailSetting;

class UserController extends Controller
{
    use \App\Traits\MailInfo;

    public function index()
    {
        $lims_user_list = User::get();
        // menghitung jumlah user
        $numberOfUserAccount = User::where('is_active', true)->count();
        return view('backend.user.index', compact('lims_user_list', 'numberOfUserAccount'));
    }

    public function create()
    {
        $role = Role::find(Auth::user()->role_id);
        $lims_role_list = Roles::get();
        $business = Business::get();
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        $numberOfUserAccount = User::where('is_active', true)->count();
        return view('backend.user.create', compact('lims_role_list', 'lims_warehouse_list', 'numberOfUserAccount','business'));
    }

    public function generatePassword()
    {
        $id = Keygen::numeric(6)->generate();
        return $id;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
            ],
            'email' => [
                'email', 'max:255'
            ],
        ]);

        $data = $request->all();
        $message = 'User created successfully';
        $roleName = Role::find($request->role_id)->name;

        $user = new User;
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->phone = $request->phone_number;
        if($request->role_id == 1){
            $user->warehouse_id = NULL;
            $user->business_id = NULL;
        } elseif($request->role_id == 2){
            $user->warehouse_id = NULL;
            $user->business_id = $request->business_id;
        } else{
            $user->warehouse_id = $request->warehouse_id;
            $user->business_id = NULL;
        }
        $user->save();

        $user->assignRole($roleName);
        return redirect('admin/user')->with('message1', $message);
    }

    public function edit($id)
    {
        $role = Role::find(Auth::user()->role_id);
        $business = Business::get();
        $user = User::find($id);
        $lims_role_list = Roles::get();
        // $lims_biller_list = Biller::where('is_active', true)->get();
        $lims_warehouse_list = Warehouse::where('is_active', true)->get();
        return view('backend.user.edit', compact('user', 'lims_role_list', 'lims_warehouse_list', 'business'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => [
                'max:255',
            ],
            'email' => [
                'email',
                'max:255',
            ],
        ]);

        $input = $request->except('password');
        if(!isset($input['is_active']))
            $active = false;
        $user = User::find($id);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        if($request->password){
            $user->password = bcrypt($request->password);
        }
        if(!$request->is_active){
            $user->is_active = false;
        }
        $user->email = $request->email;
        $user->role_id = $request->role_id;
        $user->phone = $request->phone;
        if($request->role_id == 1){
            $user->warehouse_id = NULL;
            $user->business_id = NULL;
        } elseif($request->role_id == 2){
            $user->warehouse_id = NULL;
            $user->business_id = $request->business_id;
        } else{
            $user->warehouse_id = $request->warehouse_id;
            $user->business_id = NULL;
        }
        $user->save();
        $user->syncRoles($request->role_id);

        return redirect('admin/user')->with('message2', 'Data updated successfullly');
    }

    public function profile($id)
    {
        $lims_user_data = User::find($id);
        return view('backend.user.profile', compact('lims_user_data'));
    }

    public function profileUpdate(Request $request, $id)
    {
        $input = $request->all();
        $lims_user_data = User::find($id);
        $lims_user_data->update($input);
        return redirect()->back()->with('message3', 'Data updated successfullly');
    }

    public function changePassword(Request $request, $id)
    {
        $input = $request->all();
        $lims_user_data = User::find($id);
        if($input['new_pass'] != $input['confirm_pass'])
            return redirect("user/" .  "profile/" . $id )->with('message2', "Please Confirm your new password");

        if (Hash::check($input['current_pass'], $lims_user_data->password)) {
            $lims_user_data->password = bcrypt($input['new_pass']);
            $lims_user_data->save();
        }
        else {
            return redirect("user/" .  "profile/" . $id )->with('message1', "Current Password doesn't match");
        }
        auth()->logout();
        return redirect('/');
    }

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

    public function destroy($id)
    {
        $lims_user_data = User::find($id);
        $lims_user_data->delete();
        if(Auth::id() == $id){
            auth()->logout();
            return redirect('/login');
        }
        else
            return redirect('admin/user')->with('message3', 'Data deleted successfullly');
    }

    public function notificationUsers()
    {
        $notification_users = DB::table('users')->where([
            ['is_active', true],
            ['id', '!=', \Auth::user()->id],
            ['role_id', '!=', '5']
        ])->get();

        $html = '';
        foreach($notification_users as $user){
            $html .='<option value="'.$user->id.'">'.$user->name . ' (' . $user->email. ')'.'</option>';
        }

        return response()->json($html);
    }

    public function allUsers()
    {
        $lims_user_list = DB::table('users')->where('is_active', true)->get();

        $html = '';
        foreach($lims_user_list as $user){
            $html .='<option value="'.$user->id.'">'.$user->name . ' (' . $user->phone. ')'.'</option>';
        }

        return response()->json($html);
    }
}
