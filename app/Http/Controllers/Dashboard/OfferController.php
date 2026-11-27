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

            $offerType = $request->input('offer_type');
            $isPercentage = ($offerType === 'percentage');

            if ($request->dis_amount && $offerType == 'flat') {
                $discount_value = $request->dis_amount;
            } elseif ($request->dis_percentage && $offerType == 'percentage') {
                $discount_value = $request->dis_percentage;
            }
            
            $rules = [
                'offer_type' => 'required|string',
                'product_type' => 'required|in:1,2,3',
                'start_date' => 'required|date|after_or_equal:today',
                'expiry_date' => 'required|date|after_or_equal:start_date',
                // 'total_usage' => 'required|numeric|min:1',
                // 'max_users' => 'required|numeric|min:1',
                'template' => 'required|string',
                'custom_url' => 'required|string',
                'offer_images.*' => 'mimetypes:image/png,image/jpg,image/jpeg|max:2048', 
                'offer_images' => 'required|array|max:4',
            ];

            if ($isPercentage) {
                $rules['dis_percentage'] = 'required|numeric|min:1|max:99';
                $rules['dis_amount'] = 'nullable';
            } else {
                $rules['dis_amount'] = 'required|numeric|min:0.01';
                $rules['dis_percentage'] = 'nullable';
            }

            $validator = \Validator::make($request->all(), $rules);

            $validator->after(function ($validator) use ($request, $product_type, $brand_id, $category_id, $subcategory_id, $product_id) {
                // Custom Rule 1: Validate discount value based on offer type
                if ($request->offer_type == 'percentage') {
                    if (empty($request->dis_percentage)) {
                        $validator->errors()->add('dis_percentage', 'Discount Percentage is required.');
                    } elseif ($request->dis_percentage < 1 || $request->dis_percentage > 99) {
                        $validator->errors()->add('dis_percentage', 'Discount Percentage must be between 1% and 99%.');
                    }
                } elseif ($request->offer_type == 'flat') {
                    if (empty($request->dis_amount)) {
                        $validator->errors()->add('dis_amount', 'Discount Amount is required.');
                    } elseif ($request->dis_amount <= 0) {
                        $validator->errors()->add('dis_amount', 'Discount Amount must be greater than 0.');
                    }
                }

                $start_date = $request->input('start_date');
                $expiry_date = $request->input('expiry_date');

                // Custom Rule 2: Check for date overlap conflict with other active Offers (within the same scope: Brand vs Brand, Category vs Category, Product vs Product)
                if ($start_date && $expiry_date) {
                    $conflictingOffer = $this->findConflictingOffer($product_type, $brand_id, $category_id, $subcategory_id, $product_id, $start_date, $expiry_date);
                    if ($conflictingOffer) {
                        $targetDetails = $this->getTargetDetails($product_type, $brand_id, $category_id, $subcategory_id, $product_id);
                        $startFmt = date('d-m-Y', strtotime($conflictingOffer->start_date));
                        $expiryFmt = date('d-m-Y', strtotime($conflictingOffer->expiry_date));
                        $validator->errors()->add('conflict', "This {$targetDetails['type']} already has an active offer from {$startFmt} to {$expiryFmt}.");
                    }
                }

                // Custom Rule 3: Check for conflict with active Bogo offer within the same scope & date range
                if ($start_date && $expiry_date) {
                    $conflictBogo = $this->findConflictingBogo($product_type, $brand_id, $category_id, $subcategory_id, $product_id, $start_date, $expiry_date);
                    if ($conflictBogo) {
                        $targetDetails = $this->getTargetDetails($product_type, $brand_id, $category_id, $subcategory_id, $product_id);
                        $startFmt = $conflictBogo->start_date ? date('d-m-Y', strtotime($conflictBogo->start_date)) : 'N/A';
                        $endFmt = $conflictBogo->end_date ? date('d-m-Y', strtotime($conflictBogo->end_date)) : 'N/A';
                        $validator->errors()->add('conflict', "Conflict: A BOGO offer for this {$targetDetails['type']} '{$targetDetails['name']}' is already active from {$startFmt} to {$endFmt}.");
                    }
                }
            });

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $newStatus = 1;

            $offer = new Offers();
            $offer->offer_type = $request->input('offer_type');
            $offer->dis_amount = $discount_value;
            // $offer->min_amount = $request->input('min_amount');
            // $offer->max_amount = $request->input('max_amount');
            $offer->start_date = $request->input('start_date');
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

                        if (extension_loaded('gd')) {
                            try {
                                $resizedImage = Image::make($images)->resize(480, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                                $resizedImage->save($path . '/' . $imagesAR);
                            } catch (\Exception $e) {
                                $images->move($path, $imagesAR);
                            }
                        } else {
                            $images->move($path, $imagesAR);
                        }
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

            $usedCount = DB::table('offer_user')->where('offer_id', $offer->id)->count();

            // When offer has already been used in orders (usedCount > 0):
            // ONLY Expiry Date can be modified. All other fields are protected from changes.
            if ($usedCount > 0) {
                $validator = \Validator::make(
                    $request->all(),
                    [
                        'offer_id' => 'required|exists:offers,id',
                        'expiry_date' => 'required|date|after_or_equal:' . $offer->start_date,
                    ],
                    [
                        'expiry_date.required' => 'Expiry Date is required.',
                        'expiry_date.date' => 'Enter a valid date.',
                        'expiry_date.after_or_equal' => 'Expiry date must be on or after start date (' . date('d-m-Y', strtotime($offer->start_date)) . ').',
                    ]
                );

                $validator->after(function ($validator) use ($offer, $request, $id) {
                    $expiry_date = $request->input('expiry_date');
                    if ($expiry_date) {
                        $conflictingOffer = $this->findConflictingOffer(
                            $offer->product_type,
                            $offer->brand_id,
                            $offer->category_id,
                            $offer->subcategory_id,
                            $offer->product_id,
                            $offer->start_date,
                            $expiry_date,
                            $id
                        );
                        if ($conflictingOffer) {
                            $targetDetails = $this->getTargetDetails($offer->product_type, $offer->brand_id, $offer->category_id, $offer->subcategory_id, $offer->product_id);
                            $startFmt = date('d-m-Y', strtotime($conflictingOffer->start_date));
                            $expiryFmt = date('d-m-Y', strtotime($conflictingOffer->expiry_date));
                            $validator->errors()->add('conflict', "This {$targetDetails['type']} already has an active offer from {$startFmt} to {$expiryFmt}.");
                        }

                        $conflictBogo = $this->findConflictingBogo(
                            $offer->product_type,
                            $offer->brand_id,
                            $offer->category_id,
                            $offer->subcategory_id,
                            $offer->product_id,
                            $offer->start_date,
                            $expiry_date
                        );
                        if ($conflictBogo) {
                            $targetDetails = $this->getTargetDetails($offer->product_type, $offer->brand_id, $offer->category_id, $offer->subcategory_id, $offer->product_id);
                            $startFmt = $conflictBogo->start_date ? date('d-m-Y', strtotime($conflictBogo->start_date)) : 'N/A';
                            $endFmt = $conflictBogo->end_date ? date('d-m-Y', strtotime($conflictBogo->end_date)) : 'N/A';
                            $validator->errors()->add('conflict', "Conflict: A BOGO offer for this {$targetDetails['type']} '{$targetDetails['name']}' is already active from {$startFmt} to {$endFmt}.");
                        }
                    }
                });

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }

                $offer->expiry_date = $request->input('expiry_date');
                $offer->save();

                Alert::success('Success', 'Offer expiry date updated successfully');
                return response()->json(['success' => 'true']);
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
            
            $offerTypes = $request->input('offer_types');
            $isPercentage = ($offerTypes === 'percentage');

            if ($request->dis_amount && $offerTypes == 'flat') {
                $discount_value = $request->dis_amount;
            } elseif ($request->dis_percentage && $offerTypes == 'percentage') {
                $discount_value = $request->dis_percentage;
            }
            
            $rules = [
                'offer_id' => 'required|exists:offers,id',
                'offer_types' => 'required|string',
                'type' => 'required|in:1,2,3',
                // 'min_amount' => 'required|numeric|min:0',
                // 'max_amount' => 'required|numeric',
                'start_date' => 'required|date',
                'expiry_date' => 'required|date|after_or_equal:start_date',
                // 'total_usage' => 'required|numeric|min:1',
                // 'max_users' => 'required|numeric|min:1',
                'template' => 'required|string',
                'custom_url' => 'required|string',
                'offer_image.*' => 'mimetypes:image/png,image/jpg,image/jpeg|max:2048', 
            ];

            if ($isPercentage) {
                $rules['dis_percentage'] = 'required|numeric|min:1|max:99';
                $rules['dis_amount'] = 'nullable';
            } else {
                $rules['dis_amount'] = 'required|numeric|min:0.01';
                $rules['dis_percentage'] = 'nullable';
            }

            $validator = \Validator::make($request->all(), $rules);

                $validator->after(function ($validator) use ($request, $product_type, $brand_id, $category_id, $subcategory_id, $product_id, $id) {
                    // Custom Rule 1: Validate discount value based on offer type
                    if ($request->offer_types == 'percentage') {
                        if (empty($request->dis_percentage)) {
                            $validator->errors()->add('dis_percentage', 'Discount Percentage is required.');
                        } elseif ($request->dis_percentage < 1 || $request->dis_percentage > 99) {
                            $validator->errors()->add('dis_percentage', 'Discount Percentage must be between 1% and 99% (100% is not allowed).');
                        }
                    } elseif ($request->offer_types == 'flat') {
                        if (empty($request->dis_amount)) {
                            $validator->errors()->add('dis_amount', 'Discount Amount is required.');
                        } elseif ($request->dis_amount <= 0) {
                            $validator->errors()->add('dis_amount', 'Discount Amount must be greater than 0.');
                        }
                    }

                    $start_date = $request->input('start_date');
                    $expiry_date = $request->input('expiry_date');

                    // Custom Rule 2: Check for date overlap conflict with other active Offers (within the same scope: Brand vs Brand, Category vs Category, Product vs Product)
                    if ($start_date && $expiry_date) {
                        $conflictingOffer = $this->findConflictingOffer($product_type, $brand_id, $category_id, $subcategory_id, $product_id, $start_date, $expiry_date, $id);
                        if ($conflictingOffer) {
                            $targetDetails = $this->getTargetDetails($product_type, $brand_id, $category_id, $subcategory_id, $product_id);
                            $startFmt = date('d-m-Y', strtotime($conflictingOffer->start_date));
                            $expiryFmt = date('d-m-Y', strtotime($conflictingOffer->expiry_date));
                            $validator->errors()->add('conflict', "This {$targetDetails['type']} already has an active offer from {$startFmt} to {$expiryFmt}.");
                        }
                    }

                    // Custom Rule 3: Check for conflict with active Bogo offer within the same scope & date range
                    if ($start_date && $expiry_date) {
                        $conflictBogo = $this->findConflictingBogo($product_type, $brand_id, $category_id, $subcategory_id, $product_id, $start_date, $expiry_date);
                        if ($conflictBogo) {
                            $targetDetails = $this->getTargetDetails($product_type, $brand_id, $category_id, $subcategory_id, $product_id);
                            $startFmt = $conflictBogo->start_date ? date('d-m-Y', strtotime($conflictBogo->start_date)) : 'N/A';
                            $endFmt = $conflictBogo->end_date ? date('d-m-Y', strtotime($conflictBogo->end_date)) : 'N/A';
                            $validator->errors()->add('conflict', "Conflict: A BOGO offer for this {$targetDetails['type']} '{$targetDetails['name']}' is already active from {$startFmt} to {$endFmt}.");
                        }
                    }
                });

                if ($validator->fails()) {
                    return response()->json($validator->errors(), 422);
                }

                $offer->offer_type = $request->input('offer_types');
                $offer->dis_amount = $discount_value;
                // $offer->min_amount = $request->input('min_amount');
                // $offer->max_amount = $request->input('max_amount');
                $offer->start_date = $request->input('start_date');
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

                        if (extension_loaded('gd')) {
                            try {
                                $resizedImage = Image::make($images)->resize(480, null, function ($constraint) {
                                    $constraint->aspectRatio();
                                });
                                $resizedImage->save($path . '/' . $imagesAR);
                            } catch (\Exception $e) {
                                $images->move($path, $imagesAR);
                            }
                        } else {
                            $images->move($path, $imagesAR);
                        }

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
        $usedCount = DB::table('offer_user')->where('offer_id', $id)->count();
        if ($usedCount > 0) {
            return redirect()->route('offer')
                ->with('errorMessage', 'Cannot delete: This offer has already been used in orders (Total Used Count: ' . $usedCount . ').');
        }

        $offer = Offers::find($id);
        if ($offer) {
            $offer->status = 2;
            $offer->save();

            return redirect()->route('offer')
                ->with('doneMessage', 'Record deleted successfully');
        }

        return redirect()->route('offer')
            ->with('errorMessage', 'Offer not found.');
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
                $usedCount = DB::table('offer_user')->where('offer_id', $editData->id)->count();

                $html = view('dashboard.offer.edit', [
                    'editData' => $editData,
                    'categories' => $categories,
                    'brand' => $brand,
                    'product' => $product,
                    'subcategories' => $subcategories,
                    'usedCount' => $usedCount
                ])->render();
               
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

                // Check for delete action (status == 2)
                if ($status == 2) {
                    $usedOffers = DB::table('offer_user')
                        ->whereIn('offer_id', $ids)
                        ->pluck('offer_id')
                        ->unique()
                        ->toArray();

                    if (!empty($usedOffers)) {
                        $deletableIds = array_values(array_diff($ids, $usedOffers));

                        if (!empty($deletableIds)) {
                            Offers::whereIn('id', $deletableIds)->update(['status' => 2]);
                            return response()->json([
                                'success' => true,
                                'msg' => count($deletableIds) . ' offer(s) deleted successfully. ' . count($usedOffers) . ' offer(s) could not be deleted because total used count is greater than 0.'
                            ]);
                        } else {
                            return response()->json([
                                'success' => false,
                                'error' => 'Cannot delete: Selected offer(s) have already been used in orders (Total Used Count > 0).'
                            ]);
                        }
                    }
                }

                // Check for activate action (status == 1)
                if ($status == 1) {
                    $offersToActivate = Offers::whereIn('id', $ids)->get();
                    $conflictingIds = [];
                    $conflictMsgs = [];
                    foreach ($offersToActivate as $off) {
                        if ($off->expiry_date && $off->expiry_date < date('Y-m-d')) {
                            $conflictingIds[] = $off->id;
                            $conflictMsgs[] = "Offer #{$off->id} (Expired)";
                            continue;
                        }
                        $conflictOffer = $this->findConflictingOffer(
                            $off->product_type, $off->brand_id, $off->category_id,
                            $off->subcategory_id, $off->product_id, $off->start_date,
                            $off->expiry_date, $off->id
                        );
                        $conflictBogo = $this->findConflictingBogo(
                            $off->product_type, $off->brand_id, $off->category_id,
                            $off->subcategory_id, $off->product_id, $off->start_date,
                            $off->expiry_date
                        );
                        if ($conflictOffer || $conflictBogo) {
                            $conflictingIds[] = $off->id;
                            $targetDetails = $this->getTargetDetails($off->product_type, $off->brand_id, $off->category_id, $off->subcategory_id, $off->product_id);
                            $conflictMsgs[] = "Offer #{$off->id} ({$targetDetails['name']})";
                        }
                    }

                    if (!empty($conflictingIds)) {
                        $activatableIds = array_values(array_diff($ids, $conflictingIds));
                        if (!empty($activatableIds)) {
                            Offers::whereIn('id', $activatableIds)->update(['status' => 1]);
                            return response()->json([
                                'success' => true,
                                'msg' => count($activatableIds) . ' offer(s) activated successfully. ' . count($conflictingIds) . ' offer(s) (' . implode(', ', $conflictMsgs) . ') could not be activated due to active Offer/BOGO date conflicts or expiry.'
                            ]);
                        } else {
                            return response()->json([
                                'success' => false,
                                'error' => 'Cannot activate: Selected offer(s) (' . implode(', ', $conflictMsgs) . ') conflict with already active Offer/BOGO on the same target or are expired.'
                            ]);
                        }
                    }
                }

                Offers::wherein('id', $ids)->update(['status' => $status]);

                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) deleted successfully']);
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
            },
            'brand',
            'category',
            'subcategory',
            'product'
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
            // Check if offer is expired
            if ($offer->expiry_date && $offer->expiry_date < date('Y-m-d')) {
                return response()->json([
                    'success' => false,
                    'msg' => "This offer expired on " . date('d-m-Y', strtotime($offer->expiry_date)) . "."
                ]);
            }

            // Check for conflict before activating (within same scope)
            if ($offer->start_date && $offer->expiry_date) {
                // 1. Conflict with active Offer
                $conflict = $this->findConflictingOffer(
                    $offer->product_type,
                    $offer->brand_id,
                    $offer->category_id,
                    $offer->subcategory_id,
                    $offer->product_id,
                    $offer->start_date,
                    $offer->expiry_date,
                    $offer->id
                );

                if ($conflict) {
                    $targetDetails = $this->getTargetDetails($offer->product_type, $offer->brand_id, $offer->category_id, $offer->subcategory_id, $offer->product_id);
                    $startFmt = date('d-m-Y', strtotime($conflict->start_date));
                    $expiryFmt = date('d-m-Y', strtotime($conflict->expiry_date));
                    return response()->json([
                        'success' => false,
                        'msg' => "This {$targetDetails['type']} already has an active offer from {$startFmt} to {$expiryFmt}."
                    ]);
                }

                // 2. Conflict with active BOGO offer
                $conflictBogo = $this->findConflictingBogo(
                    $offer->product_type,
                    $offer->brand_id,
                    $offer->category_id,
                    $offer->subcategory_id,
                    $offer->product_id,
                    $offer->start_date,
                    $offer->expiry_date
                );

                if ($conflictBogo) {
                    $targetDetails = $this->getTargetDetails($offer->product_type, $offer->brand_id, $offer->category_id, $offer->subcategory_id, $offer->product_id);
                    $startFmt = $conflictBogo->start_date ? date('d-m-Y', strtotime($conflictBogo->start_date)) : 'N/A';
                    $endFmt = $conflictBogo->end_date ? date('d-m-Y', strtotime($conflictBogo->end_date)) : 'N/A';
                    return response()->json([
                        'success' => false,
                        'msg' => "BOGO offer for this {$targetDetails['type']} '{$targetDetails['name']}' is already active from {$startFmt} to {$endFmt}."
                    ]);
                }
            }

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
            'offer_type', 'product_type', 'dis_amount', 'start_date', 'expiry_date',
            'created_at', 'min_amount', 'max_amount', 'total_usage', 'max_users', 'status'
        ];
    
        if (!in_array($columnName, $validColumns)) {
            $columnName = 'id';
        }
    
        $query = Offers::query()->where('status', '!=', 2);
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('offer_type', 'like', "%$search%")
                  ->orWhere('product_type', 'like', "%$search%")
                  ->orWhere('dis_amount', 'like', "%$search%")
                  ->orWhere('start_date', 'like', "%$search%")
                  ->orWhere('expiry_date', 'like', "%$search%")
                  ->orWhere('created_at', 'like', "%$search%")
                  ->orWhere('min_amount', 'like', "%$search%")
                  ->orWhere('max_amount', 'like', "%$search%")
                  ->orWhere('total_usage', 'like', "%$search%")
                  ->orWhere('max_users', 'like', "%$search%")
                  ->orWhereHas('brand', function ($b) use ($search) {
                      $b->where('title', 'like', "%$search%");
                  })
                  ->orWhereHas('category', function ($c) use ($search) {
                      $c->where('title', 'like', "%$search%");
                  })
                  ->orWhereHas('subcategory', function ($sc) use ($search) {
                      $sc->where('title', 'like', "%$search%");
                  })
                  ->orWhereHas('product', function ($p) use ($search) {
                      $p->where('product_name', 'like', "%$search%");
                  });
            });
        }
    
        $totalRecords = $query->count();
    
        $offers = $query->with(['brand', 'category', 'subcategory', 'product'])
            ->orderBy($columnName, $columnSortOrder)
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

            
            $offerCount = $usageCounts[$offer->id] ?? 0;

            if ($offer->expiry_date >= $today && $offer->status != 0 ) {
                $mailBtn = '<button class="btn btn-sm warning send-offer" data-id="' . $offer->id . '" title="Publish"> <small><i class="material-icons">send</i> </small> </button>';
            }

            if ($offer->expiry_date >= $today) {
                $editBtn = '<a class="btn btn-sm success paddingset edit-offer" data-id="' . $offer->id . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
                
                if ($offerCount == 0) {
                    $deleteBtn = '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$offer->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
                } else {
                    $deleteBtn = '<button class="btn btn-sm light disabled" title="Cannot delete: Total used count is '.$offerCount.'" disabled style="margin-left: -4px; opacity: 0.45; cursor: not-allowed;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
                }
            }
    
            $discountDisplay = '-';
            if ($offer->dis_amount !== null && $offer->dis_amount !== '') {
                if ($offer->offer_type == 'percentage') {
                    $discountDisplay = ($offer->dis_amount + 0) . '%';
                } else {
                    $discountDisplay = 'GH₵ ' . $offer->dis_amount;
                }
            }

            $data_arr[] = [
                'offer' => ucfirst($offer->offer_type),
                'product' => $this->mapProductType($offer->product_type), 
                'applied_item' => $this->getAppliedItemName($offer),
                'discount' => $discountDisplay,
                'startDate' => !empty($offer->start_date) ? date('Y-m-d', strtotime($offer->start_date)) : '-',
                'expiry' => date('Y-m-d', strtotime($offer->expiry_date)),
                'created' => date('Y-m-d', strtotime($offer->created_at)),
                'count' => $offerCount,
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

    private function getAppliedItemName($offer)
    {
        $name = '-';
        if ($offer->product_type == 1) {
            $name = $offer->brand ? ucfirst($offer->brand->title) : '-';
        } elseif ($offer->product_type == 2) {
            if (!empty($offer->subcategory_id) && $offer->subcategory) {
                $catTitle = $offer->category ? ucfirst($offer->category->title) . ' > ' : '';
                $name = $catTitle . ucfirst($offer->subcategory->title);
            } elseif ($offer->category) {
                $name = ucfirst($offer->category->title);
            }
        } elseif ($offer->product_type == 3) {
            $name = $offer->product ? ucfirst($offer->product->product_name) : '-';
        }

        if ($name !== '-' && mb_strlen($name) > 25) {
            $truncated = mb_substr($name, 0, 25) . '...';
            return '<span title="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" data-toggle="tooltip">' . htmlspecialchars($truncated, ENT_QUOTES, 'UTF-8') . '</span>';
        }

        return htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    }

    private function findConflictingOffer($product_type, $brand_id, $category_id, $subcategory_id, $product_id, $start_date, $expiry_date, $ignore_id = null)
    {
        $query = Offers::where('status', 1)
            ->where('product_type', $product_type);

        if ($ignore_id) {
            $query->where('id', '!=', $ignore_id);
        }

        if ($product_type == 1) {
            $query->where('brand_id', $brand_id);
        } elseif ($product_type == 2) {
            if ($subcategory_id) {
                $query->where('subcategory_id', $subcategory_id);
            } else {
                $query->where('category_id', $category_id)->whereNull('subcategory_id');
            }
        } elseif ($product_type == 3) {
            $query->where('product_id', $product_id);
        }

        $query->where(function ($dateQ) use ($start_date, $expiry_date) {
            $dateQ->where('start_date', '<=', $expiry_date)
                  ->where('expiry_date', '>=', $start_date);
        });

        return $query->first();
    }

    private function findConflictingBogo($product_type, $brand_id, $category_id, $subcategory_id, $product_id, $start_date = null, $expiry_date = null)
    {
        $query = Bogo::where('status', 1)
            ->where('product_type', $product_type);

        if ($product_type == 1 && $brand_id) {
            $query->where('brand_id', $brand_id);
        } elseif ($product_type == 2) {
            if ($subcategory_id) {
                $query->where('subcategory_id', $subcategory_id);
            } else {
                $query->where('category_id', $category_id)->whereNull('subcategory_id');
            }
        } elseif ($product_type == 3 && $product_id) {
            $query->where('product_id', $product_id);
        }

        if ($start_date && $expiry_date) {
            $query->where(function ($dateQ) use ($start_date, $expiry_date) {
                $dateQ->where(function ($q) use ($start_date, $expiry_date) {
                    $q->where('start_date', '<=', $expiry_date)
                      ->where('end_date', '>=', $start_date);
                })->orWhere(function ($q) {
                    $q->whereNull('start_date')->orWhereNull('end_date');
                });
            });
        }

        return $query->first();
    }

    private function getTargetDetails($product_type, $brand_id, $category_id, $subcategory_id, $product_id)
    {
        if ($product_type == 1 && $brand_id) {
            $brand = Brand::find($brand_id);
            return ['type' => 'Brand', 'name' => $brand ? $brand->title : "ID: $brand_id"];
        } elseif ($product_type == 2) {
            if ($subcategory_id) {
                $subcat = SubCategories::find($subcategory_id);
                return ['type' => 'Subcategory', 'name' => $subcat ? $subcat->title : "ID: $subcategory_id"];
            } elseif ($category_id) {
                $cat = Categories::find($category_id);
                return ['type' => 'Category', 'name' => $cat ? $cat->title : "ID: $category_id"];
            }
        } elseif ($product_type == 3 && $product_id) {
            $product = Product::find($product_id);
            return ['type' => 'Product', 'name' => $product ? $product->product_name : "ID: $product_id"];
        }

        return ['type' => 'Target', 'name' => 'N/A'];
    }


  
}
