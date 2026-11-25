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
use Illuminate\Support\Str;
use App\Models\Bogo;
use App\Models\Offers;


class HomeController extends Controller
{

    public function __construct()
    {  
        $this->setting = Setting::find(1);
        $this->user_id = isset(auth()->guard('user')->user()->id) ? auth()->guard('user')->user()->id : '';
    }

    public function index()
    {
        $today = Carbon::now()->toDateString();

        // 1. Get all active valid offers
        $activeOffers = Offers::where('status', 1)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->whereDate('expiry_date', '>=', $today)
            ->orderByDesc('dis_amount')
            ->orderByDesc('id')
            ->get();

        // 2. Get all active valid BOGOs
        $activeBogos = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderByDesc('id')
            ->get();

        $offerProducts = $this->getActiveOfferProductsQuery($activeOffers, $activeBogos)->orderBy('p.id', 'desc')->get();
        $bogoProducts = $this->getActiveBogoProductsQuery($activeBogos)->orderBy('p.id', 'desc')->get();

        // 3. Combine both Offers & BOGO products, removing duplicate products by product_id
        $combinedOffers = $offerProducts->concat($bogoProducts)->unique('product_id');

        $total_offer_count = $combinedOffers->count();
        // Limit to max 21 items for home page display (20 items + 21st blurred View All item if count > 20)
        $offer_product = $combinedOffers->take(21);

        $best_seller_product = Product::activeProductsBasedOnRelations()->where('is_product_bestseller',1)->limit('20')->orderby('updated_at','desc')->get();


        // Bogo & offer status for offer and best Seller
        $today = Carbon::now()->toDateString();


        // Offer Bogo & Discount Check
        foreach ($offer_product as $bogoOffer) {
            $deal = Helper::resolveProductDeal($bogoOffer->id, $bogoOffer->category_id, $bogoOffer->subcategory_id, $bogoOffer->brand_id, $bogoOffer->final_price ?? $bogoOffer->variant_price);
            $bogoOffer->bogo_status = $deal['is_bogo'];
            $bogoOffer->offer_status = $deal['is_offer'];
            $bogoOffer->discount_amount = $deal['discount_amount'];
            $bogoOffer->offer_type = $deal['offer_type'];
        }

        // Best Seller bogo
        foreach ($best_seller_product as $best) {
            $deal = Helper::resolveProductDeal($best->id, $best->category_id, $best->subcategory_id, $best->brand_id, $best->product_price);
            $best->bogo_status = $deal['is_bogo'];
            $best->offer_status = $deal['is_offer'];
            $best->discount_amount = $deal['discount_amount'];
            $best->offer_type = $deal['offer_type'];
        }
    
        $blogs = Blog::where('status',1)->limit(3)->orderby('created_at','desc')->get();
        $banners = Banner::where('status',1)->limit(6)->orderby('created_at','desc')->where('highlight',null)->where('offer',null)->get();
        $banners_highlight = Banner::where('status',1)->where('highlight',1)->limit(6)->orderby('created_at','desc')->get();
        $banners_offer = Banner::where('status',1)->where('offer',1)->limit(1)->orderby('created_at','desc')->latest()->first();
        $categories = Categories::where('status',1)->orderby('created_at','desc')->get();
       // $bannerData =  Banner::where('status',1)->get();
        return view("frontend.home",compact('offer_product','total_offer_count','best_seller_product','blogs','banners','banners_highlight','banners_offer','categories'));
    }

