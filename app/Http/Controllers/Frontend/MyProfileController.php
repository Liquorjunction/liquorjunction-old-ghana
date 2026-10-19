<?php

namespace App\Http\Controllers\Frontend;

use Session;
use Validator;
use Mail;
use App\Models\Setting;
use App\Models\MainUser;
use App\Models\UserAddress;
use App\Models\UserBillAddress;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Notification;
use App\Models\ContactUS;
use App\Models\EmailTemplate;
use App\Models\Quote;
use App\Models\Cart;
use Illuminate\Http\Request;
use App\Models\FavoriteProduct;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;
use App\Models\Region;
use App\Models\Area;
use App\Models\Categories;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Rating;
use Hash;
use Illuminate\Contracts\Session\Session as SessionSession;
use App\Models\LoyaltyPoints;
use App\Models\Offers;
use App\Models\Bogo;
use Carbon\Carbon;


class MyProfileController extends Controller
{

    private $uploadPath = "uploads/idproof";

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function __construct()
    {
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = $this->user_id;
        // dd($user_id);
        if ($user_id) {
            $order = DB::table('order')->leftjoin('main_users', 'main_users.id', '=', 'order.supplier_id')->leftjoin('order_detail', 'order_detail.order_id', '=', 'order.id')->leftjoin('product', 'product.id', '=', 'order_detail.product_id')->select('order.*', 'main_users.first_name as fname', 'main_users.last_name as lname', 'order_detail.product_id as p_id', 'product.product_name as pname', 'product.discount_price as pprice', 'product.product_image as pimage', 'order_detail.quantity as quantity')->where('order.user_id', $user_id)->where('order.status', '!=', 2)->latest()->first();

            $UserAddressData = DB::table('user_address')->where('user_id', $user_id)->where('billing_address', 1)->where('status', 1)->first();
            // dd($UserAddressData);
            $UserAddressShippingData = DB::table('user_address')->where('user_id', $user_id)->where('billing_address', 0)->where('status', 1)->first();
            // echo "<pre>";print_r($UserAddressShippingData);exit();
            //    echo "<pre>";print_r($user_id);exit();
            $myProfile = MainUser::where('id', $user_id)->where('status', '!=', 2)->first();
            $categoryName = DB::table('categories')->where('id', $myProfile->category_id)->first();
            // echo "<pre>";print_r($myProfile->toArray());exit();
            return view('frontend.my-profile.my-account', compact('myProfile', 'order', 'UserAddressData', 'UserAddressShippingData', 'categoryName'));
        } else {
            // echo "<pre>";print_r("jjj");exit();
            // return view('frontEnd.home');
            return redirect()->route('frontend.home');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MyProfile  $myProfile
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MyProfile  $myProfile
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $user_id = $this->user_id;
        $myProfile = MainUser::where('id', $user_id)->where('status', '!=', 2)->first();
        $countryData = DB::table('countries')->where('status',1)->orderby('phonecode', 'ASC')->get();
        return view('frontend.my-profile.edit-profile', compact('myProfile', 'countryData'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MyProfile  $myProfile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        if ($request->ajax()) {
            $response = 1;
            $user_id = $this->user_id;
            $validator = Validator::make($request->all(), [
                'first_name' => [
                    'required', 
                    'regex:/^[a-zA-Z]+$/u'
                    
                ],
                'phone' => ['required', 
                            // 'regex:/(01)[0-9]{9}/'
                            ], 
                // 'email' => ['required', Rule::unique('main_users')->ignore($user_id)->where(function ($query) {
                //     return $query->where('status', '!=', '2');
                // })]
                'email' => [
                    'required',
                    Rule::unique('main_users')->ignore($user_id) 
                ]
            ]);

              // Return validation errors
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check if phone already exists for another user
            $phoneExist = MainUser::where('phone', $request->phone)
                        ->where('id', '!=', $user_id)
                        ->first();

            if ($phoneExist) {
                return response()->json([
                    'success' => 'false',
                    'message' => 'Phone number already in use by another user.',
                ], 422);
            }

            $user = MainUser::find($user_id);
            $user->first_name = isset($request->first_name) ? $request->first_name : '';
            $user->last_name = isset($request->last_name) ? $request->last_name : '';
            $user->email = isset($request->email) ? $request->email : '';
            $user->phone = isset($request->phone) ? $request->phone : '';
            $user->phone_code = isset($request->phone_code) ? $request->phone_code : '';
            $user->post_code = @$request->post_code;
            $user->country = @$request->country;
            $user->street_address = @$request->street_address;
            $user->city = @$request->city;
            $user->user_type = 1;
            $user->save();

            Alert::success(\Helper::language('success'), __('backend.user_profile_update_successfully'));

            return response()->json(['success' => 'true']);
        }
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MyProfile  $myProfile
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        //
    }

    public function favorite(Request $request)
    {
        $user_id = $this->user_id;    

        $favorite = DB::table('favorite_product')->select(DB::raw('group_concat(product_id) as product_ids'))->groupBy('user_id')->where('user_id',$user_id)
        ->where('status','!=',2)->first();
        
        $productIds = @$favorite->product_ids ? explode(',', $favorite->product_ids) : [];
       
        $favourite_Product = Product::activeProductsBasedOnRelations()->whereIn('product.id',$productIds )->get();

        // Special offer check
        $today = Carbon::now()->toDateString();

        if($favourite_Product)
        {
            foreach ($favourite_Product as $favour) {
                    $bogo = Bogo::where('status', 1)
                        ->whereDate('start_date', '<=', $today)
                        ->whereDate('end_date', '>=', $today)
                        ->where(function ($query) use ($favour) {
                            $query->where('product_id', $favour->id)
                                ->orWhere(function ($q) use ($favour) {
                                    $q->whereNotNull('subcategory_id')
                                        ->where('subcategory_id', $favour->subcategory_id);
                                })
                                ->orWhere(function ($q) use ($favour) {
                                    $q->whereNull('subcategory_id')
                                        ->where('category_id', $favour->category_id);
                                });

                            if (!empty($favour->brand_id)) {
                                $query->orWhere('brand_id', $favour->brand_id);
                            }
                        })
                        ->first();

                    $favour->bogo_status = $bogo ? true : false; 


                    // offer check
                     $offer = Offers::where('status', 1)
                        ->whereDate('expiry_date', '>=', $today)
                        ->where(function ($query) use ($favour) {
                    $query->where('product_id', $favour->id)
                        ->orWhere(function ($q) use ($favour) {
                            $q->whereNotNull('subcategory_id')
                                ->where('subcategory_id', $favour->subcategory_id);
                        })
                        ->orWhere(function ($q) use ($favour) {
                            $q->whereNull('subcategory_id')
                                ->where('category_id', $favour->category_id);
                        });

                    if (!empty($favour->brand_id)) {
                        $query->orWhere('brand_id', $favour->brand_id);
                    }
                })
                ->first();

                if ($offer) {
                    $favour->offer_status = true;
                    $favour->discount_amount = $offer->dis_amount;
                    $favour->offer_type = $offer->offer_type;
                } else {
                    $favour->offer_status = false;
                    $favour->discount_amount = 0;
                    $favour->offer_type = null;
                }
            }
        }

        return view("frontend.my-profile.favourite",compact('favorite','user_id','favourite_Product','productIds'));
    }

    public function points(Request $request)
    {
        
        $user_id = $this->user_id;    

        if (!$user_id) {
           return redirect()->back()->with('error', 'User not authenticated.');
        }

        try {
            $points = LoyaltyPoints::where('user_id', $user_id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    // Credit points: order must be delivered
                    $q->where('type', 'credit')
                       ->where('status', 1)
                      ->whereIn('order_id', function($sub) {
                          $sub->select('order_id')
                              ->from('order')
                              ->where('order_status', 3);
                      });
                })->orWhere(function ($q) {
                    // Debit points: order must be delivered
                    $q->where('type', 'debit')
                       ->where('status', 1)
                       ->whereIn('order_id', function($sub) {
                            $sub->select('order_id')
                                ->from('order')
                                ->where('order_status', 3);
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();

            $totalPoints = 0;
            foreach ($points as $point) {
                if ($point->type === 'credit') {
                    $totalPoints += $point->points;
                } elseif ($point->type === 'debit') {
                    $totalPoints -= $point->points;
                }
            }

        } catch (\Exception $error) {
            Log::error('Error fetching loyalty points' . $error->getMessage());
            return redirect()->back()->with('error', 'Unable to fetch loyalty points.');
        }

        return view("frontend.my-profile.rewards",compact('user_id','points','totalPoints'));
    }

    public function statusUpdate(Request $request)
    {
        if ($request->id != "") {
          
            
            $fav_product=FavoriteProduct::where('product_id', $request->id)->where('user_id', $request->user_id)->update(['status' => 2]);

            // dd($fav_product);
        }

        Alert::success(\Helper::language('success'), __('backend.product_unfav_successfully'));
        return response()->json(['success' => 'true']);
    }

    public function myaddress()
    {
        $user_id = $this->user_id;
        //$UserAddressData = DB::table('user_address')->where('user_id', $user_id)->where('status', 1)->get();
        $UserAddressData = UserAddress::withWhereHas('country', function ($query){
            $query->where('status', 1);
        })->withWhereHas('region', function ($query){
            $query->where('status', 1);
        })->withWhereHas('area', function ($query){
            $query->where('status', 1);
        })->where([['user_id', $user_id], ['status', 1]])->get();

        $UserBillAddressData = UserBillAddress::withWhereHas('country', function ($query){
            $query->where('status', 1);
        })->withWhereHas('region', function ($query){
            $query->where('status', 1);
        })->withWhereHas('area', function ($query){
            $query->where('status', 1);
        // })->where([['user_id', $user_id], ['status', 1]])->get();
        })->where([['user_id', $user_id], ['status', 1],['default',1]])->get();

        return view('frontend.my-profile.myaddress', compact('UserAddressData','UserBillAddressData'));
    }

    public function editAddress($id)
    {
        $user_id = $this->user_id;

        $UserAddressData = DB::table('user_address')->where('user_id', $user_id)->where('id', $id)->where('status', 1)->first();
        $countryData = DB::table('countries')->where('status',1)->orderby('phonecode', 'ASC')->get();
        $region = Region::where('status',1)->orderby('title','ASC')->get();
        // $area = Area::where('status',1)->orderby('title','ASC')->get();
        $area = [];

        if ($UserAddressData && $UserAddressData->region_id) {
            $area = Area::where('status', 1)
                ->where('region_id', $UserAddressData->region_id)
                ->orderby('title', 'ASC')
                ->get();
        }

        return view('frontend.my-profile.edit-address', compact('UserAddressData','countryData','region','area'));
    }

    public function editBillAddress($id)
    {
        $user_id = $this->user_id;

        $UserAddressData = DB::table('user_bill_address')->where('user_id', $user_id)->where('id', $id)->where('status', 1)->first();
        $countryData = DB::table('countries')->where('status',1)->orderby('phonecode', 'ASC')->get();
        $region = Region::where('status',1)->orderby('title','ASC')->get();
        // $area = Area::where('status',1)->orderby('title','ASC')->get();

        $area = [];

        if ($UserAddressData && $UserAddressData->region_id) {
            $area = Area::where('status', 1)
                ->where('region_id', $UserAddressData->region_id)
                ->orderby('title', 'ASC')
                ->get();
        }


        return view('frontend.my-profile.edit-bill-address', compact('UserAddressData','countryData','region','area'));
    }

    public function addAddress()
    {
        $user_id = $this->user_id;
        $countryData = DB::table('countries')->where('status',1)->orderby('phonecode', 'ASC')->get();
        $region = Region::where('status',1)->orderby('title','ASC')->get();
        $area = Area::where('status',1)->orderby('title','ASC')->get();
        return view('frontend.my-profile.add-address',compact('countryData','region','area'));
        $area = Area::where('status',1)->orderby('title','ASC')->get();
    }

    public function addBillAddress()
    {
        $user_id = $this->user_id;
        $countryData = DB::table('countries')->where('status',1)->orderby('phonecode', 'ASC')->get();
        $region = Region::where('status',1)->orderby('title','ASC')->get();
        $area = Area::where('status',1)->orderby('title','ASC')->get();
        return view('frontend.my-profile.add-bill-address',compact('countryData','region','area'));
        $area = Area::where('status',1)->orderby('title','ASC')->get();
    }

    public function storeAddress(Request $request)
    {
        $user_id = $this->user_id;
        $checkout_page = @$request->checkout_page;
        $add_address = new UserAddress();

        $add_address->name = $request->name;
        // $add_address->last_name = $request->last_name;
        $add_address->user_id = $user_id;
        $add_address->phonecode = $request->phonecode;
        $add_address->phone = $request->phone;
        // $add_address->state = $request->states;
        $add_address->zip_code = $request->zip_code;
        $add_address->city = $request->city;
        $add_address->address = $request->address;
        $add_address->country_id = $request->country_id;
        $add_address->region_id = $request->region_id;
        $add_address->area_id = $request->area_id;

        if ($checkout_page == 0) {
            $add_address->is_selected_address_id = 1;
        } else {
            $add_address->is_selected_address_id = 0;
        }
        $add_address->status = 1;
        $add_address->billing_address = isset($request->billing_address) ? $request->billing_address : '0';

        // Delivery Options
        if($request->delivery_options)
        {
            $add_address->delivery_options=$request->delivery_options;
        }

        if($request->instruction)
        {
            $add_address->delivery_instructions=$request->instruction;
        }

        $add_address->save();
        Alert::success(\Helper::language('success'), __('backend.user_address_added_successfully'));
        return response()->json(['success' => 'true']);
    }

    public function storeBillAddress(Request $request)
    {
        $user_id = $this->user_id;
        $default = $request->isDefault;
        $add_address = new UserBillAddress();
        
        $existing_default_address = UserBillAddress::where('user_id', $user_id)
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

        $add_address->name = $request->bill_name;
        $add_address->user_id = $user_id;
        $add_address->phonecode = $request->bill_phonecode;
        $add_address->phone = $request->bill_phone;
        $add_address->zip_code = $request->bill_zip_code;
        $add_address->city = $request->bill_city;
        $add_address->address = $request->bill_address;
        $add_address->country_id = $request->bill_country_id;
        $add_address->region_id = $request->bill_region_id;
        $add_address->area_id = $request->bill_area_id;

        $add_address->default = $default;
      
        $add_address->status = 1;

        $add_address->save();
        Alert::success(\Helper::language('success'), __('backend.user_address_added_successfully'));
        return response()->json(['success' => 'true']);
    }


    public function updateAddress(Request $request)
    {   
        $user_id = $this->user_id;
        $edit_address = UserAddress::find($request->edit_address_id);
        $edit_address->user_id = $user_id;
        $edit_address->name = $request->name;
        // $edit_address->last_name = $request->last_name;
        $edit_address->phonecode = $request->phonecode;
        $edit_address->phone = $request->phone;
        // $edit_address->state = $request->states;
        $edit_address->zip_code = $request->zip_code;
         $edit_address->city = $request->city;
        $edit_address->address = $request->address;
        $edit_address->country_id = $request->country_id;
        $edit_address->region_id = $request->region_id;
        $edit_address->area_id = $request->area_id;

        $edit_address->status = 1;
         $edit_address->billing_address = isset($request->billing_address) ? $request->billing_address : '0';

         if($request->delivery_options)
         {
             $edit_address->delivery_options=$request->delivery_options;
         }
 
         if($request->instruction)
         {
            $edit_address->delivery_instructions=$request->instruction;
         }
         else
         {
            $edit_address->delivery_instructions=null;
         }

        $edit_address->save();

        Alert::success(\Helper::language('success'), __('backend.user_address_update_successfully'));
        return response()->json(['success' => 'true']);
    }

    public function updateBillAddress(Request $request)
    {   
        $user_id = $this->user_id;
        $edit_address = UserBillAddress::find($request->edit_bill_address_id);
        $default = $request->isDefault;

        $existing_default_address = UserBillAddress::where('user_id', $user_id)
        ->where('default', 1)
        ->where('id', '!=', $request->edit_bill_address_id)
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

        $edit_address->user_id = $user_id;
        $edit_address->name = $request->bill_name;
        $edit_address->phonecode = $request->bill_phonecode;
        $edit_address->phone = $request->bill_phone;
        $edit_address->zip_code = $request->bill_zip_code;
        $edit_address->city = $request->bill_city;
        $edit_address->address = $request->bill_address;
        $edit_address->country_id = $request->bill_country_id;
        $edit_address->region_id = $request->bill_region_id;
        $edit_address->area_id = $request->bill_area_id;
        $edit_address->status = 1;
        
        $edit_address->default = $default;
        $edit_address->save();

        Alert::success(\Helper::language('success'), __('backend.user_address_update_successfully'));
        return response()->json(['success' => 'true']);
    }

    public function addressRemove(Request $request)
    {
        $address_id = $request->address_id;
        $user_id = $this->user_id;
        // echo "<pre>";print_r($request->toArray());exit();
        $updatepsw = UserAddress::where('user_id', $user_id)->where('id', $address_id)->update(array(
            'status' => 2,
        ));

        Alert::success(\Helper::language('success'), __('backend.address_remove_successfully'));
        return response()->json(['success' => 'true']);
    }

    public function billaddressremove(Request $request)
    {
        $address_id = $request->address_id;
        $user_id = $this->user_id;

        $updatepsw = UserBillAddress::where('user_id', $user_id)->where('id', $address_id)->update(array(
            'status' => 2,
            'default' => 0,
        ));

        Alert::success(\Helper::language('success'), __('backend.address_remove_successfully'));
        return response()->json(['success' => 'true']);
    }

    public function getSubcatlist(Request $request)
    {
        $id = $request->id;
        $data['sub'] = Region::where('country_id', '=', $id)->where('status',1)->get(["title", "id"]);
        return response()->json($data);
    }
    public function getArealist(Request $request)
    {
        $id = $request->id;
        $data['sub'] = Area::where('region_id', '=', $id)->where('status',1)->get(["title", "id"]);
        return response()->json($data);
    }

    public function changePassword()
    {
        $user_id = $this->user_id;

        return view('frontend.my-profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');
        $user_id = $this->user_id;

        $validator = Validator::make($request->all(), [
                'old_password' => [
                    'required', function ($attribute, $value, $fail) {
                        if (!Hash::check($value, auth()->guard('user')->user()->password)) {
                            $fail(\Helper::language('old_password_is_incorrect'));
                        }
                    },
                ],
                'new_password'     => 'required|min:6',
                'confirm_password' => 'required|min:6|same:new_password',
            ], 
            [
                'old_password.required' => \Helper::language('old_password_field_is_required'),
                'old_password.min' => \Helper::language('old_password_length_must_be_at_least'),                    
                'new_password.required'=>\Helper::language('new_password_field_required'),
                'new_password.between'=>\Helper::language('new_password_length'),
                'confirm_password.required'=>\Helper::language('confirm_password_required'),
                'confirm_password.min'=>\Helper::language('confirm_password_len'),
                'confirm_password.same'=>\Helper::language('new_confirm_password_not_match_web'),
            ]
        );
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
       
        $user_data = DB::table('main_users')->where('id', $user_id)->first();
        if (!empty($user_data)) {
            $updatepsw = MainUser::where('id', $user_data->id)->update(array(
                'password' => bcrypt($new_password),
            ));
            Alert::success(\Helper::language('success'), __('backend.change_password_successfully'));
            return response()->json(['success' => 'true']);
        } else {
            abort(404);
        }
    }

    public function logoutUser()
    {
        /* dd();Cart*/
        $user_id = auth()->guard('user')->id();
        $cartdata = \DB::table('cart')->where('user_id', $user_id)->where('status', 1)->get();

        $userdata = \DB::table('main_users')->where('id', $user_id)->first();

        if (!empty($cartdata) && count($cartdata) > 0) {
            //$ismail = $this->attachment_remind_order($userdata->email, $userdata->name);
            Auth::guard('user')->logout();
            Session::flush();
        } else {
            Auth::guard('user')->logout();
            Session::flush();
        }
        if($userdata->is_guest_user != 1){
                    if($userdata->remember_token == null){
            $remember_token = \Str::random(64);
            MainUser::where('id', $user_id)->update(['remember_token' => $remember_token ]);
        }else{
            MainUser::where('id', $user_id)->update(['remember_token' => $userdata->remember_token ]);
        }
        }

        /*\Cookie::queue(\Cookie::forget('admin_email'));
        \Cookie::queue(\Cookie::forget('admin_password'));*/
        return response()->json(['success' => 'true']);
    }

    public function attachment_remind_order($email, $name)
    {


        $setting = Setting::find(1);
        // $from_email = $setting['mail_no_replay'];
        $from_email = env('MAIL_FROM_ADDRESS', 'info@liquorjunctionghana.com');
        $emailtemp = Emailtemplate::find('14');

        // $from_email = $setting['from_email'];
        $data = array('email' => $email, 'name' => $name, 'from_email' => $from_email, 'support_name' => $setting['support_name'], 'title' => $emailtemp['title'], 'subject' => $emailtemp['subject']);

        Mail::send('dashboard.remind_order', $data, function ($message) use ($data) {

            $message->to($data['email'], $data['title'])->subject($data['subject']);
            //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

            $message->from($data['from_email'], $data['support_name']);
        });
    }

    
    // public function reOrder(Request $request)
    // {
    //     // echo "<pre>";print_r($request->toArray());exit();
    //     $order_id = $request->id;
    //     $user_id = $this->user_id;


    //     $orderDetailData = DB::table('order_detail')->where('order_id',$order_id)->get();
    //     // echo "<pre>";print_r($orderDetailData);exit;

    //     foreach ($orderDetailData as $detail) {
    //         $supplerCheck = DB::table('product')->leftjoin('main_users','main_users.id','=','product.supplier_id')->where('product.id',$detail->product_id)->select('product.*')->first();
    //         $cartData = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.user_id',$user_id)->where('cart.product_id',$detail->product_id)->where('cart.status',1)->select('cart.*')->first();
    //         $cartDatacheck = DB::table('cart')->leftjoin('product','product.id','=','cart.product_id')->where('cart.user_id',$user_id)->where('cart.status',1)->select('cart.*','product.supplier_id')->first();

    //         $quantity = $detail->quantity;
    //         $offer_price = $detail->quantity*(isset($supplerCheck->discount_price) ? $supplerCheck->discount_price : '0');
    //         $product_price_data = $detail->quantity*(isset($supplerCheck->retail_price) ? $supplerCheck->retail_price : '0');
    //         if ($offer_price==0) {

    //             $total_price = $product_price_data;
    //         }else{

    //             $total_price = $offer_price;
    //         }

    //         // if (@$cartDatacheck->supplier_id != @$supplerCheck->supplier_id && !empty($cartDatacheck)) {
    //         //    Alert::warning('Warning',__('backend.other_wholesaler_product_added_in_cart'));
    //         // return response()->json(['warning' => 'true','code'=>0]);
    //         // }else{
    //         if (!empty($cartData)) {
    //             $updatepsw = Cart::where('user_id', $user_id)->where('product_id',$detail->product_id)->update(array(
    //                 'product_price' => $product_price_data,
    //                 'offer_price' => $offer_price,
    //                 'quantity' => $quantity,
    //                 'total_price' => $total_price,
    //             ));
    //         }else{
    //             $uniqid = uniqid();
    //             $cart = new Cart();
    //             $cart->uniqid = $uniqid;
    //             $cart->product_id = $detail->product_id;
    //             $cart->supplier_id = $supplerCheck->supplier_id;
    //             $cart->product_price = $product_price_data;
    //             $cart->offer_price = $offer_price;
    //             $cart->user_id = $user_id;
    //             $cart->quantity = $quantity;
    //             $cart->total_price = $total_price;
    //             $cart->status = 1;
    //             $cart->save();
    //         }
    //         // }

    //     }
    //     Alert::success('Success',__('backend.cart_added_successfully'));
    //     return response()->json(['success' => 'true']);





    //     // echo "<pre>";print_r($orderData);
    // }


    // public function requestQuote()
    // {
    //     $user_id = $this->user_id;
    //     //dd($user_id);
    //     // echo "<pre>";print_r($user_id);exit();

    //     // $myQuoteData = DB::table('quote')->leftjoin('categories','categories.id','=','quote.category_id')->where('quote.assign_user_id',$user_id)->select('quote.*','categories.title')->orderby('quote.id','DESC')->paginate(5);

    //     $requestQuote = DB::table('quote_send')->leftjoin('quote','quote.id','=','quote_send.quote_id')->leftjoin('categories','categories.id','=','quote.category_id')->leftjoin('time_frame','time_frame.id','=','quote.time_frame_id')->leftJoin('material_category','material_category.id','=','quote.material_id')->select('quote_send.*','categories.title','quote.post_code','quote.quote_image','quote.post_code','time_frame.name as time_frame_name','material_category.name as material_category_name')->where('quote_send.to_id',$user_id)->where('quote_send.status',1)->where('quote.status',1)->orderby('quote_send.id','DESC')->paginate(5);

    //     // echo "<pre>";print_r($orderData->toArray());exit();

    //     return view('frontEnd.my-profile.request-quote',compact('requestQuote'));
    // }

  
    // public function community()
    // {
    //     $communityData = DB::table('community')->leftjoin('product','product.id','=','community.product_id')->leftJoin('main_users','main_users.id','=','community.user_id')->select('community.*','main_users.first_name','main_users.last_name','product.product_name')->where('community.status',1)->orderby('id','DESC')->limit(10)->get();
    //     // echo "<pre>";print_r($communityData);exit();
    //     return view('frontEnd.cms.community',compact('communityData'));
    // }


    // public function howItWorks()
    // {
    //     $cmsData = DB::table('cms')->where('id',5)->first();

    //     return view('frontEnd.cms.how-its-work',compact('cmsData'));
    // }

    public function readNotification(Request $request)
    {
        // echo "string";exit();

        $user_id = $this->user_id;

        $notificationList = DB::table('notification')->join('order','order.id','notification.order_id')->select('notification.*', 'order.id as orderId','order.order_id as order_id' )->where('notification.sender_id',$user_id)->orderby('notification.id','DESC')->get();
        // dd($notificationList);
        $updatepsw = Notification::where('sender_id', $user_id)->update(array(
            'is_read' => 1,
        ));

        $html  = view('frontend.notification')->with(['notificationList' => $notificationList])->render();

        return response()->json(['success' => true,'html' => $html]);
    }

    public function addRating(Request $request)
    {
        // dd($request->all());
        $user_id = \Auth::guard('user')->user()->id;
        
        $orderID =$request->orderId;
        $productID = $request->productId;
        // dd($productID);
       // $validateData = request()->validate();
       
        $validator = Validator::make($request->all(), [
            'rating'=>'required',
            'review' => 'nullable|required',
        ]);
        
            
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ratings = $request->input('rating');     
        $rating = new Rating();
        $rating->user_id = $user_id;
        $rating->product_id = $productID;  // Replace with the actual product ID
        $rating->order_id = $orderID; 
        $rating->ratings = $ratings;
        $rating->review = $request->review;
        $rating->status = '1';
        $rating->save();
        
        $get_average =  \Helper::avrageRating($productID);
        $update_product =  Product::where('id', $productID)->update(array('average_rating' => $get_average));
        
        return response()->json(['message' => 'Ratings added successfully']);
    }
    
    


}
