<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\WebmasterSetting;
use App\Models\Setting;
use Auth;
use Illuminate\Http\Request;
use Mail;
use Helper;

class WebmasterSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,21,'read');
        if($check_view_permission==false){
            abort(404);
        } 
    }
    
    public function edit()
    {
        $WebmasterSetting = Setting::find(1);
        return view("dashboard.webmaster.settings.home", compact("WebmasterSetting"));
    }

    public function update(Request $request)
    {

        $WebMaintenanceSetting = Setting::find(1);
        if (!empty($WebMaintenanceSetting)) {
            $WebMaintenanceSetting->site_maintenance = isset($request->maintenance) && $request->maintenance == 'yes' ? 1 : 0;
            $WebMaintenanceSetting->save();
        }

        $Setting = Setting::find(1);



        $this->validateSettingRequest($Setting->id);
        if (!empty($Setting)) {
            //isset($data->page_name) ? $data->page_name: '',currency_symbol
            // $Setting->language_id = isset($request->language_id	) ? $request->language_id: '';
            $Setting->commission_in_per = isset($request->commission_in_per) ? $request->commission_in_per : '';
            $Setting->currency_symbol = isset($request->currency_symbol) ? $request->currency_symbol : '';
            $Setting->popular_dance_category = isset($request->popular_dance_category) ? $request->popular_dance_category : 0;
            $Setting->popular_dance_class = isset($request->popular_dance_class) ? $request->popular_dance_class : 0;
            $Setting->popular_instructor = isset($request->popular_instructor) ? $request->popular_instructor : 0;
            $Setting->android_version = isset($request->android_version) ? $request->android_version : 0;
            $Setting->ios_version = isset($request->ios_version) ? $request->ios_version : 0;
            $Setting->delivery_days = isset($request->delivery_days) ? $request->delivery_days : 0;
            $Setting->delivery_amount = isset($request->delivery_amount) ? $request->delivery_amount : 0;
            $Setting->delivery_fee = isset($request->delivery_fee) ? $request->delivery_fee : 0;
            $Setting->tax = isset($request->tax) ? $request->tax : 0;
            $Setting->android_version_update = isset($request->android_version_update) ? $request->android_version_update : 0;
            $Setting->ios_version_update = isset($request->ios_version_update) ? $request->ios_version_update : 0;
           
            $this->storeVideo($Setting);
            $Setting->save();
        }
        $WebmasterSetting = Setting::find(1);
        if (!empty($WebmasterSetting)) {
            $WebmasterSetting->mail_driver = $request->mail_driver;
            $WebmasterSetting->mail_host = $request->mail_host;
            $WebmasterSetting->mail_port = $request->mail_port;
            $WebmasterSetting->mail_username = $request->mail_username;
            $WebmasterSetting->mail_password = $request->mail_password;
            $WebmasterSetting->mail_encryption = $request->mail_encryption;
            $WebmasterSetting->mail_no_replay = $request->mail_no_replay;
            $WebmasterSetting->mail_title = $request->mail_title;
            $WebmasterSetting->mail_template = $request->mail_template;
            
           
           // $WebmasterSetting->currency_sybmol =  isset($request->currency_sybmol) ? $request->currency_sybmol : '';
            $WebmasterSetting->support_name =  isset($request->support_name) ? $request->support_name : '';
            $WebmasterSetting->support_email =  isset($request->support_email) ? $request->support_email : '';
            $WebmasterSetting->facebook_link = isset($request->facebook_link) ? $request->facebook_link : '';
            $WebmasterSetting->instagram_link =  isset($request->instagram_link) ? $request->instagram_link : '';
            $WebmasterSetting->twitter_link =  isset($request->twitter_link) ? $request->twitter_link : '';
            $WebmasterSetting->tiktok_link =  isset($request->tiktok_link) ? $request->tiktok_link : '';
            $WebmasterSetting->youtube_link =  isset($request->youtube_link) ? $request->youtube_link : '';
            $WebmasterSetting->map_distance =  isset($request->map_distance) ? $request->map_distance : '';
            $WebmasterSetting->phone = isset($request->phone) ? $request->phone : '';
            $WebmasterSetting->address = isset($request->address) ? $request->address : '';
             $WebmasterSetting->android_version = isset($request->android_version) ? $request->android_version : 0;
            $WebmasterSetting->ios_version = isset($request->ios_version) ? $request->ios_version : 0;
            $WebmasterSetting->delivery_days = isset($request->delivery_days) ? $request->delivery_days : 0;

            $WebmasterSetting->android_version_update = isset($request->android_version_update) ? $request->android_version_update : 0;
            $WebmasterSetting->ios_version_update = isset($request->ios_version_update) ? $request->ios_version_update : 0;
            $WebmasterSetting->tax = isset($request->tax) ? $request->tax : 0;
            $WebmasterSetting->delivery_fee = isset($request->delivery_fee) ? $request->delivery_fee : 0;
            $WebmasterSetting->delivery_amount = isset($request->delivery_amount) ? $request->delivery_amount : 0;
           // $this->storeVideo($WebmasterSetting);
            $WebmasterSetting->save();

            $OLD_BACKEND_PATH = env("BACKEND_PATH");
            // Update .env file
            $env_update = $this->changeEnv([
                'MAIL_DRIVER' => $request->mail_driver,
                'MAIL_HOST' => $request->mail_host,
                'MAIL_PORT' => $request->mail_port,
                'MAIL_USERNAME' => $request->mail_username,
                'MAIL_PASSWORD' => $request->mail_password,
                'MAIL_ENCRYPTION' => $request->mail_encryption,
                'MAIL_FROM_ADDRESS' => $request->mail_no_replay,
                'DATE_FORMAT' => $request->date_format,
                'SUPPORT_NAME' => isset($request->support_name) ? $request->support_name : '',
                'SUPPORT_EMAIL' => isset($request->support_email) ? $request->support_email : '',
                'FACEBOOK_LINK' => isset($request->facebook_link) ? $request->facebook_link : '',
                'INSTAGRAM_LINK' => isset($request->instagram_link) ? $request->instagram_link : '',
                'LINKEDIN_LINK' => isset($request->twitter_link) ? $request->twitter_link : '',
                'TIKTOK_LINK' => isset($request->tiktok_link) ? $request->tiktok_link : '',
                'YOUTUBE_LINK' => isset($request->youtube_link) ? $request->youtube_link : '',
                'COMMISSION_IN_PER' => isset($request->commission_in_per) ? $request->commission_in_per : '',
                'CURRENCY_SYMBOL' => isset($request->currency_symbol) ? $request->currency_symbol : '',
                'POPULAR_DANCE_CATEGORY' => isset($request->popular_dance_category) ? $request->popular_dance_category : 0,
                'POPULAR_DANCE_CLASS' => isset($request->popular_dance_class) ? $request->popular_dance_class : 0,
                'POPULAR_INSTRUCTOR' => isset($request->popular_instructor) ? $request->popular_instructor : 0,
                'PAGINATION_LIMIT' => isset($request->pagination_limit) ? $request->pagination_limit : 4,
                               
            ]);
            $WebinfoSetting = Setting::find(1);
            // dd($WebinfoSetting);    
            if (!empty($WebinfoSetting)) {
                //isset($data->page_name) ? $data->page_name: '',currency_symbol
                $WebinfoSetting->language_id = isset($request->language_id	) ? $request->language_id: '';
                $WebinfoSetting->phone = isset($request->phone) ? $request->phone : '';
                $WebinfoSetting->address = isset($request->address) ? $request->address : '';
                $WebinfoSetting->address_fr = isset($request->address_fr) ? $request->address_fr : '';
                $WebinfoSetting->map_url = isset($request->map_url) ? $request->map_url : '';
                $WebinfoSetting->email = isset($request->email) ? $request->email : '';
                $WebinfoSetting->fax = isset($request->address) ? $request->fax : '';

               
                // $this->langauge_id($Setting);
                $WebinfoSetting->save();
            }

            if ($OLD_BACKEND_PATH != 'user') {
                // redirect to new admin path
                return redirect()->to("admin/webmaster")->with('doneMessage', __('backend.saveDone'))->with('active_tab', $request->active_tab);
            }
 
            return redirect()->action('Dashboard\WebmasterSettingsController@edit')
                ->with('doneMessage', __('backend.saveDone'))
                ->with('active_tab', $request->active_tab);
        } else {
            return redirect()->route('adminHome');
        }
    }

    private function storeVideo($user)
    {

        // $formFileName = "intro_video";
        // $fileFinalName_ar = "";
        // if (request()->$formFileName != "") {
        //     $fileFinalName_ar = time() . rand(1111,
        //             9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
        //     $uploadPath = "/uploads/banners/";
        //     $path = public_path() . $uploadPath;
        //     request()->file($formFileName)->move($path, $fileFinalName_ar);

        //     $user->update([
        //        'intro_video' => $fileFinalName_ar,
        //    ]);
        // }

         $formFileName = "intro_video";
         $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/banners/";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

             $user->update([             
                'intro_video' => $fileFinalName_ar,
            ]);
         }
    }

    public function validateRequest($id = "")
    {

        if ($id != "") {

            $validateData = request()->validate([
                'facebook_link' => 'url',
                'instagram_link' => 'url',
                'twitter_link' => 'url',
                'tiktok_link' => 'url',
                'youtube_link' => 'url',
                'intro_video' => 'mimes:mp4',
                'support_email'  =>  'required|regex:/(.+)@(.+)\.(.+)/i'
            
            ], [
                'intro_video.mimes' => 'The video must be a file of type: mp4.'
            ]);
        } else {

             $validateData = request()->validate([
                'facebook_link' => 'url',
                'instagram_link' => 'url',
                'twitter_link' => 'url',
                'tiktok_link' => 'url',
                'youtube_link' => 'url',
                'intro_video' => 'required|mimes:mp4',
                'support_email'  =>  'required|regex:/(.+)@(.+)\.(.+)/i'
            
            ], [
                'intro_video.required' => 'The video must be required.',
                'intro_video.mimes' => 'The video must be a file of type: mp4.'
            ]);
        }

        return $validateData;
    }

    

    public function changeEnv($data = array())
    {
        if (count($data) > 0) {

            // Read .env-file
            $env = file_get_contents(base_path() . '/.env');

            // Split string on every " " and write into array
            $env = preg_split('/\s+/', $env);;


            // Loop through given data
            foreach ((array)$data as $key => $value) {

                // add KEY if not exist
                $KEY_EXIST = 0;
                foreach ($env as $env_key => $env_value) {
                    $entry = explode("=", $env_value, 2);
                    if ($entry[0] == $key) {
                        $KEY_EXIST = 1;
                    }
                }
                if (!$KEY_EXIST) {
                    $env[$key] = $key . "=";
                }

                // Loop through .env-data
                foreach ($env as $env_key => $env_value) {

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if ($entry[0] == $key) {
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents(base_path() . '/.env', $env);

            return true;
        } else {
            return false;
        }
    }

    // public function validateSettingRequest($id="")
    // {

    //     if($id !="")
    //     {
    //         $validateData =request()->validate([
    //             'support_email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
    //             'mail_no_replay' => 'required|regex:/(.+)@(.+)\.(.+)/i',
    //         ]);

    //     }else{

    //         $validateData =request()->validate([
    //             'support_email' => 'required|regex:/(.+)@(.+)\.(.+)/i',
    //             'mail_no_replay' => 'required|regex:/(.+)@(.+)\.(.+)/i',
    //         ]);
            
    //     }

    //     return $validateData;
    // }


    public function validateSettingRequest($id = "")
    {
        request()->validate([
            'support_email' => 'nullable|regex:/(.+)@(.+)\.(.+)/i',
            'mail_no_replay' => 'nullable|regex:/(.+)@(.+)\.(.+)/i',
        ]);
    }


    

    

    // public function mail_smtp_check(Request $request)
    // {
    //     if ($request->mail_driver == "smtp" && $request->mail_host != "" && $request->mail_port != "") {
    //         try {
    //             function server_parse($socket, $expected_response)
    //             {
    //                 $server_response = '';
    //                 while (substr($server_response, 3, 1) != ' ') {
    //                     if (!($server_response = fgets($socket, 256))) {
    //                         return 'Error while fetching server response codes';
    //                     }
    //                 }

    //                 if (!(substr($server_response, 0, 3) == $expected_response)) {
    //                     return $server_response;
    //                 }
    //             }

    //             //Connect to the host on the specified port
    //             $smtpServer = $request->mail_host;
    //             $username = $request->mail_username;
    //             $password = $request->mail_password;
    //             $port = $request->mail_port;
    //             $timeout = 20;
    //             $output = "";

    //             $socket = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
    //             if (!$socket) {
    //                 return json_encode(array("stat" => "error", "error" => "$errstr ($errno)"));
    //             } else {

    //                 server_parse($socket, '220');

    //                 fwrite($socket, 'EHLO ' . $smtpServer . "\r\n");
    //                 $output .= server_parse($socket, '250');
    //                 if ($output != "") {
    //                     $output .= "<br>";
    //                 }
    //                 fwrite($socket, 'AUTH LOGIN' . "\r\n");
    //                 $output .= server_parse($socket, '334');
    //                 if ($output != "") {
    //                     $output .= "<br>";
    //                 }
    //                 fwrite($socket, base64_encode($username) . "\r\n");
    //                 $output .= server_parse($socket, '334');
    //                 if ($output != "") {
    //                     $output .= "<br>";
    //                 }
    //                 fwrite($socket, base64_encode($password) . "\r\n");
    //                 $output .= server_parse($socket, '235');

    //                 if ($output == "") {
    //                     return json_encode(array("stat" => "success"));
    //                 } else {
    //                     return json_encode(array("stat" => "error", "error" => $output));
    //                 }
    //             }
    //         } catch (\Exception $e) {
    //             return json_encode(array("stat" => "error", "error" => "$errstr ($errno)"));
    //         }
    //     }
    //     return json_encode(array("stat" => "error", "error" => "Failed .. no data to connect"));
    // }

//     public function mail_test(Request $request)
//     {
//         $WebmasterSetting = WebmasterSetting::find(1);
//         if (!empty($WebmasterSetting)) {

//             $WebmasterSetting->mail_driver = $request->mail_driver;
//             $WebmasterSetting->mail_host = $request->mail_host;
//             $WebmasterSetting->mail_port = $request->mail_port;
//             $WebmasterSetting->mail_username = $request->mail_username;
//             $WebmasterSetting->mail_password = $request->mail_password;
//             $WebmasterSetting->mail_encryption = $request->mail_encryption;
//             $WebmasterSetting->mail_no_replay = $request->mail_no_replay;
//             $WebmasterSetting->save();


//             $env_update = $this->changeEnv([
//                 'MAIL_DRIVER' => $request->mail_driver,
//                 'MAIL_HOST' => $request->mail_host,
//                 'MAIL_PORT' => $request->mail_port,
//                 'MAIL_USERNAME' => $request->mail_username,
//                 'MAIL_PASSWORD' => $request->mail_password,
//                 'MAIL_ENCRYPTION' => $request->mail_encryption,
//                 'MAIL_FROM_ADDRESS' => $request->mail_no_replay,
//             ]);

//             if ($request->mail_driver == "smtp" && $request->mail_host != "" && $request->mail_port != "") {
//                 try {
//                     $email_subject = "Test Mail From " . env("APP_NAME");
//                     $email_body = "This is a Test Mail \r\n
// Mail Driver: " . $request->mail_driver . "
// Mail Host: " . $request->mail_host . "
// Mail Port: " . $request->mail_port . "
// Mail Username: " . $request->mail_username . "
// Email from: " . $request->mail_no_replay . "
// Email to: " . $request->mail_test . "
// ";
//                     $to_email = $request->mail_test;
//                     $to_name = "";
//                     $from_email = $request->mail_no_replay;
//                     $from_name = env("APP_NAME");
//                     Mail::send([], [], function ($message) use ($email_subject, $email_body, $to_email, $to_name, $from_email, $from_name) {
//                         $message->from($from_email, $from_name)
//                             ->to($to_email, $to_name)
//                             ->subject($email_subject)
//                             ->setBody($email_body);
//                     });
//                     return json_encode(array("stat" => "success"));
//                 } catch (\Exception $e) {
//                     return json_encode(array("stat" => "error"));
//                 }
//             }
//         }
//         return json_encode(array("stat" => "error"));
//     }
}
