<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MainUser;
use App\Models\Post;
use Illuminate\Validation\Rule;
use Auth;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Mail;
use Carbon\Carbon;
use Storage;
use Session;


class UsersAdminController extends Controller
{
    private $uploadPath = "/uploads/website_users/";

    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
        return view("dashboard.website_users.list", compact("start", "end"));
    }


    public function create()
    {
        return view("dashboard.website_users.create");
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Users = MainUser::find($id);

        if($Users->user_type == 2)
        {
            return view("dashboard.website_users.edit", compact("Users"));
        }
        else
        {
            return view("dashboard.website_users.instructor_edit", compact("Users"));
        }

      
        
    }

    public function show($id)
    {        
        $Users = MainUser::find($id);
        if($Users->user_type == 2)
        {
            return view("dashboard.website_users.show", compact("Users"));
        }
        else
        {
            return view("dashboard.website_users.instructor_show", compact("Users"));
        }

    }

    // public function postList($id)
    // {
    //     $start = '';
    //     $end = '';
    //     $start = Carbon::now()->format('m-d-Y');
    //     $end = Carbon::now()->format('m-d-Y');
    //     $post = Post::where('user_id', $id);
    //     \Session::put('uid', $id);

    //     return view("dashboard.post.list", compact("post","start", "end"));
    // }

    // public function postShow($id)
    // {
    //     $post = Post::find($id);

    //     return view("dashboard.post.show", compact("post"));
    // }

    // public function postDestroy($id)
    // {
    //     $post = Post::find($id);
    //     $post->delete();
        
    //     return redirect()->route('post')
    //         ->with('doneMessage', 'Record deleted successfully');
    // }

    public function destroy($id)
    {
        MainUser::where('id', $id)->update(['status' => 2]);
        
        return redirect()->route('users')
            ->with('doneMessage', 'Record deleted successfully');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {

        $otp = mt_rand(1000, 9999);
        $this->validateRequest();
        $User = new MainUser;
        $User->name = isset($request->name) ? $request->name : '';
        $User->email = isset($request->email) ? $request->email : '';
        // $User->otp = $otp;
        $User->updated_at = date('Y-m-d H:i:s');
        $User->save();
        $logo = \Config::get('app.url') . '/public/assets/dashboard/images/logo.png';
        $email = $request->email;
        $name = $request->name;
       // $ismail = $this->attachment_email_register($email, $name, $otp, $logo);
        return redirect()->route('users')->with('doneMessage', 'Record created successfully.');
    }

    public function update(Request $request, $id)
    {

        $User = MainUser::find($id);
        $authId = Auth::user()->id;
        if($User->user_type == 2)
        {
            $this->validateRequest1($id);
        }
        else
        {
            $this->validateRequest($id);
        }
        
        if (!empty($User)) 
        {
           // dd($User);
            
            $User->name = isset($request->name) ? $request->name : '';
            $User->email = isset($request->email) ? $request->email : '';
            $User->phone = isset($request->phone) ? $request->phone : '';
            $User->about_me = isset($request->about_me) ? $request->about_me : '';
            $User->country_code = isset($request->country_code) ? $request->country_code : '';
            $User->dance_group_name = isset($request->dance_group_name) ? $request->dance_group_name : '';
            $User->category_dance_instructor = isset($request->category_dance_instructor) ? $request->category_dance_instructor : '';
            $User->instructor_facebook_link = isset($request->instructor_facebook_link) ? $request->instructor_facebook_link : '';
            $User->instructor_instagram_link = isset($request->instructor_instagram_link) ? $request->instructor_instagram_link : '';
            $User->instructor_tiktok_link = isset($request->instructor_tiktok_link) ? $request->instructor_tiktok_link : '';
            $User->instructor_web_link = isset($request->instructor_web_link) ? $request->instructor_web_link : '';
            $User->instructor_location = isset($request->instructor_location) ? $request->instructor_location : '';
            $User->updated_at = date('Y-m-d H:i:s');
            $User->update_by = $authId;
            $User->save();


            $this->storeImage($User);
            $this->storePortfolioImage($User);
            $this->storeVideo($User);
            // if(!empty($request->password)){

            //     $updatepsw = User::where('id', $id)->update(array(
            //         'password' => Hash::make($request->password),
            //      ));


            //     $logo = \Config::get('app.url').'/public/assets/dashboard/images/logo.png';
            //     $email = $User->email;
            //     $password = $request->password;
            //     $name = $User->fullname;

            //     $ismail = $this->attachment_email_update_password($email, $password, $name, $logo);


            // }


            return redirect()->route('users')->with('doneMessage', 'Record updated successfully.');
        } else {
            return redirect()->route('users')->with('errorMessage', __('backend.something_wrong'));
        }
    }


    public function attachment_email_update_password($email, $password, $name, $logo)
    {


        $setting = Setting::find(1);

        $from_email = $setting['from_email'];
        $data = array('email' => $email, 'password' => $password, 'name' => $name, 'id' => '3', 'logo' => $logo, 'from_email' => $from_email);

        Mail::send('password_update', $data, function ($message) use ($data) {

            $message->to($data['email'], 'OnlyDance')->subject('Password has been update succesfully!');

            $message->from($data['from_email'], 'OnlyDance');
        });
    }


    private function storeImage($user)
    {

        // if (request()->has('profile')) {
        //     $avatarName = time() . '.' . request()->profile->getClientOriginalExtension();
        //     $file = request()->file('profile');
        //     $name = $avatarName;
        //     $filePath = public_path().'/uploads/website_users/' . $name;
        //     dd($filePath);
        //     Storage::disk('public')->put($filePath, file_get_contents($file));

        //     $user->update([
        //         'profile' => $avatarName,
        //     ]);
        // }

         $formFileName = "profile";
         $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/website_users/";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

             $user->update([
                'profile' => $fileFinalName_ar,
            ]);
         }
    }

    private function storePortfolioImage($user)
    {

        // if (request()->has('profile')) {
        //     $avatarName = time() . '.' . request()->profile->getClientOriginalExtension();
        //     $file = request()->file('profile');
        //     $name = $avatarName;
        //     $filePath = public_path().'/uploads/website_users/' . $name;
        //     dd($filePath);
        //     Storage::disk('public')->put($filePath, file_get_contents($file));

        //     $user->update([
        //         'profile' => $avatarName,
        //     ]);
        // }

         $formFileName = "instructor_portfolio_image";
         $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/website_users/";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

             $user->update([
                'instructor_portfolio_image' => $fileFinalName_ar,
            ]);
         }
    }

    private function storeVideo($user)
    {

        // if (request()->has('image_name')) {
        //     $avatarName = time() . '.' . request()->image_name->getClientOriginalExtension();
        //     $file = request()->file('image_name');
        //     $name = $avatarName;
        //     $filePath = 'vehicle_type/' . $name;
        //     Storage::disk('public')->put($filePath, file_get_contents($file));

        //     $user->update([
        //         'image' => $avatarName,
        //     ]);
        // }

        $formFileName = "instructor_portfolio_video";
        $fileFinalName_ar = "";
        if (request()->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
            $uploadPath = "/uploads/website_users/videos/";
            $path = public_path() . $uploadPath;
            request()->file($formFileName)->move($path, $fileFinalName_ar);

            $user->update([
               'instructor_portfolio_video' => $fileFinalName_ar,
           ]);
        }
    }


    public function validateRequest($id = "")
    {

        if ($id != "") {

            $validateData = request()->validate([
                'name' => 'required',
                'about_me' => 'required',
                'category_dance_instructor' => 'required',
                'dance_group_name' => 'required',
                'instructor_facebook_link' => 'required|url',
                'instructor_instagram_link' => 'required|url',
                'instructor_tiktok_link' => 'required|url',
                'instructor_web_link' => 'required|url',
                'instructor_location' => 'required',
                'phone' => [
                    'required',
                    'numeric', Rule::unique('main_users')->ignore($id)->where(function ($query) use ($id) {
                        return $query->where('status', '!=', '2');
                    })
                ],
                'email' => [
                    'required', 'regex:/(.+)@(.+)\.(.+)/i',
                    'email',
                    Rule::unique('main_users')->ignore($id)->where(function ($query) use ($id) {
                        return $query->where('status', '!=', '2');
                    })
                ],
                'instructor_portfolio_image' => 'image|mimes:jpg,jpeg,png|max:2048',
                'profile' => 'image|mimes:jpg,jpeg,png|max:2048',
                'instructor_portfolio_video' => 'mimes:mp4|max:4096'
            ], [
                'phone.required' => 'The mobile number field is required.',
                'profile.mimes' => 'The Image field is only support jpg, jpeg, png file type only.',
                'instructor_portfolio_image.mimes' => 'The Image field is only support jpg, jpeg, png and file type only.',
                'instructor_portfolio_video.mimes' => 'The video must be a file of type: mp4.',
                'profile.image' => 'The Profile Photo must be an image.'
            ]);
        } else {

            $validateData = request()->validate(
                [
                    'name' => 'required',
                    'about_me' => 'required',
                    'category_dance_instructor' => 'required',
                    'dance_group_name' => 'required',
                    'instructor_facebook_link' => 'required|url',
                    'instructor_instagram_link' => 'required|url',
                    'instructor_tiktok_link' => 'required|url',
                    'instructor_web_link' => 'required|url',
                    'instructor_location' => 'required',
                    'email' => [
                        'required', 'regex:/(.+)@(.+)\.(.+)/i', 'email',
                        Rule::unique('users')->where(function ($query) use ($id) {
                            return $query->where('status', '!=', '2');
                        })
                    ],
                    'phone' => [
                        'numeric', Rule::unique('users')->where(function ($query) use ($id) {
                            return $query->where('status', '!=', '2');
                        })
                    ],

                ]
            );
        }

        return $validateData;
    }

    public function validateRequest1($id = "")
    {

        if ($id != "") {

            $validateData = request()->validate([
                'name' => 'required',
                'country_code' => 'required',
                'phone' => [
                    'required',
                    'numeric', Rule::unique('main_users')->ignore($id)->where(function ($query) use ($id) {
                        return $query->where('status', '!=', '2');
                    })
                ],
                'email' => [
                    'required', 'regex:/(.+)@(.+)\.(.+)/i',
                    'email',
                    Rule::unique('main_users')->ignore($id)->where(function ($query) use ($id) {
                        return $query->where('status', '!=', '2');
                    })
                ],
                'profile' => 'image|mimes:jpg,jpeg,png|max:2048'
            ], [
                'phone.required' => 'The mobile number field is required.',
                'profile.mimes' => 'The Image field is only support jpg, jpeg, png file type only.'
            ]);
        } else {

            $validateData = request()->validate(
                [
                    'name' => 'required',
                    'country_code' => 'required',
                    'email' => [
                        'required', 'regex:/(.+)@(.+)\.(.+)/i', 'email',
                        Rule::unique('users')->where(function ($query) use ($id) {
                            return $query->where('status', '!=', '2');
                        })
                    ],
                    'phone' => [
                        'numeric', Rule::unique('users')->where(function ($query) use ($id) {
                            return $query->where('status', '!=', '2');
                        })
                    ],

               'profile' => 'image|mimes:jpg,jpeg,png|max:2048'
            ], [
                'phone.required' => 'The mobile number field is required.',
                'profile.mimes' => 'The Image field is only support jpg, jpeg, png file type only.'
            ]);
        }

        return $validateData;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update all selected resources in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param buttonNames , array $ids[]
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {


        //
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $id_multiple = explode(",", $request->ids);

                $status = $request->status;
                $is_popular_insructor = $request->is_popular_insructor;

                
                if ($status == 2) {
                    MainUser::wherein('id', $ids)->update(['status' => 2]);
                    return response()->json(['success' => true, 'msg' => 'Record(s) delete successfully']);
                } else if ($status == 4) {
                    MainUser::wherein('id', $ids)->update(['status' => 4]);
                    return response()->json(['success' => true, 'msg' => 'Record(s) deactive successfully']);
                }  else if ($is_popular_insructor == 1) {
                    MainUser::wherein('id', $ids)->update(['is_popular_insructor' => 1]);
                    return response()->json(['success' => true, 'msg' => 'Record(s) create successfully']);
                }else if ($is_popular_insructor == 0) {
                    MainUser::wherein('id', $ids)->update(['is_popular_insructor' => 0]);
                    return response()->json(['success' => true, 'msg' => 'Record(s) remove successfully']);
                }else {
                    MainUser::wherein('id', $ids)->update(['status' => 3]);
                    return response()->json(['success' => true, 'msg' => 'Record(s) active successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
    }
    public function userexport(Request $request)
    {

        if (!empty($request->startdate && $request->enddate)) {

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');


            $totalAr = \DB::table('users')
                ->where('users.status', '!=', 2);
            $totalAr = $totalAr->whereDate('users.createddate', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('users.createddate', '<=', $to_date);
            $totalAr = $totalAr->orderBy('users.id', 'DESC')->groupby('users.id')->get();
        } else {
            $totalAr = \DB::table('users')
                ->where('users.status', '!=', 2);
            $totalAr = $totalAr->orderBy('users.id', 'DESC')->groupby('users.id')->get();
        }



        $filename = 'user_report' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No', 'Full Name', 'Email', 'Mobile Number', 'Created Date', 'Status', 'Verify Status'));
        $i = 1;
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $fullname = isset($data->name) ? $data->name : '';
            $mobile_number = isset($data->phone) ? $data->phone : '';
            $email = isset($data->email) ? $data->email : '';

            if ($data->status == 1) {
                $status = 'active';
            } else {

                $status = 'deactive';
            }

            if ($data->is_varify == 1) {
                $status2 = 'varify';
            } else {

                $status2 = 'not varify';
            }


            $createddate = isset($data->createddate) ? $data->createddate : '';
            $date = \Helper::formatDate($createddate) . ' ' . date('H:i:s', strtotime($createddate));
            fputcsv($file, array($i, $fullname, $email, $mobile_number, $date, $status, $status2));

            $i++;
        }
    }

    // public function anydataPost(Request $request)
    // {

    //     $draw = $request->get('draw');

    //     $start = $request->get("start");

    //     $rowperpage = $request->get("length");
    //     $columnIndex_arr = $request->get('order');
    //     $columnName_arr = $request->get('columns');
    //     $order_arr = $request->get('order');
    //     $search_arr = $request->get('search');

    //     //echo "<pre>";print_r($order_arr);exit;
    //     $columnIndex = $columnIndex_arr[0]['column']; // Column index
    //     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
    //     $columnSortOrder = '';
    //     if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
    //         $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    //     }
    //     $searchValue = $search_arr['value']; // Search value
    //     if ($columnIndex == 0) {
    //         $sort = 'posts.id';
    //     } elseif ($columnIndex == 1) {
    //         $sort = 'posts.title';
    //     } elseif ($columnIndex == 2) {
    //         $sort = 'posts.created_at';
    //     } else {
    //         $sort = 'posts.id';
    //     }

    //     $sortBy = 'DESC';
    //     if ($columnSortOrder != "") {
    //         $sortBy = $columnSortOrder;
    //     }

    //     $user_id = $value = \Session::get('uid');        ;
    //     $totalAr = \DB::table('posts')->select('id', 'title', 'file' ,'created_at','status')
    //     ->where('user_id', $user_id)->where('status', '!=', 2);

    //     if ($searchValue != "") {

    //     $totalAr = $totalAr->where(function ($query) use ($searchValue) {
    //             $query->orWhere('title', 'like', '%' . $searchValue . '%')
    //                 ->orWhere('created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
    //         });
    //     }

    //     $totalRecords = $totalAr->groupby('id')->get()->count();

    //     $totalAr = $totalAr->orderBy($sort, $sortBy)
    //         ->skip($start)
    //         ->take($rowperpage)
    //         ->groupby('id')
    //         ->get();
       
    //     /* print_r($totalAr);
    //     exit;*/
    //     $data_arr = [];
    //     $utype = '';
    //     foreach ($totalAr as $key => $data) {
            
    //         $title = isset($data->title) ? $data->title : '';

    //         $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
    //         if ($data->status == 1) {
    //             $status = '<i class="fa fa-check text-success inline " style="margin-left: 15px;"><span class="hide">active</span></i>';
    //         } else {

    //             $status = '<i class="fa fa-times text-danger inline " style="margin-left: 15px;"><span class="hide">deactive</span></i>';
    //         }


    //         $postShow =  route('postShow', ['id' => $data->id]);

    //         $postDelete =  route('post.delete',['id'=>$data->id]);

    //         $options = '<a  class="btn btn-sm show-eyes list" href="' . $postShow . '" title="View"> </a>';

    //         $options .=  '<button  class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
           
    //         $data_arr[] = array(
    //             "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
    //             "title" =>   $title,
    //             "createddate" =>   $createddate,
    //             "status" => $status,
    //             "options" => $options
    //         );
    //     }

    //     $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecords,
    //         "aaData" => $data_arr
    //     );

    //     echo json_encode($response);
    // }


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
            $sort = 'user_type';
        } elseif ($columnIndex == 2) {
            $sort = 'name';
        } elseif ($columnIndex == 3) {
            $sort = 'email';
        } elseif ($columnIndex == 4) {
            $sort = 'phone';
        } elseif ($columnIndex == 5) {
            $sort = 'is_popular_insructor';
        } elseif ($columnIndex == 6) {
            $sort = 'created_at';
        } elseif ($columnIndex == 7) {
            $sort = 'status';
        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $totalAr = \DB::table('main_users')->select('id', 'user_type',
        'name', 'email', 'phone', 'profile', 'country_code','is_popular_insructor','status', 'created_at')
        ->where('status', '!=', 2);

        if (isset($start_date)) {
            $min_date = Carbon::parse($start_date)->format('Y-m-d H:i:s');
            $totalAr->where('created_at', '>=', $min_date);
        }

        if (isset($end_date)) {
            $min_date = Carbon::parse($end_date)->format('Y-m-d');
            $totalAr->where('created_at', '<=', $min_date . ' 23:59:59');
        }
               

        if ($searchValue != "") {

        $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $search = "";
                $search1 = "";
                if($searchValue == 'Normal' || $searchValue == 'normal')
                {
                    $search = '2';
                    $query->orWhere('user_type', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                }
                else if($searchValue == 'Instructor' || $searchValue == 'instructor')
                {
                    $search = '3';
                    $query->orWhere('user_type', 'like', '%' . $search . '%')
                        ->orWhere('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                }
                else if($searchValue == 'Yes' || $searchValue == 'yes')
                {
                    $search1 = '1';
                    $query->orWhere('user_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('is_popular_insructor', 'like', '%' . $search1 . '%')
                        ->orWhere('created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                }
                else if($searchValue == 'No' || $searchValue == 'no')
                {
                    $search1 = '0';
                    $query->orWhere('user_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('is_popular_insructor', 'like', '%' . $search1 . '%')
                        ->orWhere('created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                }
                else
                {
                    $query->orWhere('user_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('name', 'like', '%' . $searchValue . '%')
                        ->orWhere('email', 'like', '%' . $searchValue . '%')
                        ->orWhere('phone', 'like', '%' . $searchValue . '%')
                        ->orWhere('is_popular_insructor', 'like', '%' . $searchValue . '%')
                        ->orWhere('created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                }
            });
        }

        $totalRecords = $totalAr->groupby('id')->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        
        /* print_r($totalAr);
        exit;*/
        $data_arr = [];
        $utype = '';
        $pi = '';
        foreach ($totalAr as $key => $data) {
            $user_type = isset($data->user_type) ? $data->user_type : '';
            $fullname = isset($data->name) ? $data->name : '';
            $country_code = isset($data->country_code) ? $data->country_code : '';
            $mobile_number = isset($data->phone) ? $data->phone : '';
            $email = isset($data->email) ? $data->email : '';
            $popular_instructor = isset($data->is_popular_insructor) ? $data->is_popular_insructor : '';
            if (isset($data->countrycode)) {
                $code = $data->countrycode;
            }

            if($user_type == 2)
            {
                $utype = 'Normal';
            }
            elseif($user_type == 3)
            {
                $utype = 'Instructor';
            }
            else
            {
                $utype = 'Admin';
            }

            if($popular_instructor == 1)
            {
                $pi = 'Yes';
            }
            else
            {
                $pi = 'No';
            }

            $createddate = isset($data->created_at) ? date('m-d-Y H:i:s', strtotime($data->created_at)) : '';
            if ($data->status == 3) {
                $status = '<i class="fa fa-check text-success inline " style="margin-left: 15px;"><span class="hide">active</span></i>';
            } elseif($data->status == 4) {

                $status = '<i class="fa fa-times text-danger inline " style="margin-left: 15px;"><span class="hide">deactive</span></i>';
            }


            $usersShow =  route('usersShow', ['id' => $data->id]);
            $usersEdit =  route('userEdit', ['id' => $data->id]);
            
            $postList =  route('postList', ['id' => $data->id]);

            $options = '<a  class="btn btn-sm show-eyes list" href="' . $usersShow . '" title="View"> </a>';

            $options .= '<a  class="btn btn-sm success paddingset" href="' . $usersEdit . '" title="Edit" style="margin-left: -5px;"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button  class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            $options .=  '<a  class="btn btn-sm success success paddingset" title="Post" href="' . $postList . '" style="margin-left: 3px;"> <small><i class="fa fa-clipboard" aria-hidden="true"></i> </small> </a>';

            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "user_type" =>   $utype,
                "fullname" =>   $fullname,
                "email" => $email,
                "mobile_number" => '+'.$country_code.' '.$mobile_number,
                "popular_instructor" => $pi,
                "status" => $status,
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



    public function attachment_email_register($email, $name, $otp, $logo)
    {


        $setting = Setting::find(1);
        $getEmail = EmailTemplate::where('id', 1)->first();
        $from_email = $setting['from_email'];
        $data = array('email' => $email, 'name' => $name, 'otp' => $otp, 'id' => '1', 'logo' => $logo, 'from_email' => $from_email);

        Mail::send('user_register', $data, function ($message) use ($data) {

            $message->to($data['email'], 'Promoteprep')->subject('User registration successfully');

            $message->from($data['from_email'], 'Promoteprep');
        });
    }

    public function generateToken()
    {
        return md5(rand(1, 10) . microtime());
    }
}
