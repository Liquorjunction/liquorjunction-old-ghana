<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Area;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Categories;
use App\Models\Cms;
use App\Models\Country;
use App\Models\Inquiry;
use App\Models\InquiryReason;
use App\Models\Region;
use App\Models\Faq;
use App\Models\Product;
use App\Models\SubCategories;
use App\Models\AdminNotifications;
use Mail;
use DB;
use App\Models\Setting;



class CommonController extends Controller
{

    public function getBannerList(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        $result = [];
        if ($request->type == 1) {
            $banner_info =  Banner::where('status', 1)->where('highlight', 1)->limit(6)->orderby('created_at', 'desc')->get();
        } else {
            $banner_info = Banner::where('status', 1)->limit(6)->orderby('created_at', 'desc')->where('highlight', null)->where('offer', null)->get();
        }
        if (!empty($banner_info)) {
            $banner_data = [];
            foreach ($banner_info as $value) {
                /*---Language 1 for french--*/
                if ($request->language == 1) {
                    $banner_title = $value->title_fr ?? $value->title;
                    $banner_description = $value->description_fr ?? $value->description;
                } else {
                    $banner_title = $value->title ?? '';
                    $banner_description = $value->description ?? '';
                }
                $type = "";
                $custom_url = "";
                if ($value->type == 1) {
                    $type = $value->category_id;
                } else if ($value->type == 2) {
                    $type = $value->product_id;
                } else if ($value->type == 0) {
                    $type = $value->brand_id;
                } else if ($value->type == 3) {
                    $custom_url = $value->banner_url;
                }
                $banner_data['banner_data'][] = [
                    'id' => strval($value->id),
                    'banner_image' => asset("uploads/banners/") . '/' . $value->photo,
                    'banner_type' => "$value->type",
                    'banner_cp_id' => "$type",
                    'banner_custom_url' => $custom_url,
                    'banner_title' => $banner_title,
                    'banner_description' => $banner_description,
                    'text_color' => $value->text_color,
                ];
            }
            $result['code']     =    strval(1);
            $result['message']  =   'success';
            $result['result']   =   $banner_data;
        } else {
            $result['code']     =   strval(0);
            $result['message']  =   'no_data_found';
            $result['result']   =   [];
        }

        $mainResult = $result;
        return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    }

