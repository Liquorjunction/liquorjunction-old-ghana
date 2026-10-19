<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use App\Models\MembershipPlanSubscription;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\MainUser;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Validator;
use Session;
use DB;
use Mail;
use Cookie;
use Alert;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    // protected $redirectTo = '/admin/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest')->except('logout');
    }

    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['status' => 1]);
    }

    public function showMainuserLoginForm()
    {
        // echo "string";exit();
        if (auth()->guard('web')->check()) {
            return redirect()->route('adminHome');
        } else {
            return view('auth.login');
        }
    }

    public function showWholesalerLoginForm()
    {
        // echo "string";exit();
        //  return redirect()->route('adminHome');
        if (auth()->guard('main_user')->check()) {
            return redirect()->route('adminwholesalerHome');
        } else {
            // echo "string1";exit();
            return view('auth.wholesalerlogin');
        }
    }


    // public function adminLogin(Request $request)
    // {
    //     \Validator::extend('without_spaces', function($attr, $value){

    //             return preg_match('/^\S*$/u', $value);

    //     });


    //    $validate = $this->validate($request, [
    //                 'email' => 'required|email|exists:users,email',
    //                 'password' => 'required|min:6',
    //             ],
    //             [
    //                 'email.exists' => 'These credentials do not match our records.',
    //                 'email.without_spaces' => 'Whitespace not allowed.'
    //             ]
    //         );

    //     $remember_me = $request->has('remember_me') ? true : false; 

    //     if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password')]))
    //     {      
    //         if($remember_me == true)  
    //         {
    //             $minutes = 120;

    //             Cookie::queue('admin_email',  $request->input('email'), $minutes);
    //             Cookie::queue('admin_password', $request->input('password'), $minutes);

    //         }
    //         else
    //         {
    //             \Cookie::queue(\Cookie::forget('admin_email'));
    //             \Cookie::queue(\Cookie::forget('admin_password'));

    //         }
    //            /*$user = User::where(["email" => $request->input('email')])->first();

    //         Auth::login($user, $remember_me);*/

    //          $route = route('adminHome');
    //         return response()->json(['success' => true,'route'=> $route]);
    //     }

    //     else {

    //        // return back()->withInput($request->input())->with('error','Your email or password is invalid.');
    //         return response()->json(['success' => false]);
    //     }
    // }

    public function adminLogin(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email|exists:users,email',
                'password' => 'required|min:6',
            ],
            [
                'email.exists' => 'These credentials does not match our records.',
                'email.regex' =>'Please enter valid email address.',
            ]
        );
        $remember_me = $request->has('remember_me') ? true : false;

        $user_data = User::where(["email" => $request->input('email')])->first();
        // dd($user_data->usrt);

        if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'status' => 1], $remember_me)) {
            if ($remember_me == true) {
                // echo "string";exit();
                $minutes = 120;

                Cookie::queue('admin_email',  $request->input('email'), $minutes);
                Cookie::queue('admin_password', $request->input('password'), $minutes);
            } else {
                \Cookie::queue(\Cookie::forget('admin_email'));
                \Cookie::queue(\Cookie::forget('admin_password'));
            }
            $user = User::where(["email" => $request->input('email')])->where('status', 1)->first();
            // echo "<pre>";print_r($user->toArray());exit();
            // echo "Cookie<pre>";print_r(Cookie());exit();
            if (!empty($user)) {
                $opts = array(
                    'http' => array(
                        'method' => "GET",
                        'timeout' => 1,
                        'ignore_errors' => true
                    )
                );
                $context = stream_context_create($opts);
                $ipInfo = @file_get_contents('http://ip-api.com/json/' . $ip, false, $context);
                if (@$ipInfo->timezone) {
                    Session::put('TimeZoneSession', $ipInfo->timezone);
                    // $timezone_session = $ipInfo->timezone;
                }
                Auth::login($user, $remember_me);
                return redirect(route('adminHome'));
            } else {
                // echo "string2";exit();
                return back()->withInput($request->input())->with('error', 'Your email or password is invalid.');
            }
        } else if (isset($user_data) && $user_data->status == 0) {
            if($user_data->user_type == 2){
                return back()->withInput($request->input())->with('error', 'Your account is inactive, kindly contact to super administrator.');
            }else{
                return back()->withInput($request->input())->with('error', 'Your account is inactive, kindly contact to country administrator.');

            }
        } else {

            return back()->withInput($request->input())->with('error', 'Your email or password is invalid.');
        }
    }

    public function adminWholesalerLogin(Request $request)
    {
        $this->validate(
            $request,
            [
                'email' => 'required|email|exists:main_users,email',
                'password' => 'required|min:6',
            ],
            [
                'email.exists' => 'These credentials does not match our records.',
                'email.email' => 'Please enter valid email address.',
            ]
        );
        $remember_me = $request->has('remember_me') ? true : false;
        // dd(auth()->guard());

        if (auth()->guard('main_user')->attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'status' => 1, 'user_type' => 2], $remember_me)) {
            if ($remember_me == true) {
                // echo "string";exit();
                $minutes = 120;

                Cookie::queue('wholesaler_email',  $request->input('email'), $minutes);
                Cookie::queue('wholesaler_password', $request->input('password'), $minutes);
            } else {
                \Cookie::queue(\Cookie::forget('wholesaler_email'));
                \Cookie::queue(\Cookie::forget('wholesaler_password'));
            }
            $user = MainUser::where(["email" => $request->input('email')])->where('status', 1)->first();
            // echo "<pre>";print_r($user->toArray());exit();
            // echo "Cookie<pre>";print_r(Cookie());exit();
            if (!empty($user)) {
                // echo "string1212";exit();
                // auth()->guard('wholesaler')->login($user, $remember_me);
                // auth()->guard('wholesaler')->login($user, $remember_me);
                // Auth::login($user, $remember_me);
                Auth::guard('main_user')->login($user, $remember_me);
                return redirect(route('adminwholesalerHome'));
            } else {
                // echo "string2";exit();
                return back()->withInput($request->input())->with('error', 'Your email or password is invalid.');
            }
        } else {

            return back()->withInput($request->input())->with('error', 'Your email or password is invalid.');
        }
    }

    public function adminWholesalerRegister(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit;
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirmation');
        $this->validate(
            $request,
            [
                // 'email' => 'required|email',
                'email' => ['required', 'email', Rule::unique('main_users')->where(function ($query) {
                    return $query->where('status', '!=', '2')->where('user_type', '2');
                })],
                'first_name' => 'required|max:80|regex:/^[a-zA-Z]+$/u',
                'store_name' => 'required|max:80',
                'store_description' => 'required|max:250',
                'last_name' => 'required|max:80|regex:/^[a-zA-Z]+$/u',
                'phone' => 'required',
                'abn_number' => 'required',
                'profile_picture' => 'required',
                'street_address' => 'required',
                'state' => 'required',
                'city' => 'required',
                'country' => 'required',
                'post_code' => 'required',
                // 'email' => 'required|email',
                'password' => 'required',
                'password_confirmation' => 'required',
            ],
            [
                'email.exists' => 'These credentials does not match our records.',
                'password_confirmation.required' => 'The confirm password field is required.',
                'profile_picture.required' => 'The Store logo field is required.',
                'store_name.required' => 'The Trading name field is required.',
                'post_code.required' => 'The Zip code field is required.',
            ]
        );



        if ($password != $password_confirm) {
            // return redirect()->back()->with("errorMessageConformPassword", "New password and confirm password doesn't match.");
            return redirect()->back()->withInput($request->input())->with("errorMessageConformPassword", "New password and confirm password doesn't match.");
        }

        $wholesaler_data = DB::table('wholesaler_invite_link')->where('email', '=', $request->email)->first();

        // echo "<pre>";print_r($wholesaler_data);
        if (!empty($wholesaler_data)) {
            // echo "string";exit;
            $register = new MainUser();

            $formFileName = "profile_picture";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(
                    1111,
                    9999
                ) . '.' . $request->file($formFileName)->getClientOriginalExtension();


                $uploadPath = public_path() . "/uploads/customer/";


                //$path = $this->getUploadPath();

                $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
            }

            // $password = Str::random(10);

            $register->name = @$request->first_name . ' ' . @$request->last_name;
            $register->first_name = @$request->first_name;
            $register->last_name = @$request->last_name;
            $register->email = @$request->email;
            $register->phone = @$request->phone;
            $register->abn_number = @$request->abn_number;
            $register->profile = $fileFinalName_ar;
            $register->store_name = @$request->store_name;
            $register->store_description = @$request->store_description;
            $register->street_address = @$request->street_address;
            $register->states = @$request->state;
            $register->country = @$request->country;
            $register->city = @$request->city;
            $register->post_code = @$request->post_code;
            $register->password = bcrypt($request->get('password'));
            $register->user_type = 2;
            $register->status = 1;

            $register->save();

            $fullname = @$request->first_name . ' ' . @$request->last_name;

            $ismail = $this->send_email_register_wholesaler($request->email, $password, $fullname);


            return redirect()->route('wholesalerlogin');
        } else {

            // echo "string2";exit;
            return back()->withInput($request->input())->with('error', 'You have not able to register here.');
        }
    }

    public function send_email_register_wholesaler($email, $password, $fullname)
    {


        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('7');

        // $from_email = $setting['from_email'];
        $data = array('email' => $email, 'password' => $password, 'fullname' => $fullname, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('wholesaler.send_register_wholesaler', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }


    public function forgotpass(Request $request)
    {
        if (auth()->guard('web')->check()) {
            return redirect()->route('adminHome');
        } else {
            return view('auth.passwords.email');
        }
    }

    public function forgotpasswholesaler(Request $request)
    {
        if (auth()->guard('main_user')->check()) {
            return redirect()->route('adminwholesalerHome');
        } else {
            return view('auth.passwords.wholesaler_email');
        }
    }

    public function wholesalerregister(Request $request, $id)
    {
        if (auth()->guard('main_user')->check()) {
            return redirect()->route('adminwholesalerHome');
        } else {
            // echo "<pre>";print_r($id);exit();
            $main_user_data = DB::table('wholesaler_invite_link')->where('uniqid', $id)->first();
            return view('auth.wholesalerregister', compact('main_user_data'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mainuserforgot(Request $request)
    {
        $result = $this->validateRequest();
        // $UserData = User::where('email', '=', $result['email'])->where('status',1)->first();
        $user_data = User::where(["email" => $request->input('email')])->first();
        // dd($user_data);
        $exists = User::where('email', '=', $result['email'])->where('status',1)->count();
        // dd($exists);
        if ($exists > 0) {
            $User = User::where('email', '=', $result['email'])->where('status',1)->first();
            // dd($User);
            $id = $User->id;
            $password = Str::random(10);
            $updatepsw = User::where('id', $id)->update(array(
                'password' => Hash::make($password),
            ));
            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $User->email;
            $password = $password;
            $name = $User->name;
            $ismail = $this->attachment_email($email, $password, $name, $url, $logo);

            return back()->with('success', 'Check Your email and get new password.');
        } else {
            // dd($request->user_type);
            if($user_data->user_type == 2){
                return back()->withInput($request->input())->with('error', 'Your account is inactive, kindly contact to super administrator.');
            }else{
                return back()->withInput($request->input())->with('error', 'Your account is inactive, kindly contact to country administrator.');

            }
        }
    }

    public function attachment_email($email, $password, $name, $url, $logo)
    {


        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('1');

        // $from_email = $setting['from_email'];
        $data = array('email' => $email, 'password' => $password, 'name' => $name, 'url' => $url, 'id' => '1', 'logo' => $logo, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('password_wholesaler', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }
    public function mainuserLogin(Request $request)
    {
        // Validator::extend('without_spaces', function($attr, $value){

        //     return preg_match('/^[s.match(/\w/]+$/', $value);

        // });

        $remember_me = $request->has('remember_me') ? true : false;
        $validate = $this->validate(
            $request,
            [
                'email' => 'required|email|exists:main_user,email',
                'user_type' => 'required',
                'password' => 'required|min:6',
            ],
            [
                'email.exists' => 'These credentials does not match our records.',
            
            ]
        );

        if (auth()->guard('main_user')->attempt(['email' => $request->input('email'), 'password' => $request->input('password')])) {

            $user = auth()->guard('main_user')->user();

            if ($remember_me == true) {
                $minutes = 120;

                Cookie::queue('admin_email',  $request->input('email'), $minutes);
                Cookie::queue('admin_password', $request->input('password'), $minutes);
            } else {
                \Cookie::queue(\Cookie::forget('admin_email'));
                \Cookie::queue(\Cookie::forget('admin_password'));
            }
            return redirect('/');
        } else {
            $error = 'Your email or password is invalid.';
            return view('mainuser.login', compact('error'));
            // return back()->withInput($request->input())->with('error','Your email or password is invalid.');
        }
    }


    public function logoutMainUser()
    {
        /* dd();*/

        Auth::logout();
        /*\Cookie::queue(\Cookie::forget('admin_email'));
        \Cookie::queue(\Cookie::forget('admin_password'));*/
        Session::flush();
        // return redirect('/wholesaler');
        return redirect('/admin/login');
    }

    public function logoutMainWholesaler()
    {
        /* dd();*/

        // Auth::logout();
        auth()->guard('main_user')->logout();
        // Auth::guard('main_user')->user()->logout();
        // \Cookie::queue(\Cookie::forget('wholesaler_email'));
        // \Cookie::queue(\Cookie::forget('wholesaler_password'));
        Session::flush();
        return redirect('/wholesaler');
        // return redirect('/admin/login');
    }

    public function validateRequest()
    {
        $validateData = request()->validate([
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email|exists:users',
        ], [
            'email.exists' => 'These credentials does not match our records.',
            'email.regex' => 'Please enter valid email address.'
        ]);
        return $validateData;
    }

    public function wholesalerforgot(Request $request)
    {
        // echo "string";exit();
        $result = $this->validateWholesalerRequest();

        /*echo "<pre>"; print_r($result); 
        echo $result['email'];*/
        $exists = MainUser::where('email', '=', $result['email'])->where('user_type', 2)->count();

        if ($exists > 0) {

            $User = MainUser::where('email', '=', $result['email'])->where('user_type', 2)->first();



            $id = $User->id;

            $password = Str::random(10);

            $updatepsw = MainUser::where('id', $id)->update(array(
                'password' => Hash::make($password),
            ));



            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $User->email;
            $password = $password;
            $name = $User->name;

            $ismail = $this->attachment_email($email, $password, $name, $url, $logo);

            return back()->with('success', 'Check Your email and get new password.');
        } else {
            return back()->with('error', 'Your account is inactive, kindly contact to super administrator.');
        }
    }

    public function validateWholesalerRequest()
    {
        $validateData = request()->validate([
            'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email|exists:main_users',
        ], [
            'email.exists' => 'This Email is not registered with us',
            'email.regex' => 'Please enter valid Email'
        ]);
        return $validateData;
    }
}
