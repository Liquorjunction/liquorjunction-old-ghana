<?php

// This class file to define all general functions

namespace App\Helpers;

use App;
use Carbon\Carbon;
use DB;

class ResponseHelper
{
    
    static function userCommonResponse($userdata, $login_req = false) {

        $response = [];

        $response['uniqid']        = strval(@$userdata->uniqid ?: '');
        $response['user_id']        = strval(@$userdata->id ?: '');
        $response['token']          = strval(@$userdata->remember_token ?: '');
        $response['firstname']        = strval(@$userdata->first_name ?: '');
        $response['lastname']        = strval(@$userdata->last_name ?: '');
        $response['email']          = strval(@$userdata->email ?: '');
        $response['phone_number']         = strval(@$userdata->phone ?: '');
        $response['phone_code']         = strval(@$userdata->phone_code ?: '');
        // $response['status']         = strval(@$userdata->status ?: '0');
        $response['otp']            = strval(@$userdata->otp ?: '');
        // $response['otp_expire_time']      = strval(@$userdata->otp_expire_time ?: '');
        // $response['is_verify']         = strval(@$userdata->is_verify_user ?: '0');
        // $response['country']         = strval(@$userdata->country);
        // $response['states']         = strval(@$userdata->states);
        // $response['city']         = strval(@$userdata->city);
        // $response['post_code']         = strval(@$userdata->post_code);
        // $response['street_address']         = strval(@$userdata->street_address);
        // $response['user_type']      = strval(@$userdata->user_type ?: '');
        // $response['profile_image']  = strval(@$userdata->profile ? asset( CUSTOMER_PROFILE_PATH . $userdata->profile) : '');

        return $response;

    }

    static function userCheckStatus($uniqid,$token){
            $result = [];
            $userdata = DB::table('main_users')->where('uniqid',$uniqid)->first();
            // dd($userdata);
            // echo "<pre>";print_r($uniqid);
            // echo "<pre>";print_r($token);exit();
            if (!empty($userdata)) {
            $userdata_token = DB::table('main_users')->where('uniqid',$uniqid)->where('remember_token',$token)->first();
            // dd($userdata_token);
                if (!empty($userdata_token)) {
                    if ($userdata_token->status==0) {
                        $result['code']     =  strval(-6);
                        $result['message']  =   'email_id_inactive';
                        $result['result']       =   [];
                    }elseif ($userdata_token->status==2) {
                        $result['code']     =  strval(-7);
                        $result['message']  =   'profile_deleted_inactive';
                        $result['result']       =   [];
                    }else{
                        $result['code']     =  strval(1);
                        $result['message']  =   'success';
                        $result['result']       =   [];
                    }
                }else{
                      $result['code']     =  strval(-1);
                      $result['message']  =   'invalid_token';
                      $result['result']       =   [];
                }
            }else{
                $result['code']     =  strval(-11);
                $result['message']  =   'user_not_register_our_system';
                $result['result']       =   [];
            }

            // echo "<pre>";print_r($result);exit();
            return $result;
    }
}