    public function getCategoryList(Request $request)
    {
        // dd($request->toArray());
        $result = [];
        $finalArr = [];

        if ($request->all()) {
            $categories = Categories::where('status', 1)->orderby('order_number', 'ASC');
            // dd($categories);
            $page = $request->page;

            if ($page == '') {
                $page = 1;
            }
            $categoryData = $categories->paginate(8, ['*'], 'page', $page);
            // dd($categoryData);
            $mainArr = [];
            $categoryArr = [];
            if (!empty($categoryData)) {
                foreach ($categoryData as $category) {
                    if ($request->language == 1) {
                        $title = $category->title_fr ?? $category->title;
                    } else {
                        $title = $category->title ?? '';
                    }
                    $categoryArr['category_list'][] = [
                        'category_id' => strval(@$category->id),
                        'category_name' => strval(@$title),
                        'category_image' => asset("uploads/category/") . '/' . $category->imagefile,
                        'background_image' => asset("uploads/categoryback/") . '/' . $category->photo,
                    ];
                }
                $result['code']     =  strval(1);
                $result['message']  =  'success';
                $result['result']   = $categoryArr;
                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code']     =  strval(0);
                $result['message']  =   'no_data_found';
                $result['result']       =  [];
                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {
            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];
            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    // public function searchList(Request $request)
    // {
    //     $result = [];
    //     $finalArr = [];
    //     $validator = \Validator::make($request->all(), [
    //         'keywords' => 'required',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status_code' => strval(0),
    //             'error'=>$validator->messages(),
    //             'data' => null
    //         ], 200);
    //     }

    //     if($request->all())
    //     {

    //         $keyword = $request->keywords;
    //         $setting = Setting::find(1);

    //         $categoryData = Categories::where('status', 1)->orderBy('id', 'ASC');
    //         $subcategoryData = SubCategories::withWhereHas('category', function ($query) {
    //             $query->where('status', 1);
    //         })->where('status', 1)->orderBy('id', 'ASC');            
    //         $productDataFilter = Product::withWhereHas('get_product_images', function ($query) {
    //             $query->where('status', 1);
    //         })->withWhereHas('get_product_variants', function ($query) {
    //             $query->where('status', 1);
    //         })->withWhereHas('get_category', function ($query) {
    //             $query->where('status', 1);
    //         })->withWhereHas('get_subcategory', function ($query) {
    //             $query->where('status', 1);
    //         })->withWhereHas('get_brand_details', function ($query) {
    //             $query->where('status', 1);
    //         })->where('status',1)->orderby('created_at','desc');

    //         $productQueryForCount = clone $productDataFilter;

    //         if($request->language==1){
    //             $categoryData = $categoryData->where('title_fr', 'LIKE', '%'.$keyword.'%')->get();
    //             $subcategoryData = $subcategoryData->where('title_fr', 'LIKE', '%'.$keyword.'%')->get();
    //             $productDataFilter = $productDataFilter->where('product_name_fr', 'LIKE', '%'.$keyword.'%')->get();
    //             $productCount = $productQueryForCount->where('product_name_fr', 'LIKE', '%'.$keyword.'%')->count();
    //         }else{
    //             $categoryData = $categoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
    //             $subcategoryData = $subcategoryData->where('title', 'LIKE', '%'.$keyword.'%')->get();
    //             $productDataFilter = $productDataFilter->where('product_name', 'LIKE', '%'.$keyword.'%')->get();
    //             $productCount = $productQueryForCount->where('product_name', 'LIKE', '%'.$keyword.'%')->count();
    //         }
    //         $mainArr = [];

    //         // $categoryArr = [];
    //         // if (!empty($categoryData)) {
    //         //     foreach ($categoryData as $category) {                    
    //         //         if ($request->language == 1) {
    //         //             $title = ($category->title_fr) ? $category->title_fr : $category->title;

    //         //         } else {
    //         //             $title = $category->title ?? '';
    //         //         }  

    //         //         $categoryArr[] = [
    //         //             'type' => 1,
    //         //             'id'=>strval(@$category->id),
    //         //             'title'=>strval(@$title)
    //         //         ];
    //         //     }
    //         // }
    //         // $subCategoryArr = [];
    //         // if (!empty($subcategoryData)) {
    //         //     foreach ($subcategoryData as $sub_category) {                    
    //         //         if ($request->language == 1) {
    //         //             $title = ($sub_category->title_fr) ? $sub_category->title_fr : $sub_category->title;
    //         //         } else {
    //         //             $title = $sub_category->title ?? '';
    //         //         }  

    //         //         $subCategoryArr[] = [
    //         //             'type'=>2,
    //         //             'id'=>strval(@$sub_category->id),
    //         //             'title'=>strval(@$title)
    //         //         ];
    //         //     }
    //         // }
    //         $productDataArr = [];
    //         if (!empty($productDataFilter)) {
    //             foreach ($productDataFilter as $product) {                    
    //                 if ($request->language == 1) {
    //                     $title = ($product->product_name_fr) ? $product->product_name_fr : $product->product_name;
    //                     $image_not_found = 'Image non disponible';
    //                 } else {
    //                     $title = $product->product_name ?? '';
    //                     $image_not_found = 'Image not available';
    //                 }  

    //                 $image = asset('uploads/product/default.png'); 
    //                 if (!empty($product->get_product_images) && $product->get_product_images->count() > 0) {
    //                     $firstImage = $product->get_product_images->first();
    //                     if ($firstImage && $firstImage->image && file_exists(public_path('uploads/product/' . $firstImage->image))) {
    //                         $image = asset('uploads/product/' . $firstImage->image);
    //                     }
    //                 }

    //                 $price = '';
    //                 if (!empty($product->get_product_variants) && $product->get_product_variants->count() > 0) {
    //                     $variant = $product->get_product_variants->first();
    //                     $final_price = $variant->variant_price;
    //                     $price = $setting->currency_symbol . number_format($final_price, 2);
    //                 }

    //                 $productDataArr[] = [
    //                     'type'=>3,
    //                     'id'=>strval(@$product->id),
    //                     'title'=>strval(@$title),
    //                     'price' =>strval($price),
    //                     'image' => $image
    //                 ];
    //             }
    //         }

    //         // $promoteListArr = array_merge($categoryArr,$subCategoryArr,$productDataArr);
    //         $productDataArr['productCount'] =  strval($productCount);
    //         $productDataArr['keyword'] =  strval($keyword);
    //         $result['code']     =  strval(1);
    //         $result['message']  =   'success';
    //         // $result['result'] = $promoteListArr;
    //         $result['result'] = $productDataArr;

    //         $mainResult   =   $result;
    //         return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     }else{

    //             $result['code']     =  strval(0);
    //             $result['message']  =   'something_went_wrong';
    //             $result['result']       =   NULL;

    //             $mainResult   =   $result;
    //             return response ()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
    //     }
    // }

    public function searchList(Request $request)
    {
        $result = [];
        $finalArr = [];

        $keyword = $request->query('keywords');
        $language = $request->query('language', 0);

        if (empty($keyword)) {
            $result['code'] = strval(0);
            $result['message'] = 'keywords_required';
            $result['result'] = null;
            return response()->json(new \App\Http\Resources\V1\SettingResource($result));
        }

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
        })->where('status', 1)->orderBy('created_at', 'desc');

        $productQueryForCount = clone $productDataFilter;

        if ($language == 1) {
            $categoryData = $categoryData->where('title_fr', 'LIKE', '%' . $keyword . '%')->get();
            $subcategoryData = $subcategoryData->where('title_fr', 'LIKE', '%' . $keyword . '%')->get();
            $productDataFilter = $productDataFilter->where('product_name_fr', 'LIKE', '%' . $keyword . '%')->get();
            $productCount = $productQueryForCount->where('product_name_fr', 'LIKE', '%' . $keyword . '%')->count();
        } else {
            $categoryData = $categoryData->where('title', 'LIKE', '%' . $keyword . '%')->get();
            $subcategoryData = $subcategoryData->where('title', 'LIKE', '%' . $keyword . '%')->get();
            $productDataFilter = $productDataFilter->where('product_name', 'LIKE', '%' . $keyword . '%')->get();
            $productCount = $productQueryForCount->where('product_name', 'LIKE', '%' . $keyword . '%')->count();
        }

        $productDataArr = [];
        if (!empty($productDataFilter)) {
            foreach ($productDataFilter as $product) {
                $title = $language == 1
                    ? ($product->product_name_fr ?: $product->product_name)
                    : ($product->product_name ?? '');

                $image = asset('uploads/product/default.png');
                if (!empty($product->get_product_images) && $product->get_product_images->count() > 0) {
                    $firstImage = $product->get_product_images->first();
                    if ($firstImage && $firstImage->image && file_exists(public_path('uploads/product/' . $firstImage->image))) {
                        $image = asset('uploads/product/' . $firstImage->image);
                    }
                }

                $price = '';
                if (!empty($product->get_product_variants) && $product->get_product_variants->count() > 0) {
                    $variant = $product->get_product_variants->first();
                    $final_price = $variant->variant_price;
                    $price =  number_format($final_price, 2);
                }

                $productDataArr[] = [
                    'type' => 3,
                    'id' => strval($product->id),
                    'title' => strval($title),
                    'price' => strval($price),
                    'image' => $image,
                ];
            }
        }

        $result['code'] = strval(1);
        $result['message'] = 'success';
        $result['productCount'] =  strval($productCount);
        $result['keyword'] =  strval($keyword);
        $result['result'] = $productDataArr;

        return response()->json(new \App\Http\Resources\V1\SettingResource($result));
    }

    public function Blog(Request $request)
    {
        $blogData = Blog::orderby('created_at', 'desc')->where('status', 1)->get();

        if (!empty($blogData)) {
            $mainArr = [];
            $blogArr = [];

            foreach ($blogData as $blog) {
                if ($request->language == 1) {
                    $title = $blog->title_fr ?? $blog->title;
                    $short_description = $blog->short_description_fr ?? $blog->short_description;
                    $long_description = $blog->long_description_fr ?? $blog->long_description;
                } else {
                    $title = $blog->title ?? '';
                    $short_description = $blog->short_description ?? '';
                    $long_description = $blog->long_description ?? '';
                }
                $blogList['id'] = strval(@$blog->id);
                $blogList['title'] = strval(@$title);
                $blogList['short_description'] = strval(@$short_description);
                $blogList['long_description'] = strval(@$long_description);
                $blogList['image'] = strval(@$blog->image ? asset(BLOG_PATH . $blog->image) : '');
                $blogList['created_at'] = strval(@$blog->created_at);
                // $blogList['long_description'] = strval(@$blog->long_description);
                $blogArr[] = $blogList;
            }
            $mainArr['blog_list'] = $blogArr;

            $result['code']     =  strval(1);
            $result['message']  =   'success';
            $result['result']       = $mainArr;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {

            $result['code']     =  strval(0);
            $result['message']  =   'no_data_found';
            $result['result']       =  [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function blogDetails(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'blog_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }

        // exit();
        $post = $request->all();

        if ($post) {
            $blog_id = $request->blog_id;
            $blogData = Blog::where('id', $blog_id)->where('status', 1)->first();
            if (!empty($blogData)) {

                if ($request->language == 1) {
                    $title = $blogData->title_fr ?? $blogData->title;
                    $short_description = $blogData->short_description_fr ?? $blogData->short_description;
                    $long_description = $blogData->long_description_fr ?? $blogData->long_description;
                } else {
                    $title = $blogData->title ?? '';
                    $short_description = $blogData->short_description ?? '';
                    $long_description = $blogData->long_description ?? '';
                }

                $mainArr = [];
                $mainArr['id'] = strval(@$blogData->id);
                $mainArr['title'] = strval(@$title);
                $mainArr['short_description'] = strval(@$short_description);
                $mainArr['long_description'] = strval(@$long_description);
                $mainArr['image'] = strval(@$blogData->image ? asset(BLOG_PATH . $blogData->image) : '');
                $mainArr['created_at'] = strval(@$blogData->created_at);

                $result['code']     =  strval(1);
                $result['message']  =  'success';
                $result['result']   = $mainArr;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            } else {
                $result['code']     =  strval(0);
                $result['message']  =   'no_data_found';
                $result['result']       =  [];

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
        } else {

            $result['code']     =  strval(0);
            $result['message']  =   'something_went_wrong';
            $result['result']       =   [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function faq(Request $request)
    {
        $faqData = Faq::where('status', 1)->get();

        if (!empty($faqData)) {
            $mainArr = [];
            $faqArr = [];

            foreach ($faqData as $faq) {
                if ($request->language == 1) {
                    $question_name = $faq->question_name_fr ?? $faq->question_name;
                    $answer = $faq->answer_fr ?? $faq->answer;
                } else {
                    $question_name = $faq->question_name ?? '';
                    $answer = $faq->answer ?? '';
                }

                $faqList['id'] = strval(@$faq->id);
                $faqList['question_name'] = strval(@$question_name);
                $faqList['answer'] = strval(@$answer);
                $faqArr[] = $faqList;
            }
            $mainArr = $faqArr;

            $result['code']     =  strval(1);
            $result['message']  =   'success';
            $result['result']       = $mainArr;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {

            $result['code']     =  strval(0);
            $result['message']  =   'no_data_found';
            $result['result']       =  [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function cms(Request $request)
    {

        if (!empty($request->cms_id)) {
            $cms  =  Cms::where([
                ['status', '=', '1'],
                ['id', '=', $request->cms_id]
            ])->first();
            // dd($cms);


            if ($request->language == 1) {
                $cms_title = $cms->page_name_fr ?? $cms->page_name;
                $cms_description = $cms->mobile_page_content_fr ?? $cms->mobile_page_content;
                $cms_photo = $cms->photo ?? $cms->photo;
            } else {
                $cms_title = $cms->page_name ?? '';
                $cms_description = $cms->mobile_page_content ?? '';
                $cms_photo = $cms->photo ?? $cms->photo;
            }
            $response[] =
                [
                    'id'   =>   isset($cms->id) ? strval($cms->id) : '',
                    'cms_title'   =>   isset($cms_title) ? strval($cms_title) : '',
                    'cms_description'   =>   isset($cms_description) ? strval(urldecode($cms_description)) : '',
                    'cms_photo' => !empty(trim($cms_photo)) ? asset("uploads/cms/" . urldecode($cms_photo)) : '',
                ];
        } else {
            $cms  =   Cms::where([
                ['status', '=', '1']
            ])->get();

            foreach ($cms as $item) {
                if ($request->language == 1) {
                    $cms_title = $item->page_name_fr ?? $item->page_name;
                    $cms_description = $item->mobile_page_content_fr ?? $item->mobile_page_content;
                    $cms_photo = $cms->photo ?? $cms->photo;
                } else {
                    $cms_title = $item->page_name ?? '';
                    $cms_description = $item->mobile_page_content ?? '';
                    $cms_photo = $cms->photo ?? $cms->photo;
                }

                $response[] =
                    [
                        'id'   =>   isset($item->id) ? strval($item->id) : '',
                        'cms_title'   =>   isset($cms_title) ? strval($cms_title) : '',
                        'cms_description'   =>   isset($cms_description) ? strval(urldecode($cms_description)) : '',
                        'cms_photo' => !empty(trim($cms_photo)) ? asset("uploads/cms/" . urldecode($cms_photo)) : '',
                    ];
            }
        }

        if (!empty($response) && count($response) > 0) {
            $responseArr = $response;
            $result['code']             =   strval(1);
            $result['message']          =   'success';
            $result['result']           =   $responseArr;
        } else {
            $result['code']             =   strval(1);
            $result['message']          =   'no_data_found';
            $result['result']           =   [];
        }

        $mainResult = $result;
        return response()->json($mainResult);
    }

    public function getCountrires(Request $reques)
    {
        $countryData = Country::where('status', 1)->orderby('phonecode', 'ASC')->get();

        if (!empty($countryData)) {
            $mainArr = [];
            $countyArr = [];

            foreach ($countryData as $country) {
                $countryList['id'] = strval(@$country->id);
                $countryList['shortname'] = strval(@$country->shortname);
                $countryList['name'] = strval(@$country->name);
                $countryList['phonecode'] = strval(@$country->phonecode);
                $countyArr[] = $countryList;
            }
            $mainArr = ($countyArr) ? $countyArr : NUll;

            $result['code']     =  strval(1);
            $result['message']  =   'success';
            $result['result']   = $mainArr;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {

            $result['code']     =  strval(0);
            $result['message']  =   'no_data_found';
            $result['result']       =  [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function getRegion(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'country_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }
        $countryData = Country::where('status', 1)->where('id', $request->country_id)->first();
        if (!empty($countryData)) {
            $regionData = Region::where('country_id', $countryData->id)->where('status', 1)->get();

            if (!empty($regionData)) {

                $mainArr = [];
                $regionArr = [];

                foreach ($regionData as $region) {
                    if ($request->language == 1) {
                        $title = $region->title_fr ?? $region->title;
                    } else {
                        $title = $region->title ?? '';
                    }
                    $regionList['id'] = strval(@$region->id);
                    $regionList['name'] = strval(@$region->title);
                    $regionArr[] = $regionList;
                }
                $mainArr = $regionArr;

                $result['code']     =  strval(1);
                $result['message']  =  'success';
                $result['result']   = $mainArr;

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
            $result['code']     =  strval(0);
            $result['message']  =   'no_data_found';
            $result['result']   =  [];
            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {

            $result['code']     =  strval(0);
            $result['message']  =  'no_data_found';
            $result['result']   =  [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function getArea(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'country_id' => 'required',
            'region_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }
        $countryData = Country::where('status', 1)->where('id', $request->country_id)->first();
        if (!empty($countryData)) {
            $regionData = Region::where('id', $request->region_id)->where('country_id', $countryData->id)->where('status', 1)->first();
            //dd($regionData);
            if (!empty($regionData)) {
                $areaData = Area::where('region_id', $request->region_id)->where('status', 1)->get();

                if (!empty($areaData)) {
                    //  dd($areaData);
                    $mainArr = [];
                    $areaArr = [];

                    foreach ($areaData as $area) {
                        if ($request->language == 1) {
                            $title = $area->title_fr ?? $area->title;
                        } else {
                            $title = $area->title;
                        }
                        $areaList['id'] = strval(@$area->id);
                        $areaList['name'] = strval(@$area->title);
                        $areaArr[] = $areaList;
                    }
                    $mainArr = $areaArr;

                    $result['code']     =  strval(1);
                    $result['message']  =  'success';
                    $result['result']   = $mainArr;

                    $mainResult   =   $result;
                    return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
                }
                $result['code']     =  strval(0);
                $result['message']  =  'no_data_found';
                $result['result']   =  [];

                $mainResult   =   $result;
                return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
            }
            $result['code']     =  strval(0);
            $result['message']  =   'no_data_found';
            $result['result']   =  [];
            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {

            $result['code']     =  strval(0);
            $result['message']  =   'no_data_found';
            $result['result']       =  [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function getInquiryReason(Request $request)
    {
        $inqReasonData = InquiryReason::where('status', 1)->get();
        $language = $request->language;
        if (!empty($inqReasonData)) {
            $mainArr = [];
            $reasonArr = [];

            foreach ($inqReasonData as $data) {
                if ($language == 1) {
                    $title = $data->title_fr ?? $data->title;
                } else {
                    $title = $data->title;
                }

                $reasonData['id'] = strval(@$data->id);
                $reasonData['name'] = strval(@$title);

                $reasonArr[] = $reasonData;
            }
            $mainArr = $reasonArr;

            $result['code']     = strval(1);
            $result['message']  = 'success';
            $result['result']   = $mainArr;

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {

            $result['code']     =  strval(0);
            $result['message']  =   'no_data_found';
            $result['result']   =  [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }

    public function saveQueries(Request $request)
    {
        $result = [];
        $finalArr = [];
        $validator = \Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone_code' => 'required',
            'phone_number' => 'required',
            'message_title' => 'required',
            'message_description' => 'required',
            'reasonId' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status_code' => strval(0),
                'error' => $validator->messages(),
                'data' => null
            ], 200);
        }
        // exit();
        $post = $request->all();
        if ($post) {
            $inquiry = new Inquiry;
            $inquiry->name = isset($request->name) ? $request->name : '';
            $inquiry->email = isset($request->email) ? $request->email : '';
            $inquiry->phone = isset($request->phone_number) ? $request->phone_number : '';
            $inquiry->phone_code = isset($request->phone_code) ? $request->phone_code : '';
            $inquiry->message = isset($request->message_title) ? $request->message_title : '';
            $inquiry->message_description = isset($request->message_description) ? $request->message_description : '';
            $inquiry->reason_id = isset($request->reasonId) ? $request->reasonId : '';
            $inquiry->status = 1;

            $inquiry->save();


            $admin_notification = new AdminNotifications();
            $admin_notification->inquiry_id = @$inquiry->id;

            $admin_notification->notification_type = 2;

            $admin_notification->save();

            $query_reason = InquiryReason::where('id', $inquiry->reason_id)->first();

            $adminEmail = 'info@liquorjunctionghana.com';
            $adminSubject = 'New Inquiry Received';
            $adminEmailContent = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .content { background-color: #f1f1f1; padding: 15px; border-radius: 5px; }
                .item { margin-bottom: 10px; display: flex; }
                .label { font-weight: bold; width: 150px; }
                .value { margin-left: 10px; flex: 1; }
            </style>
        </head>
        <body>
              <div class='container' style='background-color: #f1f1f1; width: 100%; padding: 20px;margin-top: -130px;'>
                <div class='content'>
                  <div class='header' style='font-size: 18px; font-weight: bold; margin-bottom: 10px;'><b>A new inquiry has been received</b></div>
                    <div class='item'><span class='label'><b>Inquiry ID:</b></span> <span class='value'>{$inquiry->id}</span></div>
                    <div class='item'><span class='label'><b>Name:</b></span> <span class='value'>{$inquiry->name}</span></div>
                    <div class='item'><span class='label'><b>Email:</b></span> <span class='value'>{$inquiry->email}</span></div>
                    <div class='item'><span class='label'><b>Phone:</b></span> <span class='value'>+{$inquiry->phone_code} {$inquiry->phone}</span></div>
                    <div class='item'><span class='label'><b>Message Title:</b></span> <span class='value'>{$inquiry->message}</span></div>
                    <div class='item'><span class='label'><b>Message Description:</b></span> <span class='value'>{$inquiry->message_description}</span></div>
                    <div class='item'><span class='label'><b>Reason:</b></span> <span class='value'>{$query_reason->title}</span></div>
                </div>
            </div>
        </body>
        </html>";
            //  print_r( $adminEmailContent);
            //  die;

            Mail::raw($adminEmailContent, function ($message) use ($adminEmail, $adminSubject) {
                $message->to($adminEmail)
                    ->subject($adminSubject)
                    ->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')); // Ensure this is properly configured in your .env file
            });


            $result['code']     = strval(1);
            $result['message']  = 'query_submitted_success';
            $result['result']   = [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        } else {
            $result['code']     =  strval(0);
            $result['message']  =  'something_went_wrong';
            $result['result']   =  [];

            $mainResult   =   $result;
            return response()->json(new \App\Http\Resources\V1\SettingResource($mainResult));
        }
    }
}
