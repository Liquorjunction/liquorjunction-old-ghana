<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DanceClass;
use App\Models\MainUser;
use Illuminate\Validation\Rule;
use Auth;
use App\Models\ClassPurchaseHistory;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Mail;
use Carbon\Carbon;
use Storage;
use File;
use RealRashid\SweetAlert\Facades\Alert;

class UsersController extends Controller
{

    public $setting;
    public function __construct()
    {
        $this->setting = Setting::find(1);
    }

    public function instructor_index()
    {
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';

        $users = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.profile','class.user_id', 'class.dance_category_id','main_users.about_me', \DB::raw('group_concat(dance_category.category_name) as category_name'))->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '=', 3)->where('main_users.user_type', '=', 3)->orderby('main_users.id', 'desc')->groupby('main_users.id')->paginate($pagination_limit);

        $setting = $this->setting;

        return view("frontEnd.users.instructor-list", compact('users', 'setting'));
    }

    public function instructor_profile($id)
    {
       // $id = decrypt($id);
        $id = base64_decode($id);
        $setting = $this->setting;

        $users = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.profile','class.user_id', 'main_users.about_me', 'class.dance_category_id','class.dance_level','level.title AS dance_level_title','class.discount','class.duration','class.class_thumbnail_image','class.favourite', 'class.price', 'class.class_name', 'class.instruction_video', 'class.class_description', 'main_users.instructor_facebook_link','main_users.instructor_instagram_link','main_users.instructor_web_link','main_users.created_at','main_users.about_me', \DB::raw('group_concat(dance_category.category_name) as category_name'))->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('level', 'level.id', '=', 'class.dance_level')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '=', 3)->where('main_users.user_type', '=', 3)->where('class.status',3)->groupby('main_users.id')->where('main_users.id', $id)->get();


        // $user_dance_class = \DB::table('main_users')->select('main_users.id','main_users.name', 'main_users.profile', 'main_users.about_me', 'class.dance_category_id','class.dance_level','class.duration','class.class_thumbnail_image','class.favourite', 'class.price', 'class.class_name', 'class.instruction_video', 'class.class_description', 'main_users.instructor_facebook_link','main_users.instructor_instagram_link','main_users.instructor_web_link','main_users.created_at','main_users.about_me', \DB::raw('group_concat(dance_category.category_name) as category_name'))->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '=', 3)->where('main_users.user_type', '=', 3)->groupby('main_users.id')->where('main_users.id', $id)->get();

        $user_dance_class = DanceClass::select('class.*','level.title AS dance_level_title','dance_category.*','class.id AS class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'desc')->where('class.status',3)->where('class.user_id', $id)->get();

        //dd($user_dance_class);
        

      //  $users = MainUser::where('id', $id)->get();

        $post = \DB::table('posts')->where('user_id', $id)->get();


        return view("frontEnd.users.instructor-detail", compact('users','user_dance_class', 'setting','post'));
    }

    public function imageFileUpload(Request $request)
    {
        $data = request()->validate([
            'file' => 'image|mimes:jpeg,jpg,png'
        ]);

        $formFileName = "file";
        $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/website_users/post/images/";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);
         }

          $data = array(
          array('user_id'=> auth()->guard('main_user')->user()->id, 'file'=> $fileFinalName_ar, 'video_file'=> '', 'title'=> '', 'description'=> '', 'status'=> 1),
        );

        $query_insert = \DB::table('posts')->insert($data);

        return response()->json($fileFinalName_ar);
    }

    public function videoFileUpload(Request $request)
    {
        $data = request()->validate([
            'file' => 'mimes:mp4'
        ]);

        $formFileName = "file";
        $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/website_users/post/videos/";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

         }

        $data = array(
          array('user_id'=> auth()->guard('main_user')->user()->id, 'file'=> '', 'video_file'=> $fileFinalName_ar, 'title'=> '', 'description'=> '', 'status'=> 1),
        );

        $query_insert = \DB::table('posts')->insert($data);

