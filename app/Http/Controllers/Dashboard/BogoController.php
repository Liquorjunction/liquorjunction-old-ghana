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
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\SubCategories;
use App\Models\Product;
use App\Models\Bogo;
use App\Models\Offers;
use Illuminate\Support\Facades\Storage;


class BogoController extends Controller
{

  public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 33, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }

    }

    public function index()
    {
        $start = '';
        $end = '';
        $start = Carbon::now()->format('m-d-Y');
        $end = Carbon::now()->format('m-d-Y');

        return view("dashboard.bogo.list", compact("start", "end"));
    }

        public function create()
    {
        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        $subcategories = SubCategories::where('status', 1)->orderby('title', 'ASC')->get();


        return view("dashboard.bogo.create", compact('categories', 'product','brand','subcategories'));
    }

        public function validateRequest($id = "")
    {
        if ($id != "") {
            $validateData = request()->validate([
                'startdate' => 'required',
                'enddate' => 'required|after_or_equal:startdate',
            ],
            [
                'startdate.required' => 'The start date field is required.',
                'enddate.required' =>'The end date field is required.',
                'enddate.after_or_equal'=>'The end date must be a date after or equal to start date.'
            ]
        );
        } else {
            $validateData = request()->validate(
                [
                    'startdate' => 'required',
                    'enddate' => 'required|after_or_equal:startdate',
                ],
                [
                    'startdate.required' => 'The start date field is required.',
                    'enddate.required' =>'The end date field is required.',
                    'enddate.after_or_equal'=>'The end date must be a date after or equal to start date.'
                ]
                
            );
        }

        return $validateData;
    }

     public function store(Request $request)
    {
        $this->validateRequest();
        
        $brand_id = $request->brand_id;
        $category_id = $request->category_id;
        $subcategory_id = $request->subcategory_id;
        $product_id = $request->product_id;
        $product_type=$request->product_type;

        if ($brand_id && $product_type==1) {
            $category_id = null;
            $product_id = null;
            $subcategory_id=null;
        }elseif ($category_id && $product_type==2) {
            $brand_id = null;
            $product_id = null;
        }elseif ($subcategory_id && $product_type==2) {
            $brand_id = null;
            $product_id = null;
        }elseif ($product_id && $product_type==3) {
            $brand_id = null;
            $category_id = null;
            $subcategory_id=null;
        }


        // Checking if same offer exist
        $conflict = Offers::where('status', 1)
        ->where(function ($query) use ($brand_id, $category_id, $subcategory_id, $product_id) {
            if ($brand_id) {
                $query->orWhere('brand_id', $brand_id);
            }

            if ($subcategory_id) {
                // If subcategory is present, compare with it (ignore category)
                $query->orWhere('subcategory_id', $subcategory_id);
            } elseif ($category_id) {
                // Only compare category if subcategory is NOT present
                $query->orWhere(function ($q) use ($category_id) {
                    $q->where('subcategory_id', null)
                    ->where('category_id', $category_id);
                });
            }

            if ($product_id) {
                $query->orWhere('product_id', $product_id);
            }
        })
        ->exists();


        if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->with('errorMessage', 'A Discount offer with the same product, brand, or category is already active.');
        }


        $newStatus = 1;

        //  update all others to 0
        if ($newStatus == 1) {
            Bogo::where('status', 1)->update(['status' => 0]);
        }

        $bogo = new Bogo;
        $bogo->start_date = isset($request->startdate) ? Carbon::createFromFormat('d-m-Y', $request->startdate)->format('Y-m-d') : '';
        $bogo->end_date = isset($request->enddate) ? Carbon::createFromFormat('d-m-Y', $request->enddate)->format('Y-m-d') : '';
        $bogo->product_type = $product_type;
        $bogo->brand_id = $brand_id;
        $bogo->category_id = $category_id;
        $bogo->subcategory_id = $subcategory_id;
        $bogo->product_id = $product_id;
        $bogo->status = $newStatus;
        $bogo->created_at = date('Y-m-d H:i:s');
        $bogo->updated_at = date('Y-m-d H:i:s');
        $bogo->created_id = Auth::user()->id;
        $bogo->updated_id = Auth::user()->id;
        $bogo->save();
        return redirect()->route('bogo')->with('doneMessage', 'Bogo Offer created successfully.');
    }

       public function show($id)
    {
        $Bogo = Bogo::find($id);

        $setting = Setting::find(1);

        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        $subcategories = SubCategories::where([['category_id', $Bogo->category_id], ['status', 1]])->orderby('title', 'ASC')->get();

        if (!empty($Bogo)) {
            return view("dashboard.bogo.show", compact("Bogo","setting","categories","brand","product","subcategories"));
        } else {
            return redirect()->route('bogo')->with('errorMessage', __('backend.something_wrong'));
        }
    }

        public function edit($id)
    {

        $Bogo = Bogo::find($id);

        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        $subcategories = SubCategories::where([['category_id', $Bogo->category_id], ['status', 1]])->orderby('title', 'ASC')->get();

        if (!empty($Bogo)) {
            return view("dashboard.bogo.edit", compact('Bogo','categories', 'product','brand','subcategories'));
        } else {
            return redirect()->route('bogo')->with('errorMessage', __('backend.something_wrong'));
        }
    }

     public function update(Request $request, $id)
    {
        $Bogo = Bogo::find($id);
        $authId = Auth::user()->id;

        if (!empty($Bogo)) {

            $this->validateRequest($id);

            $brand_id = $request->brand_id;
            $category_id = $request->category_id;
            $subcategory_id = $request->subcategory_id;
            $product_id = $request->product_id;
            $product_type=$request->product_type;

            if ($brand_id && $product_type==1) {
                $category_id = null;
                $product_id = null;
                $subcategory_id=null;
            }elseif ($category_id && $product_type==2) {
                $brand_id = null;
                $product_id = null;
            }elseif ($subcategory_id && $product_type==2) {
                $brand_id = null;
                $product_id = null;
            }elseif ($product_id && $product_type==3) {
                $brand_id = null;
                $category_id = null;
                $subcategory_id=null;
            }

            
            // Checking if same offer exist
            $conflict = Offers::where('status', 1)
            ->where(function ($query) use ($brand_id, $category_id, $subcategory_id, $product_id) {
                if ($brand_id) {
                    $query->orWhere('brand_id', $brand_id);
                }

                if ($subcategory_id) {
                    // If subcategory is present, compare with it (ignore category)
                    $query->orWhere('subcategory_id', $subcategory_id);
                } elseif ($category_id) {
                    // Only compare category if subcategory is NOT present
                    $query->orWhere(function ($q) use ($category_id) {
                        $q->where('subcategory_id', null)
                        ->where('category_id', $category_id);
                    });
                }

                if ($product_id) {
                    $query->orWhere('product_id', $product_id);
                }
            })
            ->exists();


            if ($conflict) {
                return redirect()->back()
                    ->withInput()
                    ->with('errorMessage', 'A Discount offer with the same product, brand, or category is already active.');
            }


            $Bogo->start_date = isset($request->startdate) ? Carbon::createFromFormat('d-m-Y', $request->startdate)->format('Y-m-d') : '';
            $Bogo->end_date = isset($request->enddate) ? Carbon::createFromFormat('d-m-Y', $request->enddate)->format('Y-m-d')  : '';
            $Bogo->product_type = $product_type;
            $Bogo->brand_id = $brand_id;
            $Bogo->category_id = $category_id;
            $Bogo->subcategory_id = $subcategory_id;
            $Bogo->product_id = $product_id;
            $Bogo->updated_at = date('Y-m-d H:i:s');
            $Bogo->updated_id = $authId;
            $Bogo->save();


            return redirect()->route('bogo')->with('doneMessage', 'Bogo Offer updated successfully.');
        } else {
            return redirect()->route('bogo')->with('errorMessage', __('backend.something_wrong'));
        }
    }

    public function updateAll(Request $request)
    {
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $id_multiple = explode(",", $request->ids);

                $status = $request->status;

                Bogo::wherein('id', $ids)->update(['status' => $status]);
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

    //  public function status_active(Request $request)
    // {
    //     $bogo_id = $request->id;
    //     Bogo::where('id', $bogo_id)->update(['status' => 0]);
    //     Alert::success('Success', "Bogo offer deactivated successfully");
    //     return response()->json(['success' => 'true']);
    // }

    // public function status_inactive(Request $request)
    // {
    //     $bogo_id = $request->id;
    //     Bogo::where('id', $bogo_id)->update(['status' => 1]);
    //     Alert::success('Success', "Bogo offer activated successfully");
    //     return response()->json(['success' => 'true']);
    // }

    public function status_active(Request $request)
    {
        $bogo_id = $request->id;

        if (!$bogo_id) {
            return response()->json(['success' => false, 'msg' => 'Invalid Bogo ID.']);
        }

        $bogo = Bogo::find($bogo_id);

        if (!$bogo) {
            return response()->json(['success' => false, 'msg' => 'Bogo offer not found.']);
        }

        if ($bogo->status != 0) {
            $bogo->status = 0;
            $bogo->save();

            Alert::success('Success', "Bogo offer deactivated successfully");
            return response()->json(['success' => 'true']);
        }

        return response()->json(['success' => false, 'msg' => 'Bogo offer is already deactivated.']);
    }

    public function status_inactive(Request $request)
    {
        $bogo_id = $request->id;

        if (!$bogo_id) {
            return response()->json(['success' => false, 'msg' => 'Invalid Bogo ID.']);
        }

        $bogo = Bogo::find($bogo_id);

        if (!$bogo) {
            return response()->json(['success' => false, 'msg' => 'Bogo offer not found.']);
        }

        if ($bogo->status != 1) {
            // ✅ Deactivate all others
            Bogo::where('status', 1)->where('id', '!=', $bogo_id)->update(['status' => 0]);

            // ✅ Activate this one
            $bogo->status = 1;
            $bogo->save();

            Alert::success('Success', "Bogo offer activated successfully");
            return response()->json(['success' => 'true']);
        }

        return response()->json(['success' => false, 'msg' => 'Bogo offer is already active.']);
    }



      public function anyData(Request $request)
    {

        Bogo::whereDate('end_date', '<', Carbon::today())
            ->where('status', 1)
            ->update(['status' => 0]);

        $draw = $request->get('draw');

        $start = $request->get("start");

        $rowperpage = $request->get("length");
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');

        $columnIndex = $columnIndex_arr[0]['column']; 
        $columnName = $columnName_arr[$columnIndex]['data']; 
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; 
        }
        $searchValue = $search_arr['value'];
        if ($columnIndex == 0) {
            $sort = 'id';
        }elseif ($columnIndex == 1) {
            $sort = 'product_type';
        }elseif ($columnIndex == 2) {
            $sort = 'start_date';
        }elseif ($columnIndex == 3) {
            $sort = 'end_date';
        }else {
            $sort = 'id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        if (!empty($request->startdate)) {

            $totalAr = Bogo::with('useddetail')->where('status', '!=', 2);
            dd($totalAr->get()->toArray());

            $frm_date = Carbon::createFromFormat('m-d-Y', $request->startdate)->format('Y-m-d');

            $to_date = Carbon::createFromFormat('m-d-Y', $request->enddate)->format('Y-m-d');

            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('product_type', 'like', '%' . $searchValue . '%')
                        ->orWhere('start_date', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%')
                        ->orWhere('end_date', 'like', '%' .  date('Y-m-d', strtotime($searchValue)) . '%');
                });
            }


            $totalRecords = $totalAr->groupby('bogo.id')->get()->count();

            $totalAr = $totalAr->orderBy($sort, $sortBy)
                ->skip($start)
                ->take($rowperpage)
                ->groupby('id')
                ->get();
        } else {

            $totalAr = Bogo::where('status', '!=', 2);

            if ($searchValue != "") {

                $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                    $query->orWhere('product_type', 'like', '%' . $searchValue . '%')
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

        $data_arr = [];
        $today = Carbon::now()->toDateString();

        foreach ($totalAr as $key => $data) {
            $product = isset($data->product_type) ? $data->product_type : '';
            $startdate = isset($data->start_date) ? Helper::formatDatetime($data->start_date) : '';
            $enddate = isset($data->end_date) ? Helper::formatDatetime($data->end_date) : '';

            if ($data->status == 1) {
                $status = '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="' . $data->id . '"></i>';

            } else {

                $status = '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i>';

            }

            $BogoShow =  route('bogo.show', ['id' => $data->id]);
            $BogoEdit =  route('bogo.edit', ['id' => $data->id]);


            $options  = '<div class="action-btns">';
            $options .= '<a  class="btn btn-sm show-eyes list" href="' . $BogoShow . '" title="show"> </a>';
            
            $startdateRaw = isset($data->start_date) ? $data->start_date : '';
            $enddateRaw = isset($data->end_date) ? $data->end_date : '';
            if ($startdateRaw <= $today && $enddateRaw >= $today) {
                $options .= '<a  class="btn btn-sm success paddingset" href="' . $BogoEdit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
                $options .=  '<button  class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            }
            $options .= '</div>';
            $data_arr[] = array(
                "checkbox" => '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>',
                
                // "product" =>   $this->mapProductType($product),
                "product" =>   $this->mapProductType($data->category_id,$data->subcategory_id,$data->brand_id,$data->product_id),
                "appliedon" =>  $this->productDetail($data->category_id,$data->subcategory_id,$data->brand_id,$data->product_id),
                "startdate" => $startdate,
                "enddate" => $enddate,
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

    private function mapProductType($categoryId,$subcategoryId,$brandId,$productId)
    {

        if($categoryId && $subcategoryId )
        {
            return ucfirst('Subcategory');
        }
        else if($categoryId)
        {
            return ucfirst('Category');
        }
        else if($brandId)
        {
            return ucfirst('Brand');
        }
        else if($productId)
        {
            return ucfirst('Product');
        }
    }

     private function productDetail($categoryId,$subcategoryId,$brandId,$productId)
    {

        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        $subcategories = SubCategories::where('status', 1)->orderby('title', 'ASC')->get();

        if($categoryId && $subcategoryId)
        {
            return ucfirst(optional($subcategories->firstWhere('id', $subcategoryId))->title) ?? 'N/A';
        }
        else if($categoryId)
        {
            return ucfirst(optional($categories->firstWhere('id', $categoryId))->title) ?? 'N/A';
        }
        else if($brandId)
        {
            return ucfirst(optional($brand->firstWhere('id', $brandId))->title) ?? 'N/A';
        }
        else if($productId)
        {
            return ucfirst(optional($product->firstWhere('id', $productId))->product_name) ?? 'N/A';
        }
        else
        {
            return 'N/A';
        }
    }



}