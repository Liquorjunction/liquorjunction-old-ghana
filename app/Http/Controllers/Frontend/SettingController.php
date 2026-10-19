<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\DanceClass;
use App\Models\DanceCategory;
use App\Models\ClassType;
use App\Models\MainUser;
use App\Models\ClassLession;
use App\Models\ClassLessionVideo;
use Illuminate\Validation\Rule;
use Auth;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Mail;
use Carbon\Carbon;
use Storage;

class SettingController extends Controller
{

    public function index()
    {
        $setting = Setting::find(1);
        return view("frontEnd.layouts.footer", compact('setting'));
    }

}
