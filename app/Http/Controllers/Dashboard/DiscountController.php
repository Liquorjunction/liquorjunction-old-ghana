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
use App\Models\Discount;

class DiscountController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 32, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }

    }

    public function index()
    {
        return view("dashboard.discount.list");
    }

     public function discountstore(Request $request)
    {

        // if ($request->min_amount >= $request->max_amount) {
        //     return response()->json(['max_amount' => ['Maximum amount must be greater than minimum amount.']], 422);
        // }

        if (empty($request->discount_id)) {
            $validator = \Validator::make(
                $request->all(),
                [
                    'discount_type' => 'required|string',
                    'dis_amount' => 'nullable|numeric|min:0',  
                    'dis_percentage' => 'nullable|numeric|min:0', 
                    'min_amount' => 'required|numeric|min:0',
                    // 'max_amount' => 'required|numeric',
                    'upto_amount' => 'nullable|numeric|min:0',
                    'expiry_date' => 'required|date|after:today',
                ]
            );

            $validator->after(function ($validator) use ($request) {
                if (empty($request->dis_amount) && empty($request->dis_percentage)) {
                    $validator->errors()->add('dis_amount', 'Either Discount Amount or Discount Percentage is required.');
                }
            });
    

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $discount_per=0;
            $discount_amt=0;

            if($request->dis_amount)
            {
                $discount_per=0;
                $discount_amt=$request->dis_amount;
            }
            else if($request->dis_percentage)
            {
                $discount_per=$request->dis_percentage;
                $discount_amt=0;
            }

            $newStatus = 1;

            //  update all others to 0
            if ($newStatus == 1) {
                Discount::where('status', 1)->update(['status' => 0]);
            }

            $discount = new Discount();
            $discount->discount_type = $request->input('discount_type');
            $discount->discount_amount = $discount_amt;
            $discount->discount_percentage = $discount_per;
            $discount->min_amount = $request->input('min_amount');
            // $discount->max_amount = $request->input('max_amount');
            $discount->upto_amount = $request->input('upto_amount');
            $discount->expiry_date = $request->input('expiry_date');
            $discount->status = $newStatus;
            $discount->save();

            Alert::success('Success', 'New Discount created successfully');
            return response()->json(['success' => 'true']);
        } else {

            $id = $request->discount_id;
            $validator = \Validator::make(
                $request->all(),
                [
                    'discount_id' => 'required|exists:discount,id',
                    'discount_types' => 'required|string',
                    'dis_amount' => 'nullable|numeric|min:0',  
                    'dis_percentage' => 'nullable|numeric|min:0',  
                    'min_amount' => 'required|numeric|min:0',
                    // 'max_amount' => 'required|numeric',
                    'upto_amount' => 'nullable|numeric|min:0',
                    'expiry_date' => 'required|date|after:today',
                ]
            );

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $validator->after(function ($validator) use ($request) {
                if (empty($request->dis_amount) && empty($request->dis_percentage)) {
                    $validator->errors()->add('dis_amount', 'Either Discount Amount or Discount Percentage is required.');
                }
            });
    
            $discount = Discount::find($request->input('discount_id'));

            if (!$discount) {
                return response()->json(['error' => 'Discount not found.'], 404);
            }

            $discount_per=0;
            $discount_amt=0;
            $upto_amount=0;

            if($request->discount_types=="flat")
            {
                $discount_per=0;
                $discount_amt=$request->dis_amount;
                $upto_amount=0;
            }
            else if($request->discount_types=="percentage")
            {
                $discount_per=$request->dis_percentage;
                $discount_amt=0;
                $upto_amount= $request->input('upto_amount');
            }

            $discount->discount_type = $request->input('discount_types');
            $discount->discount_amount = $discount_amt;
            $discount->discount_percentage = $discount_per;
            $discount->min_amount = $request->input('min_amount');
            // $discount->max_amount = $request->input('max_amount');
            $discount->upto_amount = $upto_amount;
            $discount->expiry_date = $request->input('expiry_date');
            $discount->save();

            Alert::success('Success','Discount updated successfully');
            return response()->json(['success' => 'true']);
        }
    }

    public function destroy($id)
    {
        $discount = Discount::find($id);
        $discount->status = 2;
        $discount->save();

        return redirect()->route('discount')
            ->with('doneMessage', 'Record deleted successfully');
    }

    public function discountedit(Request $request)
    {
        if ($request->ajax()) {
            $discount_id = $request->id;

            if ($discount_id) {
                $discountData = Discount::where('id', $discount_id)->where('status', '!=', 2)->first();

                if (!$discountData) {
                    return response()->json(['success' => false, 'msg' => 'Discount not found or inactive.']);
                }

                $html = view('dashboard.discount.edit', ['discountData' => $discountData])->render();
               
                return response()->json(['success' => true, 'html' => $html]);
            }

            return response()->json(['success' => false, 'msg' => 'Invalid Discount ID.']);
        }
    }

    public function discountUpdateAll(Request $request)
    {
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;

                Discount::wherein('id', $ids)->update(['status' => $status]);

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
        $discount_id = $request->id;
        $settings = Setting::find(1);

        $discountData = Discount::where('id', $discount_id)->where('status', '!=', 2)->first();

        if ($discountData) {
            $html = view('dashboard.discount.show', ['discountData' => $discountData,'settings' => $settings])->render();
            return response()->json(['success' => true, 'html' => $html]);
        }
        
        return response()->json(['success' => false, 'msg' => 'Discount not found or inactive.']);
    }

     public function status_active(Request $request)
    {
        $discount_id = $request->id;

        if (!$discount_id) {
            return response()->json(['success' => false, 'msg' => 'Invalid Discount ID.']);
        }

        $discount = Discount::where('id', $discount_id)->first();

        if (!$discount) {
            return response()->json(['success' => false, 'msg' => 'Discount not found.']);
        }

        if ($discount->status != 0) {
            $discount->status = 0;  
            $discount->save();
    
            Alert::success('Success', 'Discount deactivated successfully');
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'msg' => 'Discount is already deactivated.']);
    }


    public function status_inactive(Request $request)
    {
        $discount_id = $request->id;

        if (!$discount_id) {
            return response()->json(['success' => false, 'msg' => 'Invalid Discount ID.']);
        }

        $discount = Discount::where('id', $discount_id)->first();

        if (!$discount) {
            return response()->json(['success' => false, 'msg' => 'Discount not found.']);
        }

        if ($discount->status != 1) {
            Discount::where('status', 1)->where('id', '!=', $discount_id)->update(['status' => 0]);

            $discount->status = 1;  
            $discount->save();

            Alert::success('Success', 'Discount activated successfully');
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'msg' => 'Discount is already active.']);
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
    
        $validColumns = [
            // 'discount_type','discount_amount', 'min_amount', 'max_amount','expiry_date', 'status'];
            'discount_type','discount_amount', 'min_amount', 'upto_amount','created_at','expiry_date', 'status'];
    
        if (!in_array($columnName, $validColumns)) {
            $columnName = 'id';
        }

        Discount::whereDate('expiry_date', '<=', Carbon::today())
        ->where('status', 1)
        ->update(['status' => 0]);
    
        $query = Discount::query()->where('status', '!=', 2);
    
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->orWhere('discount_type', 'like', "%$search%")
                  ->orWhere('discount_amount', 'like', "%$search%")
                  ->orWhere('min_amount', 'like', "%$search%")
                //   ->orWhere('max_amount', 'like', "%$search%");
                  ->orWhere('upto_amount', 'like', "%$search%")
                  ->orWhere('created_at', 'like', "%$search%");
            });
        }
    
        $totalRecords = $query->count();
    
        $discounts = $query->orderBy($columnName, $columnSortOrder)
            ->skip($start)
            ->take($rowperpage)
            ->get();
    
        $data_arr = [];
        $today = Carbon::now()->toDateString();
        foreach ($discounts as $discount) {
            $statusIcon = $discount->status == 1
                ? '<i class="fa fa-thumbs-up text-success status_active" title="Active" data-id="'.$discount->id.'"></i>'
                : '<i class="fa fa-thumbs-down text-danger status_inactive" title="Inactive" data-id="'.$discount->id.'"></i>';
    
            $showBtn = '<a class="btn btn-sm show-discount" data-id="'.$discount->id.'" title="Show"><i class="material-icons">&#xe8f4;</i></a>';

            $editBtn = '';
            $deleteBtn = '';

            if ($discount->expiry_date >= $today) {
                    $editBtn = '<a class="btn btn-sm success edit-discount" data-id="'.$discount->id.'" title="Edit"><i class="material-icons">&#xe3c9;</i></a>';
                    $deleteBtn = '<button class="btn btn-sm warning delete-discount" data-id="'.$discount->id.'" title="Delete"><i class="material-icons">&#xe872;</i></button>'; 
            }


    
            $data_arr[] = [
                'discount' => ucfirst($discount->discount_type),
                'discount_amount' => $discount->discount_type =="flat" ? intVal($discount->discount_amount).' GH₵' : intVal($discount->discount_percentage).'%',
                'minimum' => intVal($discount->min_amount).' GH₵',
                'upto' =>$discount->discount_type =="flat" ? '-' : intVal( $discount->upto_amount).' GH₵',
                'created' => date('Y-m-d', strtotime($discount->created_at)),
                'expiry' => date('Y-m-d', strtotime($discount->expiry_date)),
                'status' => $statusIcon,
                'options' => $showBtn . ' ' . $editBtn . ' ' . $deleteBtn
            ];
        }
    
        return response()->json([
            'draw' => intval($draw),
            'iTotalRecords' => $totalRecords,
            'iTotalDisplayRecords' => $totalRecords,
            'aaData' => $data_arr,
        ]);
    }





}
