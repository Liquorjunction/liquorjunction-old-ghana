<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\MainUser;
use App\Models\MembershipPlan;
use App\Models\MembershipPlanSubscription;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Setting;
use Redirect;
use Helper;
use DB;
use Mail;
use URL;
use Config;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    private $uploadPath = "uploads/users/";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => true,
            'permissions_id' => Helper::GeneralWebmasterSettings("permission_group"),    // Permission Group ID
        ]);
    }

    public function RegisterForm(){
        return view('mainuser.register');
    }



    public function store(Request $request)
    {
        
        
        $result = $this->validateRequest();
        //dd($result);

        $main_user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.email','=',$result['email'])
                ->where('main_user.user_type','=',$result['user_type']) 
                ->where('main_user.status','!=','2')
                ->first();

       
        if($main_user == null){
            
            $main_number = DB::table('main_user')
                ->select('main_user.*') 
                ->orderBy('main_user.id', 'DESC')
                ->first();
            //dd($main_number);
            if($main_number == null){
                if($result['user_type'] == 0){
                    $uniq_number_new = 'P001';
                }else if($result['user_type'] == 2){
                    $uniq_number_new = 'H001';
                }else{
                    $uniq_number_new = 'L001';
                }
            }else{
                $uniq_number = $main_number->id + 1;
                if($result['user_type'] == 0){
                    $uniq_number_new = 'P00'.$uniq_number;
                }else if($result['user_type'] == 2){
                    $uniq_number_new = 'H00'.$uniq_number;
                }else{
                    $uniq_number_new = 'L00'.$uniq_number;
                }
            }            
            

            $client = MainUser::create([
                'uniq_id' => $uniq_number_new,
                'user_type' => $result['user_type'],
                'email' => $result['email'],
                'password' => Hash::make($result['password']),
                'original_password' => $result['password'],
                'status' => 1,
            ]);
             
            $user = $client;
            //dd($user);
            if($user->user_type == 0){
                return Redirect::route('membership', array('id' =>$user->id));
            }else{
                return Redirect::route('profile', array('id' =>$user->id));
                //return $this->profile($user);
            }
        }else if($main_user->user_type == 0){
            if($main_user->member_ship_plan == 0){
                //return $this->member_ship_plan($main_user);
                return Redirect::route('membership', array('id' =>$main_user->id));
            }else if($main_user->full_name == ''){
                //return $this->profile($main_user);
                return Redirect::route('profile', array('id' =>$main_user->id));
            }
            /*else if($main_user->medical_histrory == ''){
                return Redirect::route('profile2', array('id' =>$main_user->id));
                //return $this->profile2($main_user);
            }else if($main_user->c_doctor == 0){
                return Redirect::route('profile3', array('id' =>$main_user->id));
                //return $this->profile3($main_user);
            }*/
            else{

                return Redirect::route('regilogin',2);
                //return $this->dashbord();
            }
            
        }else{
            if($main_user->full_name == ''){
                return Redirect::route('profile', array('id' =>$main_user->id));
            }else{
                return Redirect::route('regilogin',2);
            }
            
        }
    }

    public function member_ship_plan($id){
        $member_ship = MembershipPlan::get();
        $user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.id','=',$id)
                ->first();
        return view('mainuser.membership-plan',compact('user','member_ship'));
    }

    public function profile($id){
        $user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.id','=',$id)
                ->first();
        return view('mainuser.profile',compact('user'));
    }

    public function profile2($id){

        $user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.id','=',$id)
                ->first();
        return view('mainuser.profile2',compact('user'));
    }

    public function profile3($id){
        //dd($user);

        $user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.id','=',$id)
                ->first();

        $doctor = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.user_type','=','1')
                ->where('main_user.status','!=','2')
                ->get();

        return view('mainuser.profile3',compact('user','doctor'));
    }

    public function dashbord($id){
        //dd($id);
        $user_id = $id;
        return view('mainuser.welcome',compact('user_id'));
        //return redirect('/');
    }


    public function dashbord1(){
        return view('mainuser.fail');
    }


    public function hospital_store(request $request){
        $result = $this->validateRequesthospital();
        

        $id = $result['id'];

        $formFileName = "profile";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/users/";
            //$path = $this->getUploadPath();
            $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
        }

        $userdetail = MainUser::where('id', $id)->update(array(
            'full_name' => $result['full_name'],
            'phone' => $result['phone'],
            'address' => $result['address'],
            'profile' => $fileFinalName_ar,
            //'certificates' => $fileFinalName_ar1,
            'update_by' => $result['id']
        ));

        $ismail = $this->attachment_email($id);

        return Redirect::route('regilogin',1);
    }

    public function lab_store(request $request){
        $result = $this->validateRequestlab();
        $id = $result['id'];

        $formFileName = "profile";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/users/";
            //$path = $this->getUploadPath();
            $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
        }


        $userdetail = MainUser::where('id', $id)->update(array(
            'full_name' => $result['full_name'],
            'phone' => $result['phone'],
            'address' => $result['address'],
            'profile' => $fileFinalName_ar,
            'update_by' => $result['id']
        ));

        $ismail = $this->attachment_email($id);

        return Redirect::route('regilogin',1);
    }


    public function member_ship(){
        $result = $this->validateRequestmembership();
        
        $id = $result['user_id'];
        $userdetail = MainUser::where('id', $id)->update(array(
            'member_ship_plan' => $result['member_ship_id'],
            'update_by' => $id
        ));

        $membership_plan = DB::table('membership_plan_subscription')
                ->select('membership_plan_subscription.*') 
                ->where('membership_plan_subscription.user_id','=',$id)
                ->where('membership_plan_subscription.plan_id','=',$result['member_ship_id'])
                ->where('membership_plan_subscription.status','!=','2')
                ->first();

        

        if($membership_plan == null){
            $endate =  date('Y-m-d',strtotime("+1 month"));
            $membership = MembershipPlanSubscription::create([
                'user_id' => $result['user_id'],
                'plan_id' => $result['member_ship_id'],
                'start_date' => date('Y-m-d'),
                'end_date' => $endate,
                'create_by' => $id,
                'status' => 1,
            ]);
        }else{

            $membership = MembershipPlanSubscription::where('id', $membership_plan->id)->update(array(
                'status' => 2,
                'update_by' => $membership_plan->user_id
            ));


            if($membership == 1){
                
                $endate =  date('Y-m-d',strtotime("+1 month"));

                $membership = MembershipPlanSubscription::create([
                    'user_id' => $result['user_id'],
                    'plan_id' => $result['member_ship_id'],
                    'start_date' => date('Y-m-d'),
                    'end_date' => $endate,
                    'create_by' => $id,
                    'status' => 1,
                ]);

               /// dd($membership);
            }

        }

        $main_user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.id','=',$result['user_id'])
                ->where('main_user.status','!=','2')
                ->first();

        return Redirect::route('profile', array('id' =>$main_user->id));
        //return $this->profile($main_user);
        
    }

    public function user_profile(request $request){
        $result = $this->validateRequestuser();
        //dd($request);
        $id = $result['id'];

        $formFileName = "profile";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/users/";
            //$path = $this->getUploadPath();
            $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
        }

        //dd($fileFinalName_ar);
        $userdetail = MainUser::where('id', $id)->update(array(
            'fname' => $result['fname'],
            'lname' => $result['lname'],
            'full_name' => $result['fname'].' '.$result['lname'],
            'phone' => $result['telephone'],
            'dob' => date("Y-m-d", strtotime($result['dob'])),
            'blood_type' => $result['bloodtype'],
            'address' => $result['address'],
            'profile' => $fileFinalName_ar,
            'update_by' => $result['id']
        ));

        
        
        $main_user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.id','=',$id)
                ->where('main_user.status','!=','2')
                ->first();

        $membership_plan = DB::table('membership_plan')
                ->where('id','=',$main_user->member_ship_plan)
                ->where('status','!=','2')
                ->first();
        $number = $main_user->country_code.$main_user->phone;
        if($membership_plan->amount == 0){
            return Redirect::route('regilogin',1);
        }else{
           $data = $this->getMpesaParams($number,$membership_plan->amount); 
           $new_data = $this->CallAPI($data);
           if($new_data != false){
                return Redirect::route('regilogin',1);
           }else{
                $userdetail = MainUser::where('id', $main_user->id)->update(array(
                    'status' => 2,
                ));
                return Redirect::route('regilogin1',1);
                
           }
        }
        
        //return Redirect::route('regilogin',1);

        //return Redirect::route('profile2', array('id' =>$main_user->id));
        //return $this->profile2($main_user);
    }

    public function userprofile(request $request){
        $result = $this->validateRequestuser2();
        
        $id = $result['id'];

        $formFileName = "med_document";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $uploadPath = public_path()."/uploads/users/";
            //$path = $this->getUploadPath();
            $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
        }

        $userdetail = MainUser::where('id', $id)->update(array(
            'medical_histrory' => $result['medical_histrory'],   
            'med_document' => $fileFinalName_ar,     
            'update_by' => $result['id']
        ));
        
        $main_user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.id','=',$id)
                ->where('main_user.status','!=','2')
                ->first();


        $doctor = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.user_type','=','1')
                ->where('main_user.status','!=','2')
                ->get();


        return Redirect::route('profile3', array('id' =>$main_user->id));

        //return $this->profile3($main_user,$doctor);
    }


    public function userprofile1(){
        $result = $this->validateRequestuser3();
        $id = $result['id'];
        $userdetail = MainUser::where('id', $id)->update(array(
            'c_doctor' => $result['c_doctor'],
            'update_by' => $result['id']
        ));

        

        $ismail = $this->attachment_email($id);
        return Redirect::route('regilogin',1);
        
    }

    public function attachment_email($id) {

        $setting = Setting::find(1);

        $main_user = DB::table('main_user')
                ->select('main_user.*') 
                ->where('main_user.id','=',$id)
                ->where('main_user.status','!=','2')
                ->first();

        //dd($main_user);

        $url_link = URL::to("/");
        $url = $url_link . '/login';
        $logo = Config::get('app.url').'/public/dist/assets/mainuser/images/logo.png';
        $email = $main_user->email;
        $password = $main_user->original_password;
        $name = $main_user->full_name;
        
        $from_email = env('MAIL_USERNAME');
        $data = array('id' => '2','today' => date('Y-m-d H:i:s'), 'email' => $email, 'password' => $password, 'name' => $name, 'url' => $url, 'logo' => $logo, 'from_email' => $from_email);
        
        Mail::send('mails', $data, function ($message) use ($data) {

            $message->to($data['email'], 'Healthsystem')->subject('New User Registration.');

            $message->from($data['from_email'], 'Healthsystem');
        });

        //return Redirect::route('regilogin');

    }

    /*public function attachment_email($email, $password, $name, $url, $logo) {

        $setting =Setting::find(1);

        $from_email = "noreplay.vrinsoft@gmail.com";

        $data = array('today' => date('Y-m-d H:i:s'), 'email' => $email, 'password' => $password, 'name' => $name, 'url' => $url, 'logo' => $logo, 'from_email' => $from_email);

        Mail::send('subscribemails', $data, function ($message) use ($data) {

            $message->to($data['email'], 'ManageMyuser_type')->subject('Thank you for subscribe with us.');

            $message->from($data['from_email'], 'ManageMyuser_type');
        });

    }*/

    public function validateRequest()
    {
        $validateData =request()->validate([
                'email' => 'required',
                'user_type' => 'required',
                'password' => 'required|min:6',
            ]);
            
        return $validateData;

    } 

    public function validateRequesthospital(){
        $validateData =request()->validate([
                    'id' => '',
                    'full_name' => 'required',
                    'phone' => 'required',
                    'address' => 'required|max:150',
                    'profile' => 'mimes:png,jpeg,jpg,gif,svg',
                    //'certificates' => '',
            ]);
            
        return $validateData;
    }


    public function validateRequestlab(){
        $validateData =request()->validate([
                    'id' => '',
                    'full_name' => 'required',
                    'phone' => 'required',
                    'address' => 'required|max:150',
                    'profile' => 'mimes:png,jpeg,jpg,gif,svg',
            ]);
            
        return $validateData;
    }

    public function validateRequestmembership(){
        $validateData =request()->validate([
                    'user_id' => '',
                    'member_ship_id' => '',
            ]);
            
        return $validateData;
    }


    public function validateRequestuser(){
        $validateData =request()->validate([
                    'id' => '',
                    'fname' => 'required',
                    'lname' => 'required',
                    'telephone' => 'required',
                    'dob' => 'required',
                    'bloodtype' => 'required',
                    'address' => 'required',
                    'profile' => 'mimes:png,jpeg,jpg,gif,svg',
            ]);
            
        return $validateData;
    }


    public function validateRequestuser2(){
        $validateData =request()->validate([
                    'id' => '',
                    'medical_histrory' => 'required',
                    'med_document' => '',
            ]);
            
        return $validateData;
    }


    public function validateRequestuser3(){
        $validateData =request()->validate([
                    'id' => '',
                    'c_doctor' => 'required',
            ]);
            
        return $validateData;
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = Config::get('app.APP_URL') . $uploadPath;
    }

    public function getMpesaParams($number,$payableamount){
            //$number='258'.$number;
            $data=array();
            $data['ApiKey']="151awrju34yskaw74id9fatkin6wsig1";
            $data['pubKey']="MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAyrOP7fgXIJgJyp6nP/Vtlu8kW94Qu+gJjfMaTNOSd/mQJChqXiMWsZPH8uOoZGeR/9m7Y8vAU83D96usXUaKoDYiVmxoMBkfmw8DJAtHHt/8LWDdoAS/kpXyZJ5dt19Pv+rTApcjg7AoGczT+yIU7xp4Ku23EqQz70V5Rud+Qgerf6So28Pt3qZ9hxgUA6lgF7OjoYOIAKPqg07pHp2eOp4P6oQW8oXsS+cQkaPVo3nM1f+fctFGQtgLJ0y5VG61ZiWWWFMOjYFkBSbNOyJpQVcMKPcfdDRKq+9r5DFLtFGztPYIAovBm3a1Q6XYDkGYZWtnD8mDJxgEiHWCzog0wZqJtfNREnLf1g2ZOanTDcrEFzsnP2MQwIatV8M6q/fYrh5WejlNm4ujnKUVbnPMYH0wcbXQifSDhg2jcnRLHh9CF9iabkxAzjbYkaG1qa4zG+bCidLCRe0cEQvt0+/lQ40yESvpWF60omTy1dLSd10gl2//0v4IMjLMn9tgxhPp9c+C2Aw7x2Yjx3GquSYhU6IL41lrURwDuCQpg3F30QwIHgy1D8xIfQzno3XywiiUvoq4YfCkN9WiyKz0btD6ZX02RRK6DrXTFefeKjWf0RHREHlfwkhesZ4X168Lxe9iCWjP2d0xUB+lr10835ZUpYYIr4Gon9NTjkoOGwFyS5ECAwEAAQ==";
            $data['Address'] = "api.vm.co.mz";
            $data['input_TransactionReference']="vT0FJj6lTRzjJXoOQJes";
            $data['input_Amount']=$payableamount;
            $data['input_CustomerMSISDN']=$number;//"258843330333"
            //$data['input_CustomerMSISDN']="258843330333";
            $data['input_ServiceProviderCode']="900107";
            $data['input_InitiatorIdentifier']="Zungas Initiator";
            $data['header_origin']="developer.mpesa.vm.co.mz";
            $data['input_ThirdPartyReference']=time();
            
            return $data;
    }


    public function CallAPI($data=false)
    {
        //ini_set('max_execution_time', 300);

        $data_json = json_encode($data); 
        //dd($data_json);

        $curl = curl_init();
        //curl_setopt($curl, CURLOPT_URL, 'http://ec2-18-234-225-8.compute-1.amazonaws.com:8080/testmPesa1/c2b');
        curl_setopt($curl, CURLOPT_URL, 'http://65.1.25.234:8080/testmPesa1/c2b');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 120); 
        curl_setopt($curl, CURLOPT_TIMEOUT, 120); //timeout in seconds
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_FRESH_CONNECT,TRUE);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                                            'Content-Type:application/json',
                                            'Content-Length: ' . strlen($data_json)
                                            ));

        $json_response = curl_exec($curl);
        //$curl_errorno = curl_errno($curl);
        //$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        return $json_response;
    }



}
