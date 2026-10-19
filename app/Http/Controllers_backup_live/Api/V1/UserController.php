<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Label;
use App\Models\Setting;
use App\Models\MainUser;
use App\Models\Notification;
use App\Models\Order;
use App\Models\EmailTemplate;
use App\Models\UserAddress;
use App\Models\UserBillAddress;
use App\Models\ProductVariants;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mail;
use Hash;
use Auth;
use DB;
use Helper;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;


class UserController extends Controller
{

    public function check_email($email)
    {
        $user_check = MainUser::where('email', $email)->where('status', '!=', 2)->where('user_type', '=', 1)->where('is_otp_verify', 1)->first();

        if (!empty($user_check)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function check_mobile($mobilenumber)
    {
        $user_check = MainUser::where('phone', $mobilenumber)->where('status', '!=', 2)->where('user_type', '=', 1)->where('is_otp_verify', 1)->first();

        if (!empty($user_check)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function check_age($age)
    {
        return ($age > 18) ? 1 : 0;
    }

    public function generateToken()
    {
        return md5(rand(1, 10) . microtime());
    }

    public function register(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'phone_number' => 'required',
            'phone_code' => 'required',
            'age' => 'required',
            // 'country_code' => 'required',
            'password' => 'required',
            // 'country' => 'required',
            // 'states' => 'required',
            // 'city' => 'required',
            // 'post_code' => 'required',
            // 'street_address' => 'required',
            'device_type' => 'required',
            // 'device_token' =>'required', 

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }
        $otp = mt_rand(1000, 9999);
        $otp_expire_time = Carbon::now()->addSeconds(@$setting->otp_expiration_second ?: 300);

        // $otp_expire_time =date('Y-m-d H:i:s', strtotime('+4 minutes'));
        $email_check = $this->check_email($request->email);
        $mobile_check = $this->check_mobile($request->phone_number);
        $age_check = $this->check_age($request->age);
        $token = $this->generateToken();

        if ($email_check == 1) {
            $result['code'] = strval(-2);
            $result['message'] = 'email_already_registered';
            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($mobile_check == 1) {
            $result['code'] = strval(-3);
            $result['message'] = 'number_already_register';
            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($age_check == 0) {
            $result['code'] = strval(-4);
            $result['message'] = 'user_must_be_older_than_18';
            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }


        $post = $request->all();

        if ($post) {
            $users = new MainUser();
            $users->uniqid = uniqid();

            $users->first_name = $request->input('firstname');
            $users->last_name = $request->input('lastname');
            $users->email = $request->input('email');
            $users->age = $request->input('age');
            $users->phone = $request->input('phone_number');
            $users->phone_code = $request->input('phone_code');
            $users->otp = $otp;
            // $users->otp_expire_time = $otp_expire_time;
            $users->otp_expire_time = $otp_expire_time;
            $users->remember_token = $token;
            $users->password = bcrypt($request->input('password'));
            $users->status = 1;
            $users->is_verify_user = 0;
            $users->is_phone = $request->input('device_type');
            // $users->device_token = $request->input('device_token');

            $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
            $url_link = \URL::to("/");
            $this->attachment_otp_email($users->email, $otp, $users->first_name, $logo, $url_link);

            if ($users->save()) {
                $lastid = $users->id;
                $userdata = MainUser::where('id', $lastid)->first();
                // dd($userdata);
                // $response = \App\Helpers\ResponseHelper::userCommonResponse($userdata);
                $response = [
                    'otp' => strval(@$userdata->otp ?: ''),
                    'otp_expire_time' => strval(@$userdata->otp_expire_time ?: ''),
                    'uniqid' => strval(@$userdata->uniqid ?: ''),
                    'remember_token' => strval(@$userdata->remember_token ?: '')
                ];

                $result['code'] = strval(1);
                $result['message'] = 'otp sent';
                $result['result'] = $response;
                // echo "<pre>";print_r($result);exit();
                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'something_went_wrong';
                $result['result'] = [];
                // echo "<pre>";print_r($result);exit();
                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }


    public function attachment_otp_email($email, $otp, $name, $url, $logo)
    {
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('8');
        $data = array('email' => $email, 'otp' => $otp, 'name' => $name, 'url' => $url, 'id' => '8', 'logo' => $logo, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('password', $data, function ($message) use ($data) {
            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');
            $message->from($data['from_email'], $data['support_name']);
        });
    }

    public function social_register(Request $request)
    {
        $result = [];
        $otp = mt_rand(1000, 9999);
        $finalArr = [];

        $validator = \Validator::make($request->all(), [
            'name' => 'sometimes|string',
            'social_register_id' => 'required',
            'social_register_type' => 'required|integer|in:1,2,3',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => '0',
                'message' => 'missing_some_fields',
                'result' => $validator->messages()
            ]);
        }

        $email = trim($request->input('email', ''));
        $phone = trim($request->input('phone', ''));

        if ($email === '' || strtolower($email) === 'null') {
            $email = null;
        }

        if ($phone === '' || strtolower($phone) === 'null') {
            $phone = null;
        }

        if (empty($email) && empty($phone)) {
            return response()->json([
                'code' => '0',
                'message' => 'missing_some_fields',
                'result' => [
                    'message' => 'Either email or phone is required.'
                ],
            ]);
        }

        $user = MainUser::where(function ($query) use ($request) {
            if (!empty($request->email)) {
                $query->where('email', $request->email);
            }

            if (!empty($request->phone)) {
                $query->orWhere('phone', $request->phone);
            }
        })
            ->first();

        if ($user) {
            // Update existing user

            $user->uniqid = uniqid();
            $user->name = $request->name ? $request->name : '';
            $user->is_otp_verify = '1';
            $user->remember_token = $this->generateToken();
            // $user->remember_token = $this->generateToken();
            $user->device_token = $request->device_token;
            $user->social_type = strval($request->social_register_type);
            $user->otp = $otp;
            // $user->otp_expire_time = now()->addMinutes($this->minutes);
            $user->updated_at = now();

            switch ($request->social_register_type) {
                case 1:
                    $user->google_id = strval($request->social_register_id);
                    break;
                case 2:
                    $user->facebook_id = strval($request->social_register_id);
                    break;
                case 3:
                    $user->apple_id = strval($request->social_register_id);
                    break;
            }

            $user->save();
        } else {
            // create new user

            $existingUser = MainUser::where(function ($query) use ($request) {
                if (!empty($request->email)) {
                    $query->where('email', $request->email);
                }
                if (!empty($request->phone)) {
                    $query->orWhere('phone', $request->phone);
                }
            })
                ->first();

            if ($existingUser) {
                $errorMessages = [];

                if (!empty($request->email) && $existingUser->email === $request->email) {
                    $errorMessages[] = 'The email address is already registered.';
                }

                if (!empty($request->phone) && $existingUser->phone === $request->phone) {
                    $errorMessages[] = 'The phone number is already registered.';
                }

                $resultMessage = implode(' ', $errorMessages);

                return response()->json([
                    'code' => '0',
                    'message' => 'duplicate_account',
                    'result' => $resultMessage
                ], 200);
            }

            $fname = "";
            $lname = "";

            if ($request->has('name')) {
                $parts = explode(' ', $request->input('name'));
                $fname = $parts[0] ?? '';
                $lname = $parts[1] ?? '';
            }

            $user = new MainUser();
            $user->uniqid = uniqid();
            $user->name = $request->name ? $request->name : '';
            $user->email = $request->email ? $request->email : '';
            $user->first_name = $fname;
            $user->last_name = $lname;
            $user->phone_code = '233';
            $user->phone = $request->phone ? $request->phone : '';
            $user->device_token = $request->device_token ? $request->device_token : '';
            $user->country_code = '971';
            $user->remember_token = $this->generateToken();
            $user->social_type = strval($request->social_register_type);
            $user->otp = $otp;
            // $user->otp_expire_time = now()->addMinutes($this->minutes);
            $user->is_otp_verify = '1';
            $user->status = '1';
            $user->created_at = now();
            $user->updated_at = now();

            switch ($request->social_register_type) {
                case 1:
                    $user->google_id = strval($request->social_register_id);
                    break;
                case 2:
                    $user->facebook_id = strval($request->social_register_id);
                    break;
                case 3:
                    $user->apple_id = strval($request->social_register_id);
                    break;
            }

            $user->save();
        }

        // Prepare response
        $finalArr['user_id'] = strval($user->id);
        $finalArr['uniqid'] = strval($user->uniqid);
        $finalArr['name'] = $user->name;
        $finalArr['email'] = $user->email;
        $finalArr['social_type'] = $user->social_type;
        $finalArr['remember_token'] = $user->remember_token;
        $finalArr['device_token'] = $user->device_token;
        // $finalArr['country_code'] = '971';
        // $finalArr['otp'] = strval($user->otp);

        // Send OTP email
        // $this->attachment_otp_register($user->email, $user->name, $user->otp);

        return response()->json([
            'code' => '1',
            'message' => 'success',
            'result' => $finalArr
        ]);
    }


    // public function social_register(Request $request)
    // {
    //     // echo "string";exit;
    //     $result = [];
    //     $otp = mt_rand(1000,9999);
    //     $uniqId = uniqid();
    //     $finalArr = [];

    //     $validator = \Validator::make($request->all(), [
    //         'name' => 'sometimes',
    //         'email' => 'required',
    //         // 'device_token' => 'required',
    //         'social_register_id' => 'required',
    //         'social_register_type' => 'required',
    //         // 'device_type' => 'required',
    //         // 'user_type' => 'required', // 1 = normal user, 2 = pro seller user
    //     ]);


    //     if ($validator->fails()) {
    //         $result['code']     =    strval(0);
    //         $result['message']  =   'missing_some_fields';
    //         $result['result'][]   =   $validator->messages();
    //         $mainResult[]       =   $result;
    //         return response()->json($mainResult);  
    //     }

    //     //register without verification

    //     $is_already_user_register = MainUser::where('email',$request->email)->where('status','2')->first();
    //     // dd($is_already_user_register);

    //     $otp = mt_rand(1000,9999);

    //     // if user is delete then here update detail of register but status remain 2
    //     if(!empty($is_already_user_register))
    //     {
    //         // dd(1);
    //         $update = MainUser::find($is_already_user_register->id);

    //         $update['name'] = isset($request->name) ? $request->name : '';
    //         $update['email'] = isset($request->email) ? $request->email : '';
    //         $update['remember_token'] = $this->generateToken();
    //         $update['is_otp_verify'] = strval(0);
    //         $update['social_type'] = isset($request->social_register_type) ? $request->social_register_type : '';
    //         $update['social_type'] = strval(@$request->social_register_type);
    //         if($request->social_register_type == 1){
    //             $update['google_id'] = strval(@$request->social_register_id);
    //         }elseif($request->social_register_type == 2){
    //             $update['facebook_id'] = strval(@$request->social_register_id);
    //         }else{
    //             $update['apple_id'] = strval(@$request->social_register_id);
    //         }

    //         $update['country_code'] = isset($request->country_code) ? $request->country_code : '971';
    //         $update['otp'] = $otp;
    //         $update['otp_expire_time'] =date('Y-m-d H:i:s', strtotime('+' . $this->minutes . 'minutes'));
    //         $update['status'] = '2';
    //         $update['updated_at'] = date('Y-m-d H:i:s');

    //         // $update['phone_number'] = isset($request->mobile) ? $request->mobile : '';
    //         // $update['password'] = isset($request->password) ? Hash::make($request->password) : '';
    //         // $update['firebase_token'] = isset($request->firebase_token) ? $request->firebase_token : '';
    //         // $update['device_id'] = isset($request->device_id) ? $request->device_id : '';
    //         // $update['device_type'] = isset($request->device_type) ? $request->device_type : '';


    //         if ($update->save()) {

    //             if ($request->has('device_id') && $request->device_id !== null) {
    //                 $authenticatedUserId = $update->id; 

    //                 // Update the cart records where the device ID matches the provided device ID
    //             }

    //             $finalArr['user_id'] = isset($update->id) ? strval($update->id) : '';

    //             $finalArr['token'] = isset($update->remember_token) ? strval($update->remember_token) : '';
    //             $finalArr['email'] = isset($update->email) ? strval($update->email) : '';
    //             $finalArr['social_type'] = isset($update->social_type) ? strval($update->social_type) : '';
    //             $finalArr['name'] = isset($update->name) ? strval($update->name) : '';
    //             $finalArr['country_code'] = '971';
    //             $finalArr['otp'] = isset($update->otp) ?  strval($update->otp) : '';
    //             // $finalArr['mobile'] = isset($update->phone_number) ? strval($update->phone_number) : '';


    //             $ismail = $this->attachment_otp_register($update->email,$update->name,$update->otp);

    //             $result['code']     =    strval(1);
    //             $result['message']  =   'success';
    //             $result['result'][]   =   $finalArr;
    //             $mainResult[]       =   $result;
    //             return response()->json($mainResult);

    //         } else {

    //             $result['code']     =    strval(-5);
    //             $result['message']  =   'something_went_wrong';
    //             $result['result']   =  [];
    //             $mainResult[]       =   $result;
    //             return response()->json($mainResult);
    //         }
    //     } else {
    //         // dd(2);
    //         $email_check =  $this->check_email($request->email);
    //         $mobile_check =  $this->check_mobile($request->mobile);

    //         if ($email_check == 1 && $mobile_check == 1){

    //             $update = MainUser::where('email',$request->email)->first();
    //             // dd($update);
    //             // echo "<pre>";print_r($update);exit;

    //             $update['name'] = isset($request->name) ? $request->name : '';
    //             $update['email'] = isset($request->email) ? $request->email : '';
    //             $update['is_otp_verify'] = strval(0);
    //             $update['remember_token'] = $this->generateToken();
    //             $update['social_type'] = isset($request->social_type) ? $request->social_type : '';
    //             $update['social_type'] = strval(@$request->social_register_type);
    //             if($request->social_register_type == 1){
    //                 $update['google_id'] = strval(@$request->social_register_id);
    //             }elseif($request->social_register_type == 2){
    //                 $update['facebook_id'] = strval(@$request->social_register_id);
    //             }else{
    //                 $update['apple_id'] = strval(@$request->social_register_id);
    //             }
    //             $update['country_code'] = isset($request->country_code) ? $request->country_code : '971';

    //             $update['otp'] = $otp;
    //             $update['otp_expire_time'] =date('Y-m-d H:i:s', strtotime('+' . $this->minutes . 'minutes'));
    //             $update['status'] = '1';
    //             $update['updated_at'] = date('Y-m-d H:i:s');
    //             // $update['firebase_token'] = isset($request->firebase_token) ? $request->firebase_token : '';
    //             // $update['device_id'] = isset($request->device_id) ? $request->device_id : '';
    //             // $update['phone_number'] = isset($request->mobile) ? $request->mobile : '';
    //             // $update['password'] = isset($request->password) ? Hash::make($request->password) : '';


    //             if ($update->save()) {

    //                 if ($request->has('device_id') && $request->device_id !== null) {
    //                     $authenticatedUserId = $update->id; 

    //                     // Update the cart records where the device ID matches the provided device ID
    //                 }

    //                 $finalArr['user_id'] = isset($update->id) ? strval($update->id) : '';

    //                 $finalArr['token'] = isset($update->remember_token) ? strval($update->remember_token) : '';
    //                 $finalArr['email'] = isset($update->email) ? strval($update->email) : '';
    //                 $finalArr['social_type'] = isset($update->social_type) ? strval($update->social_type) : '';
    //                 $finalArr['name'] = isset($update->name) ? strval($update->name) : '';
    //                 $finalArr['country_code'] = '971';
    //                 $finalArr['otp'] = isset($update->otp) ?  strval($update->otp) : '';
    //                 // $finalArr['mobile'] = isset($update->phone_number) ? strval($update->phone_number) : '';

    //                 $ismail = $this->attachment_otp_register($update->email,$update->name,$update->otp);


    //                 $result['code']     =    strval(1);
    //                 $result['message']  =   'success';
    //                 $result['result'][]   =   $finalArr;
    //                 $mainResult[]       =   $result;
    //                 return response()->json($mainResult);

    //             } else {

    //                 $result['code']     =    strval(-5);
    //                 $result['message']  =   'something_went_wrong';
    //                 $result['result']   =  [];
    //                 $mainResult[]       =   $result;
    //                 return response()->json($mainResult);
    //             }
    //         }

    //     }

    //     $post = $request->all();

    //     if($post)
    //     {
    //         // dd(3);
    //         $post['name'] = isset($request->name) ? $request->name : '';
    //         $post['email'] = isset($request->email) ? $request->email : '';
    //         $post['country_code'] = '+971';
    //         $post['device_token'] = $this->generateToken();
    //         $post['social_type'] = isset($request->social_register_type) ? $request->social_register_type : '';
    //         $post['social_type'] = strval(@$request->social_register_type);
    //         if($request->social_register_type == 1){
    //             $post['google_id'] = strval(@$request->social_register_id);
    //         }elseif($request->social_register_type == 2){
    //             $post['facebook_id'] = strval(@$request->social_register_id);
    //         }else{
    //             $post['apple_id'] = strval(@$request->social_register_id);
    //         }

    //         $post['is_otp_verify'] = strval(0);
    //         $post['otp'] = $otp;
    //         $post['status'] = '1';
    //         $post['created_at'] = date('Y-m-d H:i:s');
    //         $post['updated_at'] = date('Y-m-d H:i:s');
    //         // $post['phone_number'] = isset($request->mobile) ? $request->mobile : '';
    //         // $post['firebase_token'] = isset($request->firebase_token) ? $request->firebase_token : '';
    //         // $post['name'] = isset($request->username) ? $request->username : '';
    //         // $post['device_id'] = isset($request->device_id) ? $request->device_id : '';
    //         // $post['device_type'] = isset($request->device_type) ? $request->device_type : '';

    //         // $post['otp_expire_time'] =date('Y-m-d H:i:s', strtotime('+' . $this->minutes . 'minutes'));

    //         $FrontUser =  New MainUser($post);

    //         if($FrontUser->save())
    //         {

    //             // dd(4);
    //             if ($request->has('device_id') && $request->device_id !== null) {
    //                 $authenticatedUserId = $FrontUser->id; 

    //                 // Update the cart records where the device ID matches the provided device ID
    //             }

    //             $finalArr['user_id'] = isset($FrontUser->id) ?  strval($FrontUser->id) : '';
    //             $finalArr['token'] = isset($FrontUser->device_token) ?  strval($FrontUser->device_token) : '';
    //             $finalArr['email'] = isset($FrontUser->email) ?  strval($FrontUser->email) : '';
    //             $finalArr['name'] = isset($FrontUser->name) ? strval($FrontUser->name) : '';

    //             // $finalArr['mobile'] = isset($FrontUser->phone_number) ?  strval($FrontUser->phone_number) : '';
    //             // $finalArr['country_code'] = '971';
    //             // $finalArr['social_type'] = isset($FrontUser->social_type) ?  strval($FrontUser->social_type) : '';
    //             // $finalArr['otp'] = isset($FrontUser->otp) ?  strval($FrontUser->otp) : '';


    //             // $ismail = $this->attachment_otp_register($FrontUser->email,$FrontUser->name,$FrontUser->otp);


    //             $result['code']     =    strval(1);
    //             $result['message']  =   'success';
    //             $result['result'][]   =   $finalArr;
    //             $mainResult[]       =   $result;
    //             return response()->json($mainResult);

    //         } else {

    //             $result['code']     =    strval(-5);
    //             $result['message']  =   'something_went_wrong';
    //             $result['result']   =  [];
    //             $mainResult[]       =   $result;
    //             return response()->json($mainResult);
    //         }
    //     } else {

    //         $result['code']     =    strval(-5);
    //         $result['message']  =   'something_went_wrong';
    //         $result['result']   =  [];
    //         $mainResult[]       =   $result;
    //         return response()->json($mainResult);
    //     }
    // }

    public function login(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'email_mobile' => 'required',
            'password' => 'required',
            'device_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();
        
        if ($post) {
            $customerData_detail = MainUser::where('email', $request->email_mobile)->orWhere('phone', $request->email_mobile)->where('status', '!=', 2)->where('is_otp_verify', 1)->latest()->first();
            if (!empty($customerData_detail)) {
                $validate = Hash::check($request->input('password'), $customerData_detail->password);
                if (!$validate) {
                    $errorMessage = [];
                    $errorMessage['code'] = strval(-4);
                    $errorMessage['message'] = 'email_or_password_is_incorrect';
                    $errorMessage['result'] = Null;
                    $mainResult = $errorMessage;
                    return response()->json($mainResult);
                }

                if ($customerData_detail->is_verify_user != 1) {
                    // $response = \App\Helpers\ResponseHelper::userCommonResponse($customerData_detail);
                    $userdata = MainUser::where('id', $customerData_detail->id)->first();

                    $response['email'] = strval(@$userdata->email ?: '');
                    $response['uniqid'] = strval(@$userdata->uniqid ?: '');
                    $response['remember_token'] = strval(@$userdata->remember_token ?: '');

                    $result['code'] = strval(-5);
                    $result['message'] = 'verify_otp_first';
                    $result['result'] = $response;
                    $mainResult = $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                } elseif ($customerData_detail->status == '0') {
                    $result['code'] = strval(-6);
                    $result['message'] = 'inactive_account';
                    $result['result'] = [];
                    $mainResult = $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                } elseif ($customerData_detail->status == '2') {
                    $result['code'] = strval(-6);
                    $result['message'] = 'profile_deleted_inactive';
                    $result['result'] = [];
                    $mainResult = $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                } elseif ($customerData_detail->status == '2' && $customerData_detail->deleted_by == '1') {
                    $result['code'] = strval(-7);
                    $result['message'] = 'account_not_register_with_us';
                    $result['result'] = [];
                    $mainResult = $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                } elseif ($customerData_detail->status == '2' && $customerData_detail->deleted_by == '2') {
                    $result['code'] = strval(-7);
                    $result['message'] = 'account_deleted_by_admin';
                    $result['result'] = [];
                    $mainResult = $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                } else {
                    $token = $this->generateToken();
                    $token_update = MainUser::where('id', $customerData_detail->id)->update(
                        array(
                            'remember_token' => $token,
                            'is_phone' => $request->device_type,
                            'device_token' => $request->firebase_token,
                        )
                    );
                    $userdata = MainUser::where('id', $customerData_detail->id)->first();
                    // $response = \App\Helpers\ResponseHelper::userCommonResponse($userdata);

                    $response['email'] = strval(@$userdata->email ?: '');
                    $response['uniqid'] = strval(@$userdata->uniqid ?: '');
                    $response['remember_token'] = strval(@$userdata->remember_token ?: '');

                    $result['code'] = strval(1);
                    $result['message'] = 'success';
                    $result['result'] = $response;

                    $mainResult = $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
            } else {
                $result['code'] = strval(-4);
                $result['message'] = 'email_or_password_is_incorrect';
                $result['result'] = [];
                // echo "<pre>";print_r($result);exit();
                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function otpVerification(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            // 'uniqid' => 'required',
            // 'token' => 'required',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        // dd($response);   
        // echo "<pre>";print_r();exit();
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        $post = $request->all();

        if ($post) {
            $userdata = MainUser::where('uniqid', $request->uniqid)->where('otp', $request->otp)->where('remember_token', $request->token)->latest()->first();
            if (!empty($userdata) && $userdata != "") {

                if ($userdata->otp_expire_time <= date('Y-m-d H:i:s')) {
                    $result['code'] = strval(-9);
                    $result['message'] = 'otp_expired';
                    $result['result'] = NULL;
                    $mainResult = $result;

                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }

                $update = MainUser::where('uniqid', $userdata->uniqid)->update([
                    'is_verify_user' => 1,
                    'is_otp_verify' => 1,
                    'is_phone' => @$request->device_type,
                    'device_token' => @$request->firebase_token,
                ]);

                $ismail = $this->sendRegisterToUser($userdata);
                $response = [
                    'otp' => strval(@$userdata->otp ?: ''),
                    'otp_expire_time' => strval(@$userdata->otp_expire_time ?: ''),
                    'uniqid' => strval(@$userdata->uniqid ?: ''),
                ];

                $result['code'] = strval(1);
                $result['message'] = 'user_register_successfully';
                $result['result'] = $response;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(-8);
                $result['message'] = 'otp_not_match';
                $result['result'] = NULL;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function forgototpVerification(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }



        $post = $request->all();

        if ($post) {
            $userdata = MainUser::where('email', $request->email)->where('otp', $request->otp)->where('is_otp_verify', 1)->first();
            // echo "<pre>";print_r($userdata);exit();
            if (!empty($userdata) && $userdata != "") {

                if ($userdata->otp_expire_time <= date('Y-m-d H:i:s')) {
                    $result['code'] = strval(-9);
                    $result['message'] = 'otp_expired';
                    $result['result'] = [];
                    $mainResult = $result;

                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }

                $update = MainUser::where('uniqid', $userdata->uniqid)->update([
                    'is_phone' => @$request->device_type,
                    'device_token' => @$request->firebase_token,
                    'is_verify_user' => 1,
                ]);

                // $ismail = $this->sendRegisterToUser($userdata);
                $response = \App\Helpers\ResponseHelper::userCommonResponse($userdata);

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'][] = $response;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(-8);
                $result['message'] = 'opt_not_match';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function sendRegisterToUser($userData)
    {
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('9');
        $name = $userData->first_name . ' ' . $userData->last_name;


        $data = array('email' => $userData->email, 'name' => $name, 'id' => 9, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('emails.register_user', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }

    public function resendOtp(Request $request)
    {
        $result = [];
        $finalArr = [];

        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        // echo "<pre>";print_r();exit();
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        $post = $request->all();

        if ($post) {
            $setting = Setting::find(1);
            $findUser = MainUser::where('uniqid', $request->uniqid)->where('remember_token', $request->token)->where('status', '!=', 2)->first();
            $otp = mt_rand(1000, 9999);
            $otp_expire_time = Carbon::now()->addSeconds(@$setting->otp_expiration_second ?: 300);


            if (!empty($findUser) && $findUser != "") {

                $update = MainUser::where('uniqid', $request->uniqid)->update([
                    "otp" => $otp,
                    "otp_expire_time" => $otp_expire_time,
                ]);

                $userdata = MainUser::where('uniqid', $request->uniqid)->where('status', 1)->first();
                // echo "<pre>";print_r($userdata);exit();
                //  $response = \App\Helpers\ResponseHelper::userCommonResponse($userdata);
                $response = [
                    'otp' => strval(@$userdata->otp ?: ''),
                    'otp_expire_time' => strval(@$userdata->otp_expire_time ?: ''),
                    'uniqid' => strval(@$userdata->uniqid ?: ''),
                ];

                $logo = \Config::get('app.url') . 'public/assets/dashboard/images/liquor.png';
                $url_link = \URL::to("/");
                $url = $url_link . '/';
                $email = $userdata->email;
                $otp = $otp;
                $name = $userdata->first_name;

                $ismail = $this->attachment_otp_email($email, $otp, $name, $url, $logo);

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'] = $response;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                // $result['result']   =  [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function resendforgotOtp(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }



        $post = $request->all();

        if ($post) {
            $setting = Setting::find(1);
            $findUser = MainUser::where('email', $request->email)->where('status', '!=', 2)->where('is_otp_verify', 1)->first();
            $otp = mt_rand(1000, 9999);
            $otp_expire_time = Carbon::now()->addSeconds(@$setting->otp_expiration_second ?: 300);


            if (!empty($findUser) && $findUser != "") {

                $update = MainUser::where('uniqid', $findUser->uniqid)->update([
                    "otp" => $otp,
                    "otp_expire_time" => $otp_expire_time,
                ]);

                $userdata = MainUser::where('uniqid', $findUser->uniqid)->where('status', 1)->first();
                // echo "<pre>";print_r($userdata);exit();
                $ismail = $this->sendOtpToUser($userdata, $otp);
                $response = \App\Helpers\ResponseHelper::userCommonResponse($userdata);

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'][] = $response;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function forgotPassword(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'email_phone' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        if ($post) {
            $User = MainUser::where([
                ['status', '!=', 2],
                ['is_otp_verify', 1],
            ])->where('email', $request->email_phone)->orWhere('phone', $request->email_phone)->first();
            if (!empty($User)) {

                if ($User->status == 0) {

                    $result['code'] = strval(-6);
                    $result['message'] = 'email_id_inactive';
                    $result['result'] = [];

                    $mainResult = $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                } elseif ($User->status == 2) {

                    $result['code'] = strval(-7);
                    $result['message'] = 'profile_deleted_inactive';
                    $result['result'] = [];

                    $mainResult = $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                } else {

                    $setting = Setting::find(1);

                    $id = $User->id;

                    $otp = rand(1000, 9999);
                    $otp_expire_time = Carbon::now()->addSeconds(@$setting->otp_expiration_second ?: 300);

                    $token = $this->generateToken();

                    $User->otp = $otp;
                    $User->otp_expire_time = $otp_expire_time;
                    $User->remember_token = $token;

                    if ($User->save()) {
                        $ismail = $this->sendOtpToUser($User, $otp);
                        // response = \App\Helpers\ResponseHelper::userCommonResponse($User);
                        $response = [
                            'otp' => strval(@$User->otp ?: ''),
                            'otp_expire_time' => strval(@$User->otp_expire_time ?: ''),
                            'uniqid' => strval(@$User->uniqid ?: ''),
                            'remember_token' => strval(@$User->remember_token ?: '')
                        ];

                        $result['code'] = strval(1);
                        $result['message'] = 'otp_sent';
                        $result['result'] = $response;

                        $mainResult = $result;
                        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                    } else {
                        $result['code'] = strval(0);
                        $result['message'] = 'something_went_wrong';
                        $result['result'] = [];

                        $mainResult = $result;
                        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                    }
                }
            } else {
                $result['code'] = strval(-10);
                $result['message'] = 'email_or_password_not_exist';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    //Forgot Password Otp Send in mail
    public function sendOtpToUser($userData, $otp)
    {
        $setting = Setting::find(1);
        // $emaildetail = EmailTemplate::find(8);
        // $from_email = $setting['from_email'];
        $name = $userData->first_name . ' ' . $userData->last_name;
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('15');

        $data = array('email' => $userData->email, 'name' => $name, 'otp' => $otp, 'id' => 15, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('emails.user_otp', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);

            $message->from($data['from_email'], $data['support_name']);
        });
    }

    public function resetPassword(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'new_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();
        if ($post) {
            $data = MainUser::where([
                ['uniqid', '=', $request->uniqid],
                ['status', '=', 1],
                // ['remember_token', '=', $request->token],
            ])->first();

            // dd($data);
            if (!empty($data)) {

                $customer = MainUser::where('uniqid', $request->uniqid)->update(
                    array(
                        'password' => Hash::make($request->new_password),
                    )
                );
                $customerData_detail = MainUser::where('uniqid', $request->uniqid)->first();
                // $response = \App\Helpers\ResponseHelper::userCommonResponse($customerData_detail);

                $result['code'] = strval(1);
                $result['message'] = 'password_reset_success';
                //  $result['result'][]       =  $response;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'something_went_wrong';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function viewProfile(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        // echo "<pre>";print_r();exit();
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            $userdata = MainUser::where('uniqid', $request->uniqid)->where('status', 1)->first();

            if (!empty($userdata)) {
                $response = \App\Helpers\ResponseHelper::userCommonResponse($userdata);

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'] = $response;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function changePassword(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'old_password' => 'required',
            'new_password' => 'required',
            // 'confirm_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $checkPassword = MainUser::where('uniqid', $request->uniqid)->first();
        if (empty($checkPassword)) {
            $result['code'] = strval(0);
            $result['message'] = 'no_data_found';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if (!(Hash::check($request->old_password, $checkPassword->password))) {
            return response()->json([
                'status_code' => strval(0),
                'message' => "current_password_is_incorrect",
                'result' => null
            ], 200);
        }

        // if ($request->new_password != $request->confirm_password) {
        //     return response()->json([
        //         'status_code' => strval(0),
        //         'error'=>"New password and confirm password doesn't match",
        //         'data' => null
        //     ], 200);
        // }

        if (strcmp($request->old_password, $request->new_password) == 0) {
            $result['code'] = strval(0);
            $result['message'] = 'new_password_old_password_not_mathced';
            $result['result'] = NULL;

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        // exit();
        $post = $request->all();

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        // echo "<pre>";print_r();exit();
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();

            if (!empty($userData)) {
                $updatepsw = MainUser::where('id', $userData->id)->update(
                    array(
                        'password' => bcrypt($request->new_password),
                    )
                );

                $result['code'] = strval(1);
                $result['message'] = 'change_password_successfully';
                // $result['result']       = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {

                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function logout(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        // exit();
        $post = $request->all();

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        // echo "<pre>";print_r();exit();
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();

            $updatepsw = MainUser::where('id', $userData->id)->update(
                array(
                    'remember_token' => '',
                    'device_token' => '',
                )
            );


            $result['code'] = strval(1);
            $result['message'] = 'success';
            // $result['result']       = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function updateProfile(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }



        // exit();
        $post = $request->all();

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        // echo "<pre>";print_r();exit();
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {

            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();

            $validator = \Validator::make($request->all(), [
                'first_name' => 'required',
                'last_name' => 'required',
                'phone' => [
                    // 'required',
                    Rule::unique('main_users')->ignore($userData->id)->where(function ($query) {
                        return $query->where('status', '!=', '2');
                    })
                ],
                'email' => [
                    // 'required',
                    Rule::unique('main_users')->ignore($userData->id)->where(function ($query) {
                        return $query->where('status', '!=', '2');
                    })
                ]
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status_code' => strval(0),
                    'error' => $validator->messages(),
                    'data' => null
                ], 200);
            }
            $updatepsw = MainUser::where('id', $userData->id)->update(
                array(
                    'first_name' => @$request->first_name,
                    'last_name' => @$request->last_name,
                    'email' => @$request->email,
                    'phone' => @$request->phone,
                    'street_address' => @$request->address,
                    'states' => @$request->state,
                    'country' => @$request->country,
                    'post_code' => @$request->zip_code,
                    'city' => @$request->city,
                    'phone_code' => @$request->phone_code,
                )
            );


            $result['code'] = strval(1);
            $result['message'] = 'success';
            // $result['result']       = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function contactUs(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required',
            'phone' => 'required',
            'message' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        // exit();
        $post = $request->all();


        if ($post) {
            $ismail = $this->attachment_contact_us_email($request->name, $request->email, $request->phone, $request->message);
            $result['code'] = strval(1);
            $result['message'] = 'success';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function attachment_contact_us_email($name, $email, $phone, $message)
    {
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('11');
        $logo = asset('assets/dashboard/images/liquor.png');

        $data = array('name' => $name, 'email' => $email, 'phone' => $phone, 'message' => $message, 'id' => 11, 'from_email' => $from_email, 'logo' => $logo, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('emails.contactus', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);

            $message->from($data['from_email'], $data['support_name']);
        });
    }

    public function getCount(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        // exit();
        $post = $request->all();


        if ($post) {
            $mainArr = [];
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
            $user_id = $userData->id;

            $notificationCount = DB::table('notification')->where('sender_id', $user_id)->where('is_read', 0)->count();
            $cartCount = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->leftJoin('main_users', 'main_users.id', '=', 'product.supplier_id')->select('cart.*', 'product.product_image', 'product.uniqid as product_unique_id', 'product.product_name', 'main_users.store_name', 'main_users.id as store_id')->where('cart.user_id', $user_id)->where('product.status', 1)->where('cart.status', 1)->where('product.is_admin_approve', 1)->count();

            $mainArr['notification_count'] = strval(@$notificationCount);
            $mainArr['cart_count'] = strval(@$cartCount);

            $cartData = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->leftJoin('main_users', 'main_users.id', '=', 'product.supplier_id')->select('cart.*', 'product.product_image', 'product.uniqid as product_unique_id', 'product.product_name', 'main_users.store_name', 'main_users.id as store_id')->where('cart.user_id', $user_id)->where('product.status', 1)->where('cart.status', 1)->where('product.is_admin_approve', 1)->get();

            if (!empty($cartData->toArray())) {

                $cartItemCount = $cartData->count();

                $cartTotalPrice = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->leftJoin('main_users', 'main_users.id', '=', 'product.supplier_id')->where('cart.user_id', $userData->id)->where('product.status', 1)->where('cart.status', 1)->where('product.is_admin_approve', 1)->sum('cart.product_price');
                $cartDiscountPrice = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->leftJoin('main_users', 'main_users.id', '=', 'product.supplier_id')->where('cart.user_id', $userData->id)->where('product.status', 1)->where('cart.status', 1)->where('product.is_admin_approve', 1)->sum('cart.offer_price');
                // echo "<pre>";print_r($cartData->toArray());exit();
                if ($cartDiscountPrice != 0) {
                    $totalDicountPrice = @$cartTotalPrice - @$cartDiscountPrice;
                    $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;
                } else {
                    $totalDicountPrice = @$cartDiscountPrice;
                    $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;
                }
                // $totalDicountPrice = @$cartTotalPrice - @$cartDiscountPrice;

                // $totalCartAmount = @$cartTotalPrice - @$totalDicountPrice;

                // $mainArr = [];

                $mainArr['cartTotalPrice'] = strval(round(@$cartTotalPrice, 2));
                $mainArr['totalCartAmount'] = strval(round(@$totalCartAmount, 2));
                $mainArr['totalDicountPrice'] = strval(round(@$totalDicountPrice, 2));

                // echo "<pre>";print_r($cartData->toArray());exit();
                $cartArr = [];
                foreach ($cartData as $cart) {
                    $cartList['cart_id'] = strval(@$cart->id);
                    $cartList['product_id'] = strval(@$cart->product_id);
                    $cartList['store_id'] = strval(@$cart->store_id);
                    $cartList['store_name'] = strval(@$cart->store_name);
                    $cartList['product_name'] = strval(@$cart->product_name);
                    $cartList['product_img'] = strval(@$cart->product_image ? asset(PRODUCT_PATH . $cart->product_image) : '');
                    $cartList['quantity'] = strval(@$cart->quantity);
                    $cartList['product_discounted_price'] = strval(round(@$cart->total_price, 2));
                    $cartList['product_price'] = strval(@$cart->product_price);
                    $cartArr[] = $cartList;
                }
                $mainArr['cart_list'] = $cartArr;

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'][] = $mainArr;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function deleteAccount(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();
        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
            $user_id = $userData->id;
            if (!empty($userData)) {
                $deleteCustomerData = MainUser::where('id', $user_id)->update(
                    array(
                        'status' => 2,
                        'deleted_by' => 1,
                    )
                );
                $result['code'] = strval(1);
                $result['message'] = 'success';
                // $result['result']   = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {

                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function orderList(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }
        $post = $request->all();
        logger()->info('request: ' . json_encode($post));

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
        if ($post) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();

            $orderData = Order::with('order_details', 'orderInfo')->where([['user_id', $userData->id], ['status', 1]])->orderBy('id', 'desc');
            //dd($orderData);
            $page = 1;
            if ($request->page != '') {
                $page = $request->page;
            }
            $orderDataCount = $orderData->get();
            $orderData = $orderData->paginate(10, ['*'], 'page', $page);

            logger()->info('orderDataCount: ' . json_encode($orderDataCount));


            if (!empty($orderData)) {

                $mainArr = [];

                $mainArr['total_records'] = strval(@$orderDataCount->count());
                $mainArr['records_per_page'] = "10";
                $mainArr['current_page'] = @$page;

                $orderArr = [];
                //dd($orderData );
                foreach ($orderData as $data) {
                    $order_details = $data->order_details->first();
                    $unit = Helper::getUnitById(@$order_details->variant_unit);
                    $product_info = Helper::getProductDetails(@$order_details->product_id);
                    $order_status = DB::table('order_status')->where('id', @$data->order_status)->first();
                    // $product_image = $product_info->get_product_images->first();
                    // if (file_exists(public_path() . '/uploads/product/' . $product_image->image)) {
                    //     $image_path = asset('uploads/product/' . $product_image->image);
                    // } else {
                    //     $image_path = asset('assets/frontend/images/image-not-avilable.png');
                    // }

                    if ($product_info && $product_info->get_product_images && $product_info->get_product_images->first()) {
                        $product_image = $product_info->get_product_images->first();
                        if (!empty($product_image->image) && file_exists(public_path('uploads/product/' . $product_image->image))) {
                            $image_path = asset('uploads/product/' . $product_image->image);
                        } else {
                            $image_path = asset('assets/frontend/images/image-not-avilable.png');
                        }
                    } else {
                        $image_path = asset('assets/frontend/images/image-not-avilable.png');
                    }

                    $orderDate = $data->order_date;
                    $ordersDate = \Helper::dateTz($orderDate);
                    $newDate = date("d-m-Y", strtotime($orderDate));
                    $date = \Carbon\Carbon::createFromFormat('d-m-Y', $newDate);
                    $daysToAdd = Helper::Settings('delivery_days');
                    $date1 = $date->addDays($daysToAdd);
                    //$delivery_date = date("d F Y", strtotime($date1));
                    $delivery_date = \Helper::dateTz($date1);

                    if ($request->language == 1) {
                        $title = ($product_info->product_name_fr) ? $product_info->product_name_fr : $product_info->product_name;
                        $status = ($order_status->name_fr) ? $order_status->name_fr : $order_status->name;
                    } else {
                        $title = $product_info->product_name ?? '';
                        $status = $order_status->name ?? '';
                    }

                    $orderArr[] = [
                        "order_number" => strval(@$data->order_id),
                        "order_id" => strval(@$data->id),
                        "order_date_time" => strval(@$ordersDate),
                        "order_status" => strval(@$status),
                        "note" => strval(@$data->note ? @$data->note : ''),
                        "delivery_date" => strval(@$delivery_date),
                        "product_name" => strval(@$title),
                        "product_quantity" => strval(@$order_details->quantity),
                        "product_size" => strval(@$order_details->variant_size . ' ' . $unit),
                        "product_intial_price" => strval(@$order_details->product_original_amount),
                        "product_discounted_price" => strval(@$order_details->product_total_amount),
                        "product_image" => $image_path,
                        "order_type" => strval(@$data->order_type),
                        "payable_amount" => strval(@$data->payable_amount),
                    ];
                }

                $mainArr['order_list'] = $orderArr;

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'] = $mainArr;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    // public function orderDetail(Request $request)
    // {
    //     $result = [];
    //     $finalArr = [];
    //     $validator = \Validator::make($request->all(), [
    //         'uniqid' => 'required',
    //         // 'token' => 'required',
    //         'check_status' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status_code' => strval(0),
    //             'error' => $validator->messages(),
    //             'data' => null
    //         ], 200);
    //     }

    //     $post = $request->all();
    //     // $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
    //     // if ($response['code'] != 1) {
    //     //     $mainResult = $response;
    //     //     return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     // }

    //     if ($post) {
    //         $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
    //         if ($request->check_status == 1) {

    //             $validator = \Validator::make($request->all(), [
    //                 'order_number' => 'required',
    //             ]);
    //             if ($validator->fails()) {
    //                 return response()->json([
    //                     'status_code' => strval(0),
    //                     'error' => $validator->messages(),
    //                     'data' => null
    //                 ], 200);
    //             }
    //             $orderInformation = Order::with('order_details', 'orderInfo', 'transcations')->where('order_id', $request->order_number)->where('user_id', $userData->id)->first();
    //         } else {
    //             $validator = \Validator::make($request->all(), [
    //                 'order_id' => 'required',
    //             ]);
    //             if ($validator->fails()) {
    //                 return response()->json([
    //                     'status_code' => strval(0),
    //                     'error' => $validator->messages(),
    //                     'data' => null
    //                 ], 200);
    //             }
    //             $orderInformation = Order::with('order_details', 'orderInfo', 'transcations')->where('id', $request->order_id)->where('user_id', $userData->id)->first();
    //         }

    //         // Get Invoice Details
    //         $print_order_id = $request->order_id;
    //         $fileName = 'order_' . $print_order_id . '.pdf';
    //         $directory = public_path('uploads/invoices');
    //         $filePath = $directory . '/' . $fileName;
    //         $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
    //         $publicUrl="";

    //         if (file_exists($filePath)) {
    //             unlink($filePath);  
    //         }

    //         if (!file_exists($filePath)) {
    //             $orderData = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
    //             ->join('order_info', 'order_info.order_id', '=', 'order.id')
    //             ->join('order_status', 'order_status.id', '=', 'order.status')
    //             ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
    //             ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
    //             ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
    //             ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_email', 'order_info.delivery_fee as delivery_fee','order_info.reward_amount' ,'order_info.order_from', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'transactions.payment_type', 'transactions.trans_no', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status', 'order_detail.variant_size', 'order_detail.variant_unit', 'order_detail.is_bogo','order_info.promocode_name', 'order_info.store_pickup_address', 'order_info.delivery_fee','main_users.first_name','main_users.last_name','main_users.email','main_users.phone','main_users.phone_code')->where('order.id', $print_order_id)->first();

    //             if (!$orderData) {
    //                 return response()->json([
    //                     'status_code' => '0',
    //                     'message' => 'Order not found',
    //                     'data' => null
    //                 ], 404);
    //             }


    //             $settings = Setting::find(1);

    //             $orderDetails = DB::table('order_detail')->leftjoin('order', 'order.id', '=', 'order_detail.order_id')
    //             ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
    //             ->select('order_detail.*', 'product.product_name', 'product.retail_price', 'product.discount_price')
    //             ->where('order_detail.order_id', $print_order_id)
    //             ->get();

    //             $taxes=DB::table('tax_masters')->get();


    //             // Bogo Discount Calculation
    //             $bogoDiscount = 0;
    //             $totalAmount=0;

    //             foreach ($orderDetails as $data) {
    //                 if ($data->is_bogo) {
    //                     $display_qty = floor($data->quantity / 2);

    //                     $unit_price = ($data->product_total_amount && $data->product_total_amount > 0)
    //                         ? $data->product_total_amount
    //                         : $data->product_original_amount;

    //                     $bogoDiscount += $display_qty * $unit_price;
    //                 }
    //             }

    //             foreach ($orderDetails as $data) {
    //                 $display_qty =  $data->quantity;

    //                 $unit_price = ($data->product_total_amount && $data->product_total_amount > 0)
    //                     ? $data->product_total_amount
    //                     : $data->product_original_amount;

    //                 $totalAmount += $display_qty * $unit_price;
    //             }

    //             // Tax calculation
    //             $discountAmount = $orderData->discount_amount ?? 0;
    //             $cartDiscount = $orderData->cart_discount ?? 0;
    //             $rewardAmount = $orderData->reward_amount ?? 0;

    //             $totalDiscount = $discountAmount + $cartDiscount + $rewardAmount + $bogoDiscount;

    //             $subTotal = ($totalAmount - $totalDiscount) / 1.219;

    //             $covidLevy=($taxes[0]->tax_value/100)*($subTotal);
    //             $nhil=($taxes[1]->tax_value/100)*($subTotal);
    //             $getFund=($taxes[2]->tax_value/100)*($subTotal);
    //             $vat=($taxes[3]->tax_value/100)*( $subTotal + $covidLevy + $nhil + $getFund);

    //             // Generate PDF
    //             $pdf = Pdf::loadView('frontend.layouts.orders', compact('orderData','orderDetails','settings','covidLevy','nhil','getFund','vat','subTotal','bogoDiscount','totalAmount'));

    //             // Make directory if not exists
    //             if (!is_dir($directory)) {
    //                 mkdir($directory, 0777, true);
    //             }

    //             file_put_contents($filePath, $pdf->output());

    //             $publicUrl = asset('uploads/invoices/' . $fileName);
    //         }


    //         if (!empty($orderInformation)) {

    //             $mainArr = [];
    //             $orderArr = [];

    //             $orderDate = $orderInformation->order_date;
    //             $ordersDate = \Helper::dateTz($orderDate);

    //             $newDate = date("d-m-Y", strtotime($orderDate));
    //             $date = \Carbon\Carbon::createFromFormat('d-m-Y', $newDate);
    //             $daysToAdd = Helper::Settings('delivery_days');
    //             $date1 = $date->addDays($daysToAdd);
    //             //$delivery_date = date("d F Y", strtotime($date1));
    //             $delivery_date = \Helper::dateTz($date1);

    //             $order_status = DB::table('order_status')->where('id', $orderInformation->order_status)->first();
    //             if ($request->language == 1) {
    //                 $status = ($order_status->name_fr) ? $order_status->name_fr : $order_status->name;
    //             } else {
    //                 $status = $order_status->name ?? '';
    //             }

    //             $order_type = "";
    //             $customer_name = "";
    //             $delivery_address = "";
    //             $mobile_number = "";
    //             if (!empty($orderInformation->order_type) && $orderInformation->order_type == 1) {
    //                 $order_type = 'Online';
    //                 if (strpos($orderInformation->delivery_address, ',|') !== false) {
    //                     $order_delivery_address = explode(',| ', $orderInformation->delivery_address);
    //                     //removing first element from array, it means name.         
    //                     $customer_name = array_shift($order_delivery_address);
    //                     $mobile_number = array_pop($order_delivery_address);
    //                     $delivery_address = implode(', ', $order_delivery_address);
    //                 }
    //             } else {

    //                 $order_type = 'Pickup Order';
    //                 // dd($orderInformation->orderInfo->store_pickup_address);
    //                 if (strpos($orderInformation->orderInfo->store_pickup_address, ',|') !== false) {
    //                     $order_pickup_address = explode(',| ', $orderInformation->orderInfo->store_pickup_address);
    //                     //removing first element from array, it means name.         
    //                     $customer_name = array_shift($order_pickup_address);
    //                     $delivery_address = implode('', $order_pickup_address);
    //                 }

    //             }

    //             if (@$orderInformation->transcations->payment_type == 1) {
    //                 $payment_type = 'Card(Debit/Credit)';
    //             } elseif (@$orderInformation->transcations->payment_type == 2) {
    //                 $payment_type = 'Cart';
    //             } elseif (@$orderInformation->transcations->payment_type == 3) {
    //                 $payment_type = 'Cash On Delivery';
    //             } else {
    //                 $payment_type = '-';
    //             }
    //             $mainArr["id"] = strval(@$orderInformation->id);
    //             $mainArr["invoice_url"] = $publicUrl;
    //             $mainArr["order_number"] = strval(@$orderInformation->order_id);
    //             $mainArr["order_date_time"] = strval(@$ordersDate);
    //             $mainArr["delivery_date"] = strval(@$delivery_date);
    //             $mainArr["order_status"] = strval(@$status);
    //             $mainArr["order_status_id"] = strval(@$order_status->id);
    //             $mainArr["order_type"] = strval(@$order_type);
    //             $mainArr["note"] = strval(@$orderInformation->note);
    //             $mainArr["gift_card"] = strval(@$orderInformation->gift_card);
    //             $mainArr["recipientName"] = strval(@$orderInformation->recipientName);
    //             $mainArr["delivery_options"] = strval(@$orderInformation->delivery_options);
    //             $mainArr["delivery_instructions"] = strval(@$orderInformation->delivery_instructions);
    //             $mainArr["cart_discount"] = strval(@$orderInformation->cart_discount);
    //             $mainArr["giftMessage"] = strval(@$orderInformation->giftMessage);
    //             $mainArr["payment_type"] = strval(@$payment_type);
    //             $mainArr["customer_name"] = strval(@$customer_name);
    //             $mainArr["customer_phone"] = strval(@$mobile_number);
    //             $mainArr["delivery_address"] = strval(@$delivery_address);
    //             $product_original_amount = 0;
    //             $product_discount_amount = 0;
    //             foreach ($orderInformation->order_details as $data) {
    //                 $unit = Helper::getUnitById($data->variant_unit);
    //                 $product_info = Helper::getProductDetails($data->product_id);
    //                 $product_image = $product_info->get_product_images->first();
    //                 if (file_exists(public_path() . '/uploads/product/' . $product_image->image)) {
    //                     $image_path = asset('uploads/product/' . $product_image->image);
    //                 } else {
    //                     $image_path = asset('assets/frontend/images/image-not-avilable.png');
    //                 }

    //                 if ($request->language == 1) {
    //                     $title = ($product_info->product_name_fr) ? $product_info->product_name_fr : $product_info->product_name;
    //                 } else {
    //                     $title = $product_info->product_name ?? '';
    //                 }

    //                 $product_original_amount += $data->product_original_amount * $data->quantity;
    //                 if ($data->product_total_amount != 0) {
    //                     $disc_price = ($data->product_original_amount - $data->product_total_amount);
    //                     $product_discount_amount += $disc_price * $data->quantity;
    //                 }

    //                 $ratingData = Helper::getProductsRatingOrderId($userData->id, $data->product_id, $orderInformation->id);

    //                 if ($orderInformation->order_status == 3) {
    //                     $rating = (@$ratingData->ratings) ? @$ratingData->ratings : '';
    //                     $review = (@$ratingData->review) ? @$ratingData->review : '';
    //                 }

    //                 $orderArr[] = [
    //                     "product_id" => strval(@$data->product_id),
    //                     "product_name" => strval(@$title),
    //                     "product_quantity" => strval(@$data->quantity),
    //                     "product_size" => strval(@$data->variant_size . ' ' . $unit),
    //                     "product_intial_price" => strval(@$data->product_original_amount),
    //                     "product_discounted_price" => strval(@$data->product_total_amount),
    //                     "product_image" => $image_path,
    //                     "product_rating" => strval(@$rating),
    //                     "product_review" => strval(@$review),
    //                 ];
    //             }
    //             $variant_original_price = $product_original_amount;
    //             $discount_price = $product_discount_amount;

    //             $mainArr['total_price'] = strval(@$variant_original_price);
    //             $mainArr['discount_price'] = strval(@$discount_price);
    //             $mainArr['tax'] = ($orderInformation->tax) ? strval($orderInformation->tax) : Null;
    //             $mainArr['coupon_discount'] = ($orderInformation->discount_amount) ? strval($orderInformation->discount_amount) : Null;
    //             $mainArr['delivery_fee'] = ($orderInformation->orderInfo->delivery_fee) ? strval($orderInformation->orderInfo->delivery_fee) : Null;
    //             $mainArr['paid_amount'] = ($orderInformation->payable_amount) ? strval($orderInformation->payable_amount) : Null;

    //             $mainArr['product_count'] = count($orderInformation->order_details);
    //             $mainArr['order_list'] = $orderArr;

    //             $result['code'] = strval(1);
    //             $result['message'] = 'success';
    //             $result['result'] = $mainArr;

    //             $mainResult = $result;
    //             return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //         } else {
    //             $result['code'] = strval(0);
    //             $result['message'] = 'no_data_found';
    //             $result['result'] = [];

    //             $mainResult = $result;
    //             return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //         }

    //     } else {
    //         $result['code'] = strval(0);
    //         $result['message'] = 'something_went_wrong';
    //         $result['result'] = [];

    //         $mainResult = $result;
    //         return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     }
    // }

    // 2nd sept
    public function orderDetail(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'check_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();
        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
            if ($request->check_status == 1) {

                $validator = \Validator::make($request->all(), [
                    'order_number' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => $validator->messages(),
                        'data' => null
                    ], 200);
                }
                $orderInformation = Order::with('order_details', 'orderInfo', 'transcations')->where('order_id', $request->order_number)->where('user_id', $userData->id)->first();
            } else {
                $validator = \Validator::make($request->all(), [
                    'order_id' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => $validator->messages(),
                        'data' => null
                    ], 200);
                }
                $orderInformation = Order::with('order_details', 'orderInfo', 'transcations')->where('id', $request->order_id)->where('user_id', $userData->id)->first();
            }

            // Get Invoice Details
            $print_order_id = $request->order_id;
            $fileName = 'order_' . $print_order_id . '.pdf';
            $directory = public_path('uploads/invoices');
            $filePath = $directory . '/' . $fileName;
            $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);
            $publicUrl = "";

            if (file_exists($filePath)) {
                unlink($filePath);
            }

            if (!file_exists($filePath)) {
                $orderData = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
                    ->join('order_info', 'order_info.order_id', '=', 'order.id')
                    ->join('order_status', 'order_status.id', '=', 'order.status')
                    ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
                    ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
                    ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
                    ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_email', 'order_info.delivery_fee as delivery_fee', 'order_info.reward_amount', 'order_info.order_from', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'transactions.payment_type', 'transactions.trans_no', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status', 'order_detail.variant_size', 'order_detail.variant_unit', 'order_detail.is_bogo', 'order_info.promocode_name', 'order_info.store_pickup_address', 'order_info.delivery_fee', 'main_users.first_name', 'main_users.last_name', 'main_users.email', 'main_users.phone', 'main_users.phone_code')->where('order.id', $print_order_id)->first();

                if (!$orderData) {
                    return response()->json([
                        'status_code' => '0',
                        'message' => 'Order not found',
                        'data' => null
                    ], 404);
                }


                $settings = Setting::find(1);

                $orderDetails = DB::table('order_detail')->leftjoin('order', 'order.id', '=', 'order_detail.order_id')
                    ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
                    ->select('order_detail.*', 'product.product_name', 'product.retail_price', 'product.discount_price')
                    ->where('order_detail.order_id', $print_order_id)
                    ->get();

                $taxes = DB::table('tax_masters')->get();


                // Bogo Discount Calculation
                $bogoDiscount = 0;
                $totalAmount = 0;

                foreach ($orderDetails as $data) {
                    if ($data->is_bogo) {
                        $display_qty = floor($data->quantity / 2);

                        $unit_price = ($data->product_total_amount && $data->product_total_amount > 0)
                            ? $data->product_total_amount
                            : $data->product_original_amount;

                        $bogoDiscount += $display_qty * $unit_price;
                    }
                }

                foreach ($orderDetails as $data) {
                    $display_qty =  $data->quantity;

                    $unit_price = ($data->product_total_amount && $data->product_total_amount > 0)
                        ? $data->product_total_amount
                        : $data->product_original_amount;

                    $totalAmount += $display_qty * $unit_price;
                }

                // Tax calculation
                $discountAmount = $orderData->discount_amount ?? 0;
                $cartDiscount = $orderData->cart_discount ?? 0;
                $rewardAmount = $orderData->reward_amount ?? 0;

                $totalDiscount = $discountAmount + $cartDiscount + $rewardAmount + $bogoDiscount;

                $subTotal = ($totalAmount - $totalDiscount) * 0.83333;

                //$covidLevy = ($taxes[0]->tax_value / 100) * ($subTotal);
                $nhil = ($taxes[1]->tax_value / 100) * ($subTotal);
                $getFund = ($taxes[2]->tax_value / 100) * ($subTotal);
                $vat = ($taxes[3]->tax_value / 100) * ($subTotal);

                // Generate PDF
                $pdf = Pdf::loadView('frontend.layouts.orders', compact('orderData', 'orderDetails', 'settings', 'nhil', 'getFund', 'vat', 'subTotal', 'bogoDiscount', 'totalAmount'));

                // Make directory if not exists
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }

                file_put_contents($filePath, $pdf->output());

                //$publicUrl = asset('uploads/invoices/' . $fileName);
                $publicUrl = asset('uploads/invoices/' . $fileName) . '?v=' . time();
            }


            if (!empty($orderInformation)) {

                $mainArr = [];
                $orderArr = [];

                $orderDate = $orderInformation->order_date;
                $ordersDate = \Helper::dateTz($orderDate);

                $newDate = date("d-m-Y", strtotime($orderDate));
                $date = \Carbon\Carbon::createFromFormat('d-m-Y', $newDate);
                $daysToAdd = Helper::Settings('delivery_days');
                $date1 = $date->addDays($daysToAdd);
                //$delivery_date = date("d F Y", strtotime($date1));
                $delivery_date = \Helper::dateTz($date1);

                $order_status = DB::table('order_status')->where('id', $orderInformation->order_status)->first();
                if ($request->language == 1) {
                    $status = ($order_status->name_fr) ? $order_status->name_fr : $order_status->name;
                } else {
                    $status = $order_status->name ?? '';
                }

                $order_type = "";
                $customer_name = "";
                $delivery_address = "";
                $mobile_number = "";
                if (!empty($orderInformation->order_type) && $orderInformation->order_type == 1) {
                    $order_type = 'Online';
                    if (strpos($orderInformation->delivery_address, ',|') !== false) {
                        $order_delivery_address = explode(',| ', $orderInformation->delivery_address);
                        //removing first element from array, it means name.         
                        $customer_name = array_shift($order_delivery_address);
                        $mobile_number = array_pop($order_delivery_address);
                        $delivery_address = implode(', ', $order_delivery_address);
                    }
                } else {

                    $order_type = 'Pickup Order';
                    // dd($orderInformation->orderInfo->store_pickup_address);
                    if (strpos($orderInformation->orderInfo->store_pickup_address, ',|') !== false) {
                        $order_pickup_address = explode(',| ', $orderInformation->orderInfo->store_pickup_address);
                        //removing first element from array, it means name.         
                        $customer_name = array_shift($order_pickup_address);
                        $delivery_address = implode('', $order_pickup_address);
                    }
                }

                if (@$orderInformation->transcations->payment_type == 1) {
                    $payment_type = 'Card(Debit/Credit)';
                } elseif (@$orderInformation->transcations->payment_type == 2) {
                    $payment_type = 'Cart';
                } elseif (@$orderInformation->transcations->payment_type == 3) {
                    $payment_type = 'Cash On Delivery';
                } else {
                    $payment_type = '-';
                }
                $mainArr["id"] = strval(@$orderInformation->id);
                $mainArr["invoice_url"] = $publicUrl;
                $mainArr["order_number"] = strval(@$orderInformation->order_id);
                $mainArr["order_date_time"] = strval(@$ordersDate);
                $mainArr["delivery_date"] = strval(@$delivery_date);
                $mainArr["order_status"] = strval(@$status);
                $mainArr["order_status_id"] = strval(@$order_status->id);
                $mainArr["order_type"] = strval(@$order_type);
                $mainArr["note"] = strval(@$orderInformation->note);
                $mainArr["gift_card"] = strval(@$orderInformation->gift_card);
                $mainArr["recipientName"] = strval(@$orderInformation->recipientName);
                $mainArr["delivery_options"] = strval(@$orderInformation->delivery_options);
                $mainArr["delivery_instructions"] = strval(@$orderInformation->delivery_instructions);
                $mainArr["cart_discount"] = strval(@$orderInformation->cart_discount);
                $mainArr["giftMessage"] = strval(@$orderInformation->giftMessage);
                $mainArr["payment_type"] = strval(@$payment_type);
                $mainArr["customer_name"] = strval(@$customer_name);
                $mainArr["customer_phone"] = strval(@$mobile_number);
                $mainArr["delivery_address"] = strval(@$delivery_address);
                $product_original_amount = 0;
                $product_discount_amount = 0;
                $total_quantity = 0;
                foreach ($orderInformation->order_details as $data) {
                    $unit = Helper::getUnitById($data->variant_unit);
                    $product_info = Helper::getProductDetails($data->product_id);
                    $product_image = $product_info->get_product_images->first();
                    if (file_exists(public_path() . '/uploads/product/' . $product_image->image)) {
                        $image_path = asset('uploads/product/' . $product_image->image);
                    } else {
                        $image_path = asset('assets/frontend/images/image-not-avilable.png');
                    }

                    if ($request->language == 1) {
                        $title = ($product_info->product_name_fr) ? $product_info->product_name_fr : $product_info->product_name;
                    } else {
                        $title = $product_info->product_name ?? '';
                    }

                    // if bogo
                    $effectiveQty = ($data->is_bogo) ? floor($data->quantity / 2) : $data->quantity;

                    $product_original_amount += $data->product_original_amount *  $effectiveQty;
                    if ($data->product_total_amount != 0) {
                        $disc_price = ($data->product_original_amount - $data->product_total_amount);
                        $product_discount_amount += $disc_price *  $effectiveQty;
                    }

                    $ratingData = Helper::getProductsRatingOrderId($userData->id, $data->product_id, $orderInformation->id);

                    if ($orderInformation->order_status == 3) {
                        $rating = (@$ratingData->ratings) ? @$ratingData->ratings : '';
                        $review = (@$ratingData->review) ? @$ratingData->review : '';
                    }

                    $total_quantity +=  $data->quantity;


                    $orderArr[] = [
                        "product_id" => strval(@$data->product_id),
                        "product_name" => strval(@$title),
                        "product_quantity" => strval(@$data->quantity),
                        "product_size" => strval(@$data->variant_size . ' ' . $unit),
                        "product_intial_price" => strval(@$data->product_original_amount),
                        "product_discounted_price" => strval(@$data->product_total_amount),
                        "product_image" => $image_path,
                        "product_rating" => strval(@$rating),
                        "product_review" => strval(@$review),
                        "bogo_label" => ($data->is_bogo) ? 'includes free item from BOGO' : ''
                    ];
                }
                $variant_original_price = $product_original_amount;
                $discount_price = $product_discount_amount;

                $mainArr['total_price'] = strval(@$variant_original_price);
                $mainArr['total_quantity'] = $total_quantity;
                $mainArr['discount_price'] = strval(@$discount_price);
                $mainArr['tax'] = ($orderInformation->tax) ? strval($orderInformation->tax) : Null;
                $mainArr['coupon_discount'] = ($orderInformation->discount_amount) ? strval($orderInformation->discount_amount) : Null;
                $mainArr['delivery_fee'] = ($orderInformation->orderInfo->delivery_fee) ? strval($orderInformation->orderInfo->delivery_fee) : Null;
                $mainArr['reward_amount'] = ($orderInformation->orderInfo->reward_amount) ? strval($orderInformation->orderInfo->reward_amount) : '0';
                $mainArr['paid_amount'] = ($orderInformation->payable_amount) ? strval($orderInformation->payable_amount) : Null;

                $mainArr['product_count'] = count($orderInformation->order_details);
                $mainArr['order_list'] = $orderArr;

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'] = $mainArr;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function orderStatus(Request $request)
    {
        // dd(1);
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();
        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
            $orderInformation = Order::with('order_details', 'orderInfo', 'transcations')->where('id', $request->order_id)->where('user_id', $userData->id)->first();

            //dd($orderInformation);
            if (!empty($orderInformation)) {
                foreach ($orderInformation->order_details as $key => $order_detail) {
                    $order_quantity = $order_detail->quantity;
                    $varinat_id = $order_detail->variant_id;

                    $varinat_info = ProductVariants::where('id', $varinat_id)->first();
                    if ($varinat_info != "") {
                        $varinat_info->sold_qty = ($varinat_info->sold_qty - $order_quantity);
                        $varinat_info->available_qty = ($varinat_info->available_qty + $order_quantity);
                        $varinat_info->save();
                    }
                }
                $mainArr = [];
                $order_status = Order::where('id', $orderInformation->id)->update(['order_status' => 4]);
                $customer_details = DB::table('main_users')->find($orderInformation->user_id);

                if ($customer_details->user_type == 1) {
                    $currentUser = 'Customer';
                } else if ($customer_details->user_type == 2) {
                    $currentUser = 'Wholesaler';
                } else {
                    $currentUser = 'SubAdmin';
                }

                $order_status = Order::where('id', $orderInformation->id)->update([
                    'order_status' => 4,
                    'cancelled_by' => $currentUser,
                    'cancelled_user' => $orderInformation->user_id
                ]);



                // dd($customer_details);
                $setting = Setting::find(1);
                $template_id = 20;
                $emaildetail = EmailTemplate::find($template_id);
                // dd($emaildetail);
                $from_email = $setting['mail_username'];
                // dd($from_email);
                $logo = asset('assets/dashboard/images/Logo/logo.png');
                $sendemail = @$customer_details->email;
                // $sendemail = 'shubham.vrinsoft@gmail.com';
                $order = Order::leftjoin('order_status', 'order_status.id', '=', 'order.id')
                    ->select('order.*', 'order_status.name as order_status',)
                    ->where('order.id', $request->order_id)->first();
                $message = "Online Order Cancelled";
                $data = array(
                    'user_name' => @$customer_details->first_name,
                    'sendname' => @$customer_details->first_name,
                    'sendemail' => @$sendemail,
                    'id' => $template_id,
                    'order' => $order,
                    'order_status' => $message,
                    'from_email' => $from_email,
                );
                // dd($data);
                // try {
                //     // dd(5);
                //     Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data, $emaildetail) {
                //         $message->to($data['sendemail'], 'Liquor')->subject($emaildetail->subject);
                //         $message->from($data['from_email'], $emaildetail->title);
                //     });

                // } catch (\Throwable $th) {
                //     // throw $th;
                // }
                $ismail = $this->order_cancel_email($customer_details->first_name, $customer_details->first_name, $sendemail, $order, $message);

                $result['code'] = strval(1);
                $result['message'] = 'your_order_has_been_cancelled';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function order_cancel_email($user_name, $sendname, $sendemail, $order, $order_status)
    {
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('20');
        // echo "<pre>";print_r($setting->toArray());exit();
        // $from_email = $setting['from_email'];
        $data = array('user_name' => $user_name, 'sendname' => $sendname, 'sendemail' => $sendemail, 'order' => $order, 'order_status' => $order_status, 'id' => '20', 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data) {

            $message->to($data['sendemail'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }

    public function trackOrder(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();
        if ($post) {
            $orderInformation = Order::with('order_details', 'orderInfo', 'transcations')->where('order_id', $request->order_id)->first();
            //dd( $orderInformation);


            if (!empty($orderInformation)) {
                $order_status = DB::table('order_status')->where('id', $orderInformation->order_status)->first();
                if ($request->language == 1) {
                    $status_title = ($order_status->name_fr) ? $order_status->name_fr : $order_status->name;
                } else {
                    $status_title = $order_status->name;
                }

                $result['code'] = strval(1);
                $result['message'] = strval(@$status_title);

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'invalid_order_number';
                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function applyCouponCode(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }
    }

    public function addressList(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
            $UserAddressData = UserAddress::withWhereHas('country', function ($query) {
                $query->where('status', 1);
            })->withWhereHas('region', function ($query) {
                $query->where('status', 1);
            })->withWhereHas('area', function ($query) {
                $query->where('status', 1);
            })->where([['user_id', $userData->id], ['status', 1]])->get();
            if (!empty($UserAddressData)) {
                $user_address = [];
                foreach ($UserAddressData as $data) {
                    ($data->address) ? $address = ', <br>' . $data->address : '';
                    ($data->area->title) ? $address .= ', <br>' . $data->area->title : '';
                    ($data->region->title) ? $address .= ', <br>' . $data->region->title : '';
                    ($data->country->name) ? $address .= ',<br> ' . $data->country->name : '';

                    $phone_number = '(+' . $data->phonecode . ') ' . $data->phone;
                    ($phone_number) ? $address .= ',<br> ' . $phone_number : '';

                    $user_address[] = [
                        'id' => $data->id,
                        'name' => $data->name,
                        'address' => $address,
                    ];
                }

                $result['code'] = strval(1);
                $result['message'] = 'your_order_has_been_cancelled';
                $result['result'] = $user_address;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }
    public function myAddressList(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        // echo "<pre>";print_r();exit();
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();

            //$userAddress = DB::table('user_address')->where('user_id',$userData->id)->where('status',1)->get();

            $userAddress = UserAddress::withWhereHas('country', function ($query) {
                $query->where('status', 1);
            })->withWhereHas('region', function ($query) {
                $query->where('status', 1);
            })->withWhereHas('area', function ($query) {
                $query->where('status', 1);
            })->where([['user_id', $userData->id], ['status', 1]])->get();

            $UserBillAddressData = UserBillAddress::withWhereHas('country', function ($query) {
                $query->where('status', 1);
            })->withWhereHas('region', function ($query) {
                $query->where('status', 1);
            })->withWhereHas('area', function ($query) {
                $query->where('status', 1);
                // })->where([['user_id', $user_id], ['status', 1]])->get();
            })->where([['user_id', $userData->id], ['status', 1], ['default', 1]])->get();


            $mainArr = [];

            if (!empty($userAddress)) {
                $addressArr = [];
                $billAddressArr = [];
                foreach ($userAddress as $get_result) {
                    $full_address = $get_result->address ?: '';
                    $full_address .= ($get_result->area->title) ? ', ' . @$get_result->area->title : '';
                    $full_address .= ($get_result->region->title) ? ', ' . @$get_result->region->title : '';
                    $full_address .= ($get_result->country->name) ? ', ' . @$get_result->country->name : '';
                    //if()
                    //  dd($full_address);                  

                    $phone_code = '+' . @$get_result->phonecode;

                    $addressList['address_id'] = strval(@$get_result->id);
                    $addressList['customer_name'] = strval(@$get_result->name);
                    $addressList['customer_phone'] = strval(@$get_result->phone);
                    $addressList['customer_phonecode'] = strval(@$phone_code);
                    $addressList['country_id'] = strval(@$get_result->country_id);
                    $addressList['region_id'] = strval(@$get_result->region_id);
                    $addressList['area_id'] = strval(@$get_result->area_id);
                    $addressList['customer_address'] = strval(@$full_address);
                    $addressArr[] = $addressList;
                }

                // Process billing addresses
                foreach ($UserBillAddressData as $bill_result) {
                    $full_bill_address = $bill_result->address ?: '';
                    $full_bill_address .= ($bill_result->area->title) ? ', ' . @$bill_result->area->title : '';
                    $full_bill_address .= ($bill_result->region->title) ? ', ' . @$bill_result->region->title : '';
                    $full_bill_address .= ($bill_result->country->name) ? ', ' . @$bill_result->country->name : '';

                    $bill_phone_code = '+' . @$bill_result->phonecode;

                    $billAddress['bill_address_id'] = strval(@$bill_result->id);
                    $billAddress['customer_name'] = strval(@$bill_result->name);
                    $billAddress['customer_phone'] = strval(@$bill_result->phone);
                    $billAddress['customer_phonecode'] = strval(@$bill_phone_code);
                    $billAddress['city'] = strval(@$bill_result->city);
                    $billAddress['zip_code'] = strval(@$bill_result->zip_code);
                    $billAddress['country_id'] = strval(@$bill_result->country_id);
                    $billAddress['region_id'] = strval(@$bill_result->region_id);
                    $billAddress['area_id'] = strval(@$bill_result->area_id);
                    $billAddress['customer_address'] = strval(@$full_bill_address);
                    $billAddressArr[] = $billAddress;
                }

                $mainArr['address_list'] = $addressArr;
                $mainArr['billing_address_list'] = $billAddressArr;

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'] = $mainArr;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function myAddressListOld29jan(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        // echo "<pre>";print_r();exit();
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {

            $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();

            $userAddress = DB::table('user_address')->where('user_id', $userData->id)->where('status', 1)->get();
            //   dd($userAddress);
            $mainArr = [];

            if (!empty($userAddress)) {
                $addressArr = [];
                foreach ($userAddress as $address) {
                    $phone_code = '+' . @$address->phonecode;

                    $addressList['address_id'] = strval(@$address->id);
                    $addressList['customer_name'] = strval(@$address->name);
                    $addressList['customer_phone'] = strval(@$address->phone);
                    $addressList['customer_phonecode'] = strval(@$phone_code);
                    $addressList['country_id'] = strval(@$address->country_id);
                    $addressList['region_id'] = strval(@$address->region_id);
                    $addressList['area_id'] = strval(@$address->area_id);
                    $addressList['customer_address'] = strval(@$address->address);
                    $addressArr[] = $addressList;
                }

                $mainArr['address_list'] = $addressArr;

                $result['code'] = strval(1);
                $result['message'] = 'success';
                $result['result'] = $mainArr;

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code'] = strval(0);
                $result['message'] = 'no_data_found';
                $result['result'] = [];

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function addressManager(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();

        logger()->info("-------------------post response----------------------");
        logger()->info($post);

        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        logger()->info("-------------------request type----------------------");
        logger()->info($request->type);

        if ($post) {
            if ($request->type == 2) {
                $validator = \Validator::make($request->all(), [
                    'name' => 'required',
                    'phone' => 'required',
                    'phonecode' => 'required',
                    'country_id' => 'required',
                    'region_id' => 'required',
                    'area_id' => 'required',
                    'address' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => $validator->messages(),
                        'data' => null
                    ], 200);
                }

                $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
                logger()->info("-------------------User Data Edit----------------------");
                logger()->info(json_encode($userData));
                // $editAddress = UserAddress::where('id', $request->address_id)->update(
                //     array(
                //         'name' => @$request->name,
                //         'phone' => @$request->phone,
                //         'phonecode' => @$request->phonecode,
                //         'country_id' => @$request->country_id,
                //         'region_id' => @$request->region_id,
                //         'area_id' => @$request->area_id,
                //         'address' => @$request->address,
                //     )
                // );

                logger()->info("-------------------Edit address ID----------------------");
                logger()->info($request->address_id);

                $editAddress = UserAddress::find($request->address_id);

                if (!$editAddress) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => ['address' => ['Address not found']],
                        'data' => null
                    ], 200);
                }

                if ($editAddress) {
                    if ($request->has('name')) $editAddress->name = $request->name;
                    if ($request->has('phone')) $editAddress->phone = $request->phone;
                    if ($request->has('phonecode')) $editAddress->phonecode = $request->phonecode;
                    if ($request->has('country_id')) $editAddress->country_id = $request->country_id;
                    if ($request->has('region_id')) $editAddress->region_id = $request->region_id;
                    if ($request->has('area_id')) $editAddress->area_id = $request->area_id;
                    if ($request->has('address')) $editAddress->address = $request->address;
                    if ($request->has('city')) $editAddress->city = $request->city;
                    if ($request->has('zip_code')) $editAddress->zip_code = $request->zip_code;
                    $editAddress->status = 1;


                    // added on 25 aug
                    $editAddress->billing_address = isset($request->billing_address) ? $request->billing_address : '0';

                    // Delivery options
                    if ($request->has('delivery_options')) {
                        $editAddress->delivery_options = $request->delivery_options;
                    }

                    // Instruction handling
                    $editAddress->delivery_instructions = $request->has('instruction') ? $request->instruction : null;

                    $editAddress->save();
                }


                $result['code'] = strval(1);
                $result['message'] = 'address_updated_successfully';
                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } elseif ($request->type == 1) {
                $validator = \Validator::make($request->all(), [
                    'name' => 'required',
                    'phone' => 'required',
                    'phonecode' => 'required',
                    'country_id' => 'required',
                    'region_id' => 'required',
                    'area_id' => 'required',
                    'address' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => $validator->messages(),
                        'data' => null
                    ], 200);
                }

                $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
                if (!$userData) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => 'Invalid user.',
                        'data' => null
                    ], 200);
                }

                logger()->info("-------------------Add adress----------------------");
                logger()->info(json_encode($userData));

                $add_address = new UserAddress();
                $add_address->name = $request->name;
                $add_address->user_id = $userData->id;
                $add_address->phone = $request->phone;
                $add_address->phonecode = $request->phonecode;
                $add_address->area_id = $request->area_id;
                $add_address->address = $request->address;
                $add_address->country_id = $request->country_id;
                $add_address->region_id = $request->region_id;
                $add_address->zip_code = $request->input('zip_code', '');
                $add_address->city = $request->input('city', '');
                $add_address->status = 1;

                // added on 25 aug
                $add_address->is_selected_address_id = ($request->has('checkout_page') && $request->checkout_page == 0) ? 1 : 0;
                $add_address->billing_address = $request->input('billing_address', '0');
                // Delivery Options
                if ($request->filled('delivery_options')) {
                    $add_address->delivery_options = $request->delivery_options;
                }

                if ($request->filled('instruction')) {
                    $add_address->delivery_instructions = $request->instruction;
                }

                $add_address->save();

                $result['code'] = strval(1);
                $result['message'] = 'address_added_successfully';

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();

                $removeAddress = UserAddress::where('user_id', $userData->id)->where('id', $request->address_id)->update(
                    array(
                        'status' => 2,
                    )
                );
                logger()->info("-------------------removeAddress---------------------");
                logger()->info(json_encode($removeAddress));

                $result['code'] = strval(1);
                $result['message'] = 'address_removed_successfully';

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function billAddressManager(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'uniqid' => 'required',
            'token' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $post = $request->all();
        $response = \App\Helpers\ResponseHelper::userCheckStatus($request->uniqid, $request->token);
        if ($response['code'] != 1) {
            $mainResult = $response;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }

        if ($post) {
            if ($request->type == 2) {
                $validator = \Validator::make($request->all(), [
                    'name' => 'required',
                    'phone' => 'required',
                    'phonecode' => 'required',
                    'country_id' => 'required',
                    'region_id' => 'required',
                    'area_id' => 'required',
                    'address' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => $validator->messages(),
                        'data' => null
                    ], 200);
                }

                $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
                $editAddress = UserBillAddress::find($request->bill_address_id);

                if (!$editAddress) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => ['address' => ['Address not found']],
                        'data' => null
                    ], 200);
                }


                $default = intval($request->isDefault);
                $existing_default_address = UserBillAddress::where('user_id', $userData->id)
                    ->where('default', 1)
                    ->where('id', '!=', $request->bill_address_id)
                    ->first();

                // Case 1: If no other address exists with default = 1 and $request->bill_default == 0, then keep this one as default.
                if (!$existing_default_address && $default == 0) {
                    $default = 1;
                }

                // Case 2: If an existing default address exists and $request->bill_default == 1, then set that one to non-default
                if ($default == 1 && $existing_default_address) {
                    $existing_default_address->default = 0;
                    $existing_default_address->save();
                }

                if ($request->has('name')) $editAddress->name = $request->name;
                if ($request->has('phone')) $editAddress->phone = $request->phone;
                if ($request->has('phonecode')) $editAddress->phonecode = $request->phonecode;
                if ($request->has('country_id')) $editAddress->country_id = $request->country_id;
                if ($request->has('region_id')) $editAddress->region_id = $request->region_id;
                if ($request->has('area_id')) $editAddress->area_id = $request->area_id;
                if ($request->has('address')) $editAddress->address = $request->address;
                if ($request->has('city')) $editAddress->city = $request->city;
                if ($request->has('zip_code')) $editAddress->zip_code = $request->zip_code;
                $editAddress->status = 1;

                //$editAddress->default = $default;
                $editAddress->default = 1;
                $editAddress->save();

                $result['code'] = strval(1);
                $result['message'] = 'address_updated_successfully';
                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } elseif ($request->type == 1) {
                $validator = \Validator::make($request->all(), [
                    'name' => 'required',
                    'phone' => 'required',
                    'phonecode' => 'required',
                    'country_id' => 'required',
                    'region_id' => 'required',
                    'area_id' => 'required',
                    'address' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => $validator->messages(),
                        'data' => null
                    ], 200);
                }

                $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
                $default = intval($request->isDefault);

                $existing_default_address = UserBillAddress::where('user_id', $userData->id)
                    ->where('default', 1)
                    ->first();

                if ($default == 1) {
                    // If there's an existing default address, set it to non-default first
                    if ($existing_default_address) {
                        $existing_default_address->default = 0;
                        $existing_default_address->save();
                    }
                } else {
                    // If no address is being marked as default, check if we need to mark the new address as default
                    if (!$existing_default_address) {
                        // If no default address exists, set the new address as default
                        $default = 1;
                    }
                }

                $add_address = new UserBillAddress();
                $add_address->name = $request->name;
                $add_address->user_id = $userData->id;
                $add_address->phone = $request->phone;
                $add_address->phonecode = $request->phonecode;
                $add_address->area_id = $request->area_id;
                $add_address->address = $request->address;
                $add_address->country_id = $request->country_id;
                $add_address->region_id = $request->region_id;
                $add_address->zip_code = $request->input('zip_code', '');
                $add_address->city = $request->input('city', '');
                $add_address->status = 1;
                //$add_address->default = $default;
                $add_address->default = 1;

                $add_address->save();

                $result['code'] = strval(1);
                $result['message'] = 'address_added_successfully';

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $userData = DB::table('main_users')->where('uniqid', $request->uniqid)->first();
                $removeAddress = UserBillAddress::where('user_id', $userData->id)
                    ->where('id', $request->bill_address_id)
                    ->first();

                if (!$removeAddress) {
                    return response()->json([
                        'status_code' => strval(0),
                        'error' => ['address' => ['Address not found']],
                        'data' => null
                    ], 200);
                }

                $removeAddress->status = 2;
                $removeAddress->save();

                $result['code'] = strval(1);
                $result['message'] = 'address_removed_successfully';

                $mainResult = $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code'] = strval(0);
            $result['message'] = 'something_went_wrong';
            $result['result'] = [];

            $mainResult = $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    // public function getInvoice(Request $request)
    // {
    //     $result = [];
    //     $validator = \Validator::make($request->all(), [
    //         'order_id' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status_code' => strval(0),
    //             'error'=>$validator->messages(),
    //             'data' => null
    //         ], 200);
    //     }

    //     $order_id = $request->order_id;
    //     $fileName = 'order_' . $order_id . '.pdf';
    //     // $directory = storage_path('app/public/uploads/invoices');
    //     $directory = public_path('uploads/invoices');
    //     $filePath = $directory . '/' . $fileName;

    //     if (!file_exists($filePath)) {

    //         $orderData = Order::join('main_users', 'main_users.id', '=', 'order.user_id')
    //         ->join('order_info', 'order_info.order_id', '=', 'order.id')
    //         ->join('order_status', 'order_status.id', '=', 'order.status')
    //         ->leftjoin('countries', 'countries.id', '=', 'order_info.country_id')
    //         ->rightjoin('transactions', 'transactions.order_id', '=', 'order.id')
    //         ->join('order_detail', 'order_detail.order_id', '=', 'order.id')
    //         ->select('order.*', 'order_info.customer_name as customer_name', 'order_info.customer_email', 'order_info.delivery_fee as delivery_fee','order_info.reward_amount' ,'order_info.order_from', 'order_info.customer_mobile as customer_mobile', 'countries.name as country_name', 'transactions.payment_type', 'transactions.trans_no', 'transactions.payment_status as payment_status', 'order_status.name as delivery_status', 'order_detail.variant_size', 'order_detail.variant_unit', 'order_detail.is_bogo','order_info.promocode_name', 'order_info.store_pickup_address', 'order_info.delivery_fee','main_users.first_name','main_users.last_name','main_users.email','main_users.phone','main_users.phone_code')->where('order.id', $order_id)->first();

    //         if (!$orderData) {
    //             return response()->json([
    //                 'status_code' => '0',
    //                 'message' => 'Order not found',
    //                 'data' => null
    //             ], 404);
    //         }


    //         $settings = Setting::find(1);

    //         $orderDetails = DB::table('order_detail')->leftjoin('order', 'order.id', '=', 'order_detail.order_id')
    //         ->leftJoin('product', 'product.id', '=', 'order_detail.product_id')
    //         ->select('order_detail.*', 'product.product_name', 'product.retail_price', 'product.discount_price')
    //         ->where('order_detail.order_id', $order_id)
    //         ->get();

    //         $taxes=DB::table('tax_masters')->get();


    //         // Bogo Discount Calculation
    //         $bogoDiscount = 0;
    //         $totalAmount=0;

    //         foreach ($orderDetails as $data) {
    //             if ($data->is_bogo) {
    //                 $display_qty = floor($data->quantity / 2);

    //                 $unit_price = ($data->product_total_amount && $data->product_total_amount > 0)
    //                     ? $data->product_total_amount
    //                     : $data->product_original_amount;

    //                 $bogoDiscount += $display_qty * $unit_price;
    //             }
    //         }

    //         foreach ($orderDetails as $data) {
    //             $display_qty =  $data->quantity;

    //             $unit_price = ($data->product_total_amount && $data->product_total_amount > 0)
    //                 ? $data->product_total_amount
    //                 : $data->product_original_amount;

    //             $totalAmount += $display_qty * $unit_price;
    //         }

    //         // Tax calculation
    //         $discountAmount = $orderData->discount_amount ?? 0;
    //         $cartDiscount = $orderData->cart_discount ?? 0;
    //         $rewardAmount = $orderData->reward_amount ?? 0;

    //         $totalDiscount = $discountAmount + $cartDiscount + $rewardAmount + $bogoDiscount;

    //         $subTotal = ($totalAmount - $totalDiscount) / 1.219;

    //         $covidLevy=($taxes[0]->tax_value/100)*($subTotal);
    //         $nhil=($taxes[1]->tax_value/100)*($subTotal);
    //         $getFund=($taxes[2]->tax_value/100)*($subTotal);
    //         $vat=($taxes[3]->tax_value/100)*( $subTotal + $covidLevy + $nhil + $getFund);

    //         // Generate PDF
    //         $pdf = Pdf::loadView('frontend.layouts.orders', compact('orderData','orderDetails','settings','covidLevy','nhil','getFund','vat','subTotal','bogoDiscount','totalAmount'));

    //         // Make directory if not exists
    //         if (!is_dir($directory)) {
    //             mkdir($directory, 0777, true);
    //         }

    //         file_put_contents($filePath, $pdf->output());
    //     }

    //     // Generate public URL
    //     // $publicUrl = asset('storage/uploads/invoices/' . $fileName);
    //     $publicUrl = asset('uploads/invoices/' . $fileName);

    //      return response()->json([
    //         'code' => strval(1),
    //         'message' => 'success',
    //         'data' => [
    //             'invoice_url' => $publicUrl
    //         ]
    //     ], 200);

    // }


}
