<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Label;
use App\Models\Setting;

class SettingController extends Controller
{
    //
    
public function version(Request $request)
{
    $setting = Setting::find(1);

    if (!$setting) {
        return response()->json([
            'code' => "0",
            'message' => 'no_data_found',
            'result' => []
        ]);
    }

    // Always return valid string version (VERY IMPORTANT)
    $androidVersion = !empty($setting->android_version)
        ? (string) $setting->android_version
        : '1.0.0';

    $iosVersion = !empty($setting->ios_version)
        ? (string) $setting->ios_version
        : '1.0.0';

    return response()->json([
        'code' => "1",
        'message' => 'success',
        'result' => [
            'android_version' => $androidVersion,
            'ios_version' => $iosVersion,
        ]
    ]);
}
    public function label(Request $request)
    {
        // echo "string";exit();
        $result = [];
        $labels = [];
        $language_id = isset($request->language_id) ? $request->language_id : 1;

        if(!empty($request->input('updated_date'))){
            
            $labelList  =   Label::where([
                ['status', '=', '1']
            ])->where('label_type',0)->where('updated_at', '>=',$request->input('updated_date'))->get();

        }else{
            $labelList  =   Label::where([
                ['status', '=', '1'],
            ])->where('label_type',0)->orderBy('id', 'DESC')->get();
        } 
        foreach($labelList as $lkey => $lvalue){
            $labels[$lkey]['key'] =  strval($lvalue['label_name']);            
            $labels[$lkey]['value'] =  strval($lvalue['label_value']);          
            $labels[$lkey]['value_fr'] =  strval($lvalue['label_value_fr']);            
        }

        if(!empty($labelList) && count($labelList) > 0){
            $labelArr=$labels;
            $result['code']     =   strval(1);
            $result['message']  =   'success';
            $result['updated_date']   =   date('Y-m-d H:i:s');
            $result['result']       =   $labelArr;

        }
        else
        {
            $result['code']     =   strval(1);
            $result['message']  =   'no_data_found';
            $result['updated_date']   =   date('Y-m-d H:i');
            $result['result']       =   [];
        }

        $mainResult=$result;
        // return response()->json($mainResult); 
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
       
    }

    public function general_settings(Request $request) {

       
        $setting = Setting::find(1);
        // echo "<pre>";print_r($setting->toArray());exit();
        // $WebmasterSetting = WebmasterSetting::find(1);
       

        if(!empty($setting)){
            $response=array();
            $response['otp_time']   = urldecode($setting->support_email);
            $response['contact_email']   = urldecode($setting->support_email);
            $response['contact_phone'] = urldecode($setting->phone);
            $response['address']   = urldecode($setting->address);
            $response['map_url']   = urldecode($setting->map_url);
            $response['currency_symbol'] = urldecode($setting->currency_symbol);
            $response['fax']   = urldecode($setting->fax);
            // $response['android_app_version']  = urldecode($setting->android_version);
            // $response['ios_app_version'] =  urldecode($setting->ios_version);
            // $response['force_update_android']  = urldecode($setting->android_version_update);
            // $response['force_update_ios']  = urldecode($setting->ios_version_update);
            $response['facebook_url'] =  urldecode($setting->facebook_link);
            $response['instagram_url']  =  urldecode($setting->instagram_link);
            // $response['twitter_url']  =   urldecode($setting->twitter_link);
            $response['linkedin_url'] =   urldecode($setting->twitter_link);
            $response['base_url']  =   env('APP_URL');
            $response['otp_time_expire'] = '300'; //sec

            $result['code']     =   strval(1);

            $result['message']  =   'success';
            $result['result']   =   $response;
            
        }else{
            $result['code']     =   strval(1);
            $result['message']  =   'no_data_found';
            $result['result']   =   [];
        }

        $mainResult = $result;
        return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        
    }
}
