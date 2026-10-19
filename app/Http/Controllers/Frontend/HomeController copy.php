<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MainUser;
use App\Models\Product;
use App\Models\Blog;
use App\Models\Brand;
use App\Models\Banner;
use App\Models\Categories;
use App\Models\SubCategories;
use Illuminate\Validation\Rule;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Auth;
use Alert;
use DB;
use Mail;
use Carbon\Carbon;
use Storage;
Use Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Level;
use Helper;


class HomeController extends Controller
{

    public function __construct()
    {  //Session::flush();
        // Session::forget('test1');
        // $test = Helper::getCartQuantity(2);
        ///dd(Session::all());
        //$car_old_array = Session::all();
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
    }

    public function index()
    {
        $offer_product = Product::activeProductsBasedOnRelations()->where('offer',1)->limit('20')->orderby('updated_at','desc')->get();
        $best_seller_product = Product::activeProductsBasedOnRelations()->where('is_product_bestseller',1)->limit('20')->orderby('updated_at','desc')->get();
    
        $blogs = Blog::where('status',1)->limit(3)->orderby('created_at','desc')->get();
        $banners = Banner::where('status',1)->limit(6)->orderby('created_at','desc')->where('highlight',null)->where('offer',null)->get();
        $banners_highlight = Banner::where('status',1)->where('highlight',1)->limit(6)->orderby('created_at','desc')->get();
        $banners_offer = Banner::where('status',1)->where('offer',1)->limit(1)->orderby('created_at','desc')->latest()->first();
        $categories = Categories::where('status',1)->orderby('created_at','desc')->get();
       // $bannerData =  Banner::where('status',1)->get();
        return view("frontend.home",compact('offer_product','best_seller_product','blogs','banners','banners_highlight','banners_offer','categories'));
    }

    public function changeLanguage(Request $request){
        $language  = $request->language;
        return session()->put('language', $language);
    }

    public function setTimeZone(Request $request){
        $timezone  = $request->timezone;
        return session()->put('timzone_in_session', $timezone);
    }

    // public function autoSuggestion(Request $request){ 
    //     $keyword =  $request->keyword;
    //     $enter =  $request->enter;
    //     $categoryData = DB::table('categories')->leftjoin('product','product.category_id','=','categories.id')->where('categories.status', 1)->orderBy('categories.id', 'asc')->where('categories.title', 'LIKE', '%'.$keyword.'%')->select('categories.*','product.id as product_id','product.category_id as product_category_id')->groupby('categories.id')->get();
    //    dd($categoryData);
    //   //  $data = compact('');
    //     //return view('frontend.ajax-home', $data)->render();
    // }

    public function subscribeEmail(Request $request)
    {
        //dd($request->email);
        $validator = Validator::make($request->all(), [
                    'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|unique:news_latter',
            ]);

        $data = [
                    'email' =>$request->email,
                    'status' => 1
                ];
    
        if ($validator->fails())
        {
            
            return response()->json($validator->errors(),422);
        }
        else
        {
            $query_insert = \DB::table('news_latter')->insert($data);

            // $logo = \Config::get('app.url').'public/assets/dashboard/images/liquor.png';
            // $url_link = \URL::to("/");
            // $url = $url_link . '/';
            // $email = $request->email;

            // $ismail = $this->attachment_email($email, $url, $logo);

            Alert::success(\Helper::language('success'), __(@Helper::language('newsletter_subscribed_message')));
            return response()->json(
                [
                    'success' => true,
                    'message' => @Helper::language('newsletter_subscribed_message')
                ]
            );
        }  
       //return back()->with('success', 'Your email is subscribed..!');
       return back()->with('success', @Helper::language('newsletter_subscribed_message'));
    }

    // public function attachment_email($email, $url, $logo) {

       
    //     $setting = Setting::find(1);
    //     $from_email = 'admin@vrinsoft.com';
       
