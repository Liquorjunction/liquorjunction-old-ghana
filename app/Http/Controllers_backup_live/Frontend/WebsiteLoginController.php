<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Session\Store;
use Illuminate\Validation\Rule;
use Auth;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\MainUser;
use App\Models\EmailTemplate;
use App\Models\Favourite;
use Mail;
use Carbon\Carbon;
use Cookie;
use Storage;
use Socialite;
use DB;
use Session;
use File;
use Helper;
use Illuminate\Contracts\Session\Session as SessionSession;
use Illuminate\Support\Facades\Session as FacadesSession;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Stripe\BillingPortal\Session as BillingPortalSession;
use Stripe\Checkout\Session as CheckoutSession;

class WebsiteLoginController extends Controller
{
    public $setting;

    private $uploadPath = "uploads/idproof";

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    use AuthenticatesUsers;

    public function __construct()
    {
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {

        try {


            $user = Socialite::driver('google')->stateless()->user();

            if (empty($user->email) && empty($user->phone)) {
                session()->flash('error', 'Google account does not provide an email or phone number. Please use another login method.');
                Alert::warning('Warning', 'Google account missing email or phone number.');
                return redirect()->route('websitelogin');
            }

            $finduser = MainUser::where('google_id', $user->id)->where('status', '!=', '2')->first();

            if ($finduser) {

                Auth::guard('user')->login($finduser);

                session()->flash('success', 'Login successfully');
                return redirect()->route('frontend.home');
            } else {
                $userExist = MainUser::where(function ($query) use ($user) {
                    if (!empty($user->email)) {
                        $query->where('email', $user->email);
                    }
                    if (!empty($user->phone)) {
                        $query->orWhere('phone', $user->phone);
                    }
                })
                    ->first();

                if ($userExist) {
                    $errors = [];

                    if (!empty($user->email) && $userExist->email === $user->email) {
                        $errors['email'] = ['The email address is already registered.'];
                    }

                    if (!empty($user->phone) && $userExist->phone === $user->phone) {
                        $errors['phone'] = ['The phone number is already registered.'];
                    }

                    session()->flash('error', $errors);
                    Alert::warning('Warning', 'User already exists with this email or phone.');
                    return redirect()->route('websitelogin');
                }

                $uniqid = uniqid();
                $nameParts = explode(' ', $user->name);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                $newUser = MainUser::create([
                    'uniqid' => $uniqid,
                    'email' => $user->email,
                    'name' => $user->name,
                    'google_id' => $user->id,
                    'is_verify_user' => 1,
                    'status' => 1,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'social_type' => 1,
                ]);

                Auth::guard('user')->login($newUser);
                session()->flash('success', 'Login successfully');
                return redirect()->route('frontend.home')->with('key', $newUser);
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
            session()->flash('error', 'Something went wrong. Please try again.');
            return redirect()->route('websitelogin');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {

        try {
            $user = Socialite::driver('facebook')->stateless()->user();

            if (empty($user->email) && empty($user->phone)) {
                session()->flash('error', 'Facebook account does not provide an email or phone number. Please use another login method.');
                Alert::warning('Warning', 'Facebook account missing email or phone number.');
                return redirect()->route('websitelogin');
            }

            $finduser = MainUser::where('facebook_id', $user->id)->where('status', '!=', '2')->first();

            if ($finduser) {
                Auth::guard('user')->login($finduser);
                session()->flash('success', 'Login successfully');
                return redirect()->route('frontend.home');
            } else {

                $userExist = MainUser::where(function ($query) use ($user) {
                    if (!empty($user->email)) {
                        $query->where('email', $user->email);
                    }
                    if (!empty($user->phone)) {
                        $query->orWhere('phone', $user->phone);
                    }
                })
                    ->first();

                if ($userExist) {
                    $errors = [];

                    if (!empty($user->email) && $userExist->email === $user->email) {
                        $errors['email'] = ['The email address is already registered.'];
                    }

                    if (!empty($user->phone) && $userExist->phone === $user->phone) {
                        $errors['phone'] = ['The phone number is already registered.'];
                    }

                    session()->flash('error', $errors);
                    Alert::warning('Warning', 'User already exists with this email or phone.');
                    return redirect()->route('websitelogin');
                }

                $uniqid = uniqid();
                $nameParts = explode(' ', $user->name);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                $newUser = MainUser::create([

                    'uniqid' => $uniqid,
                    'email' => $user->email,
                    'name' => $user->name,
                    'facebook_id' => $user->id,
                    'is_verify_user' => 1,
                    'status' => 1,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'social_type' => 2,
                ]);

                Auth::guard('user')->login($newUser);
                session()->flash('success', 'Login successfully');
                return redirect()->route('frontend.home');
            }
        } catch (Exception $e) {
            // dd($e->getMessage());
            session()->flash('error', 'Something went wrong. Please try again.');
            return redirect()->route('websitelogin');
        }
    }

    public function redirectToApple()
    {
        return Socialite::driver('apple')->stateless()->redirect();
    }

    public function handleAppleCallback()
    {

        try {

            $appleUser = Socialite::driver('apple')->stateless()->user();

            if (empty($appleUser->email) && empty($appleUser->phone)) {
                session()->flash('error', 'Apple account does not provide an email or phone number. Please use another login method.');
                Alert::warning('Warning', 'Apple account missing email or phone number.');
                return redirect()->route('websitelogin');
            }

            $finduser = MainUser::where('apple_id', $appleUser->id)->where('status', '!=', '2')->first();
            if ($finduser) {

                Auth::guard('user')->login($finduser);

                session()->flash('success', 'Login successfully');
                return redirect()->route('frontend.home');
            } else {

                $userExist = MainUser::where(function ($query) use ($appleUser) {
                    if (!empty($appleUser->email)) {
                        $query->where('email', $appleUser->email);
                    }
                    if (!empty($appleUser->phone)) {
                        $query->orWhere('phone', $appleUser->phone);
                    }
                })
                    ->first();

                if ($userExist) {
                    $errors = [];

                    if (!empty($appleUser->email) && $userExist->email === $appleUser->email) {
                        $errors['email'] = ['The email address is already registered.'];
                    }

                    if (!empty($appleUser->phone) && $userExist->phone === $appleUser->phone) {
                        $errors['phone'] = ['The phone number is already registered.'];
                    }

                    session()->flash('error', $errors);
                    Alert::warning('Warning', 'User already exists with this email or phone.');
                    return redirect()->route('websitelogin');
                }


                $uniqid = uniqid();
                $nameParts = explode(' ', $appleUser->name);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';

                $newUser = MainUser::create([
                    'uniqid' => $uniqid,
                    'email' => $appleUser->email,
                    'name' => $appleUser->name,
                    'apple_id' => $appleUser->id,
                    'is_verify_user' => 1,
                    'status' => 1,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'social_type' => 3,
                ]);

                Auth::guard('user')->login($newUser);

                session()->flash('success', 'Login successfully');
                return redirect()->route('frontend.home')->with('key', $newUser);
            }
        } catch (Exception $e) {
            session()->flash('error', 'Something went wrong. Please try again.');
            return redirect()->route('websitelogin');
        }
    }

    public function websiteLoginForm()
    {
        // echo "string";exit();
        if (auth()->guard('user')->check()) {
            $user_id = $this->user_id;
            // echo "<pre>";print_r($user_id);exit();
            return redirect()->route('frontend.home', compact('user_id'));
        } else {
            // echo "string";exit();
            return view("frontend.auth.login");
        }
        // echo "<pre>";print_r($this->user_id);exit();
    }

    public function websiteRegisterForm()
    {
        if (auth()->guard('user')->check()) {
            $user_id = $this->user_id;
            return redirect()->route('frontend.home', compact('user_id'));
        } else {

            $phonecode = DB::table('countries')->orderby('phonecode', 'ASC')->where('status', 1)->get();
            $countryData = DB::table('countries')->orderby('name')->where('status', 1)->get();
            // dd($phonecode);
            return view("frontend.auth.register", compact('phonecode', 'countryData'));
        }
    }

    public function websiteRegister(Request $request)
    {


        if ($request->ajax()) {
            $response = 1;
            $password = $request->input('password');
            $confirm_password = $request->input('confirm_password');

            $validator = \Validator::make(
                $request->all(),
                [
                    'first_name' => ['required', 'min:3', 'max:30'],
                    'last_name' => ['required', 'min:3', 'max:30'],
                    'age' => ['required', 'integer', 'min:18', 'max:100'],
                    'email' => [
                        'required',
                        'email',
                        // Rule::unique('main_users')
                        //     ->where(function ($query) {
                        //         return $query->where('status', '!=', '2')->where('is_otp_verify', 1);
                        //     })

                        Rule::unique('main_users', 'email')
                            ->where(function ($query) {
                                return $query->where('status', '!=', '2')
                                    ->where('is_otp_verify', 1);
                            }),
                    ],
                    'phone' => [
                        'required',
                        'min:8',
                        'max:15',
                        Rule::unique('main_users')->where(function ($query) {
                            return $query->where('status', '!=', '2')->where('is_otp_verify', 1);
                        })
                    ],
                    'password' => 'required|min:6',
                    'confirm_password' => 'required|same:password|min:6'
                ],
                [
                    'first_name.required' => \Helper::language('first_name_required'),
                    'first_name.min' => \Helper::language('first_name_min_valiadation_msg'),
                    'first_name.max' => \Helper::language('first_name_max_validation'),

                    'last_name.required' => \Helper::language('last_name_field_is_required'),
                    'last_name.min' => \Helper::language('last_name_min_valiadation_msg'),
                    'last_name.max' => \Helper::language('last_name_max_validation'),

                    'age.required' => 'Age field is required',
                    'age.min' => 'Minimum age allowed is 18',
                    'age.max' => 'Maximum age allowed is 100',

                    'email.required' => \Helper::language('email_field_required'),
                    'email.email' => \Helper::language('enter_valid_email_validation'),

                    'phone.required' => \Helper::language('phone_number_field_is_required'),
                    'phone.min' => \Helper::language('phone_number_min_max'),
                    'phone.max' => \Helper::language('phone_number_min_max'),
                    // 'phone.exists' => 'The phone number already exits',
                    'phone.unique' => 'The phone number already exists',

                    'password.required' => \Helper::language('password_field_required_validation'),
                    //'password.confirmed' => 'The password and confirm password field does not match.',
                    'password.min' => \Helper::language('password_length'),

                    'confirm_password.required' => \Helper::language('confirm_password_required'),
                    'confirm_password.min' => \Helper::language('confirm_password_len'),
                    'confirm_password.same' => 'The password and confirm password field does not match.',
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $userExist = MainUser::where(function ($query) use ($request) {
                $query->where('email', $request->email)
                    ->orWhere('phone', $request->phone);
            })
                ->first();

            if ($userExist) {
                $errors = [];

                if ($userExist->email === $request->email) {
                    $errors['email'] = ['The email address is already registered.'];
                }

                if ($userExist->phone === $request->phone) {
                    $errors['phone'] = ['The phone number is already registered.'];
                }

                Alert::warning('Warning', $errors);
                return response()->json($errors, 422);
            }


            unset($_SESSION["otp_phone"]);
            // Session::set('otp_phone', $request->phone);
            \Session::put('otp_phone', $request->phone);
            \Session::put('email', $request->email);
            \Session::put('first_name', $request->first_name);
            \Session::put('phone_code', $request->phone_code);

            $otp = mt_rand(1000, 9999);

            // $user->otp_expire_time = date('Y-m-d H:i:s', strtotime(Helper::getOtpExpireTime()));
            $otp_expire_time = Carbon::now()->addSeconds(@$setting->otp_expiration_second ?: 300);

            $user = new MainUser;
            $uniqid = uniqid();
            $user->uniqid = @$uniqid;
            $user->label_type = 1;
            $user->first_name = isset($request->first_name) ? $request->first_name : '';
            $user->last_name = isset($request->last_name) ? $request->last_name : '';
            $user->email = isset($request->email) ? $request->email : '';
            $user->age = isset($request->age) ? $request->age : '';
            $user->phone = isset($request->phone) ? $request->phone : '';
            $user->phone_code = isset($request->phone_code) ? $request->phone_code : '';
            $user->password = isset($request->password) ? Hash::make($request->password) : '';
            $user->otp = $otp;

            $user->otp_expire_time = $otpExpireTime = now()->addMinutes(5)->toDateTimeString();
            $user->user_type = 1;
            $user->status = 2;
            $user->save();


            // $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            // $url_link = \URL::to("/");
            // $url = $url_link . '/';
            // $email = $user->email;
            // $otp = $otp;
            // $name = $user->name;
            // $phonecode = $request->phone_code;

            // try {
            //     $ismail = $this->attachment_otp_email($email, $otp, $name, $url, $logo);
            // } catch (Exception $e) {
            //     // Log the error message
            //     error_log($e->getMessage());

            //     // Optionally, display a user-friendly message
            //     echo "An error occurred while sending the email. Please try again later.";
            // }


            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $user->email;
            $otp = $otp;
            $name = $user->name;
            $phonecode = $request->phone_code;
            $otp_phone = $request->phone;

            try {

                $ismail = $this->attachment_otp_email($email, $otp, $name, $url, $logo);
                // dd($ismail);
                // $sendsms= \Helper::sendTwilioSMS("+".$phonecode.$otp_phone, 'Dear Customer, Your OTP for login is:'.$otp);



                $sendsms = \Helper::sendTwilioSMS(
                    "+" . $phonecode . $otp_phone,
                    'Dear Customer, Your OTP for login is ' . $otp . ' and it will be valid for 5 Mins - Liquor Junction Ghana.'
                );
            } catch (\Exception $e) {
                // Handle the exception and log the error if necessary

            }
            return response()->json(['success' => 'true']);
        }
        abort(404);
    }

    public function webisteLogin(Request $request)
    {

        $checkout_value = Session::get('checkout_value') ? Session::get('checkout_value') : 0;
        $counter = Session::get('counter');
        $pack_size = Session::get('pack_size');


        $validator = \Validator::make(
            $request->all(),
            [
                // 'email' => 'email',
                // 'phone' =>['phone:main_users','phone'],
            ],
            [
                // 'email.exists' =>  \Helper::language('these_credentials_do_not_match_our_records'),
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pattern = "^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$";
        if (strpos($request->input('email'), '@') !== false || (strlen($request->input('email')) >= 8 && strlen($request->input('email')) <= 15)) {


            $data = MainUser::where('email', $request->input('email'))->orWhere('phone', $request->input('email'))->where('user_type', 1)->orderBy('id', 'desc')->first();
            // echo "<pre>";print_r($data);exit();
            // $id =$request->user_id;
            // dd($id);
            // dd($data);
            if (!empty($data)) {
                $id = $data->id;
                \Session::put('id', $data->id);
                \Session::put('first_name', $data->first_name);
                \Session::put('email', $data->email);
                \Session::put('otp_phone', $data->phone);

                if ($data->is_otp_verify != 1) {
                    return response()->json([
                        'status' => 'error',
                        'errors' => \Helper::language('otp_verification_pending'),
                        'id' => $id,
                    ], 500);

                    //$sendsms= \Helper::sendTwilioSMS("+".$phonecode.$otp_phone, 'Your Otp is:'.$otp);     
                }
                if ($data->status == 0) {
                    // echo "string2";exit();
                    return response()->json(array('status' => 'error', 'errors' => \Helper::language('your_account_deactivated_admin_side')), 500);
                } else {
                    // echo "string";exit();
                    if (
                        auth()->guard('user')->attempt(['email' => $request->input('email'), 'password' => $request->input('password'), 'status' => 1, 'user_type' => 1, 'is_otp_verify' => 1])
                        || auth()->guard('user')->attempt(['phone' => $request->input('email'), 'password' => $request->input('password'), 'status' => 1, 'user_type' => 1, 'is_otp_verify' => 1])
                    ) {
                        $token = $this->generateToken();
                        MainUser::where('id', $id)->update(['web_token' => $token]);
                        Helper::afterLoginAddUserCartItemData();
                        // echo "string";exit();
                        Alert::success(\Helper::language('success'), __('backend.login_successfully'));
                        // return redirect()->route('frontend.home');

                        // dd("else");
                        // die;
                        // Respond with success

                        // if ($checkout_value == 1) {
                        //     $checkoutUrl = route('checkout', ['counter' => $counter, 'pack_size' => $pack_size]);
                        //     return redirect()::to($checkoutUrl);
                        // }
                        // return response()->json(['success' => 'true']);
                        return response()->json([
                            'success' => true,
                            'checkout_value' => $checkout_value,
                            'counter' => $counter,
                            'pack_size' => $pack_size,
                        ]);
                    } else {
                        return response()->json(array('status' => 'error_password', 'errors' => \Helper::language('email_or_password_is_invalid')), 500);
                    }
                }
            } else {
                return response()->json(array('status' => 'error', 'errors' => \Helper::language('these_credentials_do_not_match_our_records')), 500);
            }
        } else {
            return response()->json(array('status' => 'error', 'errors' => \Helper::language('these_credentials_do_not_match_our_records')), 500);
        }
    }

    public function generateToken()
    {
        return md5(rand(1, 10) . microtime());
    }

    public function saveToken(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());
        $authid = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
        // echo "<pre>";print_r($authid);exit();
        MainUser::where('id', $authid)->where('status', '1')->update(['device_token' => $request->token]);


        return response()->json(['token saved successfully.']);
    }

    public function send_email_user_create($email, $password, $first_name)
    {
        $setting = Setting::find(1);
        //$from_email = $setting['mail_no_replay'];
        $from_email = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $emailtemp = Emailtemplate::find('9');
        // $from_email = $setting['from_email'];
        $data = array('email' => $email, 'password' => $password, 'fullname' => $first_name, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        // echo "<pre>";print_r($data);exit();
        Mail::send('dashboard.send_subadmin_register', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }

    public function websiteSendOtplogin(Request $request)
    {
        // $id = Session::get('id');
        // $otp_phone = Session::get('otp_phone');
        // $email = Session::get('email');
        // $name = Session::get('first_name');
        // $otp = mt_rand(1000, 9999);
        // $otp_expire_time = $otpExpireTime = now()->addMinutes(5)->toDateTimeString();
        // $userdata = MainUser::find($id);

        $id = $request->query('id');
        $userdata = MainUser::find($id);
        $email = $userdata->email;
        $name = $userdata->first_name;
        $otp = mt_rand(1000, 9999);
        $otp_expire_time = $otpExpireTime = now()->addMinutes(5)->toDateTimeString();
        $updatepsw = MainUser::where('id', $id)->update([
            'otp_expire_time' => $otp_expire_time,
            'otp' => $otp,
            'status' => 1,
        ]);
        $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
        $url_link = \URL::to("/");
        $url = $url_link . '/';
        $email = $email;
        $otp = $otp;
        $name = $name;
        $phonecode = $userdata->phone_code;
        // dd($phonecode);
        $ismail = $this->attachment_otp_email($email, $otp, $name, $url, $logo);
        // $sendsms= \Helper::sendTwilioSMS("+".$phonecode.$otp_phone, 'Your Otp is:'.$otp);

        return redirect()->route('websitesendotp')->with('success', @Helper::language('otp_send_successfully'));
    }
    public function websiteSendOtpForm()
    {
        if (auth()->guard('user')->check()) {
            $user_id = $this->user_id;
            return redirect()->route('frontend.home', compact('user_id'));
        } else {
            $id = Session::get('id');
            $user_id = $id;
            $otp_phone = Session::get('otp_phone');
            $forgot_email = Session::get('email');
            // $users = MainUser::where('phone', $otp_phone)->latest()->first();
            $users = MainUser::where('email', $forgot_email)->first();
            if (!empty($users)) {
                $datetime1 = new \DateTime();
                $datetime2 = new \DateTime($users->otp_expire_time);
                $interval = $datetime1->diff($datetime2);
                $elapsed = $interval->format('%I:%S');
                $invert = $interval->invert;
                return view("frontend.auth.resendotp", compact('forgot_email', 'otp_phone', 'invert', 'elapsed', 'users', 'id', 'user_id'));
            }
            Alert::success(\Helper::language('success'), __('backend.user_register_successfully'));
        }
    }

    public function webisteOtpVerification(Request $request)
    {
        if ($request->digit_1 == "") {
            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_required')), 500);
        }
        if ($request->digit_2 == "") {

            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_required')), 500);
        }

        if ($request->digit_3 == "") {

            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_required')), 500);
        }
        if ($request->digit_4 == "") {
            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_required')), 500);
        }

        $data = $request->all();
        $userid = $request->user_id;
        $otp = implode("", $data);
        $id = Session::get('id');
        $mobile_number = Session::get('otp_phone');
        $email = Session::get('email');
        $current_time = date('Y-m-d H:i:s');
        // $user = MainUser::where('otp', '=', $otp)->where('phone', $mobile_number)->first();
        $user = MainUser::where('email', $email)->where('otp', $otp)->where('phone', $mobile_number)->first();
        \DB::enableQueryLog();
        if (!$user) {
            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_incorrect')), 500);
        } else {

            if ($current_time > $user->otp_expire_time) {
                return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_expired_msg')), 500);
            }

            try {
                $updatepsw = MainUser::where('id', $user->id)->update([
                    'is_otp_verify' => 1,
                    'status' => 1,
                    'is_verify_user' => 1,
                ]);

                // Print the SQL query
                \DB::enableQueryLog();
                \DB::getQueryLog();
                $ismail = $this->attachment_register_email($user);
                Alert::success(\Helper::language('success'), __('backend.user_register_successfully'));

                return response()->json(['success' => 'true']);
            } catch (\Exception $e) {
                dd($e->getMessage()); // Print any exceptions
            }
        }
    }

    public function websiteResendOtpForm(Request $request)
    {
        // dd($request->all());
        // echo "<pre>";print_r($request->toArray());exit();
        $id = Session::get('id');
        // dd($id);
        $phonecode = Session::get('phone_code');
        // dd($phonecode);
        $userdata = MainUser::where('phone_code')->first();
        // dd($userdata);
        $otp_phone = Session::get('otp_phone');
        $forgot_email = Session::get('forgot_email');
        // dd($forgot_email);
        $otp_expire_time = $otpExpireTime = now()->addMinutes(5)->toDateTimeString();
        $otp = mt_rand(1000, 9999);
        $email = Session::get('email');
        $name = Session::get('first_name');
        // $phonecode=$userdata->phone_code;

        if (!empty($otp_phone)) {
            // $User = MainUser::where('email', '=', $request->email)->where('status', 2)->latest()->first();
            // dd($User);
            // $user=MainUser::where('email','=',$request->email)->where('status',2)->latest()->first();
            $updatepsw = MainUser::where('phone', $otp_phone)->update(
                array(
                    'otp_expire_time' => $otp_expire_time,
                    'otp' => $otp,
                )
            );

            $datetime1 = new \DateTime();
            $datetime2 = new \DateTime($otp_expire_time);
            $interval = $datetime1->diff($datetime2);
            $elapsed = $interval->format('%I:%S');
            $invert = $interval->invert;
            // dd($user);
            // dd($email);

            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $email;
            $otp = $otp;
            $name = $name;

            $ismail = $this->attachment_otp_email($email, $otp, $name, $url, $logo);
            // dd($ismail);
            // $sendsms= \Helper::sendTwilioSMS("+".$phonecode.$otp_phone, 'Your Otp is:'.$otp);

            Alert::success(\Helper::language('success'), __('backend.otp_resend_successfully'));
            // return redirect()->route('frontend.home');
            return response()->json(['success' => 'true']);
        } else {
            $User = MainUser::where('email', '=', $forgot_email)->where('status', 1)->latest()->first();
            // dd($User);
            $updatepsw = MainUser::where('email', $forgot_email)->update(
                array(
                    'otp_expire_time' => $otp_expire_time,
                    'otp' => $otp,
                    'status' => 1,
                )
            );



            $datetime1 = new \DateTime();
            $datetime2 = new \DateTime($otp_expire_time);
            $interval = $datetime1->diff($datetime2);
            $elapsed = $interval->format('%I:%S');
            $invert = $interval->invert;

            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $User->email;
            $otp = $otp;
            $name = $User->first_name;
            //  dd($logo,$url_link,$url,$email,$otp,$name);

            $ismail = $this->attachment_otp_email($email, $otp, $name, $url, $logo);
            // dd($updatepsw);
            // dd($ismail);
            Alert::success(\Helper::language('success'), __('backend.otp_resend_successfully'));
            // return redirect()->route('frontend.home');
            return response()->json(['success' => 'true']);
        }

        // return view("frontEnd.auth.resendotp",compact('otp_phone','invert','elapsed'));
    }

    public function attachment_register_email($user)
    {
        $setting = Setting::find(1);
        // $emaildetail = EmailTemplate::find(9);
        $name = $user->first_name . ' ' . $user->last_name;
        //$from_email = $setting['mail_no_replay'];
        $from_email = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $emailtemp = Emailtemplate::find('9');

        $data = array('email' => $user->email, 'name' => $name, 'id' => 9, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('emails.register_user', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);

            $message->from($data['from_email'], $data['support_name']);
        });
    }
    public function attachment_email_register($name, $email, $url, $logo)
    {
        // dd($user);
        $setting = Setting::find(1);

        // $emaildetail = EmailTemplate::find(9);
        //$from_email = $setting['mail_no_replay'];
        $from_email = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $emailtemp = Emailtemplate::find('9');

        $data = array('email' => $email, 'name' => $name, 'id' => 9, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('emails.register_user', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);

            $message->from($data['from_email'], $data['support_name']);
        });
    }

    public function forgotpassword()
    {

        if (auth()->guard('user')->check()) {
            $user_id = $this->user_id;
            return redirect()->route('frontend.home', compact('user_id'));
        } else {

            return view("frontend.auth.forgot-password");
        }
    }

    public function forgotPasswordForm(Request $request)
    {
        Session::forget('otp_phone');
        $userDataCount = MainUser::where('email', '=', $request->email)->latest()->first();
        // echo "<pre>";print_r($exists);exit();
        if (!empty($userDataCount)) {
            if (!empty($userDataCount) && $userDataCount->status != 1 && $userDataCount->is_otp_verify == 1) {
                return response()->json(array('status' => 'error_forgot_password', 'errors' => \Helper::language('email_id_inactive')), 500);
            }
            // echo "string";exit();
            $User = MainUser::where('email', '=', $request->email)->latest()->first();
            // dd($User);
            $id = $User->id;
            $otp = mt_rand(1000, 9999);
            $updatepsw = MainUser::where('id', $id)->update(
                array(
                    'otp' => $otp,
                    'otp_expire_time' => $otpExpireTime = now()->addMinutes(5)->toDateTimeString(),
                )
            );

            \Session::put('forgot_email', $request->email);

            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $User->email;
            $otp = $otp;
            $name = $User->first_name;

            $ismail = $this->forgot_otp_email($email, $otp, $name, $url, $logo);
            // echo "string";exit();
            Alert::success(\Helper::language('success'), __('backend.otp_sent_successfully'));
            // return redirect()->route('frontend.home');
            return response()->json(['success' => 'true']);
        } else {
            return response()->json(array('status' => 'error_forgot_password', 'errors' => \Helper::language('email_does_not_exist')), 500);
        }
    }

    public function websiteForgotResendOtpForm(Request $request)
    {
        //  dd($request->all());
        // echo "<pre>";print_r($request->toArray());exit();
        $id = Session::get('id');
        // dd($id);
        $phonecode = Session::get('phone_code');
        // dd($phonecode);
        $userdata = MainUser::where('phone_code')->first();
        // dd($userdata);
        $otp_phone = Session::get('otp_phone');
        $forgot_email = Session::get('forgot_email');
        // dd($forgot_email);
        $otp_expire_time = $otpExpireTime = now()->addMinutes(5)->toDateTimeString();
        $otp = mt_rand(1000, 9999);
        $email = Session::get('email');
        $name = Session::get('first_name');
        // $phonecode=$userdata->phone_code;
        // dd($phonecode);
        // dd($name);


        if (!empty($otp_phone)) {
            // $User = MainUser::where('email', '=', $request->email)->where('status', 2)->latest()->first();
            // dd($User);
            // $user=MainUser::where('email','=',$request->email)->where('status',2)->latest()->first();
            $updatepsw = MainUser::where('phone', $otp_phone)->update(
                array(
                    'otp_expire_time' => $otp_expire_time,
                    'otp' => $otp,
                )
            );

            $datetime1 = new \DateTime();
            $datetime2 = new \DateTime($otp_expire_time);
            $interval = $datetime1->diff($datetime2);
            $elapsed = $interval->format('%I:%S');
            $invert = $interval->invert;
            // dd($user);
            // dd($email);

            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $email;
            $otp = $otp;
            $name = $name;

            $ismail = $this->forgot_resendotp_email($email, $otp, $name, $url, $logo);
            // dd($ismail);
            // $sendsms= \Helper::sendTwilioSMS("+".$phonecode.$otp_phone, 'Your Otp is:'.$otp);

            Alert::success(\Helper::language('success'), __('backend.otp_resend_successfully'));
            // return redirect()->route('frontend.home');
            return response()->json(['success' => 'true']);
        } else {
            $User = MainUser::where('email', '=', $forgot_email)->where('status', 1)->latest()->first();
            // dd($User);
            $updatepsw = MainUser::where('email', $forgot_email)->update(
                array(
                    'otp_expire_time' => $otp_expire_time,
                    'otp' => $otp,
                    'status' => 1,
                )
            );



            $datetime1 = new \DateTime();
            $datetime2 = new \DateTime($otp_expire_time);
            $interval = $datetime1->diff($datetime2);
            $elapsed = $interval->format('%I:%S');
            $invert = $interval->invert;

            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $url = $url_link . '/';
            $email = $User->email;
            $otp = $otp;
            $name = $User->first_name;
            //  dd($logo,$url_link,$url,$email,$otp,$name);

            $ismail = $this->forgot_resendotp_email($email, $otp, $name, $url, $logo);
            // dd($updatepsw);
            // dd($ismail);
            Alert::success(\Helper::language('success'), __('backend.otp_resend_successfully'));
            // return redirect()->route('frontend.home');
            return response()->json(['success' => 'true']);
        }

        // return view("frontEnd.auth.resendotp",compact('otp_phone','invert','elapsed'));
    }
    public function forgot_otp_email($email, $otp, $name, $url, $logo)
    {
        $setting = Setting::find(1);
        //$from_email = $setting['mail_no_replay'];
        $from_email = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $emailtemp = Emailtemplate::find('18');
        // dd($emailtemp);
        // echo "<pre>";print_r($setting->toArray());exit();
        // $from_email = $setting['from_email'];
        $data = array('email' => $email, 'otp' => $otp, 'name' => $name, 'url' => $url, 'id' => '18', 'logo' => $logo, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('password', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }
    public function forgot_resendotp_email($email, $otp, $name, $url, $logo)
    {
        $setting = Setting::find(1);
        // $from_email = $setting['mail_no_replay'];
        $from_email = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $emailtemp = Emailtemplate::find('19');
        // echo "<pre>";print_r($setting->toArray());exit();
        // $from_email = $setting['from_email'];
        $data = array('email' => $email, 'otp' => $otp, 'name' => $name, 'url' => $url, 'id' => '19', 'logo' => $logo, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('password', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }
    // public function attachment_otp_email($email, $otp, $name, $url, $logo)
    // {
    //     $setting = Setting::find(1);
    //     $from_email = $setting['mail_no_replay'];
    //     $emailtemp = Emailtemplate::find('8');
    //     // echo "<pre>";print_r($setting->toArray());exit();
    //     // $from_email = $setting['from_email'];
    //     $data = array('email' => $email, 'otp' => $otp, 'name' => $name, 'url' => $url, 'id' => '8', 'logo' => $logo, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

    //     Mail::send('password', $data, function ($message) use ($data) {

    //         $message->to($data['email'], $data['title'])->subject($data['subject']);
    //         //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

    //         $message->from($data['from_email'], $data['support_name']);
    //     });
    // }
    public function attachment_otp_email($email, $otp, $name, $url, $logo)
    {
        // Fetch the email template
        $emailtemp = Emailtemplate::find('8');

        if (empty($email)) {
            \Log::error("Missing email address. Email not sent.");
            return;
        }

        // Prepare the data for the email
        $data = array(
            'email' => $email,
            'otp' => $otp,
            'name' => $name,
            'url' => $url,
            'id' => '8',
            'logo' => $logo,
            'from_email' => config('mail.from.address'),  // Use configured from email
            'support_name' => config('mail.from.name'),   // Use configured from name
            'title' => $emailtemp['title'],
            'subject' => $emailtemp['subject']
        );

        // Send the email
        Mail::send('password', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['title'])->subject($data['subject']);
            $message->from($data['from_email'], $data['support_name']);
        });
    }


    public function websiteForgotOtpForm()
    {
        if (auth()->guard('user')->check()) {
            $user_id = $this->user_id;
            return redirect()->route('frontend.home', compact('user_id'));
        } else {

            $forgot_email = Session::get('forgot_email');

            $users = MainUser::where('email', $forgot_email)->latest()->first();

            if (!empty($users)) {
                $datetime1 = new \DateTime();
                $datetime2 = new \DateTime($users->otp_expire_time);
                $interval = $datetime1->diff($datetime2);
                $elapsed = $interval->format('%I:%S');
                $invert = $interval->invert;


                return view("frontend.auth.forgot-otp", compact('forgot_email', 'invert', 'elapsed', 'users'));
            }
        }
    }
    public function attachment_email($email, $password, $name, $url, $logo)
    {


        $setting = Setting::find(1);
        // $from_email = $setting['mail_no_replay'];
        $from_email = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $emailtemp = Emailtemplate::find('1');

        // $from_email = $setting['from_email'];
        $data = array('email' => $email, 'password' => $password, 'name' => $name, 'url' => $url, 'id' => '1', 'logo' => $logo, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);
        // dd($data);
        Mail::send('password_wholesaler', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }
    public function webisteOtpForgotVerification(Request $request)
    {
        // dd($request);
        // echo "<pre>";print_r($request->toArray());exit();
        if (empty($request->digit_1)) {
            // echo "string";exit();
            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_required')), 500);
        }
        if ($request->digit_2 == "") {
            // echo "string2";exit();

            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_required')), 500);
        }

        if ($request->digit_3 == "") {
            // echo "string3";exit();

            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_required')), 500);
        }
        // echo "<pre>";print_r($request->digit_4);exit();
        if ($request->digit_4 == "") {
            // echo "string4";exit();
            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_required')), 500);
        }

        $data = $request->all();
        // dd($data);
        $otp = implode("", $data);
        $current_time = date('Y-m-d H:i:s');
        $forgot_email = Session::get('forgot_email');

        $user = MainUser::where('otp', '=', $otp)->where('email', $forgot_email)->where('user_type', '1')->where('status', 1)->latest()->first();
        // dd($user);
        // echo "<pre>";print_r($user->toArray());exit();
        if (!$user) {
            // $error['otp'] = "OTP is incorrect.";
            // return back()->withInput($request->input())->withErrors($error);
            return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_incorrect')), 500);
        } else {

            if ($current_time > $user->otp_expire_time) {
                return response()->json(array('status' => 'error_otp', 'errors' => \Helper::language('otp_expired_msg')), 500);
            }

            Alert::success(\Helper::language('success'), __('backend.otp_verification_successfully'));
            // return redirect()->route('frontend.home');
            return response()->json(['success' => 'true']);
        }
    }

    public function changePassword()
    {
        // echo "string";exit();
        if (auth()->guard('user')->check()) {
            $user_id = $this->user_id;
            return redirect()->route('frontend.home', compact('user_id'));
        } else {

            return view("frontend.auth.change-password");
        }
    }


    public function changePasswordForm(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');
        $forgot_email = Session::get('forgot_email');

        if ($new_password != $confirm_password) {

            // return redirect()->back()->with("errorMessageConformPassword", "New password and confirm password doesn't match.");
            return response()->json(array('status' => 'error_password_match', 'errors' => \Helper::language('new_confirm_password_not_match_web')), 500);
        }

        $user_data = DB::table('main_users')->where('email', '=', $forgot_email)->latest()->first();
        //dd($user_data); 
        // echo "<pre>";print_r($user_data);exit();
        if (!empty($user_data)) {

            $updatepsw = MainUser::where('id', $user_data->id)->update(
                array(
                    'password' => bcrypt($new_password),
                )
            );
            //dd($updatepsw);
            // $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            // $url_link = \URL::to("/");
            // $url = $url_link . '/';
            // $email = $user_data->email;
            // $password = decrypt($user_data->password);
            // // dd($password);
            // $firstname = $user_data->first_name;

            // $ismail = $this->attachment_email($email, $password, $firstname, $url, $logo);

            Alert::success(\Helper::language('success'), __('backend.change_password_successfully'));
            // return redirect()->route('frontend.home');
            return response()->json(['success' => 'true']);
        } else {
        }
    }
}
