<?php

namespace App\Http\Controllers\Frontend\auth;


use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Auth;
use App\Models\MembershipPlanSubscription;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Setting;
use Validator;
use Session;
use DB;
use Mail;
use Cookie;
use Illuminate\Support\Str;

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

    use AuthenticatesUsers;

   
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
       
        if (auth()->guard('web')->check()) {
            return redirect()->route('adminHome');
        } else {
            return view('auth.login');
        }

    }


    public function webisteLogin(Request $request)
    {
        $remember_me = $request->has('remember_me') ? true : false; 

        if (auth()->attempt(['email' => $request->input('email'), 'password' => $request->input('password'),'user_type'=>'1'], $remember_me))
        {      
            if($remember_me == true)  
            {
                $minutes = 120;
              
                Cookie::queue('website_email',  $request->input('email'), $minutes);
                Cookie::queue('website_password', $request->input('password'), $minutes);

            }
            else
            {
                \Cookie::queue(\Cookie::forget('website_email'));
                \Cookie::queue(\Cookie::forget('website_password'));
                
            }
               /*$user = User::where(["email" => $request->input('email')])->first();
    
            Auth::login($user, $remember_me);*/

             $route = route('adminHome');
            return response()->json(['success' => true,'route'=> $route]);
        }
        
        else {
            
           // return back()->withInput($request->input())->with('error','Your email or password is invalid.');
            return response()->json(['success' => false]);
        }
    }
    public function forgotpass(Request $request)
    {
        if (auth()->guard('web')->check()) {
            return redirect()->route('adminHome');
        } else {
         return view('auth.passwords.email');
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

        /*echo "<pre>"; print_r($result); 
        echo $result['email'];*/
         $exists = User::where('email','=',$result['email'])->count();
         
        if ($exists>0) 
        {

            $User = User::where('email','=',$result['email'])->first();


           
                $id = $User->id;

                $password = Str::random(10);

                $updatepsw = User::where('id', $id)->update(array(
                   'password' => Hash::make($password),
                ));

               
                
                $logo = \Config::get('app.url').'/public/assets/dashboard/images/liquor.png';
                $url_link = \URL::to("/");
                $url = $url_link . '/';
                $email = $User->email;
                $password = $password;
                $name = $User->name;

                $ismail = $this->attachment_email($email, $password, $name, $url, $logo);

                return back()->with('success','Check Your email and get new password.');
        }
        else
        {
            return back()->with('error','Email does not exist in the system.');
        }
    }

    public function attachment_email($email, $password, $name, $url, $logo) {

       
        $setting = Setting::find(1);
        $from_email = 'admin@vrinsoft.com';
       
       // $from_email = $setting['from_email'];
        $data = array('email' => $email, 'password' => $password, 'name' => $name, 'url' => $url,'id'=>'1','logo' => $logo, 'from_email' => $from_email);
       
        Mail::send('password', $data, function ($message) use ($data) {

        $message->to($data['email'], 'OnlyDance')->subject('Password has been reset successfully!');
        //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

        $message->from($data['from_email'], 'OnlyDance');
        });

    }
    public function mainuserLogin(Request $request)
    {
        
        
        $validate = $this->validate($request, [
                    'email' => 'required|email|exists:main_user,email',
                    'user_type' => 'required',
                    'password' => 'required|min:6',
                ],
                [
                    'email.exists' => 'These credentials do not match our records.',
                ]
            );
        
        if (auth()->guard('main_user')->attempt(['email' => $request->input('email'), 'password' => $request->input('password'),'user_type' => $request->input('user_type'),'status' => 1 ]))
        {

            $user = auth()->guard('main_user')->user();

            // $membership = MembershipPlanSubscription::where('user_id', $user->id)
            //     ->where('plan_id', $user->member_ship_plan)
            //     ->where('status', 1)
            //     ->first();

            // //dd($membership->end_date);
            // $now_date = date('Y-m-d');

            // if(isset($membership->end_date)){
            //     $end_date = $membership->end_date;
            // }else{
            //     $end_date = '';
            // }

            // if($end_date == $now_date){
            //     return redirect('/user/membership/'.$user->id);
            // }else{
            //     return redirect('/');
            // }
            return redirect('/');
        }else {
            $error = 'Your email or password is invalid.';
            return view('mainuser.login',compact('error'));
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
        return redirect('/admin/login');
    }

     public function validateRequest()
    {
            $validateData =request()->validate([
                    'email' => 'required|email|exists:users',
            ]);
        return $validateData;
    } 
}
