<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Promocode;

use Illuminate\Validation\Rule;
use Auth;
use Illuminate\Config;
use Illuminate\Support\Facades\Hash;
use App\Models\Setting;
use App\Models\EmailTemplate;
use App\Models\History;
use App\Models\Questionhistory;
use Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helper;
use Alert;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Product;
use DB;



class PromocodeController extends Controller
{
    private $uploadPath = "uploads/school-profile/";

    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type,13,'read');
        if($check_view_permission==false){
            abort(404);
        } 
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');
        return view("dashboard.promocode.list", compact("start", "end"));
    }


    public function create()
    {
        // $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        // $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        // $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        // $subcategories = SubCategories::where('status', 1)->orderby('title', 'ASC')->get();


        // return view("dashboard.promocode.create", compact('categories', 'product','brand','subcategories'));
        return view("dashboard.promocode.create");
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $Promocode = Promocode::find($id);

        // $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        // $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        // $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        // $subcategories = SubCategories::where([['category_id', $Promocode->category_id], ['status', 1]])->orderby('title', 'ASC')->get();

        if (!empty($Promocode)) {
            // return view("dashboard.promocode.edit", compact('Promocode','categories', 'product','brand','subcategories'));
            return view("dashboard.promocode.edit", compact('Promocode'));
        } else {
            return redirect()->route('promocode')->with('errorMessage', __('backend.something_wrong'));
        }
    }

    public function show($id)
    {
        $Promocode = Promocode::find($id);

        $setting = Setting::find(1);

        if (!empty($Promocode)) {
            return view("dashboard.promocode.show", compact("Promocode","setting"));
        } else {
            return redirect()->route('promocode')->with('errorMessage', __('backend.something_wrong'));
        }
    }






    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    public function store(Request $request)
    {
        $this->validateRequest();
        
        // $brand_id = $request->brand_id;
        // $category_id = $request->category_id;
        // $subcategory_id = $request->subcategory_id;
        // $product_id = $request->product_id;
        // $product_type=$request->product_type;

        // if ($brand_id && $product_type==1) {
        //     $category_id = null;
        //     $product_id = null;
        //     $subcategory_id=null;
        // }elseif ($category_id && $product_type==2) {
        //     $brand_id = null;
        //     $product_id = null;
        // }elseif ($subcategory_id && $product_type==2) {
        //     $brand_id = null;
        //     $product_id = null;
        // }elseif ($product_id && $product_type==3) {
        //     $brand_id = null;
        //     $category_id = null;
        //     $subcategory_id=null;
        // }



        $Promocode = new Promocode;
        $Promocode->promo_name = isset($request->promo_code) ? $request->promo_code : '';
        $Promocode->start_date = isset($request->startdate) ? Carbon::createFromFormat('d-m-Y', $request->startdate)->format('Y-m-d') : '';
        $Promocode->end_date = isset($request->enddate) ? Carbon::createFromFormat('d-m-Y', $request->enddate)->format('Y-m-d') : '';
        $Promocode->discount_percentage = isset($request->discount_percentage) ? $request->discount_percentage : '';
        $Promocode->minimum_amount = isset($request->min_amount) ? $request->min_amount : '';
        $Promocode->total_usage = isset($request->total_usage) ? $request->total_usage : '';
        // $Promocode->product_type = $product_type;
        // $Promocode->brand_id = $brand_id;
        // $Promocode->category_id = $category_id;
        // $Promocode->subcategory_id = $subcategory_id;
        // $Promocode->product_id = $product_id;
        // $Promocode->allowed_time = isset($request->allowed_time) ? $request->allowed_time : '';
        $Promocode->status = 1;
        $Promocode->created_at = date('Y-m-d H:i:s');
        $Promocode->updated_at = date('Y-m-d H:i:s');
        $Promocode->created_id = Auth::user()->id;
        $Promocode->updated_id = Auth::user()->id;
        $Promocode->save();
        return redirect()->route('promocode')->with('doneMessage', 'Promocode created successfully.');
    }

    public function update(Request $request, $id)
    {
        $Promocode = Promocode::find($id);
        $authId = Auth::user()->id;

        if (!empty($Promocode)) {

            $this->validateRequest($id);

            // $brand_id = $request->brand_id;
            // $category_id = $request->category_id;
            // $subcategory_id = $request->subcategory_id;
            // $product_id = $request->product_id;
            // $product_type=$request->product_type;

            // if ($brand_id && $product_type==1) {
            //     $category_id = null;
            //     $product_id = null;
            //     $subcategory_id=null;
            // }elseif ($category_id && $product_type==2) {
            //     $brand_id = null;
            //     $product_id = null;
            // }elseif ($subcategory_id && $product_type==2) {
            //     $brand_id = null;
            //     $product_id = null;
            // }elseif ($product_id && $product_type==3) {
            //     $brand_id = null;
            //     $category_id = null;
            //     $subcategory_id=null;
            // }

            $Promocode->promo_name = isset($request->promo_code) ? $request->promo_code : '';
            $Promocode->start_date = isset($request->startdate) ? Carbon::createFromFormat('d-m-Y', $request->startdate)->format('Y-m-d') : '';
            $Promocode->end_date = isset($request->enddate) ? Carbon::createFromFormat('d-m-Y', $request->enddate)->format('Y-m-d')  : '';
            $Promocode->discount_percentage = isset($request->discount_percentage) ? $request->discount_percentage : '';
            $Promocode->minimum_amount = isset($request->min_amount) ? $request->min_amount : '';
            $Promocode->total_usage = isset($request->total_usage) ? $request->total_usage : '';
            // $Promocode->product_type = $product_type;
            // $Promocode->brand_id = $brand_id;
            // $Promocode->category_id = $category_id;
            // $Promocode->subcategory_id = $subcategory_id;
            // $Promocode->product_id = $product_id;
            // $Promocode->allowed_time = isset($request->allowed_time) ? $request->allowed_time : '';
            $Promocode->updated_at = date('Y-m-d H:i:s');
            $Promocode->updated_id = $authId;
            $Promocode->save();


            return redirect()->route('promocode')->with('doneMessage', 'Promocode updated successfully.');
        } else {
            return redirect()->route('promocode')->with('errorMessage', __('backend.something_wrong'));
        }
    }


    public function attachment_email_update_password($email, $password, $name, $logo)
    {


        $setting = Setting::find(1);

        $from_email = $setting['from_email'];
        $data = array('email' => $email, 'password' => $password, 'name' => $name, 'id' => '3', 'logo' => $logo, 'from_email' => $from_email);

        Mail::send('password_update', $data, function ($message) use ($data) {

            $message->to($data['email'], 'Promoteprep')->subject('Password has been update succesfully!');

            $message->from($data['from_email'], 'Promoteprep');
        });
    }


    private function storeImage($Promocode)
    {

        if (request()->has('image')) {
            $avatarName = time() . '.' . request()->image->getClientOriginalExtension();
            $file = request()->file('image');
            $name = $avatarName;
            $filePath = 'promocode/' . $name;
            Storage::disk('public')->put($filePath, file_get_contents($file));

            $Promocode->update([
                'image' => $avatarName,
            ]);
        }
    }


    public function validateRequest($id = "")
    {
        if ($id != "") {
            $validateData = request()->validate([
                'promo_code' => 'required|max:15|unique:tbl_promocode,promo_name,' . $id,
                'startdate' => 'required',
                'enddate' => 'required|after_or_equal:startdate',
                'discount_percentage' => 'required',
                'total_usage' => 'required|numeric|min:1',
                'min_amount' => 'required|numeric|min:0',
                // 'allowed_time' => 'required',
            ],
            [
                'startdate.required' => 'The start date field is required.',
                'enddate.required' =>'The end date field is required.',
                'total_usage.required' =>'The Usage Limit field is required.',
                'min_amount.required' =>'The Minimum Amount field is required.',
                'enddate.after_or_equal'=>'The end date must be a date after or equal to start date.'
            ]
        );
        } else {
            $validateData = request()->validate(
                [
                    'promo_code' => 'required|unique:tbl_promocode,promo_name|max:15',
                    'startdate' => 'required',
                    'enddate' => 'required|after_or_equal:startdate',
                    'discount_percentage' => 'required',
                    'total_usage' => 'required|numeric|min:1',
                    'min_amount' => 'required|numeric|min:0',
                     // 'allowed_time' => 'required',
                ],
                [
                    'startdate.required' => 'The start date field is required.',
                    'enddate.required' =>'The end date field is required.',
                    'total_usage.required' =>'The Usage Limit field is required.',
                    'min_amount.required' =>'The Minimum Amount field is required.',
                    'enddate.after_or_equal'=>'The end date must be a date after or equal to start date.'
                ]
                
            );
        }

        return $validateData;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update all selected resources in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param buttonNames , array $ids[]
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $id_multiple = explode(",", $request->ids);

                $status = $request->status;

                Promocode::wherein('id', $ids)->update(['status' => $status]);
                if ($status == 2) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) deleted successfully']);
                } else if ($status == 0) {
                    return response()->json(['success' => true, 'msg' => 'Record(s) deactive successfully']);
                } else {
                    return response()->json(['success' => true, 'msg' => 'Record(s) active successfully']);
                }
            }
            return response()->json(['success' => false, 'msg' => 'Something went wrong!!']);
        }
        abort(404);
    }
    public function Promocodeexport(Request $request)
    {

        if (!empty($request->startdate && $request->enddate)) {

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');
            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');


            $totalAr = \DB::table('Promocodes')
                ->where('Promocodes.status', '!=', 2);
            $totalAr = $totalAr->whereDate('Promocodes.createddate', '>=', $frm_date);
            $totalAr = $totalAr->whereDate('Promocodes.createddate', '<=', $to_date);
            $totalAr = $totalAr->orderBy('Promocodes.id', 'DESC')->groupby('Promocodes.id')->get();
        } else {
            $totalAr = \DB::table('Promocodes')
                ->where('Promocodes.status', '!=', 2);
            $totalAr = $totalAr->orderBy('Promocodes.id', 'DESC')->groupby('Promocodes.id')->get();
        }



        $filename = 'Promocode_report' . date('d/m/Y') . '.csv';

        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '";');

        $file = fopen('php://output', 'w');

        fputcsv($file, array('No', 'Full Name', 'Email', 'Mobile Number', 'Created Date', 'Status', 'Verify Status'));
        $i = 1;
        foreach ($totalAr as $key => $data) {
            $no = $i;
            $fullname = isset($data->fullname) ? $data->fullname : '';
            $mobile_number = isset($data->mobilenumber) ? $data->mobilenumber : '';
            $email = isset($data->email) ? $data->email : '';

            if ($data->status == 1) {
                $status = 'active';
            } else {

                $status = 'deactive';
            }

            if ($data->is_varify == 1) {
                $status2 = 'varify';
            } else {

                $status2 = 'not varify';
            }


            $createddate = isset($data->createddate) ? $data->createddate : '';
            $date = \Helper::formatDate($createddate) . ' ' . date('H:i:s', strtotime($createddate));
            fputcsv($file, array($i, $fullname, $email, $mobile_number, $date, $status, $status2));

            $i++;
        }
    }

    public function status_active(Request $request)
    {
        $promocode_id = $request->id;
        Promocode::where('id', $promocode_id)->update(['status' => 0]);
        Alert::success('Success', __('backend.Promocode_deactive_sucessfully'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $promocode_id = $request->id;
        Promocode::where('id', $promocode_id)->update(['status' => 1]);
        Alert::success('Success', __('backend.Promocode_active_sucessfully'));
        return response()->json(['success' => 'true']);
    }


    public function anyData(Request $request)
    {

        Promocode::whereDate('end_date', '<', Carbon::today())
            ->where('status', 1)
            ->update(['status' => 0]);

        $draw = $request->get('draw');

        $start = $request->get("start");

        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        //echo "<pre>";print_r($order_arr);exit;
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'id';
        }elseif ($columnIndex == 1) {
            $sort = 'promo_name';
        }
        // elseif ($columnIndex == 2) {
        //     $sort = 'product_type';
        // }
        elseif ($columnIndex == 2) {
            $sort = 'discount_percentage';
        }elseif ($columnIndex == 3) {
            $sort = 'minimum_amount';
        }elseif ($columnIndex == 4) {
            $sort = 'total_usage';
        }elseif ($columnIndex == 5) {
            $sort = 'start_date';
        }elseif ($columnIndex == 6) {
            $sort = 'end_date';
        }else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        /*$data = MainPromocode::where('Promocode_type',1)->where('status','!=',2);*/
        if (!empty($request->startdate)) {


            $totalAr = Promocode::with('useddetail')->where('status', '!=', 2);
            dd($totalAr->get()->toArray());
            // $frm_date = date('Y-m-d',strtotime($request->startdate));
            // $to_date = date('Y-m-d', strtotime($request->enddate . ' +1 day'));

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');

            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');

            // $totalAr = $totalAr->whereDate('Promocodes.createddate', '>=', $frm_date);
            // $totalAr = $totalAr->whereDate('Promocodes.createddate', '<=', $to_date);


            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('promo_name', 'like', '%' . $searchValue . '%')
                        // ->orWhere('product_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('discount_percentage', 'like', '%' . $searchValue . '%')
                        ->orWhere('minimum_amount', 'like', '%' . $searchValue . '%')
                        ->orWhere('total_usage', 'like', '%' . $searchValue . '%')
                        // ->orWhere('allowed_time', 'like', '%' . $searchValue . '%')
                        ->orWhere('start_date', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                        ->orWhere('end_date', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                });
            }


            $totalRecords = $totalAr->groupby('Promocodes.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('id')
                ->get();
        } else {

            // $totalAr = Promocode::where('status', '!=', 2);
            $totalAr = Promocode::where('status', '!=', 2);



            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('promo_name', 'like', '%' . $searchValue . '%')
                        // ->orWhere('product_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('discount_percentage', 'like', '%' . $searchValue . '%')
                        ->orWhere('minimum_amount', 'like', '%' . $searchValue . '%')
                        ->orWhere('total_usage', 'like', '%' . $searchValue . '%')
                        // ->orWhere('allowed_time', 'like', '%' . $searchValue . '%')
                        ->orWhere('start_date', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                        ->orWhere('end_date', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                });
            }

            $totalRecords = $totalAr->groupby('id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('id')
                ->get();
        }

        $usageCounts = DB::table('coupon_user')
                    ->select('coupon_id', DB::raw('COUNT(*) as total_used'))
                    ->groupBy('coupon_id')
                    ->pluck('total_used', 'coupon_id');

        $data_arr = [];
        $today = Carbon::now()->toDateString();

        foreach ($totalAr as $key => $data) {
            $promocode = isset($data->promo_name) ? $data->promo_name : '';
            // $product = isset($data->product_type) ? $data->product_type : '';
            $amount = isset($data->minimum_amount) ? $data->minimum_amount : '';
            $usage = isset($data->total_usage) ? $data->total_usage : '';
            $startdate = isset($data->start_date) ? Helper::formatDatetime($data->start_date) : '';
            $enddate = isset($data->end_date) ? Helper::formatDatetime($data->end_date) : '';
            $discount = isset($data->discount_percentage) ? $data->discount_percentage . '%' : '';
            // $allowed_time = isset($data->allowed_time) ? $data->allowed_time : '';
            $used = isset($data->useddetail) ? count($data->useddetail) : '';
            // $createddate = isset($data->created_at) ? Helper::formatDatetime($data->created_at) : '';
            $couponCount = $usageCounts[$data->id] ?? 0;

            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="' . $data->id . '"></i>';

            } else {

                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i>';

            }

            $PromocodesShow =  route('promocode.show', ['id' => $data->id]);
            $PromocodesEdit =  route('promocode.edit', ['id' => $data->id]);


            $options  = '<div class="action-btns">';
            $options .= '<a  class="btn btn-sm show-eyes list" href="' . $PromocodesShow . '" title="show"> </a>';

            $startdateRaw = isset($data->start_date) ? $data->start_date : '';
            $enddateRaw = isset($data->end_date) ? $data->end_date : '';
            if ($startdateRaw <= $today && $enddateRaw >= $today) {
                $options .=  '<button  class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            }
            $options .= '<a  class="btn btn-sm success paddingset" href="' . $PromocodesEdit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            $options .= '</div>';
            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                "promocode" =>   $promocode,
                // "product" =>   $this->mapProductType($product),
                "count" => $couponCount,
                "startdate" => $startdate,
                "enddate" => $enddate,
                "discount" => $discount,
                // "allowed_time" => $allowed_time,
                "used" => $used,
                "usage" => $usage,
                "amount" => $amount.' GH₵',
                // "createddate" => $createddate,
                "status" => $status,
                "options" => $options
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

    //  private function mapProductType($type)
    // {
    //     switch ($type) {
    //         case 1: return 'Brand';
    //         case 2: return 'Category';
    //         case 3: return 'Product';
    //         default: return 'N/A';
    //     }
    // }



    public function attachment_email_register($email, $name, $otp, $logo)
    {


        $setting = Setting::find(1);
        $getEmail = EmailTemplate::where('id', 1)->first();
        $from_email = $setting['from_email'];
        $data = array('email' => $email, 'name' => $name, 'otp' => $otp, 'id' => '1', 'logo' => $logo, 'from_email' => $from_email);

        Mail::send('Promocode_register', $data, function ($message) use ($data) {

            $message->to($data['email'], 'Promoteprep')->subject('Promocode registration successfully');

            $message->from($data['from_email'], 'Promoteprep');
        });
    }

    public function generateToken()
    {
        return md5(rand(1, 10) . microtime());
    }
}