    private function getBasePromotionsQuery()
    {
        return DB::table('product as p')
            ->select(
                'pv.*',
                'pv.id as id',
                'pv.id as variant_id',
                'p.id as product_id',
                'p.average_rating',
                'p.product_name',
                'pi.image',
                'pv.final_price',
                'mvp.count',
                'p.category_id',
                'p.subcategory_id',
                'p.brand_id'
            )
            ->join('product_image as pi', function ($join) {
                $join->on('p.id', '=', 'pi.product_id')
                    ->where('pi.status', 1);
            })
            ->join('categories as c', function ($join) {
                $join->on('p.category_id', '=', 'c.id')
                    ->where('c.status', 1);
            })
            ->join('sub_categories as sc', function ($join) {
                $join->on('p.subcategory_id', '=', 'sc.id')
                    ->where('sc.status', 1);
            })
            ->join('brand as b', function ($join) {
                $join->on('p.brand_id', '=', 'b.id')
                    ->where('b.status', 1);
            })
            ->join(
                DB::raw('(SELECT *, COALESCE(NULLIF(variant_discounted_price, ""), variant_price) AS final_price FROM product_variants WHERE status = 1) as pv'),
                function ($join) {
                    $join->on('p.id', '=', 'pv.product_id');
                }
            )
            ->leftJoin('most_viewed_product as mvp', 'mvp.product_id', '=', 'p.id')
            ->where('p.status', 1)
            ->groupBy('p.id');
    }

    private function getActiveBogoProductsQuery($activeBogos)
    {
        $query = $this->getBasePromotionsQuery();

        if (empty($activeBogos) || $activeBogos->isEmpty()) {
            $query->whereRaw('1 = 0');
            return $query;
        }

        $query->where(function ($q) use ($activeBogos) {
            foreach ($activeBogos as $bogo) {
                $q->orWhere(function ($sub) use ($bogo) {
                    if (!empty($bogo->product_id)) {
                        $sub->where('p.id', $bogo->product_id);
                    } elseif (!empty($bogo->subcategory_id)) {
                        if (!empty($bogo->category_id)) {
                            $sub->where('p.category_id', $bogo->category_id);
                        }
                        $sub->where('p.subcategory_id', $bogo->subcategory_id);
                    } elseif (!empty($bogo->category_id)) {
                        $sub->where('p.category_id', $bogo->category_id);
                    } elseif (!empty($bogo->brand_id)) {
                        $sub->where('p.brand_id', $bogo->brand_id);
                    }
                });
            }
        });

        return $query;
    }

    private function getActiveOfferProductsQuery($activeOffers, $activeBogos = null)
    {
        $query = $this->getBasePromotionsQuery();

        if (empty($activeOffers) || $activeOffers->isEmpty()) {
            $query->whereRaw('1 = 0');
            return $query;
        }

        $query->where(function ($q) use ($activeOffers) {
            foreach ($activeOffers as $offer) {
                $q->orWhere(function ($sub) use ($offer) {
                    if ($offer->offer_type === 'flat') {
                        $sub->where('pv.final_price', '>', floatval($offer->dis_amount));
                    }
                    if (!empty($offer->product_id)) {
                        $sub->where('p.id', $offer->product_id);
                    } elseif (!empty($offer->subcategory_id)) {
                        if (!empty($offer->category_id)) {
                            $sub->where('p.category_id', $offer->category_id);
                        }
                        $sub->where('p.subcategory_id', $offer->subcategory_id);
                    } elseif (!empty($offer->category_id)) {
                        $sub->where('p.category_id', $offer->category_id);
                    } elseif (!empty($offer->brand_id)) {
                        $sub->where('p.brand_id', $offer->brand_id);
                    }
                });
            }
        });

        // Deal priority: BOGO > Offer (products with an active BOGO belong in BOGO tab, not Offer tab)
        if (!empty($activeBogos) && $activeBogos->isNotEmpty()) {
            $query->whereNot(function ($q) use ($activeBogos) {
                foreach ($activeBogos as $bogo) {
                    $q->orWhere(function ($sub) use ($bogo) {
                        if (!empty($bogo->product_id)) {
                            $sub->where('p.id', $bogo->product_id);
                        } elseif (!empty($bogo->subcategory_id)) {
                            if (!empty($bogo->category_id)) {
                                $sub->where('p.category_id', $bogo->category_id);
                            }
                            $sub->where('p.subcategory_id', $bogo->subcategory_id);
                        } elseif (!empty($bogo->category_id)) {
                            $sub->where('p.category_id', $bogo->category_id);
                        } elseif (!empty($bogo->brand_id)) {
                            $sub->where('p.brand_id', $bogo->brand_id);
                        }
                    });
                }
            });
        }

        return $query;
    }

