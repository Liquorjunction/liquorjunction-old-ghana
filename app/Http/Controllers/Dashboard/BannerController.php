<?php
namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Categories;
use App\Models\SubCategories;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Language;
use App\Models\Product;
use Config;
use Response;
use Mail;
use URL;
use Auth;
use File;
use Helper;
use Yajra\Datatables\Datatables;
use Redirect;
use DB;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Calculation\Category;
use Storage;
use RealRashid\SweetAlert\Facades\Alert;

class BannerController extends Controller
{
    private $uploadPath = "public/uploads/banners/";

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $check_view_permission = @Helper::GetRolePermission(Auth::user()->user_type, 4, 'read');
        if ($check_view_permission == false) {
            abort(404);
        }
    }

    public function index()
    {
        return view('dashboard.banner.list');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $language = Language::get();
        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        $subcategories = SubCategories::where('status', 1)->orderby('title', 'ASC')->get();

        return view('dashboard.banner.create', compact('language', 'categories', 'product','brand','subcategories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $authid = Auth::user()->id;
        $result = $this->validateRequest();

        // $offer = Banner::where('status','!=',2)->where('offer', 1)->count();
        $offer = Banner::whereNotIn('status', [0, 2])->where('offer', 1)->count();

        if ($offer > 0) {
            return redirect()->back()->with('errorMessage', 'An offer already exist');
        } else {

            $Banner = new Banner();
            $Banner->language_id = null;
            $Banner->title = isset($request->title) ? $request->title : '';
            $Banner->title_fr = isset($request->title_fr) ? $request->title_fr : '';
            $Banner->description = isset($request->description) ? $request->description : '';
            $Banner->description_fr = isset($request->description_fr) ? $request->description_fr : '';
            $Banner->banner_url = isset($request->banner_url) ? $request->banner_url : '';
            $Banner->type = isset($request->type) ? $request->type : '';
            $Banner->category_id = isset($request->category_id) ? $request->category_id : '';
            $Banner->subcategory_id = isset($request->subcategory_id) ? $request->subcategory_id : '';
            $Banner->product_id = isset($request->product_id) ? $request->product_id : '';
            $Banner->brand_id = isset($request->brand_id) ? $request->brand_id : '';
            $Banner->text_color = isset($request->text_color) ? $request->text_color : '#000000';
            $Banner->status = 1;
            if ($request->banner_type == 2) {
                $Banner->highlight = 1;
            }
            if ($request->banner_type == 3) {
                $Banner->offer = 1;
            }
            $Banner->save();
            // Start of Upload Files
            $formFileName = "photo";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(
                    1111,
                    9999
                ) . '.' . $request->file($formFileName)->getClientOriginalExtension();
                $uploadPath = "/uploads/banners/";
                $path = public_path() . $uploadPath;
                $request->file($formFileName)->move($path, $fileFinalName_ar);

                $Banner->update([
                    'photo' => $fileFinalName_ar,
                ]);
            }

            return redirect()->route('banner')->with('doneMessage', 'Record(s) created successfully.');
        }
    }

    public function validateRequest($id = "")
    {

        if ($id != "") {

            if (request('type') == 1) {
                $validateData = request()->validate([
                    // 'title' => ['required',Rule::unique('banners')->ignore($id)->where(function ($query) use ($id) {
                    //     return $query->where('status', '!=', '2');
                    // // })],
                    // 'title' => 'required',
                    // 'title_fr' => 'required',
                    // 'description' => 'required',
                    // 'description_fr' => 'required',
                    'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'type' => 'required',
                    'category_id' => 'required',
                    'banner_type' => 'required',

                ], [
                    // 'title.required' => 'The title field is required.',
                    // 'title_fr.required' => 'The title fr field is required.',
                    // 'description.required' => 'The description field is required.',
                    // 'description_fr.required' => 'The description fr field is required.',
                    'banner_url.required' => 'The URL field is required.',
                    'banner_url.regex' => 'Please Enter valid URL',
                    'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'

                ]);
            }
            elseif(request('type') == 0)
            {
                $validateData = request()->validate([
                   
                    'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'type' => 'required',
                    'brand_id' => 'required',
                    'banner_type' => 'required',

                ],[
                    // 'title.required' => 'The title field is required.',
                    // 'title_fr.required' => 'The title fr field is required.',
                    // 'description.required' => 'The description field is required.',
                    // 'description_fr.required' => 'The description fr field is required.',
                    'banner_url.required' => 'The URL field is required.',
                    'banner_url.regex' => 'Please Enter valid URL',
                    'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'
                ]);
            }
            elseif (request('type') == 2) {
                $validateData = request()->validate([
                    // 'title' => ['required',Rule::unique('banners')->ignore($id)->where(function ($query) use ($id) {
                    //     return $query->where('status', '!=', '2');
                    // })],
                    // 'title' => 'required',
                    // 'title_fr' => 'required',
                    // 'description' => 'required',
                    // 'description_fr' => 'required',
                    'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'type' => 'required',
                    'product_id' => 'required',
                    'banner_type' => 'required',

                ], [
                    'title.required' => 'The title field is required.',
                    'title_fr.required' => 'The title fr field is required.',
                    'description.required' => 'The description field is required.',
                    'description_fr.required' => 'The description fr field is required.',
                    'banner_url.required' => 'The URL field is required.',
                    'banner_url.regex' => 'Please Enter valid URL',
                    'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'
                ]);
            } else {
                $validateData = request()->validate([
                    //   'title' => ['required',Rule::unique('banners')->ignore($id)->where(function ($query) use ($id) {
                    //     return $query->where('status', '!=', '2');
                    // })],
                    // 'title' => 'required',
                    // 'title_fr' => 'required',
                    // 'description' => 'required',
                    // 'description_fr' => 'required',
                    'banner_url' => ['required', 'regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
                    'photo' => 'image|mimes:jpeg,png,jpg|max:2048',
                    'type' => 'required',
                    'banner_type' => 'required',
                ], [
                    'title.required' => 'The title field is required.',
                    'title_fr.required' => 'The title fr field is required.',
                    'description.required' => 'The description field is required.',
                    'description_fr.required' => 'The description fr field is required.',
                    'banner_url.required' => 'The URL field is required.',
                    'banner_url.regex' => 'Please Enter valid URL',
                    'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'
                ]);
            }
        } else {
            if (request('type') == 1) {
                $validateData = request()->validate([
                    // 'title' => [
                    //     'required',
                    //     Rule::unique('banners')->ignore('user_id')->where(function ($query) use ($id) {
                    //         return $query->where('status', '!=', '2');
                    //     })
                    // ],
                    // 'title' => 'required',
                    // 'title_fr' => 'required',
                    // 'description' => 'required',
                    // 'description_fr' => 'required',
                    'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'type' => 'required',
                    'category_id' => 'required',
                    'banner_type' => 'required',


                ], [
                    'title.required' => 'The title field is required.',
                    'title_fr.required' => 'The title fr field is required.',
                    'description.required' => 'The description field is required.',
                    'description_fr.required' => 'The description fr field is required.',
                    'banner_url.required' => 'The URL field is required.',
                    'banner_url.regex' => 'Please Enter valid URL',
                    'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'
                ]);
            } 
            elseif(request('type') == 0)
            {
                $validateData = request()->validate([
                    'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'type' => 'required',
                    'brand_id' => 'required',
                    'banner_type' => 'required',
                ],[
                    'title.required' => 'The title field is required.',
                    'title_fr.required' => 'The title fr field is required.',
                    'description.required' => 'The description field is required.',
                    'description_fr.required' => 'The description fr field is required.',
                    'banner_url.required' => 'The URL field is required.',
                    'banner_url.regex' => 'Please Enter valid URL',
                    'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'
                ]);
            }
            elseif (request('type') == 2) {
                $validateData = request()->validate([
                    // 'title' => [
                    //     'required',
                    //     Rule::unique('banners')->ignore('user_id')->where(function ($query) use ($id) {
                    //         return $query->where('status', '!=', '2');
                    //     })
                    // ],
                    // 'title' => 'required',
                    // 'title_fr' => 'required',
                    // 'description' => 'required',
                    // 'description_fr' => 'required',
                    'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'type' => 'required',
                    'product_id' => 'required',
                    'banner_type' => 'required',

                ], [
                    'title.required' => 'The title field is required.',
                    'title_fr.required' => 'The title fr field is required.',
                    'description.required' => 'The description field is required.',
                    'description_fr.required' => 'The description fr field is required.',
                    'banner_url.required' => 'The URL field is required.',
                    'banner_url.regex' => 'Please Enter valid URL',
                    'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'
                ]);
            } else {
                $validateData = request()->validate([
                    // 'title' => [
                    //     'required',
                    //     Rule::unique('banners')->ignore('user_id')->where(function ($query) use ($id) {
                    //         return $query->where('status', '!=', '2');
                    //     })
                    // ],
                    // 'title' => 'required',
                    // 'title_fr' => 'required',
                    // 'description' => 'required',
                    // 'description_fr' => 'required',
                    'banner_url' => ['required', 'regex:/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i'],
                    'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    'type' => 'required',
                    'banner_type' => 'required',
                ], [
                    'title.required' => 'The title field is required.',
                    'title_fr.required' => 'The title fr field is required.',
                    'description.required' => 'The description field is required.',
                    'description_fr.required' => 'The description fr field is required.',
                    'banner_url.required' => 'The URL field is required.',
                    'banner_url.regex' => 'Please Enter valid URL',
                    'photo.required' => 'The image field is required.',
                    'photo.image' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.mimes' => 'The image must be a file of type: jpg, jpeg, png.',
                    'photo.max' => 'The image upload not greater than 2 mb size.'
                ]);
            }
        }
        return $validateData;
    }



    private function storeImage($user)
    {

        if (request()->has('photo')) {
            $fileFinalName_ar = time() . '.' . request()->photo->getClientOriginalExtension();
            $file = request()->file('photo');
            $name = $avatarName;
            $path = $this->getUploadPath();
            Storage::disk('local')->put($filePath, file_get_contents($file));
            $request->file($formFileName)->move($path, $fileFinalName_ar);

            $user->update([
                'photo' => $avatarName,
            ]);

        }

        // if ($request->$formFileName != "") {
        //     $fileFinalName_ar = time() . rand(1111,
        //             9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
        //     $uploadPath = "/uploads/banners/";
        //     $path = public_path() . $uploadPath;
        //     $request->file($formFileName)->move($path, $fileFinalName_ar);

        //     $banner->update([             
        //        'photo' => $fileFinalName_ar,
        //    ]);
        // }
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }



    public function bannerstatusupdate(Request $request)
    {
        if ($request->ajax()) {
            if ($request->ids != "") {
                $ids = explode(",", $request->ids);
                $status = $request->status;

                Banner::wherein('id', $ids)->update(['status' => $status]);

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
    public function removeimage(Request $request)
    {

        $id = $request->user_id;

        $findImage = Banner::where('id', $id)->first();
        $imageName = $findImage['photo'];


        $userdetail_update = Banner::where('id', $id)->update(
            array(
                'photo' => "",
            )
        );

        // $image_path=env('AWS_URL').'customer/'.$imageName;

        // \Storage::disk('s3')->delete($image_path);

        return response()->json(['success' => 'Image removed successfully.']);

    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function show($id, Setting $setting)
    {
        $setting = Setting::find(1);
        $banner = Banner::find($id);
        return view('dashboard.banner.show', compact('banner', 'setting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $banner = banner::find($id);
        $language = Language::all();
        $categories = Categories::where('status', 1)->orderby('title', 'ASC')->get();
        $brand = Brand::where('status', 1)->orderby('title', 'ASC')->get();
        $product = Product::where('status', 1)->orderby('product_name', 'ASC')->get();
        $subcategories = SubCategories::where([['category_id', $banner->category_id], ['status', 1]])->orderby('title', 'ASC')->get();

        // Redirect to user list if updating user wasn't existed
        if ($banner == null) {
            return redirect()->intended('admin/banner');
        }
        return view('dashboard.banner.edit', ['banner' => $banner, 'language' => $language, 'categories' => $categories,'brand' => $brand, 'product' => $product,'subcategories'=>$subcategories]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Staff  $staff
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request, $id)
    {
        // Retrieve the banner to be updated
        $banner = Banner::findOrFail($id);

        // Validate the request data
        $this->validateRequest($id);

        // Check if the request is setting the banner type to offer
        $isOfferType = $request->banner_type == 3;

        // Check for any existing offer banners other than the current banner
        $existingOffer = Banner::where('offer', 1)
            ->where('status', '!=' , 2)
            ->where('id', '!=', $id)
            ->first();

        // Handle the case where the request is setting the banner type to offer
        if ($isOfferType) {
            // If another offer already exists, prevent the update
            if ($existingOffer) {
                return redirect()->back()->with('errorMessage', 'An offer already exists.');
            }
        }

        $brand_id = $request->brand_id;
        $category_id = $request->category_id;
        $subcategory_id = $request->subcategory_id;
        $product_id = $request->product_id;
        $type=$request->type;
        $banner_url= $request->banner_url;

        if ($brand_id && $type==0) {
            $category_id = null;
            $product_id = null;
            $banner_url= null;
            $subcategory_id = null;
        }
        elseif ($category_id && $type==1) {
            $brand_id = null;
            $product_id = null;
            $banner_url= null;
        }
        elseif ($subcategory_id && $type==1) {
            $brand_id = null;
            $product_id = null;
            $banner_url= null;
        }
        elseif ($product_id && $type==2) {
            $brand_id = null;
            $category_id = null;
            $subcategory_id = null;
            $banner_url= null;
        }
        else
        {
            $brand_id = null;
            $category_id = null;
            $category_id = null;
            $subcategory_id=null;
        }

        // Prepare the data for updating the banner
        $updateData = [
            'language_id' => null,
            'title' => $request->title,
            'title_fr' => $request->title_fr,
            'description' => $request->description,
            'description_fr' => $request->description_fr,
            'banner_url' => $banner_url,
            'type' => $request->type,
            'category_id' => $category_id,
            'subcategory_id' => $subcategory_id,
            'brand_id' => $brand_id,
            'product_id' => $product_id,
            'highlight' => $request->banner_type == 2 ? 1 : null,
            'offer' => $isOfferType ? 1 : null,
            'text_color' => $request->text_color,
            'updated_at' => now(),
        ];

        // Update the banner data in the database
        $banner->update($updateData);

        // Handle file upload if a new file is provided
        if ($request->hasFile('photo')) {
            $formFileName = "photo";
            $fileFinalName_ar = time() . rand(1111, 9999) . '.' . $request->file($formFileName)->getClientOriginalExtension();
            $uploadPath = public_path() . "/uploads/banners/";
            $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);

            // Update the banner with the new file name
            $banner->update(['photo' => $fileFinalName_ar]);
        }

        // Redirect to the banner page with a success message
        return redirect()->route('banner')->with('doneMessage', 'Record(s) updated successfully.');
    }


    // public function update(Request $request, $id)
    // {
    //     // dd($request->all());

    //     $banner = Banner::findOrFail($id);
    //     // dd($banner);
    //     $authid = Auth::user()->id;
    //     $result = $this->validateRequest($id);

    //     $isOfferType = $request->banner_type == 3;

    //     $existingOffer = Banner::where('offer', 1)
    //                        ->where('status', 1)
    //                        ->where('id', '!=', $id)
    //                        ->first();

    //     // $id = Banner::where('offer',1)->where('status',1)->first();
    //     // dd($id);

    //     if ($isOfferType) {
    //         // dd(2);
    //         // Check if there is any existing active offer other than this one.
    //         if ($existingOffer) {
    //             // dd(3);
    //             // If trying to set this banner as an offer but another offer exists, prevent it.
    //             return redirect()->back()->with('errorMessage', 'An offer already exists.');
    //         }
    //         else {
    //             // dd(4);
    //             return redirect()->route('banner')->with('doneMessage', 'Record(s) updated successfully.');
    //         }
    //     } 

    //     else {
    //         // dd(1);

    //         $updateData['language_id'] = null;
    //         $updateData['title'] = $request->title;
    //         $updateData['title_fr'] = $request->title_fr;
    //         $updateData['description'] = $request->description;
    //         $updateData['description_fr'] = $request->description_fr;
    //         $updateData['banner_url'] = $request->banner_url;
    //         $updateData['type'] = $request->type;
    //         $updateData['category_id'] = $request->category_id;
    //         $updateData['product_id'] = $request->product_id;
    //         $updateData['type'] = $request->type;
    //         //$updateData['status'] = 1;
    //         if ($request->banner_type == 2) {
    //             $updateData['highlight'] = 1;
    //         } else {
    //             $updateData['highlight'] = null;
    //         }
    //         if ($request->banner_type == 3) {
    //             $updateData['offer'] = 1;
    //         } else {
    //             $updateData['offer'] = null;
    //         }
    //         $updateData['updated_at'] = date('Y-m-d H:i:s');

    //         Banner::where('id', $id)->update($updateData);
    //         // Start of Upload Files
    //         $formFileName = "photo";
    //         $fileFinalName_ar = "";
    //         if ($request->$formFileName != "") {
    //             $fileFinalName_ar = time() . rand(
    //                 1111,
    //                 9999
    //             ) . '.' . $request->file($formFileName)->getClientOriginalExtension();
    //             $uploadPath = public_path() . "/uploads/banners/";
    //             //$path = $this->getUploadPath();
    //             $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
    //             $banner->update([
    //                 'photo' => $fileFinalName_ar,
    //             ]);
    //         }
    //         return redirect()->route('banner')->with('doneMessage', 'Record(s) updated successfully.');
    //     }

    //     // dd($result);
    //     // $Banner = new Banner();
    //     // $Banner->language_id = null;
    //     // $Banner->title = isset($request->title) ? $request->title : '';
    //     // $Banner->title_fr = isset($request->title_fr) ? $request->title_fr : '';
    //     // $Banner->description = isset($request->description) ? $request->description : '';
    //     // $Banner->description_fr = isset($request->description_fr) ? $request->description_fr : '';
    //     // $Banner->banner_url = isset($request->banner_url) ? $request->banner_url : '';
    //     // $Banner->type = isset($request->type) ? $request->type : '';
    //     // $Banner->category_id = isset($request->category_id) ? $request->category_id : '';
    //     // $Banner->product_id = isset($request->product_id) ? $request->product_id : '';
    //     // $Banner->status = 1;
    //     // $Banner->offer = isset($request->offer) ? $request->offer : '';
    //     // $Banner->highlight = isset($request->highlight) ? $request->highlight : '';
    //     // $Banner->updated_at = date('Y-m-d H:i:s');

    //     // $Banner->save();

    // }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Staff  $staff
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Banner::where('id', $id)->update(['status' => 2]);
        return response()->json(['success' => 'Record(s) Deleted successfully.']);
    }

    /**
     * delete all  the checked resource in storage.
     *
     * @param  \Illuminate\Http\Request 
     * @return \Illuminate\Http\Response
     */
    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        Banner::whereIn('id', explode(",", $ids))->update(['status' => 2]);
        return response()->json(['success' => "Record(s) Deleted successfully."]);
    }
    /**
     * Show the status of inactive .
     *
     * @return \Illuminate\Http\Response
     */
    public function status_active(Request $request)
    {
        $banner_id = $request->id;
        Banner::where('id', $banner_id)->update(['status' => 0]);
        Alert::success('Success', __('backend.banner_deactive_sucessfully'));
        return response()->json(['success' => 'true']);
    }

    public function status_inactive(Request $request)
    {
        $banner_id = $request->id;
        Banner::where('id', $banner_id)->update(['status' => 1]);
        Alert::success('Success', __('backend.banner_active_sucessfully'));
        return response()->json(['success' => 'true']);
    }
    public function ischecked(Request $request)
    {
        $id = $request->id;
        $banner = Banner::find($id);

        if ($banner->highlight == '1') {

            $banner->highlight = '0';
            \Alert::success('Success', __('This banner removed from highlight successfully'));

        } else {
            $banner->highlight = '1';
            \Alert::success('Success', __('This banner added in highlight successfully'));
        }
        $banner->save();
        return response()->json(['success' => 'true']);
    }
    public function offervalue(Request $request)
    {
        $id = $request->id;
        $banner = Banner::find($id);

        if ($banner->offer == '1') {

            $banner->offer = '0';
            \Alert::success('Success', __('This banner removed from offer successfully'));

        } else {
            $banner->offer = '1';
            \Alert::success('Success', __('This banner added in the offer successfully'));
        }
        $banner->save();
        // \Alert::success('Success', __('backend.Bestseller checked'));

        return response()->json(['success' => 'true']);
    }
    /**
     * Display a listing of the resource in datatables. 
     *
     * @return Yajra\Datatables\Datatables
     */
    public function anyData()
    {
        // $data = Banner::orderBy('id', 'DESC')->where('status','!=','2')->get();
        $data = DB::table('banners')
            ->leftjoin('categories', 'categories.id', '=', 'banners.category_id')
            ->leftjoin('product', 'product.id', '=', 'banners.product_id')
            ->leftjoin('brand', 'brand.id', '=', 'banners.brand_id')
            ->select('banners.*', 'categories.title as category_name', 'product.product_name as product_name','brand.title as brand_name')
            ->orderBy('id', 'DESC')
             ->where('banners.status', '!=', 2);


        return Datatables::of($data)
            ->addColumn('checkbox', function ($data) {
                $x = '<label class="ui-check m-a-0"> <input type="checkbox" name="ids[]" value="' . $data->id . '" class="has-value" data-id="' . $data->id . '"><i class="dark-white"></i> <input class="form-control row_no has-value" name="row_ids[]" type="hidden" value="' . $data->id . '"> </label>';
                return $x;
            })->addColumn('title', function ($data) {
                $name = isset ($data->title) ? $data->title : '';
                return $name;
            })
            ->addColumn('type', function ($data) {
                if ($data->type == 1) {
                    $type = 'Category';
                } elseif ($data->type == 2) {
                    $type = 'Product';
                } 
                elseif ($data->type == 0) {
                    $type = 'Brand';
                } 
                else {
                    $type = 'Custom URL';
                }
                return $type;
            })
            ->addColumn('banner_type', function ($data) {
                if ($data->offer == 1) {
                    $type = 'Offer';
                } elseif ($data->highlight == 1) {
                    $type = 'Highlight';
                } else {
                    $type = 'Main Banner';
                }
                return $type;
            })
            ->editColumn('photo', function ($data) {
                if ($data->photo != "") {
                    $imagefile = asset('uploads/banners/') . '/' . $data->photo;

                    return '<img  src="' . $imagefile . '" class="thumbnail" width="100px" height="100px"/>';
                } else {
                    $imagefile = asset('uploads/contacts/noimage.png');
                    return '<img  src="' . $imagefile . '" class="thumbnail" width="100px" height="100px;"/>';
                }
            })->addColumn('action', function ($data) {

                $customershow = route('banner.show', ['id' => $data->id]);
                $customeredit = route('banner.edit', ['id' => $data->id]);
                $customerdelete = route('banner.destroy', ['id' => $data->id]);


                $x = '<a href="' . $customershow . '"  class="btn btn-sm show-eyes list" title="show"> <small><i class="fas fa-eye"></i></small> </a>
                    <a href="' . $customeredit . '" class="btn btn-sm success paddingset" title="Edit"><small><i class="material-icons">&#xe3c9;</i> </small> </a></button>
                    <button class="btn btn-sm warning delete-school" title="Delete" data-id="' . $data->id . '" style="margin-left: -4px;"> <small><i class="material-icons">&#xe872;</i> </small> </button>
                   
                    ';
                return $x;

            })
            ->editColumn('status', function ($data) {
                if ($data->status == 1) {
                    return '<i class="fa fa-thumbs-up text-success inline status_active" title="Active" data-id="' . $data->id . '"></i>';
                } else {
                    return '<i class="fa fa-thumbs-down text-danger inline status_inactive" title="Deactive" data-id="' . $data->id . '"></i>';
                }
            })
            ->rawColumns(['checkbox', 'title', 'type', 'photo', 'Banner type', 'status', 'action'])
            ->make(true);
    }
}
