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
use App\Models\Level;

class DanceCategoryController extends Controller
{

    public $setting;
    public function __construct()
    {
        $this->setting = Setting::find(1);
    }
    
    public function index()
    {
        $setting = $this->setting;
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        $dance_category = DanceCategory::where('status',3)->orderby('id', 'desc')->paginate($pagination_limit);
        return view("frontEnd.dance-category.list", compact('dance_category', 'setting'));
    }

    public function danceClassList($id)
    {
        //dd($id);
        //$id = decrypt($id);
        $id = base64_decode($id);
       
        // dd("hello");
        // $dance_class = DanceClass::where('dance_category_id', $id)->get();

        $setting = $this->setting;
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';

        // $dance_class = DanceClass::join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->orderby('class.id', 'desc')->where('class.status',3)->where('class.dance_category_id', $id)->paginate($pagination_limit);

        $min_dance_class_price = DanceClass::select('class.price')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->orderby('class.id', 'desc')->where('class.status',3)->min('class.price');

        $max_dance_class_price = DanceClass::select('class.price')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->orderby('class.id', 'desc')->where('class.status',3)->max('class.price');

        $dance_class = DanceClass::select('class.*','level.title AS dance_level_title','dance_category.*','class.id AS class_id')->join('level', 'level.id', '=', 'class.dance_level')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->orderby('class.id', 'desc')->where('class.status',3)->where('class.dance_category_id', $id)->paginate($pagination_limit);

        $dance_level = Level::where('status',1)->orderby('id', 'ASC')->get();

        $min_duration = $setting->min_duration;
        $max_duration = $setting->max_duration;

       // dd($dance_class);

        return view("frontEnd.dance-class.list", compact('dance_class', 'setting', 'min_dance_class_price', 'max_dance_class_price','dance_level', 'min_duration', 'max_duration'));
    }

}