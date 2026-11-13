<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\MainUser;
use Illuminate\Http\Request;
use Session;
use Illuminate\Session\Store;
use App\Models\Country;

class StoreController extends Controller
{
    /* In-store listing */
    public function listingInStore(Request $request)
    {
        // $category_id = Session::get('category_id');
        // echo "string";print_r($category_id);exit();
        $stores = MainUser::
            leftJoin('product', 'main_users.id', '=', 'product.supplier_id')
            ->where('main_users.user_type', 2)->where('main_users.status',1)->select('main_users.*','product.in_store');
        if (isset($request->search)) {
            $searchValue = $request->search;
            $stores = $stores->where(function ($query) use ($searchValue) {
                return $query->where('main_users.store_name', 'LIKE', '%' . $searchValue . '%');
            });
        }

        // if ($category_id != "all" && !empty($category_id)) {
        //     $stores = $stores->where('product.category_id',$category_id);
        // }

        $data['stores'] = $stores->groupBy('main_users.id')
            ->orderBy('main_users.id', 'DESC')->paginate(18);
            // echo "<pre>";print_r($data);exit();
        if ($request->ajax()) {
            /* return store list html on search */
            return view('frontEnd.stores.store_list', $data)->render();
        }
        return view('frontEnd.stores.list_offline', $data);
    }

    /* Online store listing */
    public function listingOnline(Request $request)
    {
        // $category_id = Session::get('category_id');
        // echo "string";print_r($category_id);exit();
        $stores = MainUser::
            leftJoin('product', 'main_users.id', '=', 'product.supplier_id')
            ->where('main_users.user_type', 2)->where('main_users.status',1)->select('main_users.*');
        if (isset($request->search)) {
            $searchValue = $request->search;
            $stores = $stores->where(function ($query) use ($searchValue) {
                return $query->where('main_users.store_name', 'LIKE', '%' . $searchValue . '%');
            });
        }

        // if ($category_id != "all" && !empty($category_id)) {
        //     $stores = $stores->where('product.category_id',$category_id);
        // }

        $data['stores'] = $stores->groupBy('main_users.id')
            ->orderBy('main_users.id', 'DESC')->paginate(18);

        if ($request->ajax()) {
            /* return store list html on search */
            return view('frontEnd.stores.store_list', $data)->render();
        }
        return view('frontEnd.stores.list_online', $data);
    }

    public function listingFilter(Request $request)
    {
        $category_id = Session::get('category_id');
        // echo "string";print_r($category_id);exit();
        $stores = MainUser::
            leftJoin('product', 'main_users.id', '=', 'product.supplier_id')
            ->where('main_users.user_type', 2)->where('main_users.status',1)->select('main_users.*');
        if (isset($request->search)) {
            $searchValue = $request->search;
            $stores = $stores->where(function ($query) use ($searchValue) {
                return $query->where('main_users.store_name', 'LIKE', '%' . $searchValue . '%');
            });
        }

        if ($category_id != "all" && !empty($category_id)) {
            $stores = $stores->where('product.category_id',$category_id);
        }

        $data['stores'] = $stores->groupBy('main_users.id')
            ->orderBy('main_users.id', 'DESC')->paginate(18);

        if ($request->ajax()) {
            /* return store list html on search */
            return view('frontEnd.stores.store_list', $data)->render();
        }
        return view('frontEnd.stores.filter', $data);
    }
}
