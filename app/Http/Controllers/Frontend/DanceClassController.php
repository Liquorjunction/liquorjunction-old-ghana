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
use App\Models\Favourite;
use App\Models\Level;
use Xendit\Xendit;
use RealRashid\SweetAlert\Facades\Alert;
use App\Models\ClassPurchaseHistory;

class DanceClassController extends Controller
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

        $dance_class = DanceClass::select('class.*','dance_category.*','level.title AS dance_level_title','class.id AS class_id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);


        $min_dance_class_price = DanceClass::select('class.price')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->orderby('class.id', 'desc')->where('class.status',3)->min('class.price');

        $max_dance_class_price = DanceClass::select('class.price')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->orderby('class.id', 'desc')->where('class.status',3)->max('class.price');

        $min_duration = 0;

        $max_duration = 2;

        $dance_category = DanceCategory::where('status',3)->get();

        $dance_level = Level::where('status',1)->orderby('id', 'ASC')->get();
        
        return view("frontEnd.dance-class.list", compact('setting','dance_class', 'dance_category', 'min_dance_class_price', 'max_dance_class_price','dance_level','min_duration','max_duration'));
    }

    public function searchList()
    {
        $setting = $this->setting;

        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';

        $dance_class = DanceClass::join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
        

        $dance_category = DanceCategory::where('status',3)->get();
        
        return view("frontEnd.dance-class.search-list", compact('setting','dance_class', 'dance_category'));
    }

    public function playVideo(Request $request, $id)
    {
        if($request->ajax())
        {
            if($id != null)
            {
                $video = ClassLessionVideo::select('video_file')->where('id', $id)->first();

                return response()->json($video);
            }
            else
            {
                return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
            }
        }
        abort(404);
    }

    public function watchedVideo(Request $request, $id)
    {
        if($request->ajax())
        {
            if($id != null)
            {
                $watched_video = ClassLessionVideo::where('id', $id)->update(['watched' => 1]);

                $video = ClassLessionVideo::select('watched')->where('id', $id)->first();

                return response()->json($video);
            }
            else
            {
                return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
            }
        }
        abort(404);
    }

    public function addClass(Request $request)
    {
        $dance_class = DanceClass::all();

        $dance_level = Level::where('status',1)->orderby('id', 'ASC')->get();

        $dance_category = DanceCategory::where('status',3)->orderBy('category_name', 'ASC')->get();

        return view('frontEnd.dance-class.add-dance-class', compact('dance_class','dance_level','dance_category'));
    }

    public function addClassPost(Request $request)
    {
        if($request->ajax())
        {
            $post = $request->all();
            //dd($post);
            if($post)
            {
                $class = new DanceClass;
                $class->user_id = auth()->guard('main_user')->user()->id;
                $class->class_name = $request->class_name;
                $class->dance_category_id = $request->category_name;
                $class->dance_level = $request->dance_level;
                $class->duration = $request->duration.' '."min";
                $class->is_popular_dance_class = 0;

                $class->price = $request->price;
                $class->discount = $request->discount_price;
                $class->class_description = $request->class_description;
                $class->status = 3;

                if($class->save())
                {
                    $this->storeThumbnailImage($class);
                    $this->storeIntroductionVideo($class);
                    if(isset($class->id) && !empty($class->id))
                    {
                        foreach($request->lessons as $k=> $lesson)
                        {   
                          
                            $class_lesson_array = [];
                            $class_lesson_array['class_id'] = isset($class->id) ? $class->id : '';
                            $class_lesson_array['user_id'] = isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '';
                            $class_lesson_array['title'] = isset($lesson['lesson_name']) ? $lesson['lesson_name'] : '';
                            $class_lesson_array['description'] = isset($lesson['lesson_description']) ? $lesson['lesson_description'] : '';
                            $class_lesson_array['status'] = 1;

                            $class_lesson = new ClassLession($class_lesson_array);
                            $class_lesson->save();

                            if(isset($class_lesson->id) && !empty($class_lesson->id))
                            {
                                $data = [];
                                for ($i=0; $i < count($lesson['videos']['video_title']) ; $i++) 
                                { 
                                    $firstkey = $lesson['videos']['video_title'][$i];
                                    $firstfile = $lesson['videos']['video_file'][$i];
                                    $duration_of_video = $lesson['videos']['get_duration_video_upload'][$i];
                                    
                                    $data['class_lession_id'] = isset($class_lesson->id) ? $class_lesson->id : '';
                                    $data['video_name'] = isset($firstkey) ? $firstkey : ''; 
                                    $data['get_lesson_video_duration'] = isset($duration_of_video) ? $duration_of_video : '';
                                    $data['status'] = 1; 

                                    $formFileName = $firstfile;
                                   // dd($formFileName);      
                                    $fileFinalName_ar = "";
                                    if ($formFileName != "") {
                                                 $fileFinalName_ar = time() . rand(1111,
                                                         9999) . '.' . $formFileName->getClientOriginalExtension();
                                                 $uploadPath = "/uploads/class_lession/videos/";
                                                 $path = public_path() . $uploadPath;
                                                 $formFileName->move($path, $fileFinalName_ar);

                                                $data['video_file'] = $fileFinalName_ar;
                                    }
                                    $cl_id  = ClassLession::select('class_id')->where('id', $class_lesson->id)->first();
                                   // dd($cl_id);
                                    $class_lesson_video = new ClassLessionVideo($data);
                                    $class_lesson_video->save();

                                    $sum = ClassLessionVideo::where('class_lession_id', $class_lesson->id)->sum('get_lesson_video_duration');
                                    //dd($class_lesson->id);
                                    $duration_video_update = DanceClass::where('id',$cl_id->class_id)->update(['total_duration_min' => $sum]);
                                    
                                }
                                
                            }
                           
                        }
                    }
                        $route = route('my-class');
                        return response()->json(
                        [
                            'success' => true,
                            'message' => 'Data Inserted successfully',
                            'route'=> $route
                        ]
                    );
                }

            }
        }
    }

    public function editClass($id)
    {
        //  $id = decrypt($id);
        $id = base64_decode($id);

        $dance_class = DanceClass::select('class.*','dance_category.*','level.title AS dance_level_title','class.id AS class_id','class_lession.*','class_lession_video.*')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('class_lession', 'class_lession.class_id', '=', 'class.id')->join('class_lession_video', 'class_lession_video.class_lession_id', '=', 'class_lession.id')->where('class.status',3)->where('class.user_id', auth()->guard('main_user')->user()->id)->where('class.id', $id)->first();



        $dance_category = DanceCategory::where('status',3)->get();

        $dance_level = Level::where('status',1)->orderby('id', 'ASC')->get();

        $class_lesson = ClassLession::where('class_id', $id)->where('user_id', auth()->guard('main_user')->user()->id)->get();

        // $class_lesson_video = ClassLessionVideo::where('class_lession_id', $class_lesson->id)->get();

        return view('frontEnd.dance-class.edit-dance-class', compact('dance_class','dance_category','dance_level','class_lesson'));
    }

    public function updateClass(Request $request, $id)
    {
         $data = [];
        if($request->ajax())
        {
            $post = $request->all();
            if($post)
            {
                $dance_class = DanceClass::find($id);
                $dance_class->user_id = auth()->guard('main_user')->user()->id;
                $dance_class->class_name = $request->class_name;
                $dance_class->dance_category_id = $request->category_name;
                $dance_class->dance_level = $request->dance_level;
                $dance_class->duration = $request->duration.' '."min";
                $dance_class->is_popular_dance_class = 0;

                $dance_class->price = $request->price;
                $dance_class->discount = $request->discount_price;
                $dance_class->class_description = $request->class_description;
                $dance_class->status = 3;

                if($dance_class->update($post))
                {
                    $this->storeThumbnailImage($dance_class);
                    $this->storeIntroductionVideo($dance_class);
                    if(isset($dance_class->id) && !empty($dance_class->id))
                    {
                         $class_lesson = [];
                      //dd($request->lessons);
                        foreach($request->lessons as $k=> $lesson)
                        {   
                          
                           
                            $class_lesson['class_id'] = isset($dance_class->id) ? $dance_class->id : '';
                            $class_lesson['user_id'] = isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '';
                            $class_lesson['title'] = isset($lesson['lesson_name']) ? $lesson['lesson_name'] : '';
                            
                            $class_lesson['description'] = isset($lesson['lesson_description']) ? $lesson['lesson_description'] : '';
                            $class_lesson['status'] = 1;

                            $class_lesson_data = ClassLession::whereIn('id',$request->lesson_id)->first();

                            //dd($class_lesson_array);
                           $class_lesson_data->update($class_lesson);
                           // dd($lesson['lesson_name']);
                           

                            if(isset($class_lesson_data->id) && !empty($class_lesson_data->id))
                            {
                               
                               
                                // for ($i=0; $i < count($lesson['videos']['video_title']) ; $i++) 
                                // { 
                                //     $firstkey = isset($lesson['videos']['video_title'][$i]) ? $lesson['videos']['video_title'][$i] : '';
                               // $firstfile = isset($lesson['videos']['video_file'][$i]) ? $lesson['videos']['video_file'][$i] : '';

                                //     $data['class_lession_id'] = isset($class_lesson_data->id) ? $class_lesson_data->id : '';
                                //     $data['video_name'] = isset($firstkey) ? $firstkey : ''; 
                                //     $data['status'] = 1; 

                                //     $formFileName = $firstfile;
                                //    // dd($formFileName);      
                                //     $fileFinalName_ar = "";
                                //     if ($formFileName != "") {
                                //                  $fileFinalName_ar = time() . rand(1111,
                                //                          9999) . '.' . $formFileName->getClientOriginalExtension();
                                //                  $uploadPath = "/uploads/class_lession/videos/";
                                //                  $path = public_path() . $uploadPath;
                                //                  $formFileName->move($path, $fileFinalName_ar);

                                //                 $data['video_file'] = $fileFinalName_ar;
                                //     }

                                //     // $class_lesson_video = new ClassLessionVideo($data);
                                //     // $class_lesson_video->save();
                            
                                //     // $class_lesson_video = ClassLessionVideo::where('class_lession_id',$class_lesson->id)->update($data);
                                //  // dd($lesson['lesson_name']);
                                //     $class_lesson_video = ClassLessionVideo::whereIn('id',$request->video_id)->update($data);
                                    
                                // }
                                //dd($lesson['videos']['video_title']);
                                foreach($lesson['videos']['video_title'] as $v => $title) 
                                { 
                                    
                                    $formFileName = isset($lesson['videos']['video_file'][$v]) ? $lesson['videos']['video_file'][$v] : '';
                                   // dd($formFileName);      
                                    $fileFinalName_ar = "";
                                    if ($formFileName != "") {
                                                 $fileFinalName_ar = time() . rand(1111,
                                                         9999) . '.' . $formFileName->getClientOriginalExtension();
                                                 $uploadPath = "/uploads/class_lession/videos/";
                                                 $path = public_path() . $uploadPath;
                                                 $formFileName->move($path, $fileFinalName_ar);

                                                $data['video_file'] = $fileFinalName_ar;
                                    }
                                    else
                                    {
                                        // dd($request->video_id);
                                        // $delete = ClassLessionVideo::where('id',$request->video_id[$v])->delete();
                                        continue;
                                    }

                                    $data['class_lession_id'] = isset($class_lesson_data->id) ? $class_lesson_data->id : '';
                                    $data['video_name'] = isset($title) ? $title : ''; 
                                    $data['status'] = 1; 

                                    // $class_lesson_video = new ClassLessionVideo($data);
                                    // $class_lesson_video->save();
                            
                                    // $class_lesson_video = ClassLessionVideo::where('class_lession_id',$class_lesson->id)->update($data);
                                 // dd($lesson['lesson_name']);

                                    $class_lesson_video = ClassLessionVideo::where('id',$request->video_id[$v])->update($data);
                                    
                                }

                                
                            }
                           
                        }
                    }
                        $route = route('my-class');
                        return response()->json(
                        [
                            'success' => true,
                            'message' => 'Data Updated successfully',
                            'route'=> $route
                        ]
                    );
                }

            }
        }
    }

    public function deleteClass(Request $request, $id)
    {
            if ($id != "") {

            DanceClass::where('id', $id)->update(['status' => 2]);

            $route = route('my-class');

            return response()->json([

                'success' => true,
                'route' => $route,
                //Alert::success('Success', 'Your Dance class deleted successfully.'),

            ]);

           // Alert::success('Success', 'Your Dance class deleted successfully.');

            // return response()->json(['success' => true, 'msg' => 'Dance class deleted successfully']);
                 
            }
           
    }

    private function storeThumbnailImage($class)
    {

        $formFileName = "class_thumbnail_image";
        $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/dance_class/images/";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

             $class->update([
                'class_thumbnail_image' => $fileFinalName_ar,
            ]);
         }
    }

    private function storeIntroductionVideo($class)
    {
        $formFileName = "instruction_video";
        $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/dance_class/videos/";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

             $class->update([
                'instruction_video' => $fileFinalName_ar,
            ]);
         }
    }

    private function storeClassLessonVideo($class)
    {

        $formFileName = "video_file";
        $fileFinalName_ar = "";
         if (request()->$formFileName != "") {
             $fileFinalName_ar = time() . rand(1111,
                     9999) . '.' . request()->file($formFileName)->getClientOriginalExtension();
             $uploadPath = "/uploads/class_lession/videos/";
             $path = public_path() . $uploadPath;
             request()->file($formFileName)->move($path, $fileFinalName_ar);

             $class->update([
                'video_file' => $fileFinalName_ar,
            ]);
         }
    }

    public function getClassStatus(Request $request, $id)
    {
        if (isset($id) && !empty($id)) {

            $status = \DB::table('class')->select('status','user_id')->where('id', $id)->first();
            $user = \DB::table('main_users')->select('status')->where('id', $status->user_id)->first();
            $user_id = isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '';
            $user_1 = \DB::table('main_users')->select('status')->where('id', $user_id)->first();
            return response()->json(['status' => $status, 'user' => $user, 'user_1' => $user_1]);
        } else {
            return response()->json(['status' => $status, 'user' => $user, 'user_1' => $user_1]);
        }
    }

    //Check-out
    public function goToCheckOut(Request $request)
    {
         $classId = $request->class_id;
         $dance_class = DanceClass::select('class.id','class.class_name','class.instruction_video','class.discount','class.dance_level','level.title AS dance_level_title','class.user_id', 'class.status AS class_status', 'class.price','d.category_name','d.description','m.name as intructore_name','m.profile','m.instructor_facebook_link','m.instructor_instagram_link','m.instructor_web_link','cl.title','clv.class_lession_id','clv.video_name','clv.video_file','f.status','f.user_id as fav_user_id')->leftJoin('dance_category as d', 'd.id', '=', 'class.dance_category_id')->leftJoin('main_users as m', 'm.id', '=', 'class.user_id')->leftJoin('class_lession as cl', 'cl.class_id', '=', 'class.id')->leftJoin('level', 'level.id', '=', 'class.dance_level')->leftJoin('class_lession_video as clv', 'clv.class_lession_id', '=', 'cl.id')->leftJoin('favourite as f','f.class_id','=','class.id')->where('class.status', 3)->where('class.id', $classId)->first();

         $userId = isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '';

        if($dance_class != null)
        {
            $class_price = $dance_class->price;
            $discount = $dance_class->discount;
            $discount_price = ($class_price * $discount) /100;
            $total_price = $class_price - $discount_price;

            if ($total_price == $request->total_price) {
                if (isset($userId) && !empty($userId)) {
                    $response = 1;
                    return response()->json(['status' => 1, 'response' => $response]);
                } else {
                    $response = 0;
                    \Session::put('data', ['raute' => 'danceclassdetailwithid', 'id' => base64_encode($classId)]);
                    return response()->json(['status' => 0, 'response' => $response]);
                }
            }
            else
            {
                $response = 0;
                \Session::put('data', ['route' => 'danceclassdetailwithid', 'id' => base64_encode($classId)]);
                    return response()->json(['status' => 0]);
                return response()->json(['status' => 0, 'response' => $response]);
            }
        }
        else
        {
            $response = 0;
            return response()->json(['status' => 0, 'response' => $response]);
        }
    }

    public function checkOut($id)
     {
         $id = base64_decode($id);
         $dance_class = DanceClass::select('class.id', 'class.class_name', 'class.duration', 'class.class_thumbnail_image', 'class.instruction_video', 'class.dance_level', 'class.price', 'class.discount', 'd.category_name', 'd.description', 'm.name as intructore_name', 'm.profile', 'm.instructor_facebook_link', 'm.instructor_instagram_link', 'm.instructor_web_link','level.title AS dance_level_title')->join('dance_category as d', 'd.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users as m', 'm.id', '=', 'class.user_id')->where('class.id', $id)->first();


         $setting = $this->setting;
         // dd($dance_class);die;
         return view("frontEnd.dance-class.check-out", compact('dance_class','setting'));
    }

    public function getBalance(Request $request)
    {
        
        Xendit::setApiKey('xnd_development_2rHJf9c1kSC3iNeVl8YDKIkhqQwaHMQV0ZjlxuT32XQ6lHLL1KFmy7unZA19');

         $params = [
         'token_id' => '5e2e8231d97c174c58bcf644',
         'external_id' => 'card_' . rand(1, 9999) . time(),
         'authentication_id' => '5e2e8658bae82e4d54d764c0',
         'amount' => $request->amount,
         'card_cvn' => $request->card_cvn,
         'card_number' => $request->card_number,
         'card_exp_month' => $request->card_exp_month,
         'card_exp_year' => $request->card_exp_year,
         'capture' => false
         ];
         
         $createCharge = \Xendit\Cards::create($params);
         logger($createCharge);
        // dd($createCharge);
         return $createCharge;

         return response()->json($createCharge);
    }

    public function xenditCallback(Request $request) 
    {

        // echo "ok";
        // dd(1);

        $xenditXCallbackToken = 'XN7lE0Gs4GebswvoChJen5Bg8u2ROXacHNtpXOO5O9eBfu39';

        $reqHeaders = getallheaders();

       // dd($reqHeaders);
        
        $xIncomingCallbackTokenHeader = isset($reqHeaders['x-callback-token']) ? $reqHeaders['x-callback-token'] : "";

        if($xIncomingCallbackTokenHeader === $xenditXCallbackToken){
         
          $rawRequestInput = file_get_contents("php://input");
          
          $arrRequestInput = json_decode($rawRequestInput, true);
          print_r($arrRequestInput);
          
          $_id = $arrRequestInput['id'];
          $_externalId = $arrRequestInput['external_id'];
          $_userId = $arrRequestInput['user_id'];
          $_status = $arrRequestInput['status'];
          $_paidAmount = $arrRequestInput['paid_amount'];
          $_paidAt = $arrRequestInput['paid_at'];
          $_paymentChannel = $arrRequestInput['payment_channel'];
          $_paymentDestination = $arrRequestInput['payment_destination'];

        
            
        }else{
          
          http_response_code(403);
      }
    }

    public function createInvoice(Request $request) {

        $dance_class = DanceClass::select('class.id', 'class.class_name', 'class.duration', 'class.class_thumbnail_image', 'class.instruction_video', 'class.dance_level', 'class.price', 'class.user_id', 'class.discount', 'd.category_name', 'd.description', 'm.name as intructore_name', 'm.profile', 'm.instructor_facebook_link', 'm.instructor_instagram_link', 'm.instructor_web_link','level.title AS dance_level_title')->join('dance_category as d', 'd.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users as m', 'm.id', '=', 'class.user_id')->where('class.status', 3)->where('class.id', $request->class_id)->first();

        $class_price = $dance_class->price;
        $discount = $dance_class->discount;
        $discount_price = ($class_price * $discount) /100;
        $total_price = $class_price - $discount_price;
       
        if ($total_price == $request->total_price) {
            //order create with fail status / save invoice id

        $setting = $this->setting;
        $commssion_in_per = $setting->commission_in_per;

        $transaction_id = rand(1, 9999) . time();

        $instructor_amount = $dance_class->price;

        $admin_commission_amount = ($instructor_amount * $commssion_in_per) / 100;

        $total_amount = $request->total_price;

        $data[] = [
            'user_id' => $dance_class->user_id,
            'class_id' => $request->class_id,
            'transaction_id' => $transaction_id,
            'total_amount' => $total_amount,
            'admin_commission_amount' => $admin_commission_amount,
            'instructor_amount' =>  $instructor_amount,
            'status' => 2,
            'purchase_user_id' => auth()->guard('main_user')->user()->id,
            'invoice_id' => ''
        ]; 

        $insert_data = \DB::table('class_purchase_history')->insert($data);
        $data_id = ClassPurchaseHistory::select('id')->orderBy('id', 'DESC')->first();
        
        $class_price = $dance_class->price;

        $total_price = $request->total_price;
        $args = '';
        Xendit::setApiKey('xnd_development_2rHJf9c1kSC3iNeVl8YDKIkhqQwaHMQV0ZjlxuT32XQ6lHLL1KFmy7unZA19');

        //$requestData = $this->xenditCallback();
        $date = new \DateTime();
        $oid = base64_encode($data_id->id);
        $order_id = $oid;
        $redirectUrl = route('thankyou',['id' => $order_id]);
        //$redirectUrl = route('xendit-callback');
        $defParams = [
            'external_id' => 'external-' . rand(1,9999) . $date->getTimestamp(),
            'payer_email' => 'invoice_demo@xendit.co', 
            'description' => 'Checkout', 
            'amount' => $total_price,
            'success_redirect_url' => $redirectUrl,
            'failure_redirect_url' => $redirectUrl
        ];

        $data = json_decode(json_encode($request->all()), true);
       // dd($data);
        $defParams['failure_redirect_url'] = $redirectUrl;
        $defParams['success_redirect_url'] = $redirectUrl;
        $params = array_merge($defParams, $data);
        $response = [];

        try {
            $response = \Xendit\Invoice::create($params);
        } catch (\Throwable $e) {
            $response['message'] = $e->getMessage();
        }

        $invoice_id = $response['id'];

        //update invoice id

        $updateRecord = \DB::table('class_purchase_history')->where('id', $data_id->id)->limit(1)
                    ->update(array('invoice_id' => $invoice_id));
        $msg = 0;            
        return response()->json(['msg' => $msg, 'response' => $response]);            
        }else{
            // echo "string";exit;
            $response = '';
            $msg = 1;
            return response()->json(['msg' => $msg, 'response' => $response]);
        }

        

         

        // $getInvoice = \Xendit\Invoice::retrieve($id);

        // logger($response);
       // return $response;

        
    }

    public function thankYou($id)
    {
        $id = base64_decode($id);
        Xendit::setApiKey('xnd_development_2rHJf9c1kSC3iNeVl8YDKIkhqQwaHMQV0ZjlxuT32XQ6lHLL1KFmy7unZA19');

        $invoiceid= \DB::table('class_purchase_history')->select('invoice_id')->where('id', $id)->first();
        $getInvoice = \Xendit\Invoice::retrieve($invoiceid->invoice_id);
        
        $status = $getInvoice['status'];

        if($status == 'PAID')
        {
            \DB::table('class_purchase_history')->where('invoice_id', $invoiceid->invoice_id)->limit(1)->update(['status' => 1, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }
        else
        {
            \DB::table('class_purchase_history')->where('invoice_id', $invoiceid->invoice_id)->limit(1)->update(['status' => 2, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
        }

        $data = [

            'instructor_id' => auth()->guard('main_user')->user()->id,
            'amount' => '0',
            'request_status' => 0,
            'balance' => '10000'
        ];
        \DB::table('withdraw_history')->insert($data);

        // echo "<pre/>";
        // print_r($getInvoice);       

        return view('frontEnd.dance-class.thankyou');     
    }

    public function myClassRate(Request $request)
    {
        if (isset($request->class_id) && isset($request->value)) {
            $userId = isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '';
            $exist = \DB::table('review')->where([['class_id', $request->class_id], ['user_id', $userId]])->first();
            // dd($exist);die;
            if (isset($exist) && !empty($exist)) {
                $update['rate'] = isset($request->value) ? $request->value : null;
                $update['updated_at'] = date('Y-m-d H:i:s');
                $exist = \DB::table('review')->where([['class_id', $request->class_id], ['user_id', $userId]])->update($update);
                $updated = \DB::table('review')->where([['class_id', $request->class_id], ['user_id', $userId]])->first();
                return response()->json(['status' => 1, 'data' => $updated->rate]);
            } else {
                $new = [];
                $new['user_id'] = isset($userId) ? $userId : '';
                $new['class_id'] = isset($request->class_id) ? $request->class_id : '';
                $new['rate'] = isset($request->value) ? $request->value : null;
                $new['status'] =  1;
                $new['created_at'] = date('Y-m-d H:i:s');
                $new['updated_at'] = date('Y-m-d H:i:s');
                $add = \DB::table('review')->insert($new);
                $added = \DB::table('review')->where([['class_id', $request->class_id], ['user_id', $userId]])->first();
                return response()->json(['status' => 1, 'data' => $added->rate]);
            }
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function danceClassSortingLevel(Request $request)
    {
        $data = '';
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        if ($request->ajax()) 
        {
            $search = $request->get('query');
            $option = $request->get('option');

            $min_duration = $request->get('min_duration');
            
            $max_duration = $request->get('max_duration');

            $level = $request->get('level');
            $min_price = $request->get('min_dance_class_price');
            
            $max_price = $request->get('max_dance_class_price');

            $category_id = $request->get('category_id');
            $meta_keywords = explode(',',$category_id);

           // $cat_id = implode(",",$category_id);
           
            if($level != null)
            {
                if($search != null)
                {
                    if($option == 1)
                    {
                        if($category_id != null)
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                            }
                        }
                        else
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                            }
                        }
                    }
                    elseif($option == 2)
                    {
                        if($category_id != null)
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);
                            }
                        }
                        else
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit); 
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit); 
                            }  
                        }
                    }
                    elseif($option == 3)
                    {
                        if($category_id != null)
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                            }
                        }
                        else
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                            }   
                        }
                    }
                    elseif($option == 4)
                    {
                        if($category_id != null)
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                            }
                        }
                        else
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                            }   
                        }
                    }
                    else
                    {
                        if($category_id != null)
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                            }
                        }
                        else
                        {
                            if($min_price != null && $max_price != null)
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                            }
                            else
                            {
                                $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                    $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                    $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                })->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                            }
                        }
                    }
                }
                else
                {
                    if($option == 1)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                        }
                    }
                    elseif($option == 2)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);   
                        }
                    }
                    elseif($option == 3)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);   
                        }
                    }
                    elseif($option == 4)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);   
                        }
                    }
                    else
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                        }
                    }
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'DESC')->where('class.status',3)->where('dance_category.status',3)->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_level', $level)->paginate($pagination_limit);
                }
                $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'DESC')->where('class.status',3)->where('dance_category.status',3)->whereIn('class.dance_level', $level)->paginate($pagination_limit);

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }   
            else
            {
                $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                         $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                 $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            } 

            $links = '';
            $links = $dance_class->links('vendor.pagination.custom_pagination');

            return response()->json(['data' => $data, 'class' => $dance_class, 'links' => $links]);
        }
    }


    public function danceClassSortingNew(Request $request)
    {
        $data = '';
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        if ($request->ajax()) 
        {
            $search = $request->get('query');
            $option = $request->get('option');

            $min_duration = 0;
            
            $max_duration = 2;

            $level = $request->get('level');
            $meta_keywords_1 = explode(',',$level);

            $min_price = $request->get('min_dance_class_price');
            
            $max_price = $request->get('max_dance_class_price');

            $category_id = $request->get('category_id');
           // $meta_keywords = explode(',',$category_id);

            $dance_class = '';
            $dance_class_new = '';

            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status','class.total_duration_min')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where('class.status','=',3)->where('dance_category.status',3);

            

            if($search != null)
            {
                $dance_class_new = $dance_class->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    });
            }

            if($category_id != null)
            {
                $dance_class_new = $dance_class->whereIn('class.dance_category_id', $category_id);
            }

            if($min_price != null && $max_price != null)
            {
                $dance_class_new = $dance_class->whereBetween('class.price', [$min_price, $max_price]);
            }

            if($level != null)
            {
                $dance_class_new = $dance_class->whereIn('class.dance_level', $meta_keywords_1);
            }

            if($min_duration != null && $max_duration != null)
            {
                $dance_class_new = $dance_class->whereBetween('class.total_duration_min', [$min_duration, $max_duration]);
                dd($dance_class_new);
            }

            if($option == 1)
            {
                $dance_class_new = $dance_class->orderby('class.class_name', 'ASC');
            }
            elseif($option == 2)
            {
                $dance_class_new = $dance_class->orderby('class.class_name', 'DESC');
            }
            elseif($option == 3)
            {
                $dance_class_new = $dance_class->orderby('class.price', 'DESC');
            }
            elseif($option == 4)
            {
                $dance_class_new = $dance_class->orderby('class.price', 'ASC');
            }
            else
            {
                $dance_class_new = $dance_class->orderby('class.id', 'DESC');
            }

            $newquery = $dance_class_new->paginate($pagination_limit);
            
                if(isset($newquery) && !empty($newquery))
                {
                    foreach ($newquery as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                    //$route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
                else
                {
                     $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);

                    if(isset($dance_class) && !empty($dance_class))
                    {
                        foreach ($dance_class as $key => $dc) 
                        {
                             $class_price = $dc->price;
                            $discount = isset($dc->discount) ? $dc->discount : 0;
                            $discount_price = ($class_price * $discount) /100;
                            $total_price = $class_price - $discount_price;
                                        $setting = $this->setting;
                                        $currency = isset($setting) ? $setting->currency_symbol : "$";
                                        $dance_id = base64_encode($dc->class_id);
                                        $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                       // $route = route('danceclassdetail');
                                        $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                        $user = MainUser::where("id",$dc->user_id)->first();
                                        $image2 = asset("uploads/website_users/")."/".$user->profile;
                                        $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                        $image3 = asset("assets/frontend/images/play_icon_small.png");
                                        $data .= '<div class="col-md-4 col-sm-6">
                                                        <div class="course_cover_listing">
                                                            <div class="list_box">
                                                                <div class="list_box_video">
                                                                    <a href=' . $route . '>
                                                                        <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                        <span class="play_icon">
                                                                            <img src=' . $image3 . ' alt="play_icon">
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                            <div class="course_details">
                                                                <ul class="course_type">
                                                                    <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                    $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                    $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                                </ul>
                                                                <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                                <ul class="price_nav">
                                                                    <li class="main_price">
                                                                        <span>' . $currency . '</span>
                                                                        <h4>' . $total_price . '</h4>
                                                                    </li>
                                                                    <li class="offer_price">
                                                                         <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                    </li>
                                                                </ul>
                                                                <div class="course_author">
                                                                    <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                    <span>' . $user->name . '</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>';
                                   
                                
                        }
                    }
                }


            $links = '';
            $links = $newquery->links('vendor.pagination.custom_pagination');

            return response()->json(['data' => $data, 'class' => $newquery, 'links' => $links]);
        }
    }

    public function danceClassSortingPrice(Request $request)
    {
        $data = '';
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        if ($request->ajax()) 
        {
            $search = $request->get('query');
            $option = $request->get('option');

            $min_duration = $request->get('min_duration');
            
            $max_duration = $request->get('max_duration');

            $level = $request->get('level');
            $min_price = $request->get('min_dance_class_price');
            
            $max_price = $request->get('max_dance_class_price');

            $category_id = $request->get('category_id');
            $meta_keywords = explode(',',$category_id);

            if(!empty($min_price) && !empty($max_price))
            {
                if($search != null)
                {
                    if($option == 1)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                        }
                    }
                    elseif($option == 2)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);   
                        }
                    }
                    elseif($option == 3)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);   
                        }
                    }
                    elseif($option == 4)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);   
                        }
                    }
                    else
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                            })->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                        }
                    }
                }
                else
                {
                    if($option == 1)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                        }
                    }
                    elseif($option == 2)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);   
                        }
                    }
                    elseif($option == 3)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);   
                        }
                    }
                    elseif($option == 4)
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);   
                        }
                    }
                    else
                    {
                        if($category_id != null)
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                        }
                        else
                        {
                            $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->whereBetween('class.price', [$min_price, $max_price])->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                        }
                    }
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'DESC')->where('class.status',3)->where('dance_category.status',3)->whereBetween('class.price', [$min_price, $max_price])->paginate($pagination_limit);
                }
                
                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                    //$route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }
            else
            {
                $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                         $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }

            $links = '';
            $links = $dance_class->links('vendor.pagination.custom_pagination');

            return response()->json(['data' => $data, 'class' => $dance_class, 'links' => $links]);
        }
    }

    public function danceClassSortingDuration(Request $request)
    {
        $data = '';
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        if ($request->ajax()) 
        {
           // $duration = $request->get('duration');
           
            $search = $request->get('query');
            $option = $request->get('option');

            $min_duration = $request->get('min_duration');
            
            $max_duration = $request->get('max_duration');

            $level = $request->get('level');
            $min_price = $request->get('min_dance_class_price');
            
            $max_price = $request->get('max_dance_class_price');

            $category_id = $request->get('category_id');
            $meta_keywords = explode(',',$category_id);

           // $cat_id = implode(",",$category_id);
           
            if(!empty($min_duration) && !empty($max_duration))
            {
                if($level != null)
                {
                    if($search != null)
                    {
                        if($option == 1)
                        {
                            if($category_id != null)
                            {
                                if($min_price != null && $max_price != null)
                                {
                                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                    })->whereBetween('class.price', [$min_price, $max_price])->whereBetween('class.duration', [$min_duration, $max_duration])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                                }
                            }
                        }
                        elseif($option == 2)
                        {
                            if($category_id != null)
                            {
                                if($min_price != null && $max_price != null)
                                {
                                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                    })->whereBetween('class.price', [$min_price, $max_price])->whereBetween('class.duration', [$min_duration, $max_duration])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);
                                }
                            }
                        }
                        elseif($option == 3)
                        {
                            if($category_id != null)
                            {
                                if($min_price != null && $max_price != null)
                                {
                                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                    })->whereBetween('class.price', [$min_price, $max_price])->whereBetween('class.duration', [$min_duration, $max_duration])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                                }
                            }
                        }
                        elseif($option == 4)
                        {
                            if($category_id != null)
                            {
                                if($min_price != null && $max_price != null)
                                {
                                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                    })->whereBetween('class.price', [$min_price, $max_price])->whereBetween('class.duration', [$min_duration, $max_duration])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                                }
                            }
                        }
                        else
                        {
                            if($category_id != null)
                            {
                                if($min_price != null && $max_price != null)
                                {
                                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                                    })->whereBetween('class.price', [$min_price, $max_price])->whereIn('class.dance_category_id', $meta_keywords)->whereIn('class.dance_level', $level)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                                }
                            }
                        }
                    }
                    else
                    {
                        $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'DESC')->where('class.status',3)->where('dance_category.status',3)->whereBetween('class.duration', [$min_duration, $max_duration])->paginate($pagination_limit);
                    }
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'DESC')->where('class.status',3)->where('dance_category.status',3)->whereBetween('class.duration', [$min_duration, $max_duration])->paginate($pagination_limit);
                }

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }   
            else
            {
                $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                    //$route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            } 

            $links = '';
            $links = $dance_class->links('vendor.pagination.custom_pagination');

            return response()->json(['data' => $data, 'class' => $dance_class, 'links' => $links]);
        }
    }

    public function danceClassSortingCategory(Request $request)
    {
        $data = '';
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        if ($request->ajax()) 
        {
            $search = $request->get('query');
            $option = $request->get('option');

            $min_duration = $request->get('min_duration');
            
            $max_duration = $request->get('max_duration');

            $level = $request->get('level');
            $min_price = $request->get('min_dance_class_price');
            
            $max_price = $request->get('max_dance_class_price');

            $category_id = $request->get('category_id');
            

           // $cat_id = implode(",",$category_id);
           
            if($category_id != null)
            {
                if($search != null)
                {
                    if($option == 1)
                    {
                        $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                            $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                            $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                        })->whereIn('class.dance_category_id', $category_id)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                    }
                    elseif($option == 2)
                    {
                        $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                            $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                            $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                        })->whereIn('class.dance_category_id', $category_id)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);
                    }
                    elseif($option == 3)
                    {
                        $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                            $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                            $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                        })->whereIn('class.dance_category_id', $category_id)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                    }
                    elseif($option == 4)
                    {
                        $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                            $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                            $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                        })->whereIn('class.dance_category_id', $category_id)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                    }
                    else
                    {
                        $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                            $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                            $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                            $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                        })->whereIn('class.dance_category_id', $category_id)->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'desc')->paginate($pagination_limit);
                    }   
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'DESC')->where('class.status',3)->where('dance_category.status',3)->whereIn('class.dance_category_id', $category_id)->paginate($pagination_limit);
                }

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }   
            else
            {
                $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                  //  $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }

            $links = '';
            $links = $dance_class->links('vendor.pagination.custom_pagination'); 

            return response()->json(['data' => $data, 'class' => $dance_class, 'links' => $links]);
        }
    }

    public function danceClassSearch(Request $request)
    {
        $data = '';
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        if ($request->ajax()) 
        {
            $search = $request->get('query');
            $option = $request->get('option');

            $min_duration = $request->get('min_duration');
            
            $max_duration = $request->get('max_duration');

            $level = $request->get('level');
            $min_price = $request->get('min_dance_class_price');
            
            $max_price = $request->get('max_dance_class_price');

            $category_id = $request->get('category_id');
            $meta_keywords = explode(',',$category_id);

            if($search != '')
            {
                if($option == 1)
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                }
                elseif($option == 2)
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);
                }
                elseif($option == 3)
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);
                }
                elseif($option == 4)
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);
                }
                else
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.id', 'desc')->paginate($pagination_limit);
                }
                

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $out_duration = strlen($dc->duration) > 10 ? substr($dc->duration,0,10)."..." : $dc->duration;
                            $class_price = $dc->price;
                            $discount = isset($dc->discount) ? $dc->discount : 0;
                            $discount_price = ($class_price * $discount) /100;
                            $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                    //$route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $out_duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                    }
                }
            }
            else
            {
                $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title','main_users.status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $out_duration = strlen($dc->duration) > 10 ? substr($dc->duration,0,10)."..." : $dc->duration;
                          $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                             $setting = $this->setting;
                                $currency = isset($setting) ? $setting->currency_symbol : "$";
                                $dance_id = base64_encode($dc->class_id);
                                $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                //$route = route('danceclassdetail');
                                $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                $user = MainUser::where("id",$dc->user_id)->first();
                                $image2 = asset("uploads/website_users/")."/".$user->profile;
                                $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                $image3 = asset("assets/frontend/images/play_icon_small.png");
                                $data .= '<div class="col-md-4 col-sm-6">
                                                <div class="course_cover_listing">
                                                    <div class="list_box">
                                                        <div class="list_box_video">
                                                            <a href=' . $route . '>
                                                                <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                <span class="play_icon">
                                                                    <img src=' . $image3 . ' alt="play_icon">
                                                                </span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="course_details">
                                                        <ul class="course_type">
                                                            <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                            $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                            $data .='<li class="course_box blue_box"><span>' . $out_duration . '</span></li>
                                                        </ul>
                                                        <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                        <ul class="price_nav">
                                                            <li class="main_price">
                                                                <span>' . $currency . '</span>
                                                                <h4>' . $total_price . '</h4>
                                                            </li>
                                                            <li class="offer_price">
                                                                 <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                            </li>
                                                        </ul>
                                                        <div class="course_author">
                                                            <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                            <span>' . $user->name . '</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                    }
                }
            }

            $links = '';
            $links = $dance_class->links('vendor.pagination.custom_pagination');

            return response()->json(['data' => $data, 'class' => $dance_class, 'links' => $links]);
        }
    }

    public function autocompleteDanceClass(Request $request)
    {
        $data = [];
       
        $data = DanceClass::select("class_name")
            ->where("class_name", "LIKE", '%' . $request->get('query') . '%')
            ->where('status',3)
            ->pluck('class_name'); 

        return response()->json($data);
    }

    public function danceClassSorting(Request $request)
    {
        $data = '';
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        if ($request->ajax()) 
        {
            $search = $request->get('query');
            $option = $request->get('option');

            $min_duration = $request->get('min_duration');
            
            $max_duration = $request->get('max_duration');

            $level = $request->get('level');
            $min_price = $request->get('min_dance_class_price');
            
            $max_price = $request->get('max_dance_class_price');

            $category_id = $request->get('category_id');
            $meta_keywords = explode(',',$category_id);
           
            if($option == 1)
            {
                if($category_id != null)
                {
                    
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->whereIn('class.dance_category_id', $meta_keywords)->where('class.status',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.class_name', 'ASC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }
                if($search != null)
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);   
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.class_name', 'ASC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }   
            elseif($option == 2)
            {
                if($category_id != null)
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->whereIn('class.dance_category_id', $meta_keywords)->where('class.status',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.class_name', 'ASC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }
                if($search != null)
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.class_name', 'DESC')->paginate($pagination_limit);   
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.class_name', 'DESC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                    //$route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }   
            elseif($option == 3)
            {
                if($category_id != null)
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->whereIn('class.dance_category_id', $meta_keywords)->where('class.status',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.class_name', 'ASC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }
                if($search != null)
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'DESC')->paginate($pagination_limit);   
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.price', 'DESC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }  
            elseif($option == 4)
            {
                if($category_id != null)
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->whereIn('class.dance_category_id', $meta_keywords)->where('class.status',3)->where('dance_category.status',3)->orderby('class.class_name', 'ASC')->paginate($pagination_limit);
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.class_name', 'ASC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }
                if($search != null)
                {
                    $dance_class = \DB::table('class')->select('class.*','class.status AS class_status','dance_category.*','class.id AS class_id','level.status AS level_status','level.title AS dance_level_title','main_users.status AS user_status')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users', 'main_users.id', '=', 'class.user_id')->where(function($query) use ($search){
                        $query->where('class.class_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('dance_category.category_name', 'LIKE', '%'.$search.'%');
                        $query->orWhere('level.title', 'LIKE', '%'.$search.'%');
                        $query->orWhere('main_users.name', 'LIKE', '%'.$search.'%');
                    })->where('class.status','=',3)->where('dance_category.status',3)->orderby('class.price', 'ASC')->paginate($pagination_limit);   
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.price', 'ASC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }
            else
            {
                if($category_id != null)
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->whereIn('class.dance_category_id', $meta_keywords)->where('class.status',3)->where('dance_category.status',3)->orderby('class.id', 'DESC')->paginate($pagination_limit);
                }
                else
                {
                    $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'DESC')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);
                }
                $dance_class = DanceClass::select('class.*','dance_category.*','class.id AS class_id','level.title AS dance_level_title')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('level', 'level.id', '=', 'class.dance_level')->orderby('class.id', 'desc')->where('class.status',3)->where('dance_category.status',3)->paginate($pagination_limit);

                if(isset($dance_class) && !empty($dance_class))
                {
                    foreach ($dance_class as $key => $dc) 
                    {
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                                    $setting = $this->setting;
                                    $currency = isset($setting) ? $setting->currency_symbol : "$";
                                    $dance_id = base64_encode($dc->class_id);
                                    $route = route('danceclassdetailwithid',['id' => $dance_id]);
                                   // $route = route('danceclassdetail');
                                    $dance_category = DanceCategory::where('id',$dc->dance_category_id)->first();
                                    $user = MainUser::where("id",$dc->user_id)->first();
                                    $image2 = asset("uploads/website_users/")."/".$user->profile;
                                    $image1 = asset("uploads/dance_class/images/")."/".$dc->class_thumbnail_image;
                                    $image3 = asset("assets/frontend/images/play_icon_small.png");
                                    $data .= '<div class="col-md-4 col-sm-6">
                                                    <div class="course_cover_listing">
                                                        <div class="list_box">
                                                            <div class="list_box_video">
                                                                <a href=' . $route . '>
                                                                    <img class="listing_popular_img" src=' . $image1 . ' alt="listing_popular_img">
                                                                    <span class="play_icon">
                                                                        <img src=' . $image3 . ' alt="play_icon">
                                                                    </span>
                                                                </a>
                                                            </div>
                                                        </div>
                                                        <div class="course_details">
                                                            <ul class="course_type">
                                                                <li class="course_box orange_box"><span>' . $dance_category->category_name;
                                                                $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';
                                                                $data .='<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                                            </ul>
                                                            <h3><a href=' . $route . '>' . $dc->class_name . '</a></h3>
                                                            <ul class="price_nav">
                                                                <li class="main_price">
                                                                    <span>' . $currency . '</span>
                                                                    <h4>' . $total_price . '</h4>
                                                                </li>
                                                                <li class="offer_price">
                                                                     <h4 class="grey_color">' . $currency . ' ' . $dc->price . '</h4>
                                                                </li>
                                                            </ul>
                                                            <div class="course_author">
                                                                <img src=' . $image2 . ' alt="author_img" height="20%" width="20%">
                                                                <span>' . $user->name . '</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                               
                            
                    }
                }
            }

            $links = '';
            $links = $dance_class->links('vendor.pagination.custom_pagination');
            //dd($links);

            return response()->json(['data' => $data, 'class' => $dance_class, 'links' => $links]);
        }
    }

    public function favouriteStatus(Request $request)
    {
        if (isset($request->login_id) && isset($request->id)) {
            $exist = Favourite::where([['class_id', $request->id], ['user_id', $request->login_id]])->first();
            if (isset($exist) && !empty($exist)) {
                $update['status'] = isset($exist->status) && $exist->status == 1 ? 0 : 1;
                $update['updated_at'] = date('Y-m-d H:i:s');
                $exist->update($update);
                return response()->json(['status' => 1, 'is_favourite' => $exist->status]);
            } else {
                $new = new Favourite();
                $new['user_id'] = isset($request->login_id) ? $request->login_id : '';
                $new['class_id'] = isset($request->id) ? $request->id : '';
                $new['status'] =  1;
                $new['created_at'] = date('Y-m-d H:i:s');
                $new['updated_at'] = date('Y-m-d H:i:s');
                $new->save();
                return response()->json(['status' => 1, 'is_favourite' => $new->status]);
            }
        } else {
            return response()->json(['status' => 0]);
        }
    }

    public function detail()
    {
        return view("frontEnd.dance-class.detail");
    }

    public function danceClassdetail($id)   
    {
        $id = base64_decode($id);

        $setting = $this->setting;

        $pl = env("PAGINATION_LIMIT");

        $pagination_limit = isset($pl) ? $pl : '12';

        $dance_class = DanceClass::select('class.id','class.class_name','class.instruction_video','class.discount','class.dance_level','level.title AS dance_level_title','class.user_id', 'class.status AS class_status', 'class.price','d.category_name','d.description','m.name as intructore_name','m.profile','m.instructor_facebook_link','m.instructor_instagram_link','m.instructor_web_link','cl.title','clv.class_lession_id','clv.video_name','clv.video_file','f.status','f.user_id as fav_user_id')->join('dance_category as d', 'd.id', '=', 'class.dance_category_id')->join('main_users as m', 'm.id', '=', 'class.user_id')->join('class_lession as cl', 'cl.class_id', '=', 'class.id')->join('level', 'level.id', '=', 'class.dance_level')->join('class_lession_video as clv', 'clv.class_lession_id', '=', 'cl.id')->leftJoin('favourite as f','f.class_id','=','class.id')->where('class.status', 3)->where('class.id', $id)->first();

        $dance_class_with_lesson = DanceClass::join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('main_users', 'main_users.id', '=', 'class.user_id')->join('class_lession', 'class_lession.class_id', '=', 'class.id')->join('class_lession_video', 'class_lession_video.class_lession_id', '=', 'class_lession.id')->where('class.status', 3)->where('class.id', $id)->first();

        $dance_class_count = DanceClass::join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('main_users', 'main_users.id', '=', 'class.user_id')->join('class_lession', 'class_lession.class_id', '=', 'class.id')->join('class_lession_video', 'class_lession_video.class_lession_id', '=', 'class_lession.id')->where('class.status', 3)->where('class.id', $id)->count();

        $is_faourite = Favourite::where([['class_id',$id],['user_id',isset(auth()->guard('main_user')->user()->id) ? auth()->guard('main_user')->user()->id : '']])->first();


        if ($dance_class_count == 0) {

            $dance_class = DanceClass::select('class.id','class.class_name','class.instruction_video','class.status AS class_status','class.discount','class.user_id','level.title AS dance_level_title','class.dance_level','class.price','d.category_name','d.description','m.name as intructore_name','m.profile','m.instructor_facebook_link','m.instructor_instagram_link','m.instructor_web_link','f.status','f.user_id as fav_user_id')->join('dance_category as d', 'd.id', '=', 'class.dance_category_id')->join('main_users as m', 'm.id', '=', 'class.user_id')->join('level', 'level.id', '=', 'class.dance_level')->leftJoin('favourite as f','f.class_id','=','class.id')->where('class.status',3)->where('class.id', $id)->first();

        }

        return view("frontEnd.dance-class.detail", compact('dance_class', 'setting', 'dance_class_with_lesson','is_faourite'));

    }

    public function myClassList()
    {
        $setting = $this->setting;
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';
        if (auth()->guard('main_user')->check()) {
            $setting = $this->setting;
            //dd($setting);

            if (auth()->guard('main_user')->user()->user_type == 3) {
                $users = \DB::table('main_users')->select('main_users.*', 'class.id as class_id', 'dance_category.category_name', 'class.dance_level', 'class.duration', 'class.discount','level.title AS dance_level_title','class.class_name', 'class.price','cph.status as purchase_status', 'class.class_thumbnail_image',\DB::raw('COUNT(class.id) as count_class_id'))->join('class', 'class.user_id', '=', 'main_users.id')->join('level', 'level.id', '=', 'class.dance_level')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->leftjoin('class_purchase_history as cph', 'cph.class_id', '=', 'class.id')->where('main_users.status', '=', 3)->where('main_users.user_type', '=', 3)->where('class.status', '=', 3)->where('class.user_id', auth()->guard('main_user')->user()->id)->groupby('class.user_id', 'class.id')->orderBy('class.id', 'DESC')->paginate($pagination_limit);

                //dd(\DB::getQueryLog()); 
               


            } else if (auth()->guard('main_user')->user()->user_type == 2) {
                $users = \DB::table('class')->select('dance_category.category_name', 'class.id as class_id', 'level.title AS dance_level_title','class.dance_level', 'class.discount','class.duration', 'class.class_name', 'class.price', 'class.class_thumbnail_image', 'cph.status as purchase_status', 'mu.user_type', 'mu.id as user_id',\DB::raw('COUNT(cph.class_id) as count_class_id'))->leftjoin('class_purchase_history as cph', 'cph.class_id', '=', 'class.id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('main_users as mu', 'mu.id', '=', 'cph.purchase_user_id')->join('level', 'level.id', '=', 'class.dance_level')->where('class.status', '=', 3)->where('cph.purchase_user_id', auth()->guard('main_user')->user()->id)->groupby('cph.user_id', 'cph.class_id')->orderBy('class.id', 'DESC')->paginate($pagination_limit);
            }
            return view("frontEnd.dance-class.my-class", compact('users', 'setting'));
        } else {
            return redirect()->route('frontend.home');
        }
    }

    public function myClassdetail($id)
    {
        //  $id = decrypt($id);
        $id = base64_decode($id);

        $setting = $this->setting;
        $pl = env("PAGINATION_LIMIT");
        $pagination_limit = isset($pl) ? $pl : '12';

        $dance_class = DanceClass::select('class.id', 'class.class_name', 'class.class_description', 'class.user_id','class.duration', 'class.class_thumbnail_image', 'class.instruction_video', 'class.discount','class.dance_level', 'class.price', 'class_lession.title', 'class_lession_video.video_name', 'd.category_name', 'd.description', 'm.name as intructore_name', 'm.profile', 'm.instructor_facebook_link', 'm.instructor_instagram_link', 'm.instructor_web_link', 'cl.title', 'clv.class_lession_id', 'clv.video_name', 'clv.video_file','level.title AS dance_level_title')->join('dance_category as d', 'd.id', '=', 'class.dance_category_id')->join('class_lession', 'class_lession.class_id', '=', 'class.id')->join('class_lession_video', 'class_lession_video.class_lession_id', '=', 'class_lession.id')->join('main_users as m', 'm.id', '=', 'class.user_id')->join('level', 'level.id', '=', 'class.dance_level')->join('class_lession as cl', 'cl.class_id', '=', 'class.id')->join('class_lession_video as clv', 'clv.class_lession_id', '=', 'cl.id')->where('class.status', 3)->where('class.id', $id)->first();
        $dance_class_with_lesson = DanceClass::join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('main_users', 'main_users.id', '=', 'class.user_id')->join('class_lession', 'class_lession.class_id', '=', 'class.id')->join('class_lession_video', 'class_lession_video.class_lession_id', '=', 'class_lession.id')->where('class.status', 3)->where('class.id', $id)->first();

        $dance_class_count = DanceClass::join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('main_users', 'main_users.id', '=', 'class.user_id')->join('class_lession', 'class_lession.class_id', '=', 'class.id')->join('class_lession_video', 'class_lession_video.class_lession_id', '=', 'class_lession.id')->where('class.status', 3)->where('class.id', $id)->count();

        $review = \DB::table('review')->where('class_id', $id)->get();


        if ($dance_class_count == 0) {
            // $dance_class = DanceClass::join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->join('main_users', 'main_users.id', '=', 'class.user_id')->leftJoin('favourite as f','f.class_id','=','class.id')->where('class.status', 3)->where('class.id', $id)->first();
            $dance_class = DanceClass::select('class.id', 'class.class_name', 'class.duration', 'class.user_id','level.title AS dance_level_title','class.class_thumbnail_image', 'class.discount','class.instruction_video', 'class.dance_level', 'class.price', 'd.category_name', 'd.description', 'm.name as intructore_name', 'm.profile', 'm.instructor_facebook_link', 'm.instructor_instagram_link', 'm.instructor_web_link')->join('level', 'level.id', '=', 'class.dance_level')->join('dance_category as d', 'd.id', '=', 'class.dance_category_id')->join('main_users as m', 'm.id', '=', 'class.user_id')->where('class.status', 3)->where('class.id', $id)->first();
        }


        // dd($dance_class);die;
        return view("frontEnd.dance-class.my-class-detail", compact('dance_class', 'setting', 'dance_class_with_lesson','review'));
    }

    public function myClassReview(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'text' => 'required',

        ], [
            'text.required' => 'The review text field is required.'
        ]);

        $data = [
            'user_id' => auth()->guard('main_user')->user()->id,
            'class_id' => $request->class_id,
            'text' => $request->text,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($validator->fails()) {

            return response()->json($validator->errors(), 422);
        } else {
            // $exist = \DB::table('review')->where([['class_id',$data['class_id']],['user_id',$data['user_id']]])->first();
            // // dd($exist);die;
            // if(isset($exist) && !empty($exist)){
            //     $exist = \DB::table('review')->where([['class_id',$data['class_id']],['user_id',$data['user_id']]])->update($data);
            // }else{
                $query_insert = \DB::table('review')->insert($data);
            // }
            return response()->json(
                [
                    'success' => true,
                    'message' => 'Your review is submitted..!'
                ]
            );
        }
    }

    public function myClassSearch(Request $request)
    {
        $data = '';
        if ($request->ajax()) {
            $setting = $this->setting;
            $pl = env("PAGINATION_LIMIT");
            $pagination_limit = isset($pl) ? $pl : '12';
            $search = $request->get('query');
            if (auth()->guard('main_user')->check()) {
                $setting = $this->setting;
                //dd($setting);

                if (auth()->guard('main_user')->user()->user_type == 3) {
                    $users = \DB::table('main_users')->select('main_users.*', 'class.id as class_id', 'level.title AS dance_level_title','dance_category.category_name', 'cph.status as purchase_status','class.discount', 'class.dance_level', 'class.duration', 'class.class_name', 'class.price', 'class.class_thumbnail_image')->join('class', 'class.user_id', '=', 'main_users.id')->leftjoin('class_purchase_history as cph', 'cph.class_id', '=', 'class.id')->join('level', 'level.id', '=', 'class.dance_level')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->where('main_users.status', '=', 3)->where('main_users.user_type', '=', 3)->where('class.status', '=', 3)->where('class.user_id', auth()->guard('main_user')->user()->id)->where('class.class_name', 'LIKE', "%{$search}%")->groupby('class.user_id', 'class.id')->paginate($pagination_limit);
                    //dd(\DB::getQueryLog()); 
                    // dd($users);



                } else if (auth()->guard('main_user')->user()->user_type == 2) {
                    $users = \DB::table('class')->select('dance_category.category_name', 'class.id as class_id', 'class.dance_level','level.title AS dance_level_title', 'class.duration', 'class.discount','cph.status as purchase_status','class.class_name', 'class.price', 'class.class_thumbnail_image', 'cph.status as purchase_status', 'mu.user_type', 'mu.id')->join('class_purchase_history as cph', 'cph.class_id', '=', 'class.id')->join('dance_category', 'dance_category.id', '=', 'class.dance_category_id')->leftjoin('class_purchase_history as cph', 'cph.class_id', '=', 'class.id')->join('level', 'level.id', '=', 'class.dance_level')->join('main_users as mu', 'mu.id', '=', 'cph.purchase_user_id')->where('cph.purchase_user_id', auth()->guard('main_user')->user()->id)->where('class.status', '=', 3)->groupby('class.user_id', 'class.id')->where('class.class_name', 'LIKE', "%{$search}%")->paginate($pagination_limit);
                }
                if (isset($users) && !empty($users)) {
                    foreach ($users as $dc) {
                        $value = \DB::table('class_purchase_history')->where([['class_id', $dc->class_id], ['user_id', $dc->id]])->count();
                        $class_price = $dc->price;
                        $discount = isset($dc->discount) ? $dc->discount : 0;
                        $discount_price = ($class_price * $discount) /100;
                        $total_price = $class_price - $discount_price;
                        if ($search != '') {
                            $data .= '<div class="col-md-4 col-sm-6">
                            <div class="course_cover_listing">
                                <div class="list_box">
                                    <div class="list_box_video">
                                        <a href="#">
                                            <img class="listing_popular_img" src="' . asset('uploads/dance_class/images/') . '/' . $dc->class_thumbnail_image . '" alt="listing_popular_img">
                                            <span class="play_icon">
                                                <img src="assets/frontend/images/play_icon_small.png" alt="play_icon">
                                            </span>
                                        </a>
                                    </div>
                                </div>';
                            if (isset($dc->user_type) && $dc->user_type == 3 || $dc->user_type == 2) {
                                $data .= '<div class="status_label">';
                                if ($dc->purchase_status == 1) {
                                    $data .= '<span class="bg_approved text_approved">Approved</span>';
                                } else {
                                    $data .= '<span class="bg_pending text_pending">Pending</span>';
                                }

                                $data .= '</div>';
                            }
                            $data .= '<div class="course_details">
                                    <ul class="course_type">
                                        <li class="course_box orange_box"><span>' . $dc->category_name . '</span></li>';
                            $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';

                            $data .= '<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                    </ul>
                                    <h3><a href="#">' . $dc->class_name . '</a></h3>';
                            $data .= '<div class="total_purchased">
                                        <h4>Total Purchased :</h4>';
                            if (isset($dc->user_type) && $dc->user_type == 3) {
                                $data .= '<span style="color : black" >&nbsp;' . isset($value) && $value > 0 ? $value : '0' . '</span>';
                            }
                            $data .= '</div>';
                            $data .= '<ul class="price_nav">
                                        <li class="main_price">
                                            <!-- <span>&#8381;</span> -->
                                            <h4>' . $setting->currency_symbol . ' &nbsp; ' . $total_price . '</h4>
                                        </li>
                                        <li class="offer_price">
                                            <h4 class="grey_color">&#8381; ' . $dc->price . '</h4>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>';
                        } else {
                            $data .= '<div class="col-md-4 col-sm-6">
                            <div class="course_cover_listing">
                                <div class="list_box">
                                    <div class="list_box_video">
                                        <a href="#">
                                            <img class="listing_popular_img" src="' . asset('uploads/dance_class/images/') . '/' . $dc->class_thumbnail_image . '" alt="listing_popular_img">
                                            <span class="play_icon">
                                                <img src="assets/frontend/images/play_icon_small.png" alt="play_icon">
                                            </span>
                                        </a>
                                    </div>
                                </div>';
                            if (isset($dc->user_type) && $dc->user_type == 2 || $dc->user_type == 3) {
                                $data .= '<div class="status_label">';
                                if ($dc->purchase_status == 1) {
                                    $data .= '<span class="bg_approved text_approved">Approved</span>';
                                } else {
                                    $data .= '<span class="bg_pending text_pending">Pending</span>';
                                }

                                $data .= '</div>';
                            }
                            $data .= '<div class="course_details">
                                    <ul class="course_type">
                                        <li class="course_box orange_box"><span>' . $dc->category_name . '</span></li>';
                            $data .='<li class="course_box yellow_box"><span>' . $dc->dance_level_title . '</span></li>';

                            $data .= '<li class="course_box blue_box"><span>' . $dc->duration . '</span></li>
                                    </ul>
                                    <h3><a href="#">' . $dc->class_name . '</a></h3>';
                            $data .= '<div class="total_purchased">
                                    <h4>Total Purchased :</h4>';
                            if (isset($dc->user_type) && $dc->user_type == 3) {
                                $data .= '<span style="color : black;">&nbsp;' .isset($value) && $value > 0 ? $value : '0' . '</span>';
                            }
                            $data .= '</div>';
                            $data .= '<ul class="price_nav">
                                        <li class="main_price">
                                            <!-- <span>&#8381;</span> -->
                                            <h4>' . $setting->currency_symbol . ' &nbsp; ' . $total_price . '</h4>
                                        </li>
                                        <li class="offer_price">
                                            <h4 class="grey_color">&#8381; ' . $dc->price . '</h4>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>';
                        }
                    }
                }

                $links = '';
                $links = $dance_class->links('vendor.pagination.custom_pagination');

                return response()->json(['data' => $data, 'class' => $users, 'links' => $links]);
            }
        }
    }
}
