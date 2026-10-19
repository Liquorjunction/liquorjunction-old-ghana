<?php

// This class file to define all general functions

namespace App\Helpers;

use App;
use App\Models\Cart;
use App\Models\Country;
use App\Models\Setting;
use App\Models\Orders;
use App\Models\OrderTracking;
use App\Models\GeneralSetting;
use App\Models\WebmasterSetting;
use App\Models\MainUser;
use App\Models\UserAddress;
use App\Models\ProductVariants;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\Label;
use App\Models\Role;
use App\Models\RoleModulePermission;
use App\Models\Uofs;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Order;
use Config;
use DateTime;
use DateInterval;
use DateTimeZone;
use DB;
use Session;
use App\Models\EmailTemplate;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\TransactionTokens;



class Helper
{
    static function currentLanguage()
    {
        return "EN";
    }

    static function GeneralWebmasterSettings($var)
    {
        $WebmasterSetting = WebmasterSetting::find(1);
        return $WebmasterSetting->$var;
    }

    static function GeneralSiteSettings($var)
    {
        $Setting = Setting::find(1);
        return $Setting->$var;
    }

    static function Settings($var)
    {
        $Setting = Setting::find(1);
        return $Setting->$var;
    }

    //time 
    static function time_elapsed_string($datetime, $full = false)
    {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );

        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }

    // Social Share links
    static function SocialShare($social, $title)
    {
        $shareLink = "";
        $URL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        switch ($social) {
            case "facebook":
                $shareLink = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($URL);
                break;
            case "twitter":
                $shareLink = "https://twitter.com/intent/tweet?text=$title&url=" . urlencode($URL);
                break;
            case "google":
                $shareLink = "https://plus.google.com/share?url=" . urlencode($URL);
                break;
            case "linkedin":
                $shareLink = "http://www.linkedin.com/shareArticle?mini=true&url=" . urlencode($URL) . "&title=$title";
                break;
            case "tumblr":
                $shareLink = "http://www.tumblr.com/share/link?url=" . urlencode($URL);
                break;
        }

        return $shareLink;
    }

    static function formatDate($date = "")
    {
        if ($date != "") {
            $format = env("DATE_FORMAT", "Y-m-d");
            return date($format, strtotime($date));
        }
        return "";
    }
    static function formatDatetime($date = "")
    {
        if ($date != "") {
            $format = env("DATE_FORMAT", "Y-m-d");
            return date($format, strtotime($date));
        }
        return "";
    }

    static function formatDatenew($date = "")
    {
        if ($date != "") {
            $format = env("DATE_FORMAT", "Y-m-d");
            $date = Carbon::createFromFormat('d/m/Y', $date)->format($format);
            return $date;
        }
        return "";
    }

    static function dateForDB($date = "", $withTime = 0)
    {
        if ($date != "") {
            try {
                $format = env("DATE_FORMAT", "Y-m-d");
                if ($withTime) {
                    return Carbon::createFromFormat($format . " h:i A", $date)->format('Y-m-d H:i:s');
                } else {
                    return Carbon::createFromFormat($format, $date)->format('Y-m-d');
                }
            } catch (\Exception $e) {
            }
        }
        return "";
    }

    static function jsDateFormat()
    {
        $format = env("DATE_FORMAT", "Y-m-d");
        $format = str_replace("Y", "YYYY", $format);
        $format = str_replace("m", "MM", $format);
        $format = str_replace("d", "DD", $format);
        return $format;
    }


    static function getEmailtemplateContent($id, $email = "", $password = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();

        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/banner_inner_img.png');
        }
        $emailtemp = Emailtemplate::findOrFail($id);
        $vars = array(
            '{{$name}}' => $name,
            '{{$password}}' => $password,
            '{{$email}}' => $email,
            '{{ $url }}' => $url,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$phoneNumber}' => $setting->phone,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',

        );



        if (isset($parent_id) == 2) {
            $email = strtr(urldecode($emailtemp['content_po']), $vars);
        } else {
            $email = strtr(urldecode($emailtemp['content']), $vars);
        }


        return $email;
    }

    static function getEmailtemplateContentContactus($name, $email = "", $phone = "", $id = "", $from_email = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$name}}' => $name,
            '{{$email}}' => $email,
            '{{$phone}}' => $phone,
            //   '{{$content}}' => $content,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentForgotpassword($id, $email = "", $otp = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }
        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            // '{{$password}}' => $password,
            '{{$otp}}' => $otp,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentAdminForgotpassword($id, $email = "", $password = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }
        $logo = asset('assets/dashboard/images/liquor.png');
        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');


        $vars = array(
            '{{$email}}' => $email,
            '{{$password}}' => $password,
            // '{{$otp}}' => $otp,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentInviteLink($email = "", $supplier_id = "", $password = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';
        $supplier_data = DB::table('wholesaler_invite_link')->where('id', $supplier_id)->first();
        // echo "";
        $invite_link = url('/' . env('WHOLESALER_BACKEND_PATH') . '/register/' . $supplier_data->uniqid . '');
        // echo "<pre>";print_r($invite_link);exit();
        $emailtemp = Emailtemplate::findOrFail('3');
        // echo "<pre>";print_r($email);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');
        $vars = array(
            '{{$email}}' => $email,
            '{{$logo}}' => $logo,
            '{{$link}}' => $invite_link,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentSendOffer($data = [])
    {

           // Get setting
        $setting = Setting::find(1);

        $logo = asset('assets/dashboard/images/liquor.png');
        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');
    
        $emailtemp = Emailtemplate::findOrFail('23');
        // $emailtemp = Emailtemplate::findOrFail('24');

        $htmlImages = '';
        if (!empty($data['offer_images'])) {
            foreach ($data['offer_images'] as $imgUrl) {
                $htmlImages .= "<img src='{$imgUrl}' alt='Offer Image' style='display: block; max-width: 100%; height: auto; margin: 0 auto; vertical-align: middle;' />";
            }
        }


        $vars = [
            '{{$email}}' => $data['email'] ?? '',
            '{{$name}}' => $data['name'] ?? '',
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' => $setting->facebook_link ?? '',
            '{{$settingtwitter}}' => $setting->twitter_link ?? '',
            '{{$settinginstagram}}' => $setting->instagram_link ?? '',
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{{$supportemail}}' => $setting->support_email ?? '',
            '{{$btn_text}}' => $data['btn_text'] ?? '',
            '{{$btn_url}}' => $data['btn_url'] ?? '',
            '{{$offer_images}}' => $htmlImages,
            '{{$expire}}' => $data['expire'] ?? '',
        ];
    
        $content = strtr(urldecode($emailtemp['content']), $vars);
        return $content;

    }

    static function getEmailtemplateContentRequestProductApprove($email = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail('5');
        // echo "<pre>";print_r($email);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }


    static function getEmailtemplateContentRequestProductReject($email = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail('6');
        // echo "<pre>";print_r($email);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentTechnicianRequestApprove($email = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail('12');
        // echo "<pre>";print_r($email);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentTechnicianRequestReject($email = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail('13');
        // echo "<pre>";print_r($email);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentRemindOrder($email = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail('14');
        // echo "<pre>";print_r($email);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentSubadminRegister($email = "", $password = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail('4');
        $link = env('APP_URL') . 'admin';
        // echo "<pre>";print_r($email);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$password}}' => $password,
            '{{$logo}}' => $logo,
            '{{$link}}' => $link,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentWholesalerRegister($email = "", $password = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail('7');
        // echo "<pre>";print_r($email);exit();
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }
        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$password}}' => $password,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getInstructorRequestContent($id, $email = "", $name = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }
        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getSubscribeEmailContent($id, $email = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }
        $vars = array(
            '{{$email}}' => $email,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentForgotpassword_api($email = "", $link = "", $name = "", $id = "", $logo = "", $from_email)
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/banner_inner_img.png');
        }
        $vars = array(
            '{{$email}}' => $email,
            '{{$link}}' => $link,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentUpdatepassword($email = "", $password = "", $name = "", $id = "", $logo = "", $from_email = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/banner_inner_img.png');
        }
        $vars = array(
            '{{$email}}' => $email,
            '{{$password}}' => $password,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentUserSendotpemailchnage($email = "", $name = "", $otp = "", $id = "", $logo = "", $from_email = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/banner_inner_img.png');
        }
        $vars = array(
            '{{$email}}' => $email,
            '{{$otp}}' => $otp,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentResendOtp($email = "", $name = "", $otp = "", $id = "", $logo = "", $from_email = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/banner_inner_img.png');
        }
        $vars = array(
            '{{$email}}' => $email,
            '{{$otp}}' => $otp,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentResetpassword($id, $name = "", $link = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/frontend/logo/wonup.png');
        }
        $vars = array(
            '{{$name}}' => $name,
            '{{$link}}' => $link,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{$phoneNumber}' => isset($setting->phone) ? $setting->phone : '',
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function pred()
    {
        call_user_func_array('pre', func_get_args());
        die();
    }
    static function send_notification_FCM($device_token, $title, $message, $device_type, $type = "", $id = "", $status = "")
    {
        $bug_count = 1;
        // echo "<pre>";
        if ($device_type == 1) {
            $accesstoken = 'AAAAfuhbdrs:APA91bGCEcGP_0Hb3rKEy5XA8RBHVl1JBzyCfd7J4Ube3jLm83DuXnhwYlStbQ86x2a8ne4MRMr1g3rtYP2TvBqMwNK_XBFog7yNCm7rMixa4TDkoAurH4qEDeYeidr-lg_YOTiI0rLV';
            $URL = 'https://fcm.googleapis.com/fcm/send';
            $post_data = '{
            "to" : "' . $device_token . '",
            "data" : {
              "body" : "",
              "title" : "' . $title . '",
              "notification_type" : "' . $type . '",
              "id" : "' . $id . '",
              "message" : "' . $message . '",
              "badge" : "' . $bug_count . '",
            },
            "notification" : {
                "body" : "' . $message . '",
                "title" : "' . $title . '",
                "notification_type" : "' . $type . '",
                "id" : "' . $id . '",
                "message" : "' . $message . '",
                "icon" : "new",
                "sound" : "default",
                "badge" : "' . $bug_count . '",
            },
        }';
            //print_r($post_data);die;
            $crl = curl_init();
            $headr = array();
            $headr[] = 'Content-type: application/json';
            $headr[] = 'Authorization: key=' . $accesstoken;
            curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($crl, CURLOPT_URL, $URL);
            curl_setopt($crl, CURLOPT_HTTPHEADER, $headr);
            curl_setopt($crl, CURLOPT_POST, true);
            curl_setopt($crl, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, true);
            $rest = curl_exec($crl);
            // print_r($rest);exit;
            // print_r($rest);exit();
            if ($rest === false) {
                // throw new Exception('Curl error: ' . curl_error($crl));
                //print_r('Curl error: ' . curl_error($crl));
                $result_noti = 0;
            } else {
                $result_noti = 1;
            }
            //curl_close($crl);
            //print_r($result_noti);die;
            // dd($result_noti);
            return $result_noti;
        } else {
            $accesstoken = 'AAAAfuhbdrs:APA91bGCEcGP_0Hb3rKEy5XA8RBHVl1JBzyCfd7J4Ube3jLm83DuXnhwYlStbQ86x2a8ne4MRMr1g3rtYP2TvBqMwNK_XBFog7yNCm7rMixa4TDkoAurH4qEDeYeidr-lg_YOTiI0rLV';
            $URL = 'https://fcm.googleapis.com/fcm/send';
            $post_data = '{
                    "to" : "' . $device_token . '",
                "data" : {
                    "body" : "",
                  "title" : "' . $title . '",
                  "notification_type" : "' . $type . '",
                  "id" : "' . $id . '",
                  "message" : "' . $message . '",
                  "badge" : "' . $bug_count . '",
                },
                "notification" : {
                    "body" : "' . $message . '",
                    "title" : "' . $title . '",
                    "notification_type" : "' . $type . '",
                    "id" : "' . $id . '",
                    "message" : "' . $message . '",
                    "icon" : "new",
                    "sound" : "default",
                    "badge" : "' . $bug_count . '",
                },
            }';
            // print_r($post_data);die;
            $headers = array(
                'Authorization: key=' . $accesstoken,
                'Content-Type: application/json'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $rest = curl_exec($ch);
            // print_r($rest);
            if ($rest === false) {
                // throw new Exception('Curl error: ' . curl_error($crl));
                // print_r('Curl error: ' . curl_error($crl));
                $result_noti = 0;
            } else {
                $result_noti = 1;
            }
            //curl_close($crl);
            // print_r($result_noti);die;
            //  dd($result_noti);
            return $result_noti;
        }
    }

    // static function sendNotification($device_token, $title, $message, $device_type,$type="",$id="",$status="")
    // {
    //     //   $firebaseToken = User::where('device_token')->pluck('device_token')->all();
    //     // $firebaseToken = DB::table('users')
    //     //              ->select('device_token')
    //     //              ->get();
    //     $firebaseToken = MainUser::whereNotNull('device_token')->pluck('device_token')->all();              
    //     //  dd($firebaseToken);
    //     //  dd(Auth::user());
    //     $device_token = $device_token;
    //     // echo "<pre>";print_r($device_token);
    //     $SERVER_API_KEY = 'AAAAbK7g7P4:APA91bGjhaGSZhvjx4g2wruycP8Sx9b60k5tx2nrfswuhF6sX44StF872ArL-fJJstvTwEyo_V8Kp-5ydWoWWRhkHrnQ8V3DsTD3eS_WUbBBfDsz61MT33XbhbXgTcaiFVzV5s0XHxc9';
    //     $data = [
    //         "registration_ids" => $firebaseToken,
    //         "notification" => [
    //             "title" => $title,
    //             "body" => $message, 
    //             "content_available" => true,
    //             "priority" => "high", 
    //         ]
    //     ];
    //     $dataString = json_encode($data);
    //     $headers = [
    //         'Authorization: key=' . $SERVER_API_KEY,
    //         'Content-Type: application/json',
    //     ];
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    //     $response = curl_exec($ch);

    //     // echo "<pre>";print
    //     // if ($response === false) {
    //     //     // throw new Exception('Curl error: ' . curl_error($crl));
    //     //     //print_r('Curl error: ' . curl_error($crl));
    //     //         $result_noti = 0;
    //     //       } else {
    //     //         $result_noti = 1;
    //     //       }
    //     // //curl_close($crl);
    //     // //print_r($result_noti);die;
    //     //       // dd($result_noti);
    //     //       return $result_noti;
    //     // echo "<pre>";print_r($response);exit();
    //    // dd($response);
    //    // return redirect("admin/user/dashboard");
    // }

    static function fetchUserTimeZone()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {

            $ip = $forward;
        } else {

            $ip = $remote;
        }

        if ($ip == "::1") {
            $ip = $_SERVER['REMOTE_HOST'];
        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = '180.211.105.202';
        $ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
        $ipInfo = json_decode($ipInfo);
        $timezone = $ipInfo->timezone;
        return $timezone;
    }

    static function converttimeTozone($datetime, $to_utc = 0)
    {

        $timzone_in_session = \Session::get('timzone_in_session');
        
        if (isset($timzone_in_session) && $timzone_in_session != null) {
            $timezone = $timzone_in_session;
        } else {
            
            // $timezone = 'Asia/Calcutta';
            $timezone = 'Africa/Accra';

            \Session::put('timzone_in_session', $timezone);
        }

        date_default_timezone_set($timezone);
        $local_time_zone = date_default_timezone_get();
        $data = array(
            'fromTimezone' => ($to_utc == 1) ? $local_time_zone : 'UTC',
            'toTimezone' => ($to_utc == 1) ? 'UTC' : $local_time_zone,
            'dateTime' => $datetime,
            'dateTimeFormat' => env('DATE_FORMAT', 'Y-m-d') . ' h:i A'
        );
        $fromTimezone = $data['fromTimezone'];
        $toTimezone = $data['toTimezone'];
        $dateTime = $data['dateTime'];
        $dateTimeFormat = $data['dateTimeFormat'];
        $fromZoneDateTime = new \DateTime($dateTime, new \DateTimeZone($fromTimezone));
        $fromZoneDateTime->setTimezone(new \DateTimeZone($toTimezone));
        $returnDateTime = date($dateTimeFormat, strtotime($fromZoneDateTime->format('Y-m-d H:i:s')));

        return $returnDateTime;
    }

    static function get_order_status($order_id)
    {
        $userid =  auth()->guard('main_user')->id();

        $getorder = Orders::find($order_id);

        if ($getorder->order_status == '0') {
            $status = 'Order Started';
        } elseif ($getorder->order_status == '1') {
            $status = 'Milestone * completed';
        } elseif ($getorder->order_status == '2') {
            $status = 'Order completed';
        } else {
            $status = 'Order cancelled';
        }
        // dd($user_notifications_count);
        // $user_notifications_count = UsersNotifications::whereIn('user_id',$user_ids)->where('status','=','1')->where('read_status','=','0')->count();

        return $status;
    }

    static function lang_data($labelKey = "")
    {

        $label = Label::where('label_name', $labelKey)->where('status', '=', 1)->first();

        if (!empty($label)) {

            $label = Label::where('label_name', $labelKey)->where('status', '=', 1)->first();

            $labelValue = $label->label_value;

            return $labelValue;
        } else {

            $label = Label::where([['label_name', '=', $labelKey], ['status', '=', 1]])->first();

            $labelValue = $label->label_value;

            return $labelValue;
        }
    }

    static function getEmailtemplateContentUserOtp($email = "", $name = "", $otp = "", $id = "", $logo = "", $from_email = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$otp}}' => $otp,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentUserRegister($email = "", $name = "", $id = "", $url = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');

        $vars = array(
            '{{$email}}' => $email,
            '{{$name}}' => $name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentUserQuote($user_name = "", $sendname = "", $sendemail = "", $category_name = "", $post_code = "", $description = "", $image_name = "", $id = "", $logo = "")
    {
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        if ($image_name) {
            $image_name = asset('uploads/quote/') . '/' . $image_name;
            // echo "<pre>";print_r($image_name);exit();
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');
        $vars = array(
            '{{$sendemail}}' => $sendemail,
            '{{$user_name}}' => $user_name,
            '{{$sendname}}' => $sendname,
            '{{$category_name}}' => $category_name,
            '{{$post_code}}' => $post_code,
            '{{$description}}' => $description,
            '{{$image_name}}' => $image_name,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        return $email;
    }

    static function getEmailtemplateContentOrderStatusChanged($user_name, $sendname, $sendemail, $order, $order_status, $id = "", $logo = "")
    {
        // dd(5);
        /*$setting = Setting::first();*/
        $setting = Setting::first();
        $facebook = isset($setting->facebook_link) ? $setting->facebook_link : '';
        $twitter = isset($setting->twitter_link) ? $setting->twitter_link : '';
        $instagram =  isset($setting->instagram_link) ? $setting->instagram_link : '';

        $emailtemp = Emailtemplate::findOrFail($id);
        // dd($emailtemp);
        if (isset($logo) && !empty($logo)) {
            $logo = $logo;
        } else {
            $logo = asset('assets/dashboard/images/liquor.png');
        }

        $logo = asset('assets/dashboard/images/liquor.png');

        $facebook_logo = asset('assets/dashboard/images/facebook.png');
        $insta_logo = asset('assets/dashboard/images/insta.png');
        $in_logo = asset('assets/dashboard/images/in.png');
        $graphic_image = asset('assets/dashboard/images/graphic_image.png');
        $website = env('APP_URL');
        $vars = array(
            '{{$sendemail}}' => $sendemail,
            '{{$user_name}}' => $user_name,
            '{{$sendname}}' => $sendname,
            '{{$order_number}}' => $order->order_id,
            '{{$order_status}}' => $order_status,
            '{{$logo}}' => $logo,
            '{{$settingfacebook}}' =>  $facebook,
            '{{$settingtwitter}}' => $twitter,
            '{{$settinginstagram}}' => $instagram,
            '{{$facebook_logo}}' => $facebook_logo,
            '{{$insta_logo}}' => $insta_logo,
            '{{$graphic_image}}' => $graphic_image,
            '{{$website}}' => $website,
            '{{$in_logo}}' => $in_logo,
            '{$supportemail}' => isset($setting->support_email) ? $setting->support_email : '',
        );

        $email = strtr(urldecode($emailtemp['content']), $vars);
        // dd($email);exit;
        // print_r($vars);exit;
        return $email;
    }

    static function order_status($id)
    {

        $userid =  auth()->guard('main_user')->id();

        $getorder = OrderTracking::find($id);
        // echo "<pre>";print_r($id);exit();
        if ($getorder->order_status == '0') {
            $status = 'Pending';
        } elseif ($getorder->order_status == '1') {
            $status = 'Dispatched';
        } elseif ($getorder->order_status == '2') {
            $status = 'Complated';
        } else {
            $status = 'Cancel';
        }
        // dd($user_notifications_count);
        // $user_notifications_count = UsersNotifications::whereIn('user_id',$user_ids)->where('status','=','1')->where('read_status','=','0')->count();

        return $status;
    }

    static function OrderStatus($id)
    {
        $orderDetail = DB::table('order')->where('id', $id)->first();

        if ($orderDetail->order_status == '0') {
            $status = 'Pending';
        } elseif ($orderDetail->order_status == '1') {
            $status = 'Dispatched';
        } elseif ($orderDetail->order_status == '2') {
            $status = 'Completed';
        } else {
            $status = 'Cancel';
        }

        return $status;
    }
    function translatedColumn($model, $column, $language)
    {
        $translationColumn = $column . '_' . $language;
        return $model->selectRaw("COALESCE($translationColumn, $column) AS $column");
    }


    static function formatTimeLocal($date = "")
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        if ($ip == "::1") {
            $ip = $_SERVER['REMOTE_HOST'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=" . $ip);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $ip_data_in = curl_exec($ch); // string
        curl_close($ch);
        $ip_data = json_decode($ip_data_in, true);
        $ip_data = str_replace('&quot;', '"', $ip_data);
        if ($date != "") {
            if (isset($ip_data['geoplugin_timezone'])) {
                $dt = new DateTime($date);
                $timeZone = $ip_data['geoplugin_timezone'];
                $timeZone = 'Asia/Kolkata';
                $tz = new DateTimeZone($timeZone); // or whatever zone you're after
                $dt->setTimezone($tz);
                $date = $dt->format('H:i:s');
            } else {
                $user = User::where('user_type', 1)->first();
                $dt = new DateTime($date);
                $timeZone = isset($user->time_zone) ? $user->time_zone : 'UTC';
                $tz = new DateTimeZone($timeZone); // or whatever zone you're after
                $dt->setTimezone($tz);
                $date = $dt->format('H:i:s');
            }
            return $date;
        }
        return "";
    }
    static function GetRolePermission($user_type, $role_module_id, $access)
    {
        // dd($user_type);
        //This is for super admin.
        if ($user_type != 2 && $user_type != 3) {
            return true;
        }
        $roleInfo = Role::where('user_type', $user_type)->select('id')->first();
        $allowed_permissions = RoleModulePermission::where('role_id', $roleInfo->id)->where('role_module_id', $role_module_id)->first();
        if ($access == 'read' && $allowed_permissions->read == 1) {
            return true;
        } elseif ($access == 'create' && $allowed_permissions->create == 1) {
            return true;
        } elseif ($access == 'update' && $allowed_permissions->update == 1) {
            return true;
        } elseif ($access == 'delete' && $allowed_permissions->delete == 1) {
            return true;
        } else {
            return false;
        }
    }

    static function encodeUrl($str)
    {
        $encode = str_replace('=', '', base64_encode($str));
        return $encode;
    }

    static function getUnitById($id)
    {
        $get_result = Uofs::where(['status' => 1, 'id' => $id])->first();
        if (!empty($get_result)) {
            return $get_result->title;
        }
    }

    static function language($keys)
    {
        $general_setting = Helper::Settings('language_id');

        if (Session::get('language') == null && $general_setting == 2) {
            Session::put('language', 2);
        }

        if (Session::get('language') == null && $general_setting == 1) {
            Session::put('language', 1);
        }

        $get_result = Label::where(['status' => 1, 'label_name' => $keys, 'label_type' => 1])->first();

        if (!empty($get_result)) {
            if (Session::get('language') == 2) {
                $label = ($get_result->label_value_fr) ? $get_result->label_value_fr : $get_result->label_value;
            } else {
                $label = $get_result->label_value;
            }
            return $label;
        } else {
            return "";
        }
    }

    static function getCategory(){
        $categoryData = Categories::with(['subcategory'=>function($q){
            return $q->where('status','=', 1)->orderBy('sub_order_number','ASC');
        }])->where('status',1)->limit(10)->orderBy('order_number','ASC')->get();
        return $categoryData;
        // echo '<pre>';
        // print_r($categoryData);

    }

    static function getProductDetails($id)
    {
        $product_data = Product::withWhereHas('get_product_images', function ($query) {
            $query->where('status', 1);
        })->where('id', $id)->first();
        return $product_data;
    }

    static function userFavoriteProduct($product_id)
    {

        $user_id = @auth()->guard('user')->user() ? auth()->guard('user')->user()->id : '';
        $fav_data = \DB::table('favorite_product')->where('user_id', $user_id)->where('product_id', $product_id)->where('status', 1)->first();
        if (!empty($fav_data)) {
            return  $fav_data;
        }
        return false;
    }

    static function getUserCartCount()
    {
        $user_id = Auth::guard('user')->user() ? Auth::guard('user')->user()->id : '';
        if ($user_id) {
            $count = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $user_id)->where('cart.status', 1)->count();
            return $count;
        }
    }

    static function getCartDetailById($cart_id)
    {
        $user_id = Auth::guard('user')->user() ? Auth::guard('user')->user()->id : '';
        if ($user_id) {
            $query = DB::table('cart')->where('cart.id', $cart_id)->where('cart.user_id', $user_id)->where('cart.status', 1)->first();
            return $query;
        }
    }
    static function multidemisionArrayMerge($get_cart_array, $new_array)
    {
        $get_cart_array = array_combine(array_map('intval', array_keys($get_cart_array)), $get_cart_array);
        $new_array = array_combine(array_map('intval', array_keys($new_array)), $new_array);
        $result = array_replace_recursive($get_cart_array, $new_array);
        return $result;
    }
    static function cartItem(Request $request, $product_id, $variant_id, $quantity)
    {
        $get_cart_array = Session::get('cart_info');
        $request->session()->forget('cart_info');

        $quantityData = is_array($quantity) ? $quantity : ['quantity' => $quantity];

        if ($get_cart_array == "") {
            /*---Creating new product array for cart.--*/
            $cart_info = array($product_id => array($variant_id => $quantityData));
            Session::put('cart_info', $cart_info);
        } else {
            /*---session have cart values if product is exits.--*/
            if (array_key_exists($product_id, $get_cart_array)) {
                foreach ($get_cart_array as $key => $variant_array) {
                    if ($key == $product_id && array_key_exists($variant_id, $variant_array)) {
                        $new_array = array($product_id => array($variant_id => $quantityData));
                        $new_product_array = Helper::multidemisionArrayMerge($get_cart_array, $new_array);
                        Session::put('cart_info', $new_product_array);
                    } else {
                        $new_array = array($product_id => array($variant_id => $quantityData));
                        $new_product_array = Helper::multidemisionArrayMerge($get_cart_array, $new_array);
                        Session::put('cart_info', $new_product_array);
                    }
                }
            } else {
                /*---combine if already product in cart and add new cart product in sesstion.--*/
                $new_array = array($product_id => array($variant_id => $quantityData));
                $new_product_array = Helper::multidemisionArrayMerge($get_cart_array, $new_array);
                Session::put('cart_info', $result);
            }
        }
    }

    /*--- User session cart items, add in the database after login---*/
    static function afterLoginAddUserCartItemDatabk19dec()
    {
        if (isset(auth()->guard('user')->user()->id)) {
            $user_id = auth()->guard('user')->user()->id;
            $get_cart_array = Session::get('cart_info');
            if (!empty($get_cart_array)) {
                foreach ($get_cart_array as $productId => $variant_array) {
                    foreach ($variant_array as $variantId => $quantity) {

                    if (is_array($quantity)) {
                        $actual_quantity = $quantity['quantity'] ?? 1;
                        $is_bogo = $quantity['is_bogo'] ?? 0;
                    } else {
                        $actual_quantity = $quantity;
                        $is_bogo = 0;
                    }

                        $variant_info = ProductVariants::getProductDetalsBasedOnVariant()->where('id', $variantId)->first();
                        if ($variant_info->variant_discounted_price != '' && $variant_info->variant_discounted_price != 0.00) {
                            $varint_pro_price = $variant_info->variant_discounted_price;
                            $tcart_price = @$variant_info->variant_discounted_price ? ($variant_info->variant_discounted_price * $actual_quantity) : 0;
                        } else {
                            $varint_pro_price = $variant_info->variant_price;
                            $tcart_price = @$variant_info->variant_price ? ($variant_info->variant_price * $actual_quantity) : 0;
                        }
                        $cartData = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $user_id)->where('cart.product_id', $productId)->where('product_variant_id', $variantId)->where('cart.status', 1)->select('cart.*')->first();

                        if (!empty($cartData)) {
                            $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $product_id)->where('product_variant_id', $variant_id)->update(array(
                                'total_price' => $tcart_price,
                                'product_price' => $varint_pro_price,
                                'quantity' => $actual_quantity,
                            ));
                        } else {
                            $uniqid = uniqid();
                            $cart = new Cart();
                            $cart->uniqid = $uniqid;
                            $cart->product_id = $productId;
                            $cart->product_variant_id = $variantId;
                            $cart->user_id = $user_id;
                            $cart->quantity = $actual_quantity;
                            $cart->is_bogo = $is_bogo;
                            $cart->total_price = $tcart_price;
                            $cart->product_price = $varint_pro_price;
                            $cart->status = 1;
                            $cart->save();
                        }
                    }
                }
            }
        }
    }

    // static function afterLoginAddUserCartItemData()
    // {
    //     if (isset(auth()->guard('user')->user()->id)) {
    //         $user_id = auth()->guard('user')->user()->id;
    //         $get_cart_array = Session::get('cart_info');
    //         if (!empty($get_cart_array)) {
    //             foreach ($get_cart_array as $productId => $variant_array) {
    //                 foreach ($variant_array as $variantId => $quantity) {

    //                     if (is_array($quantity)) {
    //                         $actual_quantity = $quantity['quantity'] ?? 1;
    //                         $is_bogo = $quantity['is_bogo'] ?? 0;
    //                     } else {
    //                         $actual_quantity = $quantity;
    //                         $is_bogo = 0;
    //                     }

    //                     $variant_info = ProductVariants::getProductDetalsBasedOnVariant()->where('id', $variantId)->first();
    //                     //if($variant_info){
    //                     $offer_price = $variant_info->variant_discounted_price;
    //                     $varint_pro_price = $variant_info->variant_price;
    //                     if ($variant_info->variant_discounted_price != '' && $variant_info->variant_discounted_price != 0.00) {
    //                         $tcart_price = @$variant_info->variant_discounted_price ? ($variant_info->variant_discounted_price * $actual_quantity) : 0;
    //                     } else {
    //                         $tcart_price = @$variant_info->variant_price ? ($variant_info->variant_price * $actual_quantity) : 0;
    //                     }
    //                     //}
    //                     $cartData = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $user_id)->where('cart.product_id', $productId)->where('product_variant_id', $variantId)->where('cart.status', 1)->select('cart.*')->first();

    //                     if (!empty($cartData)) {
    //                         $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $productId)->where('product_variant_id', $variant_id)->update(array(
    //                             'total_price' => $tcart_price,
    //                             'product_price' => $varint_pro_price,
    //                             'offer_price' => $offer_price,
    //                             'quantity' => $actual_quantity,
    //                         ));
    //                     } else {
    //                         $uniqid = uniqid();
    //                         $cart = new Cart();
    //                         $cart->uniqid = $uniqid;
    //                         $cart->product_id = $productId;
    //                         $cart->product_variant_id = $variantId;
    //                         $cart->user_id = $user_id;
    //                         $cart->quantity = $actual_quantity;
    //                         $cart->is_bogo = $is_bogo;
    //                         $cart->total_price = $tcart_price;
    //                         $cart->product_price = $varint_pro_price;
    //                         $cart->offer_price = $offer_price;
    //                         $cart->order_type = 1;
    //                         $cart->status = 1;
    //                         $cart->save();
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }

     static function afterLoginAddUserCartItemData()
    {
        if (isset(auth()->guard('user')->user()->id)) {
            $user_id = auth()->guard('user')->user()->id;
            $get_cart_array = Session::get('cart_info');
            if (!empty($get_cart_array)) {
                foreach ($get_cart_array as $productId => $variant_array) {
                    foreach ($variant_array as $variantId => $quantity) {

                        if (is_array($quantity)) {
                            $actual_quantity = $quantity['quantity'] ?? 1;
                            $is_bogo = $quantity['is_bogo'] ?? 0;
                            $is_offer = $quantity['is_offer'] ?? 0;
                            $discount_amount = $quantity['discount_amount'] ?? null;
                            $offer_type = $quantity['offer_type'] ?? null;

                        } else {
                            $actual_quantity = $quantity;
                            $is_bogo = 0;
                            $is_offer=0;
                            $discount_amount=null;
                            $offer_type=null;
                        }

                        $variant_info = ProductVariants::getProductDetalsBasedOnVariant()->where('id', $variantId)->first();
                        //if($variant_info){
                        $offer_price = $variant_info->variant_discounted_price;
                        $varint_pro_price = $variant_info->variant_price;
                        if ($variant_info->variant_discounted_price != '' && $variant_info->variant_discounted_price != 0.00) {
                            $tcart_price = @$variant_info->variant_discounted_price ? ($variant_info->variant_discounted_price * $actual_quantity) : 0;
                        } else {
                            $tcart_price = @$variant_info->variant_price ? ($variant_info->variant_price * $actual_quantity) : 0;
                        }
                        //}
                        $cartData = DB::table('cart')->leftjoin('product', 'product.id', '=', 'cart.product_id')->where('cart.user_id', $user_id)->where('cart.product_id', $productId)->where('product_variant_id', $variantId)->where('cart.status', 1)->select('cart.*')->first();

                        if (!empty($cartData)) {
                            $updatepsw = Cart::where('user_id', $user_id)->where('product_id', $productId)->where('product_variant_id', $variantId)->update(array(
                                'total_price' => $tcart_price,
                                'product_price' => $varint_pro_price,
                                'offer_price' => $offer_price,
                                'quantity' => $actual_quantity,
                                'is_bogo' => $is_bogo,
                                'is_offer' => $is_offer,
                                'discount_amount' => $discount_amount,
                                'offer_type' => $offer_type
                            ));
                        } else {
                            $uniqid = uniqid();
                            $cart = new Cart();
                            $cart->uniqid = $uniqid;
                            $cart->product_id = $productId;
                            $cart->product_variant_id = $variantId;
                            $cart->user_id = $user_id;
                            $cart->quantity = $actual_quantity;
                            $cart->is_bogo = $is_bogo;
                            $cart->is_offer = $is_offer;
                            $cart->discount_amount = $discount_amount;
                            $cart->offer_type = $offer_type;
                            $cart->total_price = $tcart_price;
                            $cart->product_price = $varint_pro_price;
                            $cart->offer_price = $offer_price;
                            $cart->order_type = 1;
                            $cart->status = 1;
                            $cart->save();
                        }
                    }
                }
            }
        }
    }

    // static function getCartQuantity($variant_id)
    // {
    //     $get_cart_array = Session::get('cart_info');
    //     $qty = "";
    //     foreach ($get_cart_array as $key => $variant_array) {
    //         foreach ($variant_array as $key1 => $result) {
    //             if ($key1 == $variant_id) {
    //                 return $qty =  $result;
    //             }
    //         }
    //     }
    // }


    // static function getCartQuantity($variant_id)
    // {
    //     $get_cart_array = Session::get('cart_info');
    //     foreach ($get_cart_array as $key => $variant_array) {
    //         foreach ($variant_array as $key1 => $result) {
    //             if ($key1 == $variant_id && isset($result['quantity'])) {
    //                 return (int) $result['quantity'];
    //             }
    //         }
    //     }

    //     return 0; 
    // }

    static function getCartQuantity($variant_id)
    {
        $get_cart_array = Session::get('cart_info', []);
        foreach ($get_cart_array as $variant_array) {
            foreach ($variant_array as $key1 => $result) {
                // Handle both old and new formats
                if ($key1 == $variant_id) {
                    if (is_array($result) && isset($result['quantity'])) {
                        return (int) $result['quantity'];
                    } elseif (is_numeric($result)) {
                        return (int) $result;
                    }
                }
            }
        }

        return 0; 
    }


     static function getCartBogoStatus($variant_id)
    {
        $get_cart_array = Session::get('cart_info');
        foreach ($get_cart_array as $key => $variant_array) {
            foreach ($variant_array as $key1 => $result) {
                if ($key1 == $variant_id && isset($result['is_bogo'])) {
                    return (int) $result['is_bogo'];
                }
            }
        }

        return 0; 
    }


   public static function getCartOfferType($variant_id)
    {
        $get_cart_array = Session::get('cart_info');
        foreach ($get_cart_array as $key => $variant_array) {
            foreach ($variant_array as $key1 => $item) {
                if ($key1 == $variant_id && isset($item['offer_type'])) {
                    return $item['offer_type'];
                }
            }
        }
        return null;
    }

    public static function getCartDiscountAmount($variant_id)
    {
        $get_cart_array = Session::get('cart_info');
        foreach ($get_cart_array as $key => $variant_array) {
            foreach ($variant_array as $key1 => $item) {
                if ($key1 == $variant_id && isset($item['discount_amount'])) {
                    return $item['discount_amount'];
                }
            }
        }
        return null;
    }


    public static function getCartOfferStatus($variant_id)
    {
        $get_cart_array = Session::get('cart_info');
        foreach ($get_cart_array as $key => $variant_array) {
            foreach ($variant_array as $key1 => $item) {
                if ($key1 == $variant_id && isset($item['is_offer'])) {
                    return (int) $item['is_offer'];
                }
            }
        }
        return 0;
    }

    static function getUserCartQuantity($variantId, $user_id)
    {
        $cart_varinat_info = Cart::where('cart.product_variant_id', $variantId)->where('cart.user_id', $user_id)->where('cart.status', 1)->first();
        return $cart_varinat_info->quantity;
    }

    static function sendTwilioSMS($to, $message)
    {
    $twilioSid = env("TWILIO_SID");
    $twilioAuthToken = env("TWILIO_AUTH_TOKEN");
    $twilioPhoneNumber = env("TWILIO_PHONE_NUMBER");

        $client = new Client();

        $response = $client->post(
            // "https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json",
            'https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json',
            [
                'auth' => [$twilioSid, $twilioAuthToken],
                'form_params' => [
                    'From' => 'LJ-Ghana',
                    'To' => $to,
                    'Body' => $message,
                ],
            ]
        );

        $responseData = json_decode($response->getBody(), true);
       // dd($responseData);
        // You can log or handle the response data as needed
        // For example, log the Twilio SID or check for errors

        // You may also want to add error handling here
        // Check $responseData['status'] or other Twilio response fields

        return $responseData['sid']; // Return Twilio SID for reference
    }

    static function getOrderStatus($id)
    {
        $order_status = DB::table('order_status')->where('id', $id)->first();
        if (Session::get('language') == 2) {
            $status =  $order_status->name_fr;
        } else {
            $status = $order_status->name;
        }
        return $status;
    }
    static function avrageRating($product_id)
    {
        //  dd($product_id);
        $avragerating = DB::table('ratings')->where('product_id', '=', $product_id)->avg('ratings');
        // dd($avragerating);
        return @$avragerating ?: 0;
    }

    static function country($id)
    {
        $country_info = Country::where('id', '=', $id)->first();
        return $country_info;
    }

    static function getUserProductOrderReview($product_id, $order_id)
    {
        $user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
        $ratingData = DB::table('ratings')
            ->leftjoin('main_users', 'main_users.id', '=', 'ratings.user_id')
            ->leftjoin('product', 'product.id', '=', 'ratings.product_id')
            ->leftjoin('order', 'order.id', '=', 'ratings.order_id')
            ->where('ratings.status', 1)
            ->where('ratings.user_id', $user_id)
            ->where('ratings.order_id', $order_id)
            ->where('product.id', $product_id)
            ->count();
        return $ratingData;
    }
    static function storeTime($storeid, $weekid)
    {
        $storetime = DB::table('store_timing_week')->where('store_id', $storeid)->where('week_id', $weekid)->first();

        return $storetime;
    }
    static function weeklist($week_name)
    {
        // $weekId = DB::table('store_timing_week')->where('store_id',$storeid)->pluck('week_id');

        $weekInfo = DB::table('week_list')->where('name', $week_name)->first();

        //dd($weekInfo);
        // print_r($weekInfo);

        return $weekInfo;
    }

    static function  getProductsRatingOrderId($user_id, $product_id, $order_id)
    {
        $ratingData = DB::table('ratings')->where('ratings.status', 1)
            ->where('ratings.user_id', $user_id)
            ->where('ratings.product_id', $product_id)
            ->where('ratings.order_id', $order_id)
            ->first();
        return $ratingData;
    }


    //convert time to local and local to utc -- studio-freelance
    static function toLocalToUtcTime($time)
    {
        $timzone_in_session = \Session::get('timzone_in_session');
        //get current timezone
        if (isset($timzone_in_session) && $timzone_in_session != null) {
            $timezone = $timzone_in_session;
        } else {
            //$ip = \Request::ip(); //$_SERVER['REMOTE_ADDR']
            $ip = '180.211.105.202';
            $ipInfo = file_get_contents('http://ip-api.com/json/' . $ip);
            $ipInfo = json_decode($ipInfo);
            $timezone = $ipInfo->timezone;

            \Session::put('timzone_in_session', $timezone);
        }

        //$timeZone = 'Europe/London';
        $custom_date = Carbon::now()->format('Y-m-d');

        $tmp_date_time = $custom_date . ' ' . $time;
        $final_time = new \DateTime($tmp_date_time, new \DateTimeZone($timezone));

        // Set the timezone to UTC
        $final_time->setTimezone(new \DateTimeZone('UTC'));

        return $final_time->format('H:i:s');
    }

    static function numberFormat($value)
    {
        return number_format((float) @$value, 2, '.', '');
    }

    static function sendNotification($device_token, $title, $message, $order_id, $order_number)
    {
        $firebaseToken = $device_token;
        $SERVER_API_KEY = env('FCM_TOKEN');

        $data = [
            "registration_ids" => array($firebaseToken),
            "notification" => [
                "title" => $title,
                "body" => $message,
                "content_available" => true,
                "priority" => "high",
            ],
            "data" => [
                "order_id" => $order_id,
                "order_number" => $order_number
            ],
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);

        if ($response === false) {
            $result_noti = 0;
        } else {
            $result_noti = 1;
        }
        return $result_noti;
    }

    static function dateTz($date)
    {
        return date("Y-m-d\TH:i:s\Z", strtotime($date));
    }

    static function orderCancelEmail($order_id, $user_id)
    {
        $order = Order::leftjoin('order_status', 'order_status.id', '=', 'order.id')->select('order.*', 'order_status.name as order_status')->where('order.id', $order_id)->first();
        $customer_details = DB::table('main_users')->find($user_id);
        $setting = Setting::find(1);
        $template_id = 20;
        $emaildetail = EmailTemplate::find($template_id);
        $fromEmail = env('MAIL_USERNAME');
        $logo = asset('assets/dashboard/images/Logo/logo.png');
        $sendemail = @$customer_details->email;

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
        try {
            Mail::send('emails.orderstatuschanged', $data, function ($message) use ($data, $emaildetail) {
                $message->to($data['sendemail'], 'Liquor')->subject($emaildetail->subject);
                $message->from($data['from_email'], $emaildetail->title);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    // static function createToken($xmlPayload)
    // {

    //     $endpoint = "https://secure.3gdirectpay.com/API/v6/";
    //     $xmlData = $xmlPayload;

    //     $ch = curl_init();

    //     if (!$ch) {
    //         die("Couldn't initialize a cURL handle");
    //     }
    //     curl_setopt($ch, CURLOPT_URL, $endpoint);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //     curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

    //     $result = curl_exec($ch);

    //     curl_close($ch);

    //     $responseData = simplexml_load_string($result);

    //     // Extract the data
    //     $result1 = (string) $responseData->Result;
    //     $resultExplanation = (string) $responseData->ResultExplanation;
    //     $transToken = (string) $responseData->TransToken;
    //     $transRef = (string) $responseData->TransRef;

    //     if (isset($responseData->Result) && $responseData->Result == '000') {
    //         return response()->json(['token' => $responseData->TransToken]);
    //     } else {
    //         return response()->json(['error' => $responseData->ResultExplanation], 400);
    //     }
    // }
    static function createToken($xmlPayload, $grand_total_amount, $redirectUrl, $backUrl, $user_id,$user_address_id,$order_id)
    {
        $endpoint = "https://secure.3gdirectpay.com/API/v6/";
    
        $customerDetails = DB::table('main_users')->find($user_id);

        $addressDetails = DB::table('user_address')->where('id', $user_address_id)->first();
        $customerFirstName = $customerDetails->first_name;
        $customerLastName = $customerDetails->last_name;
        $customerAddress = $addressDetails->address;
        $customerPhone = $customerDetails->phone;
        $customerZip =  $addressDetails->zip_code;
        $customerCity = $addressDetails->city;
        $customerCountryCode = $customerDetails->country_code;
        $customerCountry = $customerDetails->country; 
        $customerEmail = $customerDetails->email;
        // Construct the XML payload
        $xmlData = '<?xml version="1.0" encoding="utf-8"?>
        <API3G>
            <CompanyToken>4CF16A78-27EA-47A7-B1D4-6E52343C8DC1</CompanyToken>
            <Request>createToken</Request>
            <Transaction>
                <PaymentAmount>' . $grand_total_amount . '</PaymentAmount>
                <PaymentCurrency>GHS</PaymentCurrency>
                <CompanyRef>49FKEOA</CompanyRef>
                <RedirectURL>'. $redirectUrl .'</RedirectURL>
                <BackURL>'.$backUrl.'</BackURL>
                <CompanyRefUnique>0</CompanyRefUnique>
                <PTL>5</PTL>
                     <customerFirstName>' . $customerFirstName . '</customerFirstName>
        <customerLastName>' . $customerLastName . '</customerLastName>
        <customerAddress>' . $customerAddress . '</customerAddress>
        <customerPhone>' . $customerPhone . '</customerPhone>
        <customerZip>' . $customerZip . '</customerZip>
        <customerCity>' . $customerCity . '</customerCity>
        <customerCountry>' . $customerCountry . '</customerCountry>
        <customerCountryCode>' . $customerCountryCode . '</customerCountryCode>
        <customerEmail>' . $customerEmail . '</customerEmail>
            </Transaction>
            <Services>
                <Service>
                    <ServiceType>87197</ServiceType>
                    <ServiceDescription>Food And Beverages</ServiceDescription>
                    <ServiceDate>' . date('Y-m-d H:i:s') . '</ServiceDate>
                </Service>
            </Services>
        </API3G>';
        
        $ch = curl_init();
    
        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
    
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
    
        $result = curl_exec($ch);
    
        curl_close($ch);
    
        if ($result === false) {
            return response()->json(['error' => 'cURL request failed'], 400);
        }
    
        // Debugging: Print the XML response
        // Log or print the result to inspect the XML response
        error_log($result);
    
        // Suppress errors and handle them manually
        libxml_use_internal_errors(true);
    
        $responseData = simplexml_load_string($result);
           logger()->info("responseData+++++++++++++++++++++++");
        logger()->info($responseData);
        
        logger()->info("-----------------------------------------");

    
        if ($responseData === false) {
            $errors = libxml_get_errors();
            foreach ($errors as $error) {
                error_log($error->message);
            }
            libxml_clear_errors();
            return response()->json(['error' => 'Invalid XML response'], 400);
        }
    
        // Extract the data
        $result1 = (string) $responseData->Result;
        $resultExplanation = (string) $responseData->ResultExplanation;
        $transToken = (string) $responseData->TransToken;
        $transRef = (string) $responseData->TransRef;
    
        if (isset($responseData->Result) && $responseData->Result == '000') {
            TransactionTokens::create([
                    'order_id' => $order_id,       
                    'transaction_token' => $transToken,
            ]);

            return response()->json(['token' => $responseData->TransToken]);
        } else {
            return response()->json(['error' => $responseData->ResultExplanation], 400);
        }
    }
    

    static function TransactionStatus($token)
    {
        
        
        logger()->info("+++++++++++++++++++++++++++   Helper - TransactionStatus+++++++++++++++++++++++");
        logger()->info($token);
        
        logger()->info("-----------------------------------------");

        $endpoint = "https://secure.3gdirectpay.com/API/v6/";
        $xmlData = Helper::verifyTransactionXmlPayload($token);

        $ch = curl_init();

        if (!$ch) {
            die("Couldn't initialize a cURL handle");
        }
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);

        $result = curl_exec($ch);

        curl_close($ch);

        $responseData = simplexml_load_string($result);
        
        
        logger()->info("+++++++++++++++++++++++++++CheckoutController - responseData+++++++++++++++++++++++");
        logger()->info($responseData);
        
        logger()->info("-----------------------------------------");

        if ($responseData === false) {
            return ['success' => false, 'message' => 'Invalid XML response'];
        }

        $resultCode       = (string) $responseData->Result;
        $resultExplanation= (string) $responseData->ResultExplanation;
        $transactionStatus= (string) $responseData->TransactionStatus;
        $approval         = (string) $responseData->TransactionApproval;

        return [
            'success'   => $resultCode === '000',
            'code'      => $resultCode,
            'message'   => $resultExplanation,
            'status'    => $transactionStatus,
            'approval'  => $approval,
        ];

    }

    static function generateTransactionXmlPayload()
    {
        $backUrl = url('/callBackUrl');
        $xmlPayload = '<?xml version=\"1.0\" encoding=\"utf-8\"?><API3G><CompanyToken>8D3DA73D-9D7F-4E09-96D4-3D44E7A83EA3</CompanyToken><Request>createToken</Request><Transaction><PaymentAmount>40.00</PaymentAmount><PaymentCurrency>GHS</PaymentCurrency><CompanyRef>49FKEOA</CompanyRef><RedirectURL>https://liquorjunctionghana.com</RedirectURL><BackURL>'.$backUrl.'</BackURL><CompanyRefUnique>0</CompanyRefUnique><PTL>5</PTL></Transaction><Services><Service><ServiceType>3854</ServiceType><ServiceDescription>Flight from Nairobi to Diani</ServiceDescription><ServiceDate>2013/12/20 19:00</ServiceDate></Service></Services></API3G>';
        return $xmlPayload;
    }

    static function verifyTransactionXmlPayload($token)
    {
        
        logger()->info("#################################Helper - verifyTransactionXmlPayload #################################");
        logger()->info( $token);
        logger()->info("################################# #################################");
        
        
        $xmlPayload = '<?xml version="1.0" encoding="utf-8"?><API3G><CompanyToken>4CF16A78-27EA-47A7-B1D4-6E52343C8DC1</CompanyToken><Request>verifyToken</Request><TransactionToken>' . $token . '</TransactionToken></API3G>';

        return $xmlPayload;
    }
}
