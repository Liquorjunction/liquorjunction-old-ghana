<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Product;
use App\Models\PromotedProduct;
use App\Models\Setting;
use App\Models\ProductImage;
use App\Models\Cart;
use App\Models\Notification;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Session;
use Alert;
use App\Imports\ProductImport;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\Datatables\Datatables;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    //
    private $uploadPath = "uploads/product";
    private $uploadGetPath = "/uploads/product";
    protected $image_uri = "";
    protected $no_image = "";
    protected $business_owner_id = 57;

   

    public function getImagePath(){
        return $this->uploadPath;
    }

    public function setImagePath(){
        $this->image_uri = $this->getImagePath() . '/';
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }
    public function index()
    {
        // echo "string";exit();
        $wholesalerData = DB::table('main_users')->where('status','!=','2')->where('user_type',2)->get();
        $categories = Categories::where('status',1)->orderby('title','ASC')->get();
        $categories_data = Categories::where('status','!=',2)->orderby('title','ASC')->get();
        $settings = Setting::find(1);
        // echo "<pre>";print_r($settings->toArray());exit();
        return view("wholesaler.product.list",compact('wholesalerData','categories','categories_data','settings'));
    }

    public function create()
    {
        $categories = Categories::where('status',1)->orderby('title','ASC')->get();
        $settings = Setting::find(1);
        return view("wholesaler.product.create",compact('categories','settings'));
    }

    public function store(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit;


        // echo "<pre>";print_r(auth()->guard('main_user')->user());exit;
        
       
        $this->validateRequest();
            $uniqid = uniqid();
        $supplier_id = auth()->guard('main_user')->user()->id;
        $product = new Product();
        // $validator = \Validator::make($request->all(), [
        //             'product_name' => ['required',Rule::unique('product')->where(function ($query) use($supplier_id){
        //     return $query->where('status','!=','2')->where('supplier_id',$supplier_id);
        //         })],

        //     ]);

        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }
        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $path = $this->getUploadPath();
            // echo "<pre>";print_r($path);exit();
            $request->file($formFileName)->move($path, $fileFinalName_ar);
        }

        $formFileName1 = "tech_data_sheet";
        $fileFinalName_ar1 = "";
        if ($request->$formFileName1 != "") {
            $fileFinalName_ar1 = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName1)->getClientOriginalExtension();
            $path = $this->getUploadPath();
            // echo "<pre>";print_r($path);exit();
            $request->file($formFileName1)->move($path, $fileFinalName_ar1);
        }

        
        

        $product->uniqid = $uniqid;
        $product->category_id = $request->category_id;
        $product->product_name = $request->product_name;
        $product->supplier_id = $supplier_id;
        $product->description = $request->page_content;
        $product->short_description = $request->short_description;
        $product->product_image = $fileFinalName_ar;
        $product->tech_data_sheet = $fileFinalName_ar1;
        $product->retail_price = $request->retail_price;
        $product->discount_price = $request->discount_price;
        $product->in_online = 1;
        $product->in_store = 1;
        $product->status = 1;
        $product->is_admin_approve = 0;
        $product->save();

        $number = str_pad($product->id,6,'0',STR_PAD_LEFT);
        $product_item_id = 'PRO'.$number;

        $updatepsw = Product::where('id',$product->id)->update(array(
                   'product_item_id' => @$product_item_id,
                ));

        $supplierData = DB::table('main_users')->where('id',$supplier_id)->first();
        

        
        $userData = DB::table('main_users')->where('status',1)->where('user_type',1)->whereNotNull('device_token')->get();
        // echo "<pre>";print_r($userData);exit();

        foreach ($userData as $user) {

        $title = $supplierData->store_name.' '."has added new product";
        $message = $supplierData->store_name.' '."has added new product";
        $remember_token = "f9FMF8ZF5kkno3HGTFCzxn:APA91bHYVjZFR79puFDSZQgx2gjGfoCaKmnZIZRlqaTN4guWZBUod0BkQDjqvCBx9m3xkGFwPWLahInE33dqEg9AEZbdV9jOZfP-7Jwuyp2s-1gIoX2Og47uuqLiieZ8SlOiM2dgN5lN";
        $device_token = "test";
        // echo "<pre>";print_r($device_token);exit();
        $device_type = 1;

        $notification = new Notification();

        $notification->sender_id = $user->id;
        $notification->receiver_id = $user->id;
        $notification->notification_type = 2;
        $notification->title = @$title;
        $notification->message = @$message;
        $notification->is_read = 0;
        $notification->save();

            
         $response = (new \Helper)->send_notification_FCM($remember_token, $title, $message, $device_type);
        $response = (new \Helper)->sendNotification($device_token, $title, $message, $device_type);
        }

        $formFileName2 = "property_images";
        
        if (!empty($request->property_images)) {
            
         foreach ($request->property_images as $images) {
            if ($images != "") {
                $imagesAR = time() . rand(1111,
                        9999) . '.' . $images->getClientOriginalExtension();
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
        // Alert::success('Success', 'New Product created successfully.');
        Alert::success('Success', __('backend.New_Product_created_successfully'));
    
       
        return redirect()->route('wholesalerproduct')->with('doneMessage', 'Product create successfully.');
        
    }

    public function update(Request $request,$id)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $this->validateRequest($id);
        $product_id = $id;
        $formFileName = "image";
        $fileFinalName_ar = "";
        if ($request->$formFileName != "") {
            $fileFinalName_ar = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                $path = $this->getUploadPath();
                // echo "<pre>";print_r($path);exit();
                $request->file($formFileName)->move($path, $fileFinalName_ar);
                $category = Product::where('id', $product_id)->update(array(
                
                'product_image' => $fileFinalName_ar,
            ));
            }

            $formFileName1 = "tech_data_sheet";
        $fileFinalName_ar1 = "";
        if ($request->$formFileName1 != "") {
            $fileFinalName_ar1 = time() . rand(1111,
                    9999) . '.' . $request->file($formFileName1)->getClientOriginalExtension();
                $path = $this->getUploadPath();
                // echo "<pre>";print_r($path);exit();
                $request->file($formFileName1)->move($path, $fileFinalName_ar1);
                $category = Product::where('id', $product_id)->update(array(
                
                'tech_data_sheet' => $fileFinalName_ar1,
            ));
            }
       $product = Product::where('id', $product_id)->update(array(
                'product_name' => $request->product_name,
                'description' => $request->page_content,
                'short_description' => $request->short_description,
                // 'supplier_id' => $supplier_id,
                'category_id' => $request->category_id,
                'retail_price' => $request->retail_price,
                'discount_price' => $request->discount_price,
                'in_online' => $request->in_online,
                'in_store' => $request->in_store,
            ));

       $cart = Cart::where('product_id',$product_id)->where('status',1)->update(array(
            'total_price' => isset($request->discount_price) ? $request->discount_price : $request->retail_price,
            'product_price' => $request->retail_price,
            'offer_price' => @$request->discount_price,
       ));

       if (!empty($request->deleted_image)) {
           foreach ($request->deleted_image as $delete) {
               $productimage = ProductImage::where('id', $delete)->update(array(
                'status' => 2,
            ));
           }
       }

       $formFileName2 = "property_images";
        
        if (!empty($request->property_images)) {
            
         foreach ($request->property_images as $images) {
            if ($images != "") {
                $imagesAR = time() . rand(1111,
                        9999) . '.' . $images->getClientOriginalExtension();
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

        return redirect()->route('wholesalerproduct')->with('doneMessage', 'Product updated successfully.');
    }


    public function validateRequest($id="")
    {
        if($id !="")
        {
            $validateData =request()->validate([
                'category_id' => 'required',
                'product_name' => 'required|max:40',
                'short_description' => 'required',
                'retail_price' => 'required',
                'discount_price' => '',
                // 'tech_data_sheet' => 'required',
                'page_content' => 'required',
            ],
            [
                'category_id.required' => 'The category field id required.',
                'page_content.required' => 'The long description field is required.'
            ]);

        }else{

            $validateData =request()->validate([
                'category_id' => 'required',
                'product_name' => 'required|max:40',
                'short_description' => 'required',
                'retail_price' => 'required',
                'discount_price' => '',
                'image' => 'required',
                'tech_data_sheet' => 'required|mimes:pdf',
                'page_content' => 'required',
            ],
            [
                'category_id.required' => 'The category field id required.',
                'page_content.required' => 'The long description field is required.'
            ]);
            
        }

        return $validateData;
    }

    // public function show(Request $request)
    // {
    //     $product_id = $request->product_id;
    //    $productData = DB::table('product')->leftjoin('main_users','main_users.id','=','product.supplier_id')->leftjoin('categories','categories.id','=','product.category_id')->select('product.*','main_users.first_name','main_users.last_name','categories.title','main_users.email','main_users.phone')->where('product.status','!=','2')->where('product.id',$product_id)->first();
        
    //      if(!empty($productData))
    //         {
    //             $settings = Setting::find(1);
    //             $html =  view('wholesaler.product.show')->with(['productData' => $productData,'settings' => $settings])->render();

 

    //             return response()->json(['success' => true,'html'=> $html]);
    //         }
    //         return response()->json(['success' => false,'msg'=> 'something wrong.']);
    // }

    public function show($id)
    {
        $product_id = $id;
       $productData = DB::table('product')->leftjoin('main_users','main_users.id','=','product.supplier_id')->leftjoin('categories','categories.id','=','product.category_id')->select('product.*','main_users.first_name','main_users.last_name','categories.title','main_users.email','main_users.phone')->where('product.status','!=','2')->where('product.uniqid',$product_id)->first();

       $productimage = ProductImage::where('product_id',$productData->id)->where('status',1)->get();

       $settings = Setting::find(1);


        return view('wholesaler.product.show', compact('productData','settings','productimage'));
    }

    public function edit($id)
    {
        // $Faq = Categories::find($id);
       
            // echo "<pre>";print_r($id);exit();
            $product_id = $id;
            $productData = Product::where('uniqid',$product_id)->where('status','!=',2)->first();
                $settings = Setting::find(1);
                $categories = Categories::where('status','!=',2)->orderby('title','ASC')->get();
                $productimage = ProductImage::where('product_id',$productData->id)->where('status',1)->get();
                // echo "<pre>";print_r($productimage->toArray());exit();

            return view('wholesaler.product.edit', compact('productData','settings','categories','productimage'));
      

    }

    public function wholesalerproductUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                Product::wherein('id', $ids)->update(['status' => $status]);

                $product = PromotedProduct::wherein('product_id',$ids)->update(array(
                'status' => $status,
         ));
               
                if($status == 2){
                    // Alert::success('Success', __('backend.Record_deleted_sucessfully'));
                    return response()->json(['success' => true,'msg'=>'Record deleted successfully']);
                  }else if($status == 0){
                    // Alert::success('Success', __('backend.Product_deactive_sucessfully'));
                   return response()->json(['success' => true,'msg'=>'Product deactive successfully']);
                  }else{
                    // Alert::success('Success', __('backend.Product_active_sucessfully'));
                   return response()->json(['success' => true,'msg'=>'Product active successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
        
    }

    public function status_active(Request $request){
        $product_id = $request->product_id;
         Product::where('id', $product_id)->update(['status' => 0]);

          $product = PromotedProduct::where('product_id',$product_id)->update(array(
                'status' => 0,
         ));
         Alert::success('Success', __('backend.Product_deactive_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request){
        $product_id = $request->product_id;
         Product::where('id', $product_id)->update(['status' => 1]);
         $product = PromotedProduct::where('product_id',$product_id)->update(array(
                'status' => 1,
         ));
         Alert::success('Success', __('backend.Store_active_sucessfully'));
         return response()->json(['success' => 'true']);
    }

    public function storeProduct(Request $request)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $validator = \Validator::make($request->all(), [ 'uploaded_file' => 'required|file|mimes:xls,xlsx,csv' ]); 
        if ($validator->fails()) { 
            return response()->json($validator->errors(), 422);
         }

         $filePath = $request->file('uploaded_file'); 
         $import = new ProductImport;

         Excel::import($import, $filePath);

          if ($import->no_data == 0) 
        { 
            Alert::error('Error', 'Import file will be in right format.');
            return response()->json(['success' => 'false']);
        }
        else if($import->no_data == -1)
        {
            Alert::error('Error', 'This category is not our system.');
            return response()->json(['success' => 'false']);
        }else if ($import->no_data == 2) {
            Alert::error('Error', 'Discount Price more than Retail price please check our sheet.');
            return response()->json(['success' => 'false']);
        }
        else
        {
            Alert::success('Success', 'Data import has been successfully uploaded.');
            return response()->json(['success' => 'true']);
        }


    }

    public function anyData(Request $request)
    {   
        // echo "<pre>";print_r($request->toArray());exit();
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
        $category_id = $request->get('category_id');
        $is_admin_approve = $request->get('is_admin_approve');
        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $supplier_id = auth()->guard('main_user')->user()->id;
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='id';
        }elseif ($columnIndex==1) {
             $sort='product.product_item_id';
        }elseif ($columnIndex==2) {
             $sort='product.product_name';
        }elseif ($columnIndex==3) {
            $sort='product.product_image';
        }elseif ($columnIndex==4) {
            $sort='categories.title';
        }elseif ($columnIndex==5) {
            $sort='product.retail_price';
        }elseif ($columnIndex==6) {
            $sort='product.discount_price';
        }elseif ($columnIndex==7) {
            $sort='product.discount_price';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        // $totalAr = Categories::where('status','!=','2');
        $totalAr = DB::table('product')->leftjoin('main_users','main_users.id','=','product.supplier_id')->leftjoin('categories','categories.id','=','product.category_id')->select('product.*','main_users.first_name','main_users.last_name','categories.title')->where('product.status','!=','2')->where('product.supplier_id',$supplier_id);
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('categories.title', 'like', '%' . $searchValue . '%')
                 ->orWhere('product.product_item_id', 'like', '%' . $searchValue . '%')
                 ->orWhere(DB::raw("CONCAT(`first_name`, '+', `last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                     ->orWhere('product.product_name', 'like', '%' . $searchValue . '%');
            });
        }

        if (!empty($category_id)) {
            $totalAr->where('product.category_id',$category_id);
        }

        // if (!empty($is_admin_approve)) {
        //     $totalAr->where('product.is_admin_approve',$is_admin_approve);
        // }

        if (!empty($is_admin_approve)) {
            // echo "string";exit();
            if ($is_admin_approve == 3) {
            $totalAr->where('product.is_admin_approve',0);
                
            }else{

            $totalAr->where('product.is_admin_approve','=',$is_admin_approve);
            }
        }

        $totalRecords = $totalAr->get()->count();

         $totalAr = $totalAr->orderBy($sort,$sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();
        // echo "<pre>";print_r($totalAr->toArray());exit();
        $data_arr=[];
        foreach ($totalAr as $key => $data) 
        {
            $productShow =  route('wholesalerproduct.show',['id'=>$data->uniqid]);
            $productEdit =  route('wholesalerproduct.edit',['id'=>$data->uniqid]);

            // $image = "{{ asset('uploads/product/').'/'.$data->product_image }}";
            // $image = $this->no_image;
            if($data->product_image){
                $checkFile = $this->uploadGetPath . '/' . $data->product_image;
                $image = $checkFile;
            }
            $image_show = '<div class="category-img"><a href="' . $image . '" alt="' . $image . '" target="_blank" style="cursor: pointer;"><img src="' . $image . '" alt="' . $image . '" width="80" height="40"></a></div>';

            //  if ($data->status == 1) {
            //     $status = '<i class="fa fa-thumbs-up text-success inline status_active" data-id="'.$data->id.'"></i>';
            // } else {
            //     $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" data-id="'.$data->id.'"></i>';
            // }
            $settings = Setting::find(1);
            if ($data->is_admin_approve == 1) {
                $status = '<button type="button" class="btn btn-success pointer_button">
                <span class="badge  badge-success">Verified</span>
              </button>';
            } elseif ($data->is_admin_approve ==2) {
                 $status = '<button type="button" class="btn btn-danger pointer_button">
                <span class="badge  badge-danger">Rejected</span>
              </button>';
            }else {
                $status = '<button type="button" class="btn btn-warning pointer_button">
                <span class="badge  badge-danger">Not Verified</span>
              </button>';
            }

            if ($data->status == 1) {
                $active_status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="'.$data->id.'"></i>';
            } else {
                $active_status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="'.$data->id.'"></i>';
            }

            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-product" data-id="'.$data->id.'" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="'.$productShow.'" title="Show"> </a>';

            $options .= '<a class="btn btn-sm success paddingset" href="'.$productEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= '<a class="btn btn-sm success paddingset edit-category" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "product_item_id" =>   isset($data->product_item_id) ? $data->product_item_id : '' ,
              "product_name" =>   isset($data->product_name) ? $data->product_name : '' ,
              "product_category" =>   isset($data->title) ? $data->title : '' ,
              "product_image" =>   isset($image_show) ? $image_show : '' ,
              "supplier_name" =>   $data->first_name.' '.$data->last_name ,
              "retail_price" =>   isset($data->retail_price) ? $settings->currency_symbol.' '.$data->retail_price : $settings->currency_symbol.' '.'0',
              "discount_price" =>   isset($data->discount_price) ? $settings->currency_symbol.' '.$data->discount_price : $settings->currency_symbol.' '.'0',
              "status" =>   isset($status) ? $status : '' ,
              "active_status" =>   isset($active_status) ? $active_status : '' ,
              "options" => isset($options) ? $options : '' ,
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