    //    // $from_email = $setting['from_email'];
    //     $data = array('email' => $email, 'url' => $url,'id'=>'5','logo' => $logo, 'from_email' => $from_email);
       
    //     \Mail::send('subscribe_email', $data, function ($message) use ($data) {

    //     $message->to($data['email'], 'OnlyDance')->subject('Your Email has Subscribe with us!');
    //     //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

    //     $message->from($data['from_email'], 'OnlyDance');
        
    //     });

    // }


    public function autoSuggestion(Request $request)
    {
        $keyword =  $request->keyword;
        $user_id = $this->user_id;

        $categoryData = Categories::where('status', 1)->orderBy('id', 'ASC');

        $subcategoryData = SubCategories::withWhereHas('category', function ($query) {
            $query->where('status', 1);
        })->where('status', 1)->orderBy('id', 'ASC');
        
        $productDataFilter = Product::withWhereHas('get_product_images', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_product_variants', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_category', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_subcategory', function ($query) {
            $query->where('status', 1);
        })->withWhereHas('get_brand_details', function ($query) {
            $query->where('status', 1);
        })->where('status',1)->orderby('created_at','desc');

        if(session::get('language')==2){
            $categoryData = $categoryData->where('title_fr', 'LIKE', '%'.$keyword.'%')->get();
            $subcategoryData = $subcategoryData->where('title_fr', 'LIKE', '%'.$keyword.'%')->get();
            $productDataFilter = $productDataFilter->where('product_name_fr', 'LIKE', '%'.$keyword.'%')->get();
        }else{            
            $categoryData = $categoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
            $subcategoryData = $subcategoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
            $productDataFilter = $productDataFilter->where('product_name', 'LIKE', '%'.$keyword.'%')->get();
        }

        $data = compact('categoryData','subcategoryData','productDataFilter');
        return view('frontend.searching', $data)->render();;

        
    }

    // public function attachment_quote_email($user_name,$sendname,$sendemail,$category_name,$post_code,$description,$image_name)
    // {
    //     $setting = Setting::find(1);
    //     $from_email = $setting['mail_no_replay'];
    //     $emailtemp = Emailtemplate::find('10');

    //     $data = array('user_name' => $user_name, 'sendname' => $sendname,'sendemail'=>$sendemail,'category_name'=>$category_name,'post_code'=>$post_code,'description'=>$description,'image_name'=>$image_name,'id'=>10,'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);

    //     Mail::send('emails.quote', $data, function ($message) use ($data) {

    //          $message->to($data['sendemail'], $data['title'])->subject($data['subject']);
    //     //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

    //     $message->from($data['from_email'], $data['support_name']);
    //     });
    // }

    // public function UserPromotedProduct(){
    //     // echo "string";exit();
    //      $user_id = $this->user_id;
    //      $setting = Setting::find(1);

    //    $promoteData = DB::table('promoted_product')->leftjoin('product','product.id','=','promoted_product.product_id')->where('promoted_product.status',1)->select('product.*')->orderby('promoted_product.id','DESC')->paginate(8);

    //    $productDataCount = DB::table('promoted_product')->leftjoin('product','product.id','=','promoted_product.product_id')->where('promoted_product.status',1)->select('product.*')->orderby('promoted_product.id','DESC')->count();
    //    // $promoteDataCount = $productDataCount->count();
    //    // echo "<pre>";print_r($productDataCount);exit();

    //    return view("frontEnd.product.promoted-product",compact('promoteData','user_id','productDataCount','setting'));

    // }

    public function dashboardFilterCategory(Request $request,$id)
    {
        $category_id = $request->id;
        $user_id = $this->user_id;

        $categoryDetails = Categories::where('id',$category_id)->first();

        $category_data = DB::table('product')->where('product.category_id',$category_id)->where('status',1)->where('is_admin_approve','=',1)->paginate(8);
        return view("frontend.product.product-list",compact('category_data','categoryDetails','user_id'));
    }
   
}
