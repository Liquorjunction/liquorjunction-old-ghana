<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Label;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Setting;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Yajra\Datatables\Datatables;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Cart;
use App\Models\ProductVariants;
use App\Models\Uofs;

class ProductController extends Controller
{
    //
    private $uploadPath = "uploads/product/";
    protected $image_uri = "";
    protected $no_image = "";
    protected $business_owner_id = 57;



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
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 8, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }

    }

    public function index()
    {
        // echo "string";exit();
        $wholesalerData = DB::table('main_users')->where('status', '!=', '2')->where('user_type', 2)->get();
        // echo "<pre>";print_r($wholesalerData);exit();
        return view("dashboard.product.list", compact('wholesalerData'));
    }

    public function show($id)
    {
        $product_id = $id;
        // $productData = DB::table('product')
        // ->leftjoin('main_users', 'main_users.id', '=', 'product.supplier_id')
        // ->leftjoin('categories', 'categories.id', '=', 'product.category_id')
        // ->select('product.*', 'main_users.first_name', 'main_users.last_name', 'categories.title', 'main_users.email', 'main_users.phone')
        // ->where('product.id', $product_id)
        // ->where('product.status', '!=', '2')
        // ->first();
        $productData = Product::with([
            'get_product_images' => function ($q) {
                return $q->where('status', '=', 1);
            }
        ])->where('status', '!=', '2')->where('uniqid', $product_id)->first();

        // $productData = Product::where([['uniqid',$product_id],['status', '!=', '2']])->first();


        if (!empty($productData)) {
            $settings = Setting::find(1);
            return view('dashboard.product.show')->with(['productData' => $productData, 'settings' => $settings])
                ->render();



            // return response()->json(['success' => true, 'html' => $html]);
        }
        // return response()->json(['success' => false, 'msg' => 'something wrong.']);
        // return redirect()->route('product.show')->with('doneMessage', 'Product create successfully.');
        return view('dashboard.product.show', compact('productData'));
    }

    public function create()
    {
        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $settings = Setting::find(1);
        $brands = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        $subcategories = SubCategories::where('status', 1)->orderby('title', 'ASC')->get();
        $uofs = Uofs::where('status', 1)->orderBy('id', 'asc')->get();
        return view("dashboard.product.create", compact('categories', 'settings', 'brands', 'uofs', 'subcategories'));
    }

    public function edit($uid)
    {
        $settings = Setting::find(1);
        $productData = Product::with([
            'get_product_images' => function ($q) {
                return $q->where('status', '=', 1);
            }
        ])->where('status', '!=', '2')->where('uniqid', $uid)->first();

        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $subcategories = SubCategories::where([['category_id', $productData->category_id], ['status', 1]])->orderby('title', 'ASC')->get();
        // dd($subcategories->toArray());
        $uofs = Uofs::where('status', 1)->orderBy('id', 'asc')->get();
        $brands = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        return view("dashboard.product.edit", compact('categories', 'settings', 'brands', 'productData', 'subcategories', 'uofs'));
    }
    public function getSubcatlist(Request $request)
    {
        $id = $request->id;
        $data['sub'] = SubCategories::where('category_id', '=', $id)->where('status', '!=', '2')->get();
        //   dd($data['sub']->toArray());
        // print_r($data['sub']);
        // exit;
        return response()->json($data);
    }
    public function validateRequest($id = "")
    {
        // dd($id);
        $maxVideoDuration = 30;
        if ($id != "") {
            $validator = request()->validate(
                [
                    'category_id' => 'required',
                    'subcategory_id' => 'required',
                    'brand_id' => 'required',
                    'product_name' => 'required|max:150',
                    'product_name_fr' => 'required|max:150',
                    'short_description' => 'required|max:150',
                    'short_description_fr' => 'required',
                    'video' => 'mimes:mp4|max:20480',
                    'page_content' => 'required',
                    'property_images.*' => 'mimetypes:image/png,image/jpg,image/jpeg|max:2048',
                    'short_description_fr' => 'nullable',
                    'short_description_fr' => 'required|max:150',
                    'page_content_fr' => 'required',
                    'prod_variant.*.variant_uof' => 'required',
                    'prod_variant.*.variant_size' => 'required',
                    'prod_variant.*.variant_price' => 'required',
                    'prod_variant.*.variant_qty' => 'required',
                    //'prod_variant.*.variant_discounted_price' => 'lt:prod_variant.*.variant_price',
                ],
                [
                    // 'category_id.required' => 'The category field is required.',
                    // 'subcategory_id.required' => 'The subcategory field is required.',
                    // 'brand_id.required' => 'The brand field is required.',
                    // 'page_content.required' => 'The long description field is required.',
                    // 'category_id.required' => 'The category field is required.',
                    // 'subcategory_id.required' => 'The subcategory field is required.',
                    // 'retail_price.required' => 'The original price field is required.',
                    // 'page_content.required' => 'The long description field is required.',
                    // 'brand_id.required' => 'The brand field is required.',
                    // 'property_images.mimetypes' => 'The images must be in .png, .jpg or .jpeg format.',
                    // 'page_content_fr.required' => 'The long description field is required.',
                    // 'qty.required' => 'The product qty field is required.',
                    'product_name_fr.max' => 'The product name should be maximum of 150 charecters.',
                    // 'short_description_fr.max' => 'The short description should be maximum of 150 charecters.',
                    // 'image.required' => 'The image field is requied',
                    'short_description_fr' => 'The short description filed is required',
                    'long_description_fr' => 'The long description filed is required',
                    'category_id.required' => 'The category field is required.',
                    'subcategory_id.required' => 'The subcategory field is required.',
                    'retail_price.required' => 'The original price field is required.',
                    'page_content.required' => 'The long description field is required.',
                    'brand_id.required' => 'The brand field is required.',
                    'property_images' => 'The image filed is required',
                    'property_images.max' => 'The image should be less than 2 MB.',

                    // 'property_images.max'=>'Image size not grater than 2 Mb',
                    'property_images.*.max' => 'The image should be less than 2 MB.',
                    // 'image.max' => 'The image upload not greater than 2 mb size.',

                    'property_images.mimetypes' => 'The images must be in .png, .jpg or .jpeg format.',
                    'property_images.*.mimetypes' => ['The images must be in .png, .jpg or .jpeg format.'],
                    'page_content_fr.required' => 'The long description field is required.',
                    'product_qty.required' => 'The product qty field is required.',
                    'product_name_fr.max' => 'The product name should be maximum of 150 characters.',
                    'prod_variant.*.variant_uof.required' => 'The unit field is required.',
                    'prod_variant.*.variant_size.required' => 'The variant size field is required.',
                    'prod_variant.*.variant_price.required' => 'The original price field is required.',
                    'prod_variant.*.variant_qty.required' => 'The qty field is required.',
                    //'prod_variant.*.variant_discounted_price.required' => 'The discounted price field is required.',
                    //'prod_variant.*.variant_discounted_price.lt' => 'The discounted price must be less than orginal price.',
                    'video.mimes' => 'The video must be in mp4',
                    'video.max' => 'The video should be less than 20 MB',
                    'short_description.max' => 'The short description should be maximum of 150 characters.',
                    'short_description_fr.max' => 'The short description should be maximum of 150 characters.',

                ],
                // $priceRules = [
                //     'prod_variant.*.variant_price' => 'required|numeric',
                //     'prod_variant.*.variant_discounted_price' => 'required|numeric|lt:original_price',
                // ],
            );
            // $validateData = $id !== ""
            // ? request()->validate(array_merge($validator,$priceRules))
            // : request()->validate($validator);


        } else {

            $validator = request()->validate(
                [
                    'brand_id' => 'required',
                    'category_id' => 'required',
                    'subcategory_id' => 'required',
                    // 'brand_id' => 'required',
                    // 'image'=>'required',
                    'product_name' => 'required|max:150',
                    // 'product_name_fr' => 'required|max:40',
                    'short_description' => 'required|max:150',
                    // 'retail_price' => 'required',
                    // 'discount_price' => '',
                    'video' => 'mimes:video/mp4 | max:20000',

                    'property_images' => 'required',
                    'property_images.*' => 'required|mimetypes:image/png,image/jpg,image/jpeg|max:2048',
                    'page_content' => 'required',
                    'product_name_fr' => 'required|nullable|max:150',
                    'short_description_fr' => 'nullable',
                    'short_description_fr' => 'required|max:150',
                    'video' => 'mimes:mp4| max:20480',


                    'page_content_fr' => 'nullable',

                    'page_content_fr' => 'required',

                    // 'product_qty' => 'required',
                    'prod_variant.*.variant_uof' => 'required',
                    'prod_variant.*.variant_size' => 'required',
                    'prod_variant.*.variant_price' => 'required',
                    'prod_variant.*.variant_qty' => 'required',
                    //'prod_variant.*.variant_discounted_price' => 'lt:prod_variant.*.variant_price',

                ],

                [
                    // 'image.required' => 'The image field is requied.',
                    'category_id.required' => 'The category field is required.',
                    'subcategory_id.required' => 'The subcategory field is required.',
                    'retail_price.required' => 'The original price field is required.',
                    'page_content.required' => 'The long description field is required.',
                    'brand_id.required' => 'The brand field is required.',
                    'property_images' => 'The image filed is reuired',
                    'property_images.mimetypes' => 'The images must be in .png, .jpg or .jpeg format.',
                    'property_images.*.mimetypes' => ['The images must be in .png, .jpg or .jpeg format.'],
                    'page_content_fr.required' => 'The long description field is required.',
                    'qty.required' => 'The product qty field is required.',
                    'product_name_fr.max' => 'The product name should be maximum of 150 characters.',
                    'short_description.max' => 'The short description should be maximum of 150 characters.',
                    'short_description_fr.max' => 'The short description should be maximum of 150 characters.',
                    'prod_variant.*.variant_uof.required' => 'The unit field is required.',
                    'prod_variant.*.variant_size.required' => 'The variant size field is required.',
                    'prod_variant.*.variant_price.required' => 'The original price field is required.',
                    'prod_variant.*.variant_qty.required' => 'The qty field is required.',
                    //'prod_variant.*.variant_discounted_price.required' => 'The discounted price field is required.',
                    //'prod_variant.*.variant_discounted_price.lt' => 'The discounted price must be less than orginal price.',
                    'video.max' => 'The video should be less than 20 MB',
                    'property_images.max' => 'The profile photo should be less than 2 MB.',
                    'property_images.*.max' => 'The profile photo should be less than 2 MB.',

                ],

                // $priceRules = [
                //     'prod_variant.*.variant_price' => 'required|numeric',
                //     'prod_variant.*.variant_discounted_price' => 'required|numeric|lt:original_price',
                // ],
            );

            // Combine common rules with price rules if $id is not empty
            // $validateData = $id !== ""
            //     ? request()->validate(array_merge($validator,$priceRules))
            //     : request()->validate($validator);
        }
        return $validator;
    }

    public function add_more_variant(Request $request)
    {
        $settings = Setting::find(1);
        $key = $request->count;
        $uofs = Uofs::where('status', 1)->orderBy('id', 'asc')->get();
        $html = view('dashboard.product.addMoreVariant')->with(['key' => $key, 'settings' => $settings, 'uofs' => $uofs])->render();
        return response()->json(['success' => true, 'html' => $html]);
    }

    public function remove_variant(Request $request)
    {
        $variant_id = $request->input('variant_id');
        $product_id = $request->input('product_id');

        ProductVariants::where('product_id', $product_id)->where('id', $variant_id)->delete();
        return response()->json(['success' => true, 'data' => ""]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $this->validateRequest();
        $uniqid = uniqid();
        // $supplier_id = auth()->guard('main_user')->user()->id;
        $sku = $this->createSku($request->product_name);
        $product = new Product();

        $formFileName = "video";
        $videofileFinalName_ar = "";
        if ($request->$formFileName) {
            // foreach ($request->$formFileName as $singlefile) {
            $singlefile = $request->$formFileName;
            $videofileFinalName_ar = time() . rand(
                1111,
                9999
            ) . '.' . $singlefile->getClientOriginalExtension();
            $path = $this->getUploadPath();
            $singlefile->move('uploads/product', $videofileFinalName_ar);

            // }
        }
        $product->uniqid = $uniqid;
        $product->category_id = $request->category_id;
        $product->product_name = $request->product_name;
        // $product->supplier_id = $supplier_id;
        $product->description = $request->page_content;
        $product->short_description = $request->short_description;
        // $product->product_image = $fileFinalName_ar;
        // $product->tech_data_sheet = $fileFinalName_ar1;
        // $product->retail_price = $request->retail_price;
        // $product->discount_price = $request->discount_price;
        // $product->in_online = 1;
        // $product->in_store = 1;
        $product->is_product_bestseller = $request->input('is_product_bestseller');
        $product->offer = $request->input('offer');
        $product->subcategory_id = $request->input('subcategory_id');
        $product->brand_id = $request->input('brand_id');
        $product->video = $videofileFinalName_ar;
        $product->sku = $sku;
        $product->product_name_fr = $request->input('product_name_fr');
        $product->short_description_fr = $request->input('short_description_fr');
        $product->page_content_fr = $request->input('page_content_fr');
        $product->product_qty = $request->input('product_qty');
        $product->status = 1;
        $product->is_admin_approve = 0;
        $product->save();

        $number = str_pad($product->id, 6, '0', STR_PAD_LEFT);
        $product_item_id = 'PRO' . $number;

        $updatepsw = Product::where('id', $product->id)->update(
            array(
                'product_item_id' => @$product_item_id,
            )
        );

        // $supplierData = DB::table('main_users')->where('id', $supplier_id)->first();



        // $userData = DB::table('main_users')->where('status', 1)->where('user_type', 1)->whereNotNull('device_token')->get();
        // echo "<pre>";print_r($userData);exit();

        // foreach ($userData as $user) {

        //     // $title = $supplierData->store_name . ' ' . "has added new product";
        //     // $message = $supplierData->store_name . ' ' . "has added new product";
        //     $remember_token = "f9FMF8ZF5kkno3HGTFCzxn:APA91bHYVjZFR79puFDSZQgx2gjGfoCaKmnZIZRlqaTN4guWZBUod0BkQDjqvCBx9m3xkGFwPWLahInE33dqEg9AEZbdV9jOZfP-7Jwuyp2s-1gIoX2Og47uuqLiieZ8SlOiM2dgN5lN";
        //     $device_token = "test";
        //     // echo "<pre>";print_r($device_token);exit();
        //     $device_type = 1;

        //     $notification = new Notification();

        //     $notification->sender_id = $user->id;
        //     $notification->receiver_id = $user->id;
        //     $notification->notification_type = 2;
        //     $notification->title = @$title;
        //     $notification->message = @$message;
        //     $notification->is_read = 0;
        //     $notification->save();


        //     $response = (new \Helper)->send_notification_FCM($remember_token, $title, $message, $device_type);
        //     $response = (new \Helper)->sendNotification($device_token, $title, $message, $device_type);
        // }

        $formFileName2 = "property_images";

        if (!empty($request->property_images)) {

            foreach ($request->property_images as $images) {
                if ($images != "") {
                    $imagesAR = time() . rand(
                        1111,
                        9999
                    ) . '.' . $images->getClientOriginalExtension();
                    $path = $this->getUploadPath();
                    // echo "<pre>";print_r($path);exit();
                    $images->move($path, $imagesAR);
                }
                $productimage = new ProductImage();
                $productimage->product_id = $product->id;
                $productimage->image = $imagesAR;
                $productimage->status = 1;

                $productimage->save();
            }
        }

        if ($request->input('prod_variant')) {

            foreach ($request->input('prod_variant') as $key => $value) {

                // if ($value['variant_uof'] == 2) {
                //     // Convert from ml to L
                //     $variant_size_in_l = $value['variant_size'] / 1000; // 1000 ml = 1 L
                    
                //     // Format the variant size
                //     $variant_size = $variant_size_in_l . 'L'; // Append 'L' to denote liters
                // } else {
                //     // Keep the original variant size
                //     $variant_size = $value['variant_size'];
                // }
                
                // if ($value['variant_uof'] == 2) {
                //     // Custom rounding logic
                //     $fraction = $value['variant_size'] - floor($value['variant_size']);
                //     if ($fraction < 0.5) {
                //         $rounded_size = floor($value['variant_size']);
                //     } else {
                //         $rounded_size = ceil($value['variant_size']);
                //     }
                //     $variant_size = $rounded_size . '/1'; // Represent as a simple fraction
                // } else {
                //     $variant_size = $value['variant_size'];
                // }

                $variantData = [
                    "product_id" => $product->id,
                    "variant_uof" => $value['variant_uof'],
                    "variant_size" => $value['variant_size'],
                    // "variant_size" => $variant_size,
                    "variant_price" => $value['variant_price'],
                    "variant_qty" => $value['variant_qty'],
                    "available_qty" => $value['variant_qty'],
                    //  "variant_discounted_price" => $value['variant_discounted_price'] ? $value['variant_discounted_price'] :0.00,

                ];
                // dd($variantData);

                // if(@$value['id']) {
                //     ProductVariants::where('id', $value['id'])->update($variantData);
                // } else {
                ProductVariants::create($variantData);
                // }
            }
        }
        // Alert::success('Success', 'New Product created successfully.');
        // Alert::success('Success', __('backend.New_Product_created_successfully'));


        return redirect()->route('product')->with('doneMessage', 'Product create successfully.');
    }

    public function update(Request $request, $id)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $this->validateRequest($id);
        $product_id = $id;

        $existProduct = Product::find($product_id);
        if ($product_id) {
            $formFileName = "video";
            $videofileFinalName_ar = "";
            if ($request->$formFileName) {
                // foreach ($request->$formFileName as $singlefile) {
                $singlefile = $request->$formFileName;
                $videofileFinalName_ar = time() . rand(
                    1111,
                    9999
                ) . '.' . $singlefile->getClientOriginalExtension();
                $path = $this->getUploadPath();
                $singlefile->move('uploads/product', $videofileFinalName_ar);

                // }
            }

            if (@$existProduct->video && $request->is_video_delete == 1) {
                $path = $this->getUploadPath();
                try {
                    unlink(public_path($path . '/' . $existProduct->video));
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }


            $product = Product::where('id', $product_id)->update(
                array(
                    'product_name' => $request->product_name,
                    'description' => $request->page_content,
                    'short_description' => $request->short_description,
                    // 'supplier_id' => $supplier_id,
                    'category_id' => $request->category_id,
                    'is_product_bestseller' => $request->input('is_product_bestseller') ? 1 : 0,
                    'offer' => $request->input('offer') ? 1 : 0,
                    'subcategory_id' => $request->input('subcategory_id'),
                    'brand_id' => $request->input('brand_id'),
                    'video' => $videofileFinalName_ar,
                    'product_name_fr' => $request->input('product_name_fr'),
                    'short_description_fr' => $request->input('short_description_fr'),
                    'page_content_fr' => $request->input('page_content_fr'),
                    'product_qty' => $request->input('product_qty'),
                )
            );

            $cart = Cart::where('product_id', $product_id)->where('status', 1)->update(
                array(
                    'total_price' => isset($request->discount_price) ? $request->discount_price : $request->retail_price,
                    'product_price' => $request->retail_price,
                    'offer_price' => @$request->discount_price,
                )
            );

            if (!empty($request->deleted_image)) {
                // dd($request->deleted_image);
                foreach ($request->deleted_image as $delete) {
                    $productimage = ProductImage::where('id', $delete)->update(
                        array(
                            'status' => 2,
                        )
                    );
                }
            }

            $formFileName2 = "property_images";

            if (!empty($request->property_images)) {

                foreach ($request->property_images as $images) {
                    if ($images != "") {
                        $imagesAR = time() . rand(
                            1111,
                            9999
                        ) . '.' . $images->getClientOriginalExtension();
                        $path = $this->getUploadPath();
                        // echo "<pre>";print_r($path);exit();
                        $images->move($path, $imagesAR);
                    }
                    $productimage = new ProductImage();
                    $productimage->product_id = $product_id;
                    $productimage->image = $imagesAR;
                    $productimage->status = 1;

                    $productimage->save();
                }
            }

            if ($request->input('prod_variant')) {

                foreach ($request->input('prod_variant') as $key => $value) {

                    // $newValue = $value['variant_size'];
                    // $old_variant_size = ProductVariants::where('product_id', $product_id)->where('status', 1)->get();
                    // dd($newValue);
                    // dd($old_variant_size);

                    // if ($value['variant_uof'] == 2) {
                    //     if ($newValue != $old_variant_size) {
                    //         // Convert from ml to L only if the value has changed
                    //         $variant_size_in_l = $newValue / 1000; // 1000 ml = 1 L
                            
                    //         // Format the variant size
                    //         $variant_size = $variant_size_in_l . 'L'; // Append 'L' to denote liters
                    //     } else {
                    //         // If the value hasn't changed, keep the original variant size
                    //         $variant_size = $old_variant_size;
                    //     }
                    // } else {
                    //     // Keep the original variant size
                    //     $variant_size = $newValue;
                    // }
                    
                    // if ($value['variant_uof'] == 2) {
                    //     $fraction = $value['variant_size'] - floor($value['variant_size']);
                    //     if ($fraction < 0.5) {
                    //         $rounded_size = floor($value['variant_size']);
                    //     } else {
                    //         $rounded_size = ceil($value['variant_size']);
                    //     }
                    //     $variant_size = $rounded_size . '/1'; // Represent as a simple fraction
                    // } else {
                    //     $variant_size = $value['variant_size'];
                    // }

                    if (@$value['id']) {
                        $variantData = [
                            "product_id" => $product_id,
                            "variant_uof" => $value['variant_uof'],
                            "variant_size" => $value['variant_size'],
                            "variant_price" => $value['variant_price'],
                            // "variant_discounted_price" => $value['variant_discounted_price'],
                        ];

                        $get_variant_data = ProductVariants::where('id', $value['id'])->first();
                        // if ($get_variant_data->variant_qty != "") {
                        //     $getVqty = $get_variant_data->variant_qty + $value['variant_qty'];
                        //     $getAqty = $getVqty - $get_variant_data->sold_qty;

                        //     $variantData["variant_qty"] = $getVqty;
                        //     $variantData["available_qty"] = $getAqty;
                        // }

                        $variantData["variant_qty"] = $value['variant_qty'];
                        $variantData["available_qty"] = max($value['variant_qty'] - ($get_variant_data->sold_qty ?? 0), 0);


                        ProductVariants::where('id', $value['id'])->update($variantData);
                    } else {
                        $variantData = [
                            "product_id" => $product_id,
                            "variant_uof" => $value['variant_uof'],
                            "variant_size" => $value['variant_size'],
                            // "variant_size" => $variant_size,
                            "variant_price" => $value['variant_price'],
                            "variant_qty" => $value['variant_qty'],
                            "available_qty" => $value['variant_qty'],
                            // "variant_discounted_price" => $value['variant_discounted_price'],
                        ];
                        ProductVariants::create($variantData);
                    }
                }
            }
        } else {

        }
        return redirect()->route('product')->with('doneMessage', 'Product updated successfully.');
    }

    public function updateAll(Request $request)
    {
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;

                if ($status == 2) {
                    $products = Product::with([
                        'get_product_images' => function ($query) {
                            $query->where('status', 1);
                        }
                    ])->wherein('id', $ids)->get();

                    $path = $this->getUploadPath();
                    foreach ($products as $singlePro) {
                        if ($singlePro->get_product_images->count() > 0) {
                            foreach ($singlePro->get_product_images as $singleImage) {
                                try {
                                    unlink(public_path($path . '/' . $singleImage->image));
                                } catch (\Throwable $th) {
                                    // throw $th;
                                }
                            }
                        }
                    }
                }
                if ($status == 1) {
                    $product = Product::wherein('id', $ids);
                    $brandIds = $product->pluck('brand_id');
                    $subcategory_id = $product->pluck('subcategory_id');

                    $subcategory = SubCategories::whereIn('id', $subcategory_id)->where('status', '!=', 1)->pluck('category_id');
                    $category = Categories::wherein('id', $subcategory)->where('status', '!=', 1)->get();
                    $brand = Brand::wherein('id', $brandIds)->where('status', '!=', 1)->get();
                    $brandcount = count($brand);
                    // dd($brandcount);
                    $categorycount = count($category);
                    $subcategorycount = count($subcategory);
                    if ($brandcount > 0 || $subcategorycount > 0 || $categorycount > 0) {
                        return response()->json(['success' => true, 'msg' => 'Brand, Category, Subcategory is inactive, Please first active brand, category, subcategory']);
                    }
                }
                Product::wherein('id', $ids)->update(['status' => $status]);

                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) delete successfully']);
                } else if ($status == 0) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) deactive successfully']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'Record(s) active successfully']);
                }
            }

            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
    }
    public function createSku($urlString)
    {
        $uid = Product::orderBy('created_at', 'DESC')->first();
        if ($uid && $uid->id != null) {
            $temp_uid = (int) str_replace('LQ', '', $uid->id) + 1;
            $temp_uid = str_pad($temp_uid, 6, "0", STR_PAD_LEFT);
        } else {
            $temp_uid = 1;
            $temp_uid = str_pad($temp_uid, 6, "0", STR_PAD_LEFT);
        }

        return 'LQ' . $temp_uid;
    }
    public function status_active(Request $request)
    {
        $product_id = $request->id;
        Product::where('id', $product_id)->update(['status' => 0]);
        Cart::where('product_id', $product_id)->update(array('status' => 2));
        \Alert::success('Success', __('backend.product_deactive_sucessfully'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {

        $product_id = $request->id;
        $product = Product::where('id', $product_id)->first();
        $subcategory = SubCategories::where('id', $product->subcategory_id)->first();
        $category = Categories::where('id', $product->category_id)->first();
        $brand = Brand::where('id', $product->brand_id)->first();
        if ($brand->status != 1 || $category->status != 1 || $subcategory->status != 1) {
            \Alert::warning('Warning', __('Brand, Category, Subcategory is inactive, Please first active brand, category, subcategory'));
            return response()->json(['success' => 'true']);
        }
        Product::where('id', $product_id)->update(['status' => 1]);
        \Alert::success('Success', __('backend.product_active_sucessfully'));

        return response()->json(['success' => 'true']);
    }


    public function ischecked(Request $request)
    {
        $id = $request->id;
        $product = Product::find($id);
    
        $bestsellerCount = Product::activeProductsBasedOnRelations()->where('is_product_bestseller',1)->count();
        // print_r($bestsellerCount);
        // die;
        // $nonBestsellerOfferCount = Product::where('is_product_bestseller', '!=', 1)->where('offer', 1)->count();
        //  print_r($nonBestsellerOfferCount );
        // die;
        // if ($product->$bestsellerCount >= 10) {
            if ($bestsellerCount >= 20 && $product->is_product_bestseller == 0) {
            return response()->json(['success' => 'false', 'message' => 'Best seller limit exceeded.']);
        }
    
        $product->is_product_bestseller = $product->is_product_bestseller == '1' ? '0' : '1';
    
        \Alert::success('Success', __('This product '.($product->is_product_bestseller == '1' ? 'added to' : 'removed from').' best seller successfully'));
        
        $product->save();
        // print_r($product);
        // die;
        return response()->json(['success' => 'true']);
    }
    
    public function offervalue(Request $request)
    {
        $id = $request->id;
        $product = Product::find($id);
    
        $offerCount = Product::activeProductsBasedOnRelations()->where('offer',1)->count();
        // print_r($offerCount);
        // die;
        // $nonOfferBestsellerCount = Product::where('offer', '!=', 1)->where('is_product_bestseller', 1)->count();
        // print_r($nonOfferBestsellerCount);
        //  die;
        // if ($product->$offerCount >= 10) {
            if ($offerCount >= 20 && $product->offer == 0) {
                return response()->json(['success' => 'false', 'message' => 'Offer product limit exceeded.']);
        }
    
        $product->offer = $product->offer == '1' ? '0' : '1';
        
        \Alert::success('Success', __('This product '.($product->offer == '1' ? 'added to' : 'removed from').' the offer successfully'));
    
        $product->save();
        // print_r($product);
        // die;
        return response()->json(['success' => 'true']);
    }
    

    public function anyData(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $supplier_id = $request->get('supplier_id');
        $is_admin_approve = $request->get('is_admin_approve');
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'id';
        } elseif ($columnIndex == 1) {
            $sort = 'product.sku';
        } elseif ($columnIndex == 2) {
            $sort = 'product.product_name';
        } elseif ($columnIndex == 3) {
            $sort = 'brand.title';
        } elseif ($columnIndex == 4) {
            $sort = 'categories.title';
        } elseif ($columnIndex == 5) {
            $sort = 'sub_categories.title';
        } elseif ($columnIndex == 6) {
            // $sort = 'product_variants.variant_price';
            $sort = 'first_variant.variant_price';
        } elseif ($columnIndex == 7) {
            $sort = 'product.is_product_bestseller';
        } elseif ($columnIndex == 8) {
            $sort = 'product.offer';
        } elseif ($columnIndex == 9) {
            $sort = 'product.status';

        } else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        $firstVariant = DB::table('product_variants as pv1')
        ->select('pv1.product_id', 'pv1.variant_price')
        ->whereRaw('pv1.id = (
            SELECT pv2.id
            FROM product_variants pv2
            WHERE pv2.product_id = pv1.product_id
            ORDER BY pv2.id ASC
            LIMIT 1
        )');

        // $totalAr = DB::table('product')
        //     ->leftjoin('brand', 'brand.id', '=', 'product.brand_id') // Potential issue: Check this join
        //     ->leftjoin('categories', 'categories.id', '=', 'product.category_id')
        //     ->leftjoin('product_variants', 'product_variants.product_id', '=', 'product.id')
        //     ->leftjoin('sub_categories', 'sub_categories.id', '=', 'product.subcategory_id')
        //     ->select('product.*', 'categories.title as product_category', 'brand.title as brand_name', 'sub_categories.title as product_subcategory', \DB::raw(" 
        //     MIN(product_variants.variant_price) AS product_price"))
        //     //->where('product_variants.product_id','=','product.id')
        //     // ->selectRaw('MIN(product_variants.variant_price) AS product_price')
        //     ->groupBy('product_variants.product_id')
        //     ->where('product.status', '!=', '2');



        $totalAr = DB::table('product')
            ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
            ->leftJoin('categories', 'categories.id', '=', 'product.category_id')
            ->leftJoin('sub_categories', 'sub_categories.id', '=', 'product.subcategory_id')
            ->leftJoinSub($firstVariant, 'first_variant', function ($join) {
                $join->on('first_variant.product_id', '=', 'product.id');
            })
            ->select(
                'product.*',
                'categories.title as product_category',
                'brand.title as brand_name',
                'sub_categories.title as product_subcategory',
                'first_variant.variant_price as product_price'
            )
            ->where('product.status', '!=', '2');

        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('brand.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('categories.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('sub_categories.title', 'like', '%' . $searchValue . '%')
                    ->orWhere('product.product_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('product.sku', 'like', '%' . $searchValue . '%')
                    // ->orWhere('product_variants.variant_price', 'like', '%' . $searchValue . '%');
                    ->orWhere('first_variant.variant_price', 'like', '%' . $searchValue . '%');
            });
        }
        // if (!empty($supplier_id)) {
        //     $totalAr->where('product.supplier_id', $supplier_id);
        // }product_price

        if (!empty($is_admin_approve)) {
            // echo "string";exit();
            if ($is_admin_approve == 3) {
                $totalAr->where('product.is_admin_approve', 0);
            } else {

                $totalAr->where('product.is_admin_approve', '=', $is_admin_approve);
            }
        }

        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        $selectedBestSellers = $request->input('is_product_bestseller', []);
        $selectedOffers = $request->input('offer', []);

        // foreach ($selectedBestSellers as $productId) {
        //     $product = Product::find($productId);
        //     if ($product) {
        //         // Update the 'is_product_bestseller' value in the database
        //         Product::where('id', $product)->update(['is_product_bestseller' => 1]);
        //     }else{
        //         $product->update(['is_product_bestseller' => 0]);
        //     }
        // }

        // foreach ($selectedOffers as $productId)product_price {
        //     $product = Product::find($productId);
        //     if ($product) {
        //         $product->update(['offer_value' => true]);
        //     }
        // }
        $data_arr = [];
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 8, 'read');
        $check_creation_permission = @Helper::GetRolePermission(Auth::user()->user_type, 8, 'create');
        $check_updation_permission = @Helper::GetRolePermission(Auth::user()->user_type, 8, 'update');
        $check_deletion_permission = @Helper::GetRolePermission(Auth::user()->user_type, 8, 'delete');
        $active_class = "";
        $inactive_class = "role_status_inactive";
        if (isset($check_updation_permission) && $check_updation_permission) {
            $active_class = "status_active";
            $inactive_class = "status_inactive";
        }
        foreach ($totalAr as $key => $data) {
            $productEdit = route('product.edit', ['id' => $data->uniqid]);
            $productShow = route('product.show', ['id' => $data->uniqid]);
            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline ' . $active_class . '" data-id="' . $data->id . '"></i>';
            } else {
                $status = '<i class="fa fa-thumbs-down text-danger inline ' . $inactive_class . '" data-id="' . $data->id . '"></i>';
            }
            $settings = Setting::find(1);

            if (isset($check_view_permission) && $check_view_permission) {
                $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-product" href="' . $productShow . '" title="Show"></a>';
                $options .= '<a class="btn btn-sm success paddingset" href="' . route('product.rating', ['id' => $data->id]) . '" title="Rating & review"> <i class="material-icons" style="font-size:25px;">&#9733;</i> </a>';
            }

            if (isset($check_updation_permission) && $check_updation_permission) {
                $options .= '<a class="btn btn-sm success paddingset" href="' . $productEdit . '" title="Edit"> <small><i class="material-icons" >&#xe3c9;</i> </small> </a>';
                $best_seller = '<input type="checkbox" class="bestsellervalue" style = "cursor:pointer;"name="is_product_bestseller[]" value="' . $data->id . '" ' . ($data->is_product_bestseller ? 'checked' : '') . '>';
                $offers = '<input type="checkbox" class ="offervalue" name="offer_value[]" style = "cursor:pointer;" value="' . $data->id . '" ' . ($data->offer ? 'checked' : '') . '>';
            } else {
                $best_seller = '<span style="font-size:20px;">' . ($data->is_product_bestseller ? '&#10003;' : '&#10005;') . '</span>';
                $offers = '<span style="font-size:20px;">' . ($data->offer ? '&#10003;' : '&#10005;') . '</span>';
            }

            if (isset($check_deletion_permission) && $check_deletion_permission) {
                $options .= '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            }


            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" onchange="checkChange();" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "id" => isset($data->id) ? $data->id : '',
                "product_name" => isset($data->product_name) ? $data->product_name : '',
                "product_category" => isset($data->product_category) ? $data->product_category : '',
                "product_subcategory" => isset($data->product_subcategory) ? $data->product_subcategory : '',
                "brand_name" => isset($data->brand_name) ? $data->brand_name : '',
                "product_price" => isset($data->product_price) ? $data->product_price . ' ' . @$settings->currency_symbol : '',
                "sku" => isset($data->sku) ? $data->sku : '',
                "bestseller" => isset($best_seller) ? $best_seller : '',
                "offer_value" => isset($offers) ? $offers : '',
                "status" => isset($status) ? $status : '',
                "options" => isset($options) ? $options : '',
            );
        }
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        );
        echo json_encode($response);
    }
}
