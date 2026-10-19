<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Cms;
use Auth;
use File;
use Illuminate\Config;
use Helper;
use Illuminate\Http\Request;
use Redirect;
use DB;
use Yajra\Datatables\Datatables;
use Alert;

class CmsController extends Controller
{

    // Define Default Variables

    public function __construct()
    {
        $this->middleware('auth');
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 18, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }
    }

    /**
     * Display a listing of the resource.
     * string $stat
     * @return \Illuminate\Http\Response
     */

    public function index()
    {

        $cms = Cms::get();

        $cmsData = count($cms);



        return view(
            "dashboard.cms.list",
            compact("cms", "cmsData")
        );

    }

    public function create()
    {
        return view("dashboard.cms.create");
    }


    public function store(Request $request)
    {

        $this->validateRequest();
        $cms = new Cms();
        $cms->page_name = $request->page_name;
        $cms->page_name_fr = $request->page_name_fr;
        $cms->page_content = $request->page_content;
        $cms->page_content_fr = $request->page_content_fr;
        $cms->mobile_page_content = $request->mobile_page_content;
        $cms->mobile_page_content_fr = $request->mobile_page_content_fr;
        $cms->status = 1;
        $cms->create_by = Auth::user()->id;
        $cms->created_at = date('Y-m-d H:i:s');
        $cms->save();

        return redirect()->route('cms')->with('doneMessage', 'Record created successfully.');
    }


    public function updateAll(Request $request)
    {


        //
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;

                CMS::wherein('id', $ids)->update(['status' => $status]);

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
        abort(404);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cms = Cms::find($id);
        // General for all pages
        return view('dashboard.cms.edit', compact('cms'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate all incoming requests
        $this->validateRequest();

        // Find the CMS model, or fail with a 404 error
        $cms = Cms::findOrFail($id);

        // Update CMS fields
        $cms->fill([
            'page_name' => $request->page_name,
            'page_name_fr' => $request->page_name_fr,
            'page_content' => $request->page_content,
            'page_content_fr' => $request->page_content_fr,
            'mobile_page_content' => $request->mobile_page_content,
            'mobile_page_content_fr' => $request->mobile_page_content_fr,
            'status' => 1,
            'update_by' => Auth::user()->id,
            'updated_at' => now(),
        ]);

        // Handle file upload
        $formFileName = "photo";
        if ($request->hasFile($formFileName)) {
            $file = $request->file($formFileName);
            $fileExtension = $file->getClientOriginalExtension();
            $fileFinalName_ar = time() . rand(1111, 9999) . '.' . $fileExtension;

            $uploadPath = public_path("/uploads/cms/");
            $file->move($uploadPath, $fileFinalName_ar);

            $cms->photo = $fileFinalName_ar;
        } elseif ($request->input('photo') === '') {
            $cms->photo = null;
        }

        // Save changes
        $cms->save();

        // Redirect with success message
        return redirect()->route('cms')->with('doneMessage', 'Record updated successfully.');
    }



    /**
     * Display the specified resource.
     *
     * @param  \App\Cms  $cms
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cms = Cms::find($id);
        // dd($cms);

        // General for all pages
        return view('dashboard.cms.show', compact('cms'));
    }
    public function validateRequest($id = "")
    {
        if ($id != "") {
            $validateData = request()->validate(
                [
                    'page_name' => 'required',
                    'page_name_fr' => 'required',
                    'page_content' => 'required',
                    'page_content_fr' => 'required',
                    'mobile_page_content' => 'required',
                    'mobile_page_content_fr' => 'required',
                    'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
                ],
                [
                    // 'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'
                ]
            );

        } else {

            $validateData = request()->validate([
                'page_name' => 'required',
                'page_name_fr' => 'required',
                'page_content' => 'required',
                'page_content_fr' => 'required',
                'mobile_page_content' => 'required',
                'mobile_page_content_fr' => 'required',
            ]);

        }

        return $validateData;
    }

    public function anyDataold(Request $request)
    {

        $data = Cms::where('status', '!=', 2)->orderby('id', 'desc')->get();

        return Datatables::of($data)
            ->addColumn('name', function ($data) {
                $page_name = isset ($data->page_name) ? $data->page_name : '';
                return $page_name;
            })

            ->addColumn('description', function ($data) {
                $description = isset ($data->page_content) ? $data->page_content : '';
                return $description;
            })

            ->addColumn('options', function ($data) {

                $cmsshow = route('cms.show', ['id' => $data->id]);
                $cmsedit = route('cms.edit', ['id' => $data->id]);
                $cmsdelete = route('cms.edit', ['id' => $data->id]);

                $x = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $cmsshow . '" title="Show"> </a>';


                $x .= '<a class="btn btn-sm success paddingset" href="' . $cmsedit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
                $x .= '<button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $cmsdelete . '"> <small><i class="material-icons">&#xe872;</i> </small> </button>';

                return $x;

            })

            ->rawColumns(['checkbox', 'name', 'description', 'options'])
            ->make(true);
    }

    public function status_active(Request $request)
    {
        $id = $request->id;
        Cms::where('id', $id)->update(['status' => 0]);
        // $stateData = State::where('country_id',$country_id)->get();

        // foreach ($stateData as $state) {
        //     State::where('country_id', $country_id)->update(['status' => 0]);
        //     City::where('state_id', $state->id)->update(['status' => 0]);
        // }       

        Alert::success('Success', __('backend.cms_deactive_successfully'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $id = $request->id;
        Cms::where('id', $id)->update(['status' => 1]);

        // $stateData = State::where('country_id',$country_id)->get();

        // foreach ($stateData as $state) {
        //     State::where('country_id', $country_id)->update(['status' => 1]);
        //     City::where('state_id', $state->id)->update(['status' => 1]);
        // }

        Alert::success('Success', __('backend.cms_active_successfully'));
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
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = '';
        if (isset($order_arr[0]['dir']) && $order_arr[0]['dir'] != "") {
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        }
        $searchValue = $search_arr['value']; // Search value
        if ($columnIndex == 0) {
            $sort = 'cms.id';
        } elseif ($columnIndex == 1) {
            $sort = 'cms.page_name';
        } else {
            $sort = 'cms.id';
        }

        $sortBy = 'DESC';
        if ($columnSortOrder != "") {
            $sortBy = $columnSortOrder;
        }

        /* $data =  Packages::with(['weeks','weeks.shoutout','weeks.takeAction'])->where('status','!=','2');*/

        $totalAr = Cms::where('status', '!=', 2);


        if ($searchValue != "") {
            $totalAr = $totalAr->where(function ($query) use ($searchValue) {
                $query->orWhere('page_name', 'like', '%' . $searchValue . '%');
            });
        }


        $totalRecords = $totalAr->get()->count();

        $totalAr = $totalAr->orderBy($sort, $sortBy)
            ->skip($start)
            ->take($rowperpage)
            ->get();

        /* print_r($totalAr);
         exit;*/
        $data_arr = [];

        foreach ($totalAr as $key => $data) {

            $cmsshow = route('cms.show', ['id' => $data->id]);
            $cmsedit = route('cms.edit', ['id' => $data->id]);

            if ($data->status == 1) {
                $status = '<a style="pointer-events: none"><i class="fa fa-thumbs-up text-success inline status_active" title="Active"  data-id="' . $data->id . '"></i></a>';
            } else {
                $status = '<a style="pointer-events: none"><i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i></a> ';
            }

            $options = '<a class="btn btn-sm show-eyes list box-shadow paddingset" href="' . $cmsshow . '" title="Show"> </a>';


            $options .= '<a class="btn btn-sm success paddingset" href="' . $cmsedit . '" title="Edit"> <small><i class="material-icons">&#xe3c9;</i> </small> </a>';
            // $options .= ' <button class="btn btn-sm warning delete-school" title="Delete" data-id="'.$data->id.'"> <small><i class="material-icons">&#xe872;</i> </small> </button>';
            $data_arr[] = array(
                "id" => isset($data->id) ? $data->id : '',
                "name" => isset($data->page_name) ? $data->page_name : '',
                //   "description" =>  isset($data->description_eng) ? $data->description_eng : '',
                "status" => $status,
                "options" => $options,
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
