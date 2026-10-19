<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Product;
use App\Models\Setting;
use App\Models\EmailTemplate;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Mail;
use Session;
use Yajra\Datatables\Datatables;

class RequestProductController extends Controller
{
    //
    private $uploadPath = "/uploads/product";
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
        return view("dashboard.request_product.list");
    }

    public function requestproductUpdateAll(Request $request)
    {
       
        if($request->ajax())
        {
            if ($request->ids != "") {
                $ids= explode(",", $request->ids);
                $status = $request->status;
               
                Product::wherein('id', $ids)->update(['is_admin_approve' => $status]);
                // echo "<pre>";print_r($ids);exit;
                foreach ($ids as $data) {
                    // echo "<pre>";print_r($data);
                    $product_data = DB::table('product')->where('id',$data)->first();
                    $user_data = DB::table('main_users')->where('id',$product_data->supplier_id)->first();
                    // echo "<pre>";print_r($user_data);exit();
                    $name = $user_data->first_name.' '.$user_data->last_name;
                    if ($status==1) {
                        
                    $ismail = $this->attachment_approve_email($user_data->email,$name);
                    }else{
                    $ismail = $this->attachment_reject_email($user_data->email,$name);

                    }
                }
                // exit();
                if($status == 2){
                    return response()->json(['success' => true,'msg'=>'Record(s) reject successfully']);
                  }else if($status == 0){
                   return response()->json(['success' => true,'msg'=>'Record(s) not verify successfully']);
                  }else{
                   return response()->json(['success' => true,'msg'=>'Record(s) verify successfully']);
                  }
            }
            return response()->json(['success' => false,'msg'=>'Something went wrong!!']);
        }
        abort(404);
        
    }

    public function attachment_approve_email($email,$name) {

       
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('5');
       
       // $from_email = $setting['from_email'];
        $data = array('email' => $email,'name' => $name, 'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);
       
        Mail::send('dashboard.request_product', $data, function ($message) use ($data) {

         $message->to($data['email'], $data['title'])->subject($data['subject']);
        //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

        $message->from($data['from_email'], $data['support_name']);
        });

    }

    public function attachment_reject_email($email,$name) {

       
        $setting = Setting::find(1);
        $from_email = $setting['mail_no_replay'];
        $emailtemp = Emailtemplate::find('6');
       
       // $from_email = $setting['from_email'];
        $data = array('email' => $email,'name' => $name, 'from_email' => $from_email,'support_name' => $setting['support_name'],'title' => $emailtemp['title'],'subject' => $emailtemp['subject']);
       
        Mail::send('dashboard.request_reject_product', $data, function ($message) use ($data) {

        $message->to($data['email'], $data['title'])->subject($data['subject']);
        //$message->to('manoj.vrinsofts@gmail.com', 'Upskild')->subject('Password has been reset succesfully!');

        $message->from($data['from_email'], $data['support_name']);
        });

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
        $supplier_id = $request->get('supplier_id');
        $is_admin_approve = $request->get('is_admin_approve');
        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder='';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir']!="") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex==0) {
            $sort='id';
        }elseif ($columnIndex==1) {
             $sort='product.product_name';
        }elseif ($columnIndex==2) {
            $sort='product.product_image';
        }elseif ($columnIndex==3) {
            $sort='product.category_id';
        }elseif ($columnIndex==4) {
            $sort='main_users.first_name';
        }elseif ($columnIndex==5) {
            $sort='product.retail_price';
        }elseif ($columnIndex==6) {
            $sort='product.discount_price';
        }else{
            $sort='id';
        }

        $sortBy='DESC';
        if ($columnSortOrder!="") {
            $sortBy=$columnSortOrder;
        }

        // $totalAr = Categories::where('status','!=','2');
        $totalAr = DB::table('product')->leftjoin('main_users','main_users.id','=','product.supplier_id')->leftjoin('categories','categories.id','=','product.category_id')->select('product.*','main_users.first_name','main_users.last_name','categories.title')->where('product.status','!=','2')->where('is_admin_approve',0);
               
        if ($searchValue!="") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                 $query->orWhere('categories.title', 'like', '%' . $searchValue . '%')
                 ->orWhere(DB::raw("CONCAT(`first_name`, '+', `last_name`)"), 'like', '%' . urlencode($searchValue) . '%')
                     ->orWhere('product.product_name', 'like', '%' . $searchValue . '%');
            });
        }

        if (!empty($supplier_id)) {
            $totalAr->where('product.supplier_id',$supplier_id);
        }

        if (!empty($is_admin_approve)) {
            $totalAr->where('product.is_admin_approve',$is_admin_approve);
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
            $categoryShow =  route('category.show',['id'=>$data->id]);
            $categpryEdit =  route('category.edit',['id'=>$data->uniqid]);

            // $image = "{{ asset('uploads/product/').'/'.$data->product_image }}";
            // $image = $this->no_image;
            if($data->product_image){
                $checkFile = $this->uploadPath . '/' . $data->product_image;
                $image = $checkFile;
            }
            $image_show = '<div class="category-img"><a href="' . $image . '" alt="' . $image . '" target="_blank" style="cursor: pointer;"><img src="' . $image . '" alt="' . $image . '" width="80" height="40"></a></div>';

             if ($data->is_admin_approve == 1) {
                $status = '<button type="button" class="btn btn-success">
                <span class="badge  badge-success">Verify</span>
              </button>';
            } elseif ($data->is_admin_approve ==2) {
                 $status = '<button type="button" class="btn btn-danger">
                <span class="badge  badge-danger">Reject</span>
              </button>';
            }else {
                $status = '<button type="button" class="btn btn-danger">
                <span class="badge  badge-danger">Not Verify</span>
              </button>';
            }
            // $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" title="Show"> </a>';
            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset show-product" data-id="'.$data->id.'" title="Show"> </a>';

            // $options .= '<a class="btn btn-sm success paddingset" href="'.$categpryEdit.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= '<a class="btn btn-sm success paddingset edit-category" data-id="'.$data->id.'" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .=  '<button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>';   

            $data_arr[] =array(
              "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="'.$data->id.'" class="has-value" onchange="checkChange();" data-id="'.$data->id.'"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="'.$data->id.'"> </label>',
              "id" =>   isset($data->id) ? $data->id : '' ,
              "product_name" =>   isset($data->product_name) ? $data->product_name : '' ,
              "product_category" =>   isset($data->title) ? $data->title : '' ,
              "product_image" =>   isset($image_show) ? $image_show : '' ,
              "supplier_name" =>   $data->first_name.' '.$data->last_name ,
              "retail_price" =>   isset($data->retail_price) ? $data->retail_price : '' ,
              "discount_price" =>   isset($data->discount_price) ? $data->discount_price : '' ,
              "status" =>   isset($status) ? $status : '' ,
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
