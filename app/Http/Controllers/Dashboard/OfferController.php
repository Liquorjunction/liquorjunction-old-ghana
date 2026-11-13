<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Auth;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use DB;
use Session;
use Illuminate\Support\Facades\Validator;
use Alert;
use Illuminate\Validation\Rule;
use App\Models\Offers;
use App\Models\Bogo;
use App\Models\OfferImage;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Product;
use App\Models\News;
use App\Models\EmailTemplate;
use Mail;
use Carbon\Carbon;
use Intervention\Image\Facades\Image;


class OfferController extends Controller
{

    private $uploadPath = "uploads/offers/";
    protected $image_uri = "";
    protected $no_image = "";

    public function getImagePath()
    {
        return $this->uploadPath;
    }

    public function setImagePath()
    {
        $this->image_uri = $this->getImagePath() . '/';
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 31, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }

    }

    public function index()
    {
        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        $subcategories = SubCategories::where('status', 1)->orderby('title', 'ASC')->get();
        
        return view("dashboard.offer.list", compact('categories', 'product','brand','subcategories'));
    }

    public function offerstore(Request $request)
    {

        // if ($request->min_amount >= $request->max_amount) {
        //     return response()->json(['max_amount' => ['Maximum amount must be greater than minimum amount.']], 422);
        // }

        if (empty($request->offer_id)) {
            $brand_id = $request->brand_id;
            $category_id = $request->category_id;
            $subcategory_id = $request->subcategory_id;
            $product_id = $request->product_id;
            $product_type=$request->product_type;
            $discount_value=0;

            if ($brand_id && $product_type==1) {
                $category_id = null;
                $product_id = null;
                $subcategory_id=null;
            }
            elseif ($category_id && $product_type==2) {
                $brand_id = null;
                $product_id = null;
            }
            elseif ($subcategory_id && $product_type==2) {
                $brand_id = null;
                $product_id = null;
            }
            elseif ($product_id && $product_type==3) {
                $brand_id = null;
                $category_id = null;
                $subcategory_id=null;
            }

            if($request->dis_amount)
            {
                $discount_value=$request->dis_amount;
            }
            else if($request->dis_percentage)

            {
                $discount_value=$request->dis_percentage;
            }
            
        $validator = \Validator::make(
                    $request->all(),
                    [
                        'offer_type' => 'required|string',
                        'dis_amount' => 'nullable|numeric|min:0',  
                        'dis_percentage' => 'nullable|numeric|min:0', 
                        // 'min_amount' => 'required|numeric|min:0',
                        // 'max_amount' => 'required|numeric',
                        'expiry_date' => 'required|date|after:today',
                        // 'total_usage' => 'required|numeric|min:1',
                        // 'max_users' => 'required|numeric|min:1',
                        'template' => 'required|string',
                        'custom_url' => 'required|string',
                        'offer_images.*' => 'mimetypes:image/png,image/jpg,image/jpeg|max:2048', 
                        'offer_images' => 'required|array|max:4',
                    ]
            );

            $validator->after(function ($validator) use ($request, $brand_id, $category_id, $subcategory_id, $product_id) {
                // Custom Rule 1: Ensure at least one discount value
                if (empty($request->dis_amount) && empty($request->dis_percentage)) {
                    $validator->errors()->add('dis_amount', 'Either Discount Amount or Discount Percentage is required yolo.');
                }

                // Custom Rule 2: Check for conflict
                $conflict = Bogo::where('status', 1)
                    ->where(function ($query) use ($brand_id, $category_id, $subcategory_id, $product_id) {
                        if ($brand_id) {
                            $query->orWhere('brand_id', $brand_id);
                        }
                        if ($subcategory_id) {
                            $query->orWhere('subcategory_id', $subcategory_id);
                        } elseif ($category_id) {
                            $query->orWhere(function ($q) use ($category_id) {
                                $q->whereNull('subcategory_id')->where('category_id', $category_id);
                            });
                        }
                        if ($product_id) {
                            $query->orWhere('product_id', $product_id);
                        }
                    })
                    ->exists();

                if ($conflict) {
                    $validator->errors()->add('conflict', 'A Bogo offer with the same product, brand, or category is already active.');
                }
            });

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }


            $newStatus = 1;

            //  update all others to 0
            if ($newStatus == 1) {
                Offers::where('status', 1)->update(['status' => 0]);
            }

            $offer = new Offers();
            $offer->offer_type = $request->input('offer_type');
            $offer->dis_amount = $discount_value;
            // $offer->min_amount = $request->input('min_amount');
            // $offer->max_amount = $request->input('max_amount');
            $offer->expiry_date = $request->input('expiry_date');
            // $offer->total_usage = $request->input('total_usage');
            // $offer->max_users = $request->input('max_users');
            $offer->product_type = $product_type;
            $offer->brand_id = $brand_id;
            $offer->category_id = $category_id;
            $offer->subcategory_id = $subcategory_id;
            $offer->product_id = $product_id;
            $offer->template = $request->input('template');
            $offer->custom_url = $request->input('custom_url');
            $offer->status = $newStatus;
            $offer->save();

            // Image
            $formFileName = "offer_images";
            if ($request->$formFileName != "") {
                foreach ($request->$formFileName as $images) {
                    if ($images != "") {
                        $imagesAR = time() . rand(
                            1111,
                            9999
                        ) . '.' . $images->getClientOriginalExtension();
                        $path = $this->getUploadPath();
                        // $images->move($path, $imagesAR);

                        // resize the image
                        $resizedImage = Image::make($images)->resize(480, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });

                        $resizedImage->save($path . '/' . $imagesAR);
                    }
                    $offerImage = new OfferImage();
                    $offerImage->offer_id = $offer->id;
                    $offerImage->image = $imagesAR;
                    $offerImage->status = 1;
    
                    $offerImage->save();
                }
            }

            Alert::success('Success', 'New Offer created successfully');
            return response()->json(['success' => 'true']);
        } else {

            $id = $request->offer_id;
    
            $offer = Offers::find($request->input('offer_id'));

            if (!$offer) {
                return response()->json(['error' => 'Offer not found.'], 404);
            }

            
            $brand_id = $request->brand_id;
            $category_id = $request->category_ids;
            $subcategory_id = $request->subcategory_ids;
            $product_id = $request->product_id;
            $product_type=$request->type;
            $discount_value=0;

            if ($brand_id && $product_type==1) {
                $category_id = null;
                $product_id = null;
                $subcategory_id=null;
            }
            elseif ($category_id && $product_type==2) {
                $brand_id = null;
                $product_id = null;
            }
            elseif ($subcategory_id && $product_type==2) {
                $brand_id = null;
                $product_id = null;
            }
            elseif ($product_id && $product_type==3) {
                $brand_id = null;
                $category_id = null;
                $subcategory_id=null;
            }
            
            if($request->dis_amount && $request->input('offer_types')=='flat')
            {
                $discount_value=$request->dis_amount;
            }
            else if($request->dis_percentage && $request->input('offer_types')=='percentage')

            {
                $discount_value=$request->dis_percentage;
            }
            
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'offer_id' => 'required|exists:offers,id',
                        'offer_types' => 'required|string',
                        'dis_amount' => 'nullable|numeric|min:0',  
                        'dis_percentage' => 'nullable|numeric|min:0',  
                        // 'min_amount' => 'required|numeric|min:0',
                        // 'max_amount' => 'required|numeric',
                        'expiry_date' => 'required|date|after:today',
                        // 'total_usage' => 'required|numeric|min:1',
                        // 'max_users' => 'required|numeric|min:1',
                        'template' => 'required|string',
                        'custom_url' => 'required|string',
                        'offer_image.*' => 'mimetypes:image/png,image/jpg,image/jpeg|max:2048', 
                    ]
                );

                $validator->after(function ($validator) use ($request, $brand_id, $category_id, $subcategory_id, $product_id) {
                // Custom Rule 1: Ensure at least one discount value
                if (empty($request->dis_amount) && empty($request->dis_percentage)) {
                    $validator->errors()->add('dis_amount', 'Either Discount Amount or Discount Percentage is required yolo.');
                }

                // Custom Rule 2: Check for conflict
                $conflict = Bogo::where('status', 1)
                    ->where(function ($query) use ($brand_id, $category_id, $subcategory_id, $product_id) {
                        if ($brand_id) {
                            $query->orWhere('brand_id', $brand_id);
                        }
                        if ($subcategory_id) {
                            $query->orWhere('subcategory_id', $subcategory_id);
                        } elseif ($category_id) {
                            $query->orWhere(function ($q) use ($category_id) {
                                $q->whereNull('subcategory_id')->where('category_id', $category_id);
                            });
                        }
                        if ($product_id) {
                            $query->orWhere('product_id', $product_id);
                        }
                    })
                    ->exists();

                if ($conflict) {
                    $validator->errors()->add('conflict', 'A Bogo offer with the same product, brand, or category is already active.');
                }
            });

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }
        




            $offer->offer_type = $request->input('offer_types');
            $offer->dis_amount = $discount_value;
            // $offer->min_amount = $request->input('min_amount');
            // $offer->max_amount = $request->input('max_amount');
            $offer->expiry_date = $request->input('expiry_date');
            // $offer->total_usage = $request->input('total_usage');
            // $offer->max_users = $request->input('max_users');
            $offer->product_type = $product_type;
            $offer->brand_id = $brand_id;
            $offer->category_id = $category_id;
            $offer->subcategory_id = $subcategory_id;
            $offer->product_id = $product_id;
            $offer->template = $request->input('template');
            $offer->custom_url = $request->input('custom_url');

            $offer->save();

            if (!empty($request->deleted_image)) {
                foreach ($request->deleted_image as $delete) {
                    $offerImage = OfferImage::where('id', $delete)->update(
                        array(
                            'status' => 2,
                        )
                    );
                }
            }

            if (!empty($request->offer_image)) {
                foreach ($request->offer_image as $images) {
                    if ($images != "") {
                        $imagesAR = time() . rand(
                            1111,
                            9999
                        ) . '.' . $images->getClientOriginalExtension();
                        $path = $this->getUploadPath();
                        // $images->move($path, $imagesAR);

                        // resize the image
                        $resizedImage = Image::make($images)->resize(480, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });

                         $resizedImage->save($path . '/' . $imagesAR);

                    }
                    $offerImage = new OfferImage();
                    $offerImage->offer_id = $id;
                    $offerImage->image = $imagesAR;
                    $offerImage->status = 1;

                    $offerImage->save();
                }
            }



            Alert::success('Success','Offer updated successfully');
            return response()->json(['success' => 'true']);
        }
    }

    public function destroy($id)
    {
        $offer = Offers::find($id);
        $offer->status = 2;
        $offer->save();

        return redirect()->route('offer')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function sendMail(Request $request)
    {

        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];

        $subscribers = News::all(); 

        $offer = Offers::with([
                    'get_offer_images' => function ($q) {
                        return $q->where('status', '=', 1);
                    }
                ])->where('id', $request->input('offer_id'))->where('status', '!=', 2)->first();

        if (!$offer) {
            return response()->json(['error' => 'Offer not found.'], 404);
        }


        $emailtemp = Emailtemplate::find('23');
        // $emailtemp = Emailtemplate::find('24');

        $btn_text=$offer->template;
        $btn_url=$offer->custom_url;

        $offer_images = [];
        if ($offer->get_offer_images && $offer->get_offer_images->count() > 0) {
            foreach ($offer->get_offer_images->take(4) as $img) {
                $offer_images[] = asset('uploads/offers/' . $img->image);
            }
        }

        // $hoursRemaining = 0;
        // if ($offer->expiry_date) {  
        //     $now = Carbon::now();
        //     $expiry = Carbon::parse($offer->expiry_date);
        //     $hoursRemaining = max($now->diffInHours($expiry, false), 0); 
        // }

        $expireDate ='';
        if ($offer->expiry_date) {  
            $expireDate = Carbon::parse($offer->expiry_date)->format('d F Y');
        }

        foreach ($subscribers as $subscriber) {
            $data = [
                'email' => $subscriber->email,
                'name' => '',
                'from_email' => $from_email,
                'support_name' => $setting['support_name'],
                'title' => $emailtemp['title'],
                'subject' => $emailtemp['subject'],
                'btn_text' => $btn_text,
                'btn_url' => $btn_url,
                'offer_images' => $offer_images,
                // 'expire' => $hoursRemaining,
                'expire' => $expireDate,
                'logo' => asset('assets/dashboard/images/liquor.png'),
                'settingfacebook' => $setting->facebook_link ?? '',
                'settingtwitter' => $setting->twitter_link ?? '',
                'settinginstagram' => $setting->instagram_link ?? '',
                'facebook_logo' => asset('assets/dashboard/images/facebook.png'),
                'insta_logo' => asset('assets/dashboard/images/insta.png'),
                'in_logo' => asset('assets/dashboard/images/in.png'),
            ];
    
            Mail::send('dashboard.send_offer', $data, function ($message) use ($data) {
                $message->to($data['email'], $data['title'])->subject($data['subject']);
                $message->from($data['from_email'], $data['support_name']);
            });
        }

        return response()->json(['message' => 'Emails sent successfully.']);
    }


    public function offeredit(Request $request)
    {
        if ($request->ajax()) {
            $offer_id = $request->id;

            if ($offer_id) {
                $editData = Offers::with([
                    'get_offer_images' => function ($q) {
                        return $q->where('status', '=', 1);
                    }
                ])->where('id', $offer_id)->where('status', '!=', 2)->first();

                if (!$editData) {
                    return response()->json(['success' => false, 'msg' => 'Offer not found or inactive.']);
                }

                $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
                $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
                $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
                $subcategories = SubCategories::where([['category_id', $editData->category_id], ['status', 1]])->orderby('title', 'ASC')->get();

                $html = view('dashboard.offer.edit', ['editData' => $editData,'categories' => $categories,'brand' => $brand, 'product' => $product,'subcategories'=>$subcategories])->render();
               
                return response()->json(['success' => true, 'html' => $html]);
            }

            return response()->json(['success' => false, 'msg' => 'Invalid Offer ID.']);

        }
    }


    public function offerUpdateAll(Request $request)
    {
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;

                Offers::wherein('id', $ids)->update(['status' => $status]);

                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) delete successfully']);
                } else if ($status == 0) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) deactivated successfully']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'Record(s) activated successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
      
    }

    public function show(Request $request)
    {
        $offer_id = $request->id;

        $offerData = Offers::with([
            'get_offer_images' => function ($q) {
                return $q->where('status', '=', 1);
            }
        ])->where('id', $offer_id)->where('status', '!=', 2)->first();

        if ($offerData) {
            $html = view('dashboard.offer.show', ['offerData' => $offerData])->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        
        return response()->json(['success' => false, 'msg' => 'Offer not found or inactive.']);
    }
   

    // public function status_active(Request $request)
    // {
    //     $offer_id = $request->id;

    //     if (!$offer_id) {
    //         return response()->json(['success' => false, 'msg' => 'Invalid Offer ID.']);
    //     }

    //     $offer = Offers::where('id', $offer_id)->first();

    //     if (!$offer) {
    //         return response()->json(['success' => false, 'msg' => 'Offer not found.']);
    //     }

    //     if ($offer->status != 0) {
    //         $offer->status = 0;  
    //         $offer->save();
    
    //         Alert::success('Success', 'Offer deactivated successfully');
    //         return response()->json(['success' => true]);
    //     }

    //     return response()->json(['success' => false, 'msg' => 'Offer is already deactivated.']);
    // }


    // public function status_inactive(Request $request)
    // {
    //     $offer_id = $request->id;

    //     if (!$offer_id) {
    //         return response()->json(['success' => false, 'msg' => 'Invalid Offer ID.']);
    //     }

    //     $offer = Offers::where('id', $offer_id)->first();

    //     if (!$offer) {
    //         return response()->json(['success' => false, 'msg' => 'Offer not found.']);
    //     }

    //     if ($offer->status != 1) {
    //         $offer->status = 1;  
    //         $offer->save();

    //         Alert::success('Success', 'Offer activated successfully');
    //         return response()->json(['success' => true]);
    //     }

    //     return response()->json(['success' => false, 'msg' => 'Offer is already active.']);
    // }


    public function status_active(Request $request)
    {
        $offer_id = $request->id;

        if (!$offer_id) {
            return response()->json(['success' => false, 'msg' => 'Invalid Offer ID.']);
        }

        $offer = Offers::where('id', $offer_id)->first();

        if (!$offer) {
            return response()->json(['success' => false, 'msg' => 'Offer not found.']);
        }

        if ($offer->status != 0) {
            $offer->status = 0;  
            $offer->save();
               
            Alert::success('Success', 'Offer deactivated successfully');
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'msg' => 'Offer is already deactivated.']);
    }

    public function status_inactive(Request $request)
    {
        $offer_id = $request->id;

        if (!$offer_id) {
            return response()->json(['success' => false, 'msg' => 'Invalid Offer ID.']);
        }

        $offer = Offers::where('id', $offer_id)->first();

        if (!$offer) {
            return response()->json(['success' => false, 'msg' => 'Offer not found.']);
        }

        if ($offer->status != 1) {
            Offers::where('status', 1)->where('id', '!=', $offer_id)->update(['status' => 0]);

            $offer->status = 1;  
            $offer->save();

            Alert::success('Success', 'Offer activated successfully');
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'msg' => 'Offer is already active.']);
    }

    public function anyData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $order_arr = $request->get('order');
        $column_arr = $request->get('columns');
        $search = $request->get('search')['value'];
    
        // Use 'name' instead of 'data' to ensure correct DB column usage
        $columnIndex = $order_arr[0]['column'];
        $columnName = $column_arr[$columnIndex]['name'] ?? 'id'; // fallback to 'id' if not set
        $columnSortOrder = $order_arr[0]['dir'] ?? 'desc';
    
        // Whitelist allowed columns for ordering (security)
        $validColumns = [
            'offer_type', 'product_type','dis_amount', 'min_amount', 'max_amount',
            'total_usage', 'max_users','created_at' ,'expiry_date', 'status'
        ];
    
        if (!in_array($columnName, $validColumns)) {
            $columnName = 'id';
        }

        Offers::whereDate('expiry_date', '<=', Carbon::today())
        ->where('status', 1)
        ->update(['status' => 0]);
    
        $query = Offers::query()->where('status', '!=', 2);
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('offer_type', 'like', "%$search%")
                  ->orWhere('product_type', 'like', "%$search%")
                  ->orWhere('dis_amount', 'like', "%$search%")
                  ->orWhere('min_amount', 'like', "%$search%")
                  ->orWhere('max_amount', 'like', "%$search%")
                  ->orWhere('total_usage', 'like', "%$search%")
                  ->orWhere('max_users', 'like', "%$search%")
                  ->orWhere('created_at', 'like', "%$search%");
            });
        }
    
        $totalRecords = $query->count();
    
        $offers = $query->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        $usageCounts = DB::table('offer_user')
            ->select('offer_id', DB::raw('COUNT(*) as total_used'))
            ->groupBy('offer_id')
            ->pluck('total_used', 'offer_id');
    
        $data_arr = [];
        $today = Carbon::now()->toDateString();

        foreach ($offers as $offer) {
            $statusIcon = $offer->status == 1
                ? '<i class="fa fa-thumbs-up text-success status_active" title="Active" data-id="'.$offer->id.'"></i>'
                : '<i class="fa fa-thumbs-down text-danger status_inactive" title="Inactive" data-id="'.$offer->id.'"></i>';
    
            $showBtn = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-offer" data-id="' . $offer->id . '" title="View"> </a>';

            $mailBtn = '';
            $editBtn = '';
            $deleteBtn = '';

            
            if ($offer->expiry_date >= $today && $offer->status != 0 ) {
                $mailBtn = '<button class="btn btn-sm warning send-offer" data-id="' . $offer->id . '" title="Publish"> <small><i class="material-icons">send</i> </small> </button>';
            }

            if ($offer->expiry_date >= $today) {
                $editBtn = '<a class="btn btn-sm success paddingset edit-offer" data-id="' . $offer->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
                $deleteBtn = '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$offer->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            }

            $offerCount = $usageCounts[$offer->id] ?? 0;
    
            $data_arr[] = [
                'offer' => ucfirst($offer->offer_type),
                'product' => $this->mapProductType($offer->product_type), 
                'discount' => $offer->dis_amount,
                'minimum' => $offer->min_amount,
                'maximum' => $offer->max_amount,
                'use' => $offer->total_usage,
                'users' => $offer->max_users,
                "count" => $offerCount,
                'created' => date('Y-m-d', strtotime($offer->created_at)),
                'expiry' => date('Y-m-d', strtotime($offer->expiry_date)),
                'status' => $statusIcon,
                'options' => $showBtn . ' ' . $editBtn . ' ' . $deleteBtn . ' ' . $mailBtn
            ];
        }
    
        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecords,
            'aaData' => $data_arr,
        ]);
    }
    

    private function mapProductType($type)
    {
        switch ($type) {
            case 1: return 'Brand';
            case 2: return 'Category';
            case 3: return 'Product';
            default: return 'N/A';
        }
    }


  
}