    private function getFilteredProducts($product_id = null, $brand_id = null, $subcategory_id = null, $category_id = null)
    {
        $query = $this->getBasePromotionsQuery();

        // Priority-based filtering
        if (!empty($product_id)) {
            $query->where('p.id', $product_id);
        } elseif (!empty($brand_id)) {
            $query->where('b.id', $brand_id);
        } elseif (!empty($subcategory_id)) {
            $query->where('sc.id', $subcategory_id);
        } elseif (!empty($category_id)) {
            $query->where('c.id', $category_id);
        }

        return $query->orderBy('p.id', 'desc')->get();
    }

    public function special(Request $request)
    {
        $user_id = $this->user_id;

        $categoryData = Categories::where('status', 1)->orderBy('id', 'ASC');

        $subcategoryData = SubCategories::withWhereHas('category', function ($query) {
            $query->where('status', 1);
        })->where('status', 1)->orderBy('id', 'ASC');

        $categories = Categories::with(['subcategory' => function ($query) {
            $query->where('status', 1);
        }])->where('status', 1)->get();

        $brandData = Brand::where('status', 1)->orderBy('title', 'asc')->get();

        $product_val = new Product;
        $product_max_price = $product_val->getproductMaxPrice();
        $product_min_price = $product_val->getproductMinPrice();

        $today = Carbon::now()->toDateString();

        // 1. All active offers
        $activeOffers = Offers::where('status', 1)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->whereDate('expiry_date', '>=', $today)
            ->orderByDesc('dis_amount')
            ->orderByDesc('id')
            ->get();

        // 2. All active BOGOs
        $activeBogos = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderByDesc('id')
            ->get();

        $offerProductDataFilter = $this->getActiveOfferProductsQuery($activeOffers, $activeBogos)->orderBy('p.id', 'desc')->get();
        $bogoProductDataFilter = $this->getActiveBogoProductsQuery($activeBogos)->orderBy('p.id', 'desc')->get();

        $limit = 16;

        $offer_total_product_count = $offerProductDataFilter->count();
        $bogo_total_product_count = $bogoProductDataFilter->count();

        $has_offer_products = $offer_total_product_count > 0;
        $has_bogo_products = $bogo_total_product_count > 0;

        // ---- Offer Products Pagination ----
        $offerProductData = $offerProductDataFilter->take($limit);
        $offerProductData->transform(function ($product) {
            $deal = Helper::resolveProductDeal($product->product_id, $product->category_id, $product->subcategory_id, $product->brand_id, $product->final_price ?? $product->variant_price);
            $product->offer_status = $deal['is_offer'];
            $product->offer_type = $deal['offer_type'];
            $product->discount_amount = $deal['discount_amount'];
            $product->bogo_status = $deal['is_bogo'];
            return $product;
        });

        $offer_showing_product_count = min($limit, $offer_total_product_count);

        // ---- BOGO Products Pagination ----
        $bogoProductData = $bogoProductDataFilter->take($limit);
        $bogoProductData->transform(function ($product) {
            $deal = Helper::resolveProductDeal($product->product_id, $product->category_id, $product->subcategory_id, $product->brand_id);
            $product->bogo_status = $deal['is_bogo'];
            $product->offer_status = false;
            $product->offer_type = null;
            $product->discount_amount = 0;
            return $product;
        });

        $bogo_showing_product_count = min($limit, $bogo_total_product_count);

        return view("frontend.product.offer-list", compact(
            'categoryData', 'subcategoryData', 'offerProductDataFilter', 'bogoProductDataFilter',
            'offerProductData', 'bogoProductData', 'user_id', 'brandData', 'product_max_price',
            'product_min_price', 'offer_total_product_count', 'bogo_total_product_count',
            'offer_showing_product_count', 'bogo_showing_product_count', 'categories',
            'has_offer_products', 'has_bogo_products'
        ));
    }

    public function loadfilterofferlist(Request $request)
    {
        $limit = 16;
        $brand_ids = null;
        $category_ids = null;
        $subcategory_ids = null;    

        if ($request->brand_ids) {
            $brand_ids = is_array($request->brand_ids)
                ? $request->brand_ids
                : explode(',', $request->brand_ids);
        }

        if ($request->category_ids) {
            $category_ids = is_array($request->category_ids)
                ? $request->category_ids
                : explode(',', $request->category_ids);
        }
        
        if ($request->subcategory_ids) {
            $subcategory_ids = is_array($request->subcategory_ids)
                ? $request->subcategory_ids
                : explode(',', $request->subcategory_ids);
        }

        $minprice = ($request->min_price) ? $request->min_price : '';
        $maxprice = ($request->max_price) ? $request->max_price : '';

        $today = Carbon::now()->toDateString();
        $activeOffers = Offers::where('status', 1)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')->orWhereDate('start_date', '<=', $today);
            })
            ->whereDate('expiry_date', '>=', $today)
            ->orderByDesc('dis_amount')
            ->orderByDesc('id')
            ->get();

        $activeBogos = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderByDesc('id')
            ->get();

        $offerProductDataFilter = $this->getActiveOfferProductsQuery($activeOffers, $activeBogos);

        if ($category_ids != null && $subcategory_ids == null) {
            $offerProductDataFilter->whereIn('sc.category_id', $category_ids);
        } else if ($category_ids != null && ($subcategory_ids && $subcategory_ids != null)) {
            $offerProductDataFilter->where(function ($query) use ($subcategory_ids, $category_ids) {
                $query->whereIn('sc.id', $subcategory_ids)
                      ->orWhereIn('sc.category_id', $category_ids);
            });
        } else if ($category_ids == null && ($subcategory_ids && $subcategory_ids != null)) {
            $offerProductDataFilter->whereIn('sc.id', $subcategory_ids);
        }

        if ($brand_ids && $brand_ids != null) {
            $offerProductDataFilter->whereIn('b.id', $brand_ids);
        }

        if (($minprice && $minprice != null) && ($maxprice && $maxprice != null)) {                
            $offerProductDataFilter->whereBetween('pv.final_price', [$minprice, $maxprice]);
        }

        $sort_by = $request->sort_by;
        if ($sort_by == 1) {
            $offerProductDataFilter = $offerProductDataFilter->orderBy('p.id', 'desc');
        } elseif ($sort_by == 2) {
            $offerProductDataFilter = $offerProductDataFilter->orderBy('pv.final_price', 'desc');
        } elseif ($sort_by == 3) {
            $offerProductDataFilter = $offerProductDataFilter->orderBy('pv.final_price', 'asc');
        } elseif ($sort_by == 4) {
            $offerProductDataFilter = $offerProductDataFilter->orderBy('mvp.count', 'desc')->orderBy('p.id', 'desc');
        } else {
            $offerProductDataFilter = $offerProductDataFilter->orderBy('p.id', 'desc');
        }

        $current_page_count = intval($request->current_page_count ?? 0);
        $allOfferProducts = $offerProductDataFilter->get();  

        $offer_total_product_count = count($allOfferProducts);
        $offerProductData = $allOfferProducts->skip($current_page_count)->take($limit);

        $offerProductData->transform(function ($product) {
            $deal = Helper::resolveProductDeal($product->product_id, $product->category_id, $product->subcategory_id, $product->brand_id, $product->final_price ?? $product->variant_price);
            $product->offer_status = $deal['is_offer'];
            $product->offer_type = $deal['offer_type'];
            $product->discount_amount = $deal['discount_amount'];
            $product->bogo_status = $deal['is_bogo'];
            return $product;
        });

        $offer_showing_product_count = min($current_page_count + count($offerProductData), $offer_total_product_count);

        $data = [
            'offer_total_product_count' => $offer_total_product_count,
            'offer_showing_product_count' => $offer_showing_product_count,
            'offerProductData' => $offerProductData
        ];

        return view("frontend.product.ajax_filter_offer", $data);
    }

    public function loadfilterbogolist(Request $request)
    {
        $limit = 16;
        $brand_ids = null;
        $category_ids = null;
        $subcategory_ids = null;    

        if ($request->brand_ids) {
            $brand_ids = is_array($request->brand_ids)
                ? $request->brand_ids
                : explode(',', $request->brand_ids);
        }

        if ($request->category_ids) {
            $category_ids = is_array($request->category_ids)
                ? $request->category_ids
                : explode(',', $request->category_ids);
        }
        
        if ($request->subcategory_ids) {
            $subcategory_ids = is_array($request->subcategory_ids)
                ? $request->subcategory_ids
                : explode(',', $request->subcategory_ids);
        }

        $minprice = ($request->min_price) ? $request->min_price : '';
        $maxprice = ($request->max_price) ? $request->max_price : '';

        $today = Carbon::now()->toDateString();
        $activeBogos = Bogo::where('status', 1)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->orderByDesc('id')
            ->get();

        $bogoProductDataFilter = $this->getActiveBogoProductsQuery($activeBogos);

        if ($category_ids != null && $subcategory_ids == null) {
            $bogoProductDataFilter->whereIn('sc.category_id', $category_ids);
        } else if ($category_ids != null && ($subcategory_ids && $subcategory_ids != null)) {
            $bogoProductDataFilter->where(function ($query) use ($subcategory_ids, $category_ids) {
                $query->whereIn('sc.id', $subcategory_ids)
                      ->orWhereIn('sc.category_id', $category_ids);
            });
        } else if ($category_ids == null && ($subcategory_ids && $subcategory_ids != null)) {
            $bogoProductDataFilter->whereIn('sc.id', $subcategory_ids);
        }

        if ($brand_ids && $brand_ids != null) {
            $bogoProductDataFilter->whereIn('b.id', $brand_ids);
        }

        if (($minprice && $minprice != null) && ($maxprice && $maxprice != null)) {                
            $bogoProductDataFilter->whereBetween('pv.final_price', [$minprice, $maxprice]);
        }

        $sort_by = $request->sort_by;
        if ($sort_by == 1) {
            $bogoProductDataFilter = $bogoProductDataFilter->orderBy('p.id', 'desc');
        } elseif ($sort_by == 2) {
            $bogoProductDataFilter = $bogoProductDataFilter->orderBy('pv.final_price', 'desc');
        } elseif ($sort_by == 3) {
            $bogoProductDataFilter = $bogoProductDataFilter->orderBy('pv.final_price', 'asc');
        } elseif ($sort_by == 4) {
            $bogoProductDataFilter = $bogoProductDataFilter->orderBy('mvp.count', 'desc')->orderBy('p.id', 'desc');
        } else {
            $bogoProductDataFilter = $bogoProductDataFilter->orderBy('p.id', 'desc');
        }

        $current_page_count = intval($request->current_page_count ?? 0);
        $allBogoProducts = $bogoProductDataFilter->get();  

        $bogo_total_product_count = count($allBogoProducts);
        $bogoProductData = $allBogoProducts->skip($current_page_count)->take($limit);

        $bogoProductData->transform(function ($product) {
            $deal = Helper::resolveProductDeal($product->product_id, $product->category_id, $product->subcategory_id, $product->brand_id);
            $product->bogo_status = $deal['is_bogo'];
            $product->offer_status = false;
            $product->offer_type = null;
            $product->discount_amount = 0;
            return $product;
        });

        $bogo_showing_product_count = min($current_page_count + count($bogoProductData), $bogo_total_product_count);

        $data = [
            'bogo_total_product_count' => $bogo_total_product_count,
            'bogo_showing_product_count' => $bogo_showing_product_count,
            'bogoProductData' => $bogoProductData
        ];

        return view("frontend.product.ajax_filter_bogo", $data);
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
            'g-recaptcha-response' => [new \App\Rules\ReCaptchaRule('newsletter')],
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

        $settings = Setting::find(1);

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

        $productQueryForCount = clone $productDataFilter;

        $categoryData = $categoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
        $subcategoryData = $subcategoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
        $productDataFilter = $productDataFilter->where('product_name', 'LIKE', '%'.$keyword.'%')->take(12)->get();
        $productCount = $productQueryForCount->where('product_name', 'LIKE', '%'.$keyword.'%')->count();

        $data = compact('categoryData','subcategoryData','productDataFilter','settings','productCount','keyword');
        return view('frontend.searching', $data)->render();

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