        return response()->json($fileFinalName_ar);
    }

    public function becomeAnInstructor()
    {
        if (auth()->guard('main_user')->check()) {
            $setting = $this->setting;
            
            if(auth()->guard('main_user')->user()->user_type == 2)
            {
                $users = \DB::table('main_users')->where('main_users.id',auth()->guard('main_user')->user()->id)->first();
                //dd($users->name);
            }
            else
            {
                $users = \DB::table('main_users')->select('main_users.*','dance_category.category_name','level.title AS dance_level_title','class.dance_level','class.duration')->join('class_purchase_history', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->where('main_users.status', '=', 3)->where('main_users.user_type', '=', 3)->where('main_users.id',auth()->guard('main_user')->user()->id)->first();
                 //dd($users);
            }


            return view("frontEnd.users.become-an-instructor", compact('users', 'setting'));
        } else {
            return redirect()->route('frontend.home');
        }
    }

    // public function editBecomeAnInstructor(Request $request)
    // {
    //    //dd($request->all());
    //    //dd(request()->file('profile'));
    //     $category_dance_value=$request->category_dance_instructor;
    //     //dd($category_dance_value);
    //     $request->validate(
    //         [
    //             'name'                           =>     'required|string|max:20',
    //             //'profile'                        =>     'image|mimes:jpg,png,jpeg|max:4096',
    //             'about_me'                       =>     'required',               
    //             'instructor_location'            =>     'required',
    //             // 'category_dance_instructor'      =>     'required|in:Male,Female,male,female',
    //             'category_dance_instructor'      =>     'required|not_in:0',
    //             'instructor_since'               =>     'required',                
    //             'dance_group_name'               =>     'required',
    //             'instructor_facebook_link'       =>     'required|url',
    //             'instructor_web_link'            =>     'required|url',
    //             'instructor_instagram_link'      =>     'required|url',
    //             'instructor_portfolio_image.*'   =>     'image|mimes:jpg,jpeg,png|max:4096',
    //             'instructor_portfolio_video'     =>     'mimes:mp4|max:4096'
                
    //         ], [
    //             'name.required' => 'The Name field is required.',
    //             'about_me.required' => 'The About me field is required.',
    //             'instructor_location.required' => 'The Location/Address field is required.',
    //             'category_dance_instructor.not_in' => 'The Category of dance instructor field is required.',
    //             'instructor_since.required' => 'The Instructor since field is required.',
    //             'dance_group_name.required' => 'The Affiliated Groups field is required.',
    //             'instructor_facebook_link.required' => 'The Facebook Profile Link field is required.',
    //             'instructor_web_link.required' => 'The Youtube Profile Link field is required.',
    //             'instructor_instagram_link.required' => 'The Instagram Profile Link field is required.',
    //             'instructor_portfolio_image.required' => 'Please enter Portfolio image.',
    //             'instructor_portfolio_video.required' => 'Please enter Portfolio video.'

    //     ]);
    //     ///////////////portfolio image//////////////
    //      $files = [];
    //      $name = "";
          
    //     if($request->hasfile('instructor_portfolio_image'))
    //      {
    //         foreach($request->file('instructor_portfolio_image') as $file)
    //         {
    //             $name = time().rand(1,100).'.'.$file->extension();
    //             $uploadPath = "/uploads/website_users/portfolio_image";
    //             $path = public_path() . $uploadPath;
    //             $file->move($path, $name);  
    //             $files[] = $name;  
    //         }
    //      }
  
    //      $file= new File();
    //      $file->instructor_portfolio_image = $files;
    //      $portfolio_image = implode(",",$files);

    //      ///////////////portfolio video//////////////



    //     //  $files1 = [];
    //     //   $name1 = "";
          
    //     // if($request->hasfile('instructor_portfolio_video'))
    //     //  {
    //     //     dd($request->file('instructor_portfolio_video'));
    //     //     foreach($request->file('instructor_portfolio_video') as $file1)
    //     //     {
    //     //         dd("yes 1");
    //     //         $name1 = time().rand(1,100).'.'.$file1->extension();
    //     //         $uploadPath = "/uploads/website_users/portfolio_video";
    //     //         $path = public_path() . $uploadPath;
    //     //         $file1->move($path, $name1);  
    //     //         $files1[] = $name1;  
    //     //     }
    //     //  }
  
    //     //  $file1= new File();
    //     //  $file1->instructor_portfolio_video = $files1;
    //     //  $portfolio_video = implode(",",$files1);


    //     $user = MainUser::find(auth()->guard('main_user')->user()->id);

    //     if($category_dance_value == 1){
    //        $category_dance_instructor = 1;
    //     }
    //     else{
    //         $category_dance_instructor = 2;
    //     }

    //     $user->name = isset($request->name) ? $request->name : '';        
    //     $user->about_me = isset($request->about_me) ? $request->about_me : '';
    //     $user->instructor_location = isset($request->instructor_location) ? $request->instructor_location : '';
    //     $user->category_dance_instructor = isset($category_dance_instructor) ? $category_dance_instructor: '';
    //     $user->instructor_since = isset($request->instructor_since) ? $request->instructor_since : '';       
    //     $user->dance_group_name = isset($request->dance_group_name) ? $request->dance_group_name : '';
    //     $user->instructor_facebook_link = isset($request->instructor_facebook_link) ? $request->instructor_facebook_link : '';
    //     $user->instructor_web_link = isset($request->instructor_web_link) ? $request->instructor_web_link : '';
    //     $user->instructor_instagram_link = isset($request->instructor_instagram_link) ? $request->instructor_instagram_link : '';
    //     $user->instructor_portfolio_image = isset($portfolio_image) ? $portfolio_image : '';
    //     $user->is_verify_instructor = 1;
    //     // $user->instructor_portfolio_video = isset($portfolio_video) ? $portfolio_video : '';

    //      //dd($user->category_dance_instructor);
           
    //     $user->user_type = 2;
    //     $user->status = 3;

    //    // dd($user);
    //     $user->save();

    //     $this->storeProfileImage($user);
    //     //$this->storeportfolioImage($user);
    //     $this->storeportfolioVideo($user);  
    //     Alert::success('Success', 'Your Profile Updated Successfully.');
    //     return back();      
    //     // return redirect()->route('become-an-instructor')->with("success", "Your Profile Updated Successfully.");
    // }

    private function storeProfileImage($user)
    {

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

    // public function myEarning()
    // {
    //     if (auth()->guard('main_user')->check()) {

    //         $setting = $this->setting;
    //         $earning_history = \DB::table('withdraw_history')
    //                     ->join('main_users','main_users.id','=','withdraw_history.instructor_id')
    //                     ->select('withdraw_history.*','main_users.name')
    //                     ->where('main_users.user_type','=',3)
    //                     ->where('main_users.status', '=', 3)
    //                     ->where('withdraw_history.instructor_id', auth('main_user')->user()->id)->groupBy('withdraw_history.instructor_id')->get();
                       

    //         $total_earning = \DB::table('withdraw_history')
    //                     ->join('main_users','main_users.id','=','withdraw_history.instructor_id')
    //                     ->join('class_purchase_history','class_purchase_history.user_id','=','withdraw_history.instructor_id')
    //                     ->select('withdraw_history.*','main_users.name','class_purchase_history.*')
    //                     ->where('main_users.user_type','=',3)
    //                     ->where('main_users.status', '=', 3)
    //                     ->where('class_purchase_history.user_id', auth('main_user')->user()->id)->groupBy('class_purchase_history.user_id')->sum('class_purchase_history.instructor_amount');

    //         $purchase_history = \DB::table('class_purchase_history')->select('class.class_name','class_purchase_history.instructor_amount','class_purchase_history.total_amount','class_purchase_history.created_at AS purchase_date', \DB::raw('COUNT(class_purchase_history.user_id) as count_user_id'))->join('main_users', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->orderby('class_purchase_history.id', 'DESC')->groupby('class_purchase_history.user_id', 'class_purchase_history.class_id')->where('class_purchase_history.user_id', auth('main_user')->user()->id)->get();

    //         $withdraw_history = \DB::table('withdraw_history')->orderby('withdraw_history.id', 'DESC')->where('withdraw_history.instructor_id', auth('main_user')->user()->id)->get();  
                                
    //         return view("frontEnd.users.earning-history", compact('setting','earning_history','total_earning','purchase_history','withdraw_history'));
    //     } else {
    //         return redirect()->route('frontend.home');
    //     }
    // }

    public function myEarningDetail()
    {

        return view("frontEnd.users.earning-history-detail");
    }

    public function myEarningDetailAnyData(Request $request)
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
            $sort = 'class_purchase_history.id';
        } elseif ($columnIndex == 1) {
            $sort = 'class_purchase_history.created_at';
        } elseif ($columnIndex == 2) {
            $sort = 'class.class_name';
        } elseif ($columnIndex == 3) {
            $sort = 'class_purchase_history.instructor_amount';
        } elseif ($columnIndex == 4) {
            $sort = 'class_purchase_history.total_amount';
        } else {
            $sort = 'class_purchase_history.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }


            $totalAr = \DB::table('class_purchase_history')->select('class.class_name','class_purchase_history.instructor_amount','class.id AS class_id','class_purchase_history.total_amount','class_purchase_history.created_at AS purchase_date', \DB::raw('COUNT(class_purchase_history.user_id) as count_user_id'))->join('main_users', 'class_purchase_history.user_id', '=', 'main_users.id')->join('class', 'class.id', '=', 'class_purchase_history.class_id')->orderby('class_purchase_history.id', 'DESC')->groupby('class_purchase_history.user_id', 'class_purchase_history.class_id')->where('class_purchase_history.user_id', auth('main_user')->user()->id);

            if ($searchValue != "") {

                    $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                        $query->orWhere('class.class_name', 'like', '%' . $searchValue . '%')
                            ->orWhere('class_purchase_history.total_amount', 'like', '%' . $searchValue . '%')
                             ->orWhere('class_purchase_history.instructor_amount', 'like', '%' . $searchValue . '%')
                            ->orWhere('class_purchase_history.created_at', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                    });
                
            }


            $totalRecords = $totalAr->groupby('class_purchase_history.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('class_purchase_history.id')
                ->get();
        

        /* print_r($totalAr);
        exit;*/
        $data_arr = [];
        $dl = '';
        $ps = '';
        $pc = '';
        $ctname = '';
    
        foreach ($totalAr as $key => $data) {

           
            $i = 1;

            $class_name = isset($data->class_id) ? $data->class_id : '';
            
            $total_amount = isset($data->total_amount) ? $data->total_amount : '0.0';

            $cl_name = DanceClass::where('id',$class_name)->first();
            $cname = isset($cl_name->class_name) ? $cl_name->class_name : '';

            $purchase_count = isset($data->count_user_id) ? $data->count_user_id : '0';
            $instructor_amount = isset($data->instructor_amount) ? $data->instructor_amount : '0.0';
           
            $setting = Setting::find(1);
            $currency = $setting->currency_symbol;

            $commission = $setting->commission_in_per;

            $newDate = date("d/m/Y", strtotime($data->purchase_date));
          
            $data_arr[] = array(
                "no" => $i,
                "date" => $newDate,
                "class_name" => $cname,
                "purchase_count" => $purchase_count,
                "instructor_amount" => $currency.' '.$instructor_amount.'.00',
                "total_amount" => $currency.' '.$total_amount.'.00',
                "commission" => $commission.'%',
            );

            $i++;
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        );

        echo json_encode($response);

    }

    public function websiteUserProfile()
    {
        $id = auth()->guard('main_user')->user()->id;
        if (auth()->guard('main_user')->check()) {

            $users = \DB::table('main_users')->where('main_users.status', '=', 3)->where('main_users.user_type', '=', 2)->where('main_users.id',$id)->first();
            $setting = $this->setting;

            return view("frontEnd.users.user-edit-profile", compact('setting','users', 'id'));
        } else {
            return redirect()->route('frontend.home');
        }
    }

    public function websiteUserProfileEdit(Request $request, $id)
    {
        
      //dd($request->all());
        $request->validate(
            [
                'about_me'          =>      'required',               
                'name'              =>      'required|string|max:20',
                'email' => [
                        'required',
                        'email',
                        'regex:/(.+)@(.+)\.(.+)/i',
                         Rule::unique('main_users')->ignore($id)->where(function ($query) use ($id) {
                            return $query->where('status', '!=', '2');
                        })
                    ],
                'phone' => [
                        'required',
                        'numeric',
                         Rule::unique('main_users')->ignore($id)->where(function ($query) use ($id) {
                            return $query->where('status', '!=', '2');
                        })
                    ],    	
                
            ], [
                'email.unique' => 'The email field already exist.',
                'phone.unique' => 'The phone field already exist.'

        ]);
       
        $user = MainUser::find($id);
        
        $user->about_me = isset($request->about_me) ? $request->about_me : '';
        $user->name = isset($request->name) ? $request->name : '';
        $user->email = isset($request->email) ? $request->email : '';
        $user->phone = isset($request->phone) ? $request->phone : '';
        $user->status = 3;

        $user->save();

        Alert::success(\Helper::language('success'), \Helper::language('profile_updated_successfully'));
        return back();      
        // return redirect()->route('become-an-instructor')->with("success", "Your Profile Updated Successfully.");
    }

    public function changePassword(Request $request)
    {

		if (auth()->guard('main_user')->check()) {

            $setting = $this->setting;

            return view("frontEnd.auth.change-password", compact('setting'));
        } else {
            return redirect()->route('frontend.home');
        }
    }


    //update password
    public function updatePassword(Request $request)
    {
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirmation');
        $validatedData = $request->validate(
            [
                'current_password' => 'required',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required',
            ],

            [
                'password.confirmed' => 'The new-password and confirm new-password field does not match.',
                'password.required' => 'The new password field is required.',
                'password_confirmation.required' => 'The confirm new password field is required.',
                'password.min' => 'The password must be 6 alphanumeric characters in length'

            ]
        );

        if (!(Hash::check($request->get('current_password'), auth()->guard('main_user')->user()->password))) {
            // The passwords matches
            Alert::error('Failed', 'Your current password does not match. Please try again.');
            return back();
           // return redirect()->back()->with("errorMessage", "Your current password does not match. Please try again.");
        }
        if (strcmp($request->get('current_password'), $request->get('password')) == 0) {
            //Current password and new password are same
            Alert::error('Failed', 'New Password cannot be same as your current password. Please choose a different password.');
            return back();
           // return redirect()->back()->with("errorMessage", "New Password cannot be same as your current password. Please choose a different password.");
        }
        if ($password != $password_confirm) {
            Alert::error('Failed', 'Password do not match with comfirm password.');
            return back();
            //return redirect()->back()->with("errorMessage", "Password do not match with comfirm password.");
        }
        //Change Password
        $user = auth()->guard('main_user')->user();
       // dd($user);
        $user->password = bcrypt($request->get('password'));
        \DB::table('main_users')->where('id', auth()->guard('main_user')->user()->id)->update(array('password' => $user->password));
       // $user->save();
        Alert::success(\Helper::language('success'), \Helper::language('change_password_successfully'));
        return back();
       // return redirect()->back()->with("doneMessage", "Password changed successfully !");
    }

    private function storeportfolioImage($user)
    {

        $formFileName = "instructor_portfolio_image";
        $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/website_users/portfolio_image";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

             $user->update([
                'instructor_portfolio_image' => $fileFinalName_ar,
            ]);
         }
    }

    private function storeportfolioVideo($user)
    {

        $formFileName = "instructor_portfolio_video";
        $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/website_users/portfolio_video";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

             $user->update([
                'instructor_portfolio_video' => $fileFinalName_ar,
            ]);
         }
    }


}