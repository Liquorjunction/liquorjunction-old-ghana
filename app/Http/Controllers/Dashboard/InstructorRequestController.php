<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DanceClass;
use App\Models\DanceCategory;
use App\Models\MainUser;
use Illuminate\Validation\Rule;
use Auth;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Mail;
use Carbon\Carbon;
use Storage;

class InstructorRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
        return view("dashboard.instructor-request.list", compact("start", "end"));
    }

    public function create()
    {
        return view("dashboard.instructor-request.create");
    }

    public function store(Request $request)
    {
        $this->validateRequest();
        $user = new MainUser;
        $user->is_verify_instructor = isset($request->is_verify_instructor) ? $request->is_verify_instructor : '';
        
        $user->status = 1;
        $user->save();
    
        return redirect()->route('instructor-request')->with('doneMessage', 'Instructor request created successfully.');
    }

    public function edit($id)
    {

        $instructor_request = MainUser::find($id);

        if (!empty($instructor_request)) {
            return view("dashboard.instructor-request.edit", compact("instructor_request"));
        } else {
            return redirect()->route('instructor-request')->with('errorMessage', __('backend.something_wrong'));
        }
    }

    public function show($id)
    {
        //$instructor_request = MainUser::find($id);
        $Users = \DB::table('main_users')->where('id',$id)->where('is_verify_instructor','!=',0)
                        ->where('status', '!=', 2)->first();

        return view("dashboard.website_users.instructor_show", compact("Users"));

        // $Users = MainUser::find($id);
        // if($Users->user_type == 2)
        // {
        //     return view("dashboard.website_users.show", compact("Users"));
        // }
        // else
        // {
        //     return view("dashboard.website_users.instructor_show", compact("Users"));
        // }
    }

    public function update(Request $request, $id)
    {

        $user = MainUser::find($id);
        $authId = Auth::user()->id;

        if (!empty($user)) {
            
            $this->validateRequest($id);

            $user->is_verify_instructor = isset($request->is_verify_instructor) ? $request->is_verify_instructor : '';
            if($request->is_verify_instructor == 1)
            {
                $user->user_type = 2;
            }
            elseif($request->is_verify_instructor == 2)
            {
                $user->user_type = 3;
            }
            else
            {
                $user->user_type = 2;
            }
            $user->status = 1;
            $user->save();

            // $logo = \Config::get('app.url').'/public/assets/dashboard/images/liquor.png';
            // $url_link = \URL::to("/");
            // $url = $url_link . '/';
            // $name = $user->name;
            // $email = $user->email;

            // $ismail = $this->attachment_email($name, $email, $url, $logo);

            return redirect()->route('instructor-request')->with('doneMessage', 'Instructor request updated successfully.');
        } else {
            return redirect()->route('instructor-request')->with('errorMessage', __('backend.something_wrong'));
        }
    }

    public function attachment_email($name, $email, $url, $logo) {

       
        $setting = Setting::find(1);
        $from_email = 'admin@vrinsoft.com';
       
       // $from_email = $setting['from_email'];
        $data = array('name' => $name,'email' => $email, 'url' => $url,'id'=>'2','logo' => $logo, 'from_email' => $from_email);
       
        Mail::send('instructor_request', $data, function ($message) use ($data) {

        $message->to($data['email'], 'OnlyDance')->subject('Request has been approved successfully!');
        //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

        $message->from($data['from_email'], 'OnlyDance');
        
        });

    }

    public function validateRequest($id = "")
    {

        if ($id != "") {
                $validateData = request()->validate([
                    'is_verify_instructor' => 'required|not_in:0'
                ], [
                    'is_verify_instructor.required' => 'The request status field is required.'
                ]);
        } else {
                $validateData = request()->validate([
                    'is_verify_instructor' => 'required|not_in:0',

                ], [
                    'is_verify_instructor.required' => 'The request status field is required.'
                ]);
        }

        return $validateData;
    }

    public function instructorRequestUpdateAll(Request $request)
    {
        //
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $id_multiple = explode(",", $request->ids);

                $status = $request->status;
                $is_verify_instructor = $request->is_verify_instructor;
               

                if ($is_verify_instructor == 3) {
                    $a = 3;
                    MainUser::wherein('id', $ids)->update(['is_verify_instructor' => 3]);
                    MainUser::wherein('id', $ids)->update(['user_type' => 2]);
                    return response()->json(['success' => true, 'msg' => 'Instructor request rejected successfully']);
                } else if ($is_verify_instructor == 1) {
                    $b = 1;
                    MainUser::wherein('id', $ids)->update(['is_verify_instructor' => 1]);
                    MainUser::wherein('id', $ids)->update(['user_type' => 2]);
                    return response()->json(['success' => true, 'msg' => 'Instructor request pending successfully']);
                } else if($is_verify_instructor == 2){
                    $b = 2;
                    MainUser::wherein('id', $ids)->update(['is_verify_instructor' => 2]);
                    MainUser::wherein('id', $ids)->update(['user_type' => 3]);
                    $User = MainUser::wherein('id', $ids)->get();
                    foreach ($User as $key => $u) {
                        $logo = \Config::get('app.url').'public/assets/dashboard/images/liquor.png';
                        $url_link = \URL::to("/");
                        $url = $url_link . '/';
                        $email = $u->email;
                        $name = $u->name;

                        $ismail = $this->attachment_email($name, $email, $url, $logo);
                    }
                    return response()->json(['success' => true, 'msg' => 'Instructor request approved successfully']);
                }
                else if($status == 4){
                    $aa = 4;
                    MainUser::wherein('id', $ids)->update(['status' => 4]);
                    return response()->json(['success' => true, 'msg' => 'Instructor request deleted successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
    }

    public function instructorRequestStatusUpdateAll(Request $request)
    {
        //
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $id_multiple = explode(",", $request->ids);

                $is_verify_instructor = $request->is_verify_instructor;

                MainUser::wherein('id', $ids)->update(['is_verify_instructor' => $is_verify_instructor]);
                if ($is_verify_instructor == 3) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) rejected successfully']);
                } else if ($is_verify_instructor == 1) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) pending successfully']);
                } else if($is_verify_instructor == 2){
                    return response()->json(['success' => true, 'msg' => 'Record(s) request approved successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
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

        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'id';
        } elseif ($columnIndex == 1) {
            $sort = 'name';
        } elseif ($columnIndex == 2) {
            $sort = 'email';
        } elseif ($columnIndex == 3) {
            $sort = 'phone';
        } elseif ($columnIndex == 4) {
            $sort = 'is_verify_instructor';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

            $totalAr = \DB::table('main_users')->where('is_verify_instructor','!=',0)
                        ->where('status', '!=', 2);



            if ($searchValue != "") {

                    $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                        $query->orWhere('name', 'like', '%' . $searchValue . '%')
                            ->orWhere('email', 'like', '%' . $searchValue . '%')
                            ->orWhere('phone', 'like', '%' . $searchValue . '%')
                            ->orWhere('created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                    });
                
            }


            $totalRecords = $totalAr->groupby('id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('id')
                ->get();
        

        /* print_r($totalAr);
        exit;*/
        $data_arr = [];
        $rs = '';
        foreach ($totalAr as $key => $data) {
            
            $name = isset($data->name) ? $data->name : '';
            $email = isset($data->email) ? $data->email : '';
            $phone = isset($data->phone) ? $data->phone : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $request_status = isset($data->is_verify_instructor) ? $data->is_verify_instructor : '';
            $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
          // dd($data);
            if ($data->status == 3) {
                $status = '<i class="fa fa-check text-success inline " style="margin-left: 15px;"><span class="hide">active</span></i>';
            } elseif($data->status == 4) {

                $status = '<i class="fa fa-times text-danger inline " style="margin-left: 15px;"><span class="hide">deactive</span></i>';
            }


            $requestShow =  route('instructor-request.show', ['id' => $data->id]);
            $requestEdit =  route('instructor-request.edit', ['id' => $data->id]);

            if($request_status == 1)
            {
                $rs = '<button type="button" class="btn btn-warning" style="cursor:text;">Pending</button>';
            }
            elseif($request_status == 2)
            {
                $rs = '<button type="button" class="btn btn-success" style="cursor:text;">Approved</button>';
            }
            else
            {
                $rs = '<button type="button" class="btn btn-danger" style="cursor:text;">Rejected</button>';
            }



            $options = '<a  class="btn btn-sm show-eyes list" href="' . $requestShow . '" title="View"> </a>';

            // $options .= '<a  class="btn btn-sm success paddingset" href="' . $requestEdit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button  class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "name" =>   $name,
                "email" =>   $email,
                "phone" =>   '+'.$country_code.' '.$phone,
                "request_status" => $rs,
                // "status" => $status,
                "options" => $options
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
}
