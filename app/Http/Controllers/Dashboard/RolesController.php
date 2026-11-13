<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller; 
use App\Http\Requests;
use App\Models\Setting;
use App\Models\Role;
use App\Models\UserRole;
use App\Models\User;
use App\Models\RoleModulePermission;
use App\Models\RoleModule;
use App\Models\WebmasterSetting;
use Auth;
use Illuminate\Http\Request;
use Redirect;
use File;
use Helper;
use Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Exports\RolesExport;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class RolesController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {  
        $roles = Role::count(); 
        $allowed_permissions = Helper::GetRolePermission(Auth::user()->user_type,29,'read');           
        return view("dashboard.access_control.roles.list", compact("roles",'allowed_permissions')); 
    } 

    
    public function anyData(Request $request) 
    {
         
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='roles.id';
        }elseif ($columnIndex==1) {
            $sort='roles.name';
        }else{
            $sort='roles.id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        $totalAr = Role::where('status','!=',2)->where('id','=',2);
        
          if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('name', 'like', '%' . $searchValue . '%') ;
            });
        }


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $data_arr=[];

        foreach ($totalAr as $key => $data) 
        {   
            $RoleEdit =  route('roles.edit',['id'=>base64_encode($data->id)]);
            $RoleShow =  route('roles.show',['id'=>base64_encode($data->id)]);
            $RoleDelete =  route('roles.delete',['id'=>base64_encode($data->id)]);

            $options = "";   

            //$allowed_permissions = Helper::GetRolePermission(Auth::id(),2); 

            //if(isset($allowed_permissions) && $allowed_permissions->read == 1){
                $options .= '<a class="btn btn-sm show-eyes list" href="'.$RoleShow.'" title="View Role"> </a> '; 
           // }

            //if(isset($allowed_permissions) && $allowed_permissions->update == 1){
                $options .= '<a class="btn btn-sm success paddingset" href="'.$RoleEdit.'" title="Edit Role"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>'; 
            //}

            //if(isset($allowed_permissions) && $allowed_permissions->delete == 1){
            //    $options .= '<a class="btn btn-sm success paddingset remove-record" href="javascript:void(0)" id="'.$data->id.'" title="Delete Role"> <small><i class="material-icons">&#xe872;</i> </small> </a>';
            //}
             
            if($options == ''){
                $options = "-"; 
            } 

            $data_arr[] =array(
              "id" =>   isset($data->id) ? $data->id: '',
              "slug" =>   '',
              "name" =>   isset($data->name) ? $data->name: '', 
              "options" => $options,
            );
          
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
         );
        echo json_encode($response);
    }

    public function create()
    {    
        return view('dashboard.access_control.roles.create');
    }

    
    public function StorePermission(Request $request)
    {    

        $this->validate($request, [
            'role_name' => 'required', 
        ]);

        $Role = new Role();
        $Role->name = $request->role_name;
        $Role->slug = '';
        $Role->status = 1; 
        $Role->created_at = date('Y-m-d H:i:s');
        $Role->updated_at = date('Y-m-d H:i:s'); 
        $Role->save();

        $totalmodule = RoleModule::where('status','!=',2)->get();
        foreach ($totalmodule as $key => $value) {
            $RoleModulePermission = new RoleModulePermission();
            $RoleModulePermission->role_module_id = $value->id;
            $RoleModulePermission->role_id = $Role->id;
            $RoleModulePermission->read = 0;
            $RoleModulePermission->create = 0;
            $RoleModulePermission->update = 0;
            $RoleModulePermission->delete = 0;
            $RoleModulePermission->created_at = date('Y-m-d H:i:s');
            $RoleModulePermission->updated_at = date('Y-m-d H:i:s'); 
            $RoleModulePermission->save();
        }  
 
        if(isset($request->read)){ 
            foreach ($request->read as $key => $value) {    
                RoleModulePermission::where('role_id',$Role->id)->where('role_module_id',$key)->update([ 
                    'read' => 1
                ]);
            } 
        }   
        if(isset($request->create)){
            foreach ($request->create as $key => $value) {    
                RoleModulePermission::where('role_id',$Role->id)->where('role_module_id',$key)->update([ 
                    'create' => 1
                ]);
            } 
        }
        if(isset($request->update)){
            foreach ($request->update as $key => $value) {    
                RoleModulePermission::where('role_id',$Role->id)->where('role_module_id',$key)->update([ 
                    'update' => 1
                ]);
            } 
        }    
        if(isset($request->delete)){
            foreach ($request->delete as $key => $value) {    
                RoleModulePermission::where('role_id',$Role->id)->where('role_module_id',$key)->update([ 
                    'delete' => 1
                ]);
            } 
        }
         
        return redirect()->route('roles')->with('success', 'Role Created successfully.'); 
    }

    
    public function edit($encode_id)
    {  
        $id = base64_decode($encode_id); 
        $role = Role::where('id',$id)->first();  
        //$role = Role::join('role_module_permission', `role_module_permission.role_id`,`role.id`)
       // join('role_modules',`role_modules.id`,`role_module_permission.role_module_id`)->where('id',$id)->first(); 
        $role_name = $role->name;  
        return view('dashboard.access_control.roles.edit', compact('encode_id','role_name'));
    }

    public function PermissionFilter(Request $request) 
    { 
          
        if($request->page == 'show'){
            $disabled = 'readonly';
            $pointer_events = 'style="pointer-events: none;"';
        }else{
            $disabled = ''; 
            $pointer_events = '';
        } 
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
         
        
        if($request->page != 'create'){     
            $id = base64_decode($request->encode_id);  
        }  

        $totalRecords = RoleModule::where('status','!=',2)->get(); 

        //$totalAr = RoleModule::where('status','!=',2)->->get();
        $countryAdminRoleModuleIds = RoleModulePermission::where('role_id',1)->pluck('role_module_id')->toArray();
        
        $remove_access_id = array('29','24');
        //Removed access controller for sub admin panel
        $newCountryAdminRoleModuleIds = array_diff($countryAdminRoleModuleIds, $remove_access_id);
        
        $totalAr = RoleModule::where('status','!=',2)->wherein('id',$newCountryAdminRoleModuleIds)->get();
        //dd($totalAr);
        $data_arr=[]; 
        foreach ($totalAr as $key => $data) 
        {   

            if($request->page == 'create'){  
                $read_checked = ''; 
                $create_checked = '';    
                $update_checked = ''; 
                $delete_checked = '';  
            }else{
                $check = RoleModulePermission::where('role_module_id',$data->id)->where('role_id',$id)->first(); 
                if(isset($check) && $check->read == 1){
                    $read_checked = 'checked';
                }else{
                    $read_checked = '';    
                } 
                if(isset($check) && $check->create == 1){
                    $create_checked = 'checked';
                }else{
                    $create_checked = '';    
                } 
                if(isset($check) && $check->update == 1){
                    $update_checked = 'checked';
                }else{
                    $update_checked = '';    
                } 
                if(isset($check) && $check->delete == 1){
                    $delete_checked = 'checked';
                }else{
                    $delete_checked = '';    
                } 
            } 
            $RoleEdit =  route('roles.edit',['id'=>base64_encode($data->id)]);
            $RoleShow =  route('roles.show',['id'=>base64_encode($data->id)]);
            $RoleDelete =  route('roles.delete',['id'=>base64_encode($data->id)]);
            $read = ""; 
            $read .= '<input type="checkbox" class="btn btn-sm read" data-id="'.$data->id.'" name="read['.$data->id.']" value="true" '.$disabled.'  '.$read_checked.'  '.$pointer_events.' >';
            // $create = ""; 
            // $create .= '<input type="checkbox" class="btn btn-sm create" data-id="'.$data->id.'" name="create['.$data->id.']" value="true" '.$disabled.' '.$create_checked.' '.$pointer_events.' >';
            // $update = ""; 
            // $update .= '<input type="checkbox" class="btn btn-sm update" data-id="'.$data->id.'" name="update['.$data->id.']" value="true"  '.$disabled.'  '.$update_checked.'  '.$pointer_events.'>';
            // $delete = ""; 
            // $delete .= '<input type="checkbox" class="btn btn-sm delete" data-id="'.$data->id.'" name="delete['.$data->id.']"    value="true"  '.$disabled.'  '.$delete_checked.'  '.$pointer_events.'>'; 

            $data_arr[] =array(
              "id" =>   isset($data->id) ? $data->id: '',
              "name" =>   isset($data->name) ? $data->name: '', 
              "read" => $read,
            //   "create" => $create,
            //   "update" => $update,
            //   "delete" => $delete,
            );
          
        }
        
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
         );
        echo json_encode($response);
    }

    public function UpdatePermission(Request $request)
    {  
        $this->validate($request, [
            'role_name' => 'required', 
        ]);

        $id = base64_decode($request->encode_id);  
        $all_modules = RoleModule::where('status','!=',2)->get(); 
        $read_permission = $request->read; 
        $create_permission = $request->create; 
        $update_permission = $request->update; 
        $delete_permission = $request->delete;  
        foreach ($all_modules as $key => $value) {   
            if(isset($read_permission[$value->id]) && $read_permission[$value->id] == true ){
                $is_exists = RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->count(); 
                if($is_exists > 0){
                    RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->update(['read' => 1]);
                }else{
                    $RoleModulePermission = new RoleModulePermission();
                    $RoleModulePermission->role_module_id = $value->id;
                    $RoleModulePermission->role_id = $id;
                    $RoleModulePermission->read = 1;
                    $RoleModulePermission->create = 0;
                    $RoleModulePermission->update = 0;
                    $RoleModulePermission->delete = 0;
                    $RoleModulePermission->created_at = date('Y-m-d H:i:s');
                    $RoleModulePermission->updated_at = date('Y-m-d H:i:s'); 
                    $RoleModulePermission->save(); 
                } 
            }
            if(!isset($read_permission[$value->id])  || $read_permission[$value->id] != true){
                RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->update(['read' => 0]);
            }
        } 
        foreach ($all_modules as $key => $value) {   
            if(isset($create_permission[$value->id]) && $create_permission[$value->id] == true ){
                $is_exists = RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->count(); 
                if($is_exists > 0){
                    RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->update(['create' => 1]);
                }else{
                    $RoleModulePermission = new RoleModulePermission();
                    $RoleModulePermission->role_module_id = $value->id;
                    $RoleModulePermission->role_id = $id;
                    $RoleModulePermission->read =   0;
                    $RoleModulePermission->create = 1;
                    $RoleModulePermission->update = 0;
                    $RoleModulePermission->delete = 0;
                    $RoleModulePermission->created_at = date('Y-m-d H:i:s');
                    $RoleModulePermission->updated_at = date('Y-m-d H:i:s'); 
                    $RoleModulePermission->save(); 
                } 
            }
            if(!isset($create_permission[$value->id])  || $create_permission[$value->id] != true){
                RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->update(['create' => 0]);
            }
        } 
        foreach ($all_modules as $key => $value) {   
            if(isset($update_permission[$value->id]) && $update_permission[$value->id] == true ){
                $is_exists = RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->count(); 
                if($is_exists > 0){
                    RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->update(['update' => 1]);
                }else{
                    $RoleModulePermission = new RoleModulePermission();
                    $RoleModulePermission->role_module_id = $value->id;
                    $RoleModulePermission->role_id = $id;
                    $RoleModulePermission->read = 0;
                    $RoleModulePermission->create = 0;
                    $RoleModulePermission->update = 1;
                    $RoleModulePermission->delete = 0;
                    $RoleModulePermission->created_at = date('Y-m-d H:i:s');
                    $RoleModulePermission->updated_at = date('Y-m-d H:i:s'); 
                    $RoleModulePermission->save(); 
                } 
            }
            if(!isset($update_permission[$value->id])  || $update_permission[$value->id] != true){
                RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->update(['update' => 0]);
            }
        } 
        foreach ($all_modules as $key => $value) {   
            if(isset($delete_permission[$value->id]) && $delete_permission[$value->id] == true ){
                $is_exists = RoleModulePermission::where('role_module_id',$value->id)->count(); 
                if($is_exists > 0){
                    RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->update(['delete' => 1]);
                }else{
                    $RoleModulePermission = new RoleModulePermission();
                    $RoleModulePermission->role_module_id = $value->id;
                    $RoleModulePermission->role_id = $id;
                    $RoleModulePermission->read = 0;
                    $RoleModulePermission->create = 0;
                    $RoleModulePermission->update = 0;
                    $RoleModulePermission->delete = 1;
                    $RoleModulePermission->created_at = date('Y-m-d H:i:s');
                    $RoleModulePermission->updated_at = date('Y-m-d H:i:s'); 
                    $RoleModulePermission->save(); 
                } 
            }
            if(!isset($delete_permission[$value->id])  || $delete_permission[$value->id] != true){
                RoleModulePermission::where('role_module_id',$value->id)->where('role_id',$id)->update(['delete' => 0]);
            }
        } 
        Role::where('id',$id)->update([
            'name' => $request->role_name, 
        ]);
        $encode_id = $request->encode_id;
        return redirect()->route('roles')->with('doneMessage', 'Record(s) update successfully'); 
    }

    public function show($encode_id)
    {  
        $id = base64_decode($encode_id); 
        $role = Role::where('id',$id)->first(); 
        $role_name = $role->name;  
        return view('dashboard.access_control.roles.show', compact('encode_id','role_name'));
    }

    public function destroy(Request $request)
    {   
        $users_assigned_id = UserRole::where('role_id',$request->id)->pluck('user_id')->toArray();
        $check_status = User::whereIn('id',$users_assigned_id)->where('status','!=',2)->count();  

        if($check_status > 0){
            return 2;     
        }else{
            Role::where('id',$request->id)->delete();
            RoleModulePermission::where('role_id',$request->id)->delete();  
            return 1; 
        }
        
    }

    public function export() 
    { 
        $excel = Excel::download(new RolesExport, 'roles.xlsx');
        Session::forget('data'); 
        return $excel;
    }
 
}
