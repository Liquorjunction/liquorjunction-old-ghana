<?php

namespace App\Http\Controllers\Wholesaler;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MainUser;
use Auth;
use File;
use Illuminate\Config;
use Illuminate\Http\Request;
use Redirect;
use Helper;
use Hash;

class UsersController extends Controller
{

    private $uploadPath = "uploads/users/";

    // Define Default Variables

    // public function __construct()
    // {

    //     $this->middleware('auth');

    //     // Check Permissions

    // }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    public function setUploadPath($uploadPath)
    {
        $this->uploadPath = Config::get('app.APP_URL') . $uploadPath;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // echo "<pre>";print_r($id);exit;
        $Users = MainUser::find($id);
        // echo "<pre>";print_r($Users->toArray());exit();
        if (!empty($Users)) {
            return view("wholesaler.users.edit", compact("Users"));
        } else {
            return redirect()->action('adminwholesalerHome');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // echo "<pre>";print_r($request->toArray());exit();
        $this->validate($request, [
                    
                    'profile_picture' => 'image|mimes:png,jpeg,jpg,svg',
                    'first_name' => 'required|max:80',
                    'store_name' => 'required|max:80',
                    'last_name' => 'required|max:80',
                    'abn_number' =>'required|min:11|max:11',
                    'store_description' =>'required|max:250',
                    'phone' => 'required|min:8|max:15',
                    'country' => 'required',
                    'state' => 'required',
                    'city' => 'required',
                    'street_address' => 'required',
                    'post_code' => 'required|min:4|max:10',
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email',

                ],

                [
                    'abn_number.required' => 'The ABN number field is required',
                    'abn_number.min' => 'The ABN number must be 11 characters',
                    'abn_number.max' => 'The ABN number must be 11 characters',
                    'name.max' => 'The name should not be greater than 40 characters length.',
                    'phone.min' => 'The Phone number must not less than 8 characters',
                    'phone.max' => 'The Phone number must not more than 15 characters',
                    'email.regex' => 'Please enter valid Email',
                    'post_code.min' => 'The Zip code must not less than 4 characters',
                    'post_code.max' => 'The Zip code must not more than 10 characters',
                    'post_code.required' => 'The Zip code field is required'

                ]
            );
        $User = MainUser::find($id);
        if (!empty($User)) {


            $this->validate($request, [
                    
                    'profile_picture' => 'image|mimes:png,jpeg,jpg,svg',
                    'first_name' => 'required|max:80',
                    'store_name' => 'required|max:80',
                    'last_name' => 'required|max:80',
                    'abn_number' =>'required',
                    'store_description' =>'required|max:250',
                    'phone' => 'required',
                    'country' => 'required',
                    'state' => 'required',
                    'city' => 'required',
                    'street_address' => 'required',
                    'post_code' => 'required',
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email',

                ],

                [
                    'abn_number.required' => 'The ABN number field is required',
                    'name.max' => 'The name should not be greater than 40 characters length.',
                    'email.regex' => 'Please enter valid Email',
                    'post_code.required' => 'The Zip code field is required'
                ]
            );

            if ($request->email != $User->email) {
                $this->validate($request, [
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email',
               ],

                [
                    'email.regex' => 'Please enter valid Email'
                ]
              );
            }
            // Start of Upload Files
            $formFileName = "profile_picture";
            $fileFinalName_ar = "";
            if ($request->$formFileName != "") {
                $fileFinalName_ar = time() . rand(
                    1111,
                    9999
                ) . '.' . $request->file($formFileName)->getClientOriginalExtension();

                if ($User->user_type==1) {
                    
                $uploadPath = public_path() . "/uploads/users/";
                }else{
                $uploadPath = public_path() . "/uploads/customer/";

                }

                //$path = $this->getUploadPath();

                $request->file($formFileName)->move($uploadPath, $fileFinalName_ar);
            }
            // End of Upload Files
            $fullname = @$request->first_name.' '.@$request->last_name;
            //if ($id != 1) {
            $User->name = $fullname;
            $User->first_name = $request->first_name;
            $User->store_name = $request->store_name;
            $User->store_description = $request->store_description;
            $User->abn_number = $request->abn_number;
            $User->last_name = $request->last_name;
            $User->phone = $request->phone;
            $User->country = $request->country;
            $User->states = $request->state;
            $User->city = $request->city;
            $User->street_address = $request->street_address;
            $User->post_code = $request->post_code;
            $User->email = $request->email;
            if ($request->password != "") {
                $User->password = bcrypt($request->password);
            }
            $User->permissions_id = isset($request->permissions_id) ? $request->permissions_id : 1;
            //}
            if ($request->photo_delete == 1) {
                // Delete a User file
                if ($User->profile != "") {
                    File::delete($this->getUploadPath() . $User->profile);
                }

                $User->profile = "";
            }
            if ($fileFinalName_ar != "") {
                // Delete a User file
                if ($User->profile != "") {
                    File::delete($this->getUploadPath() . $User->profile);
                }

                $User->profile = $fileFinalName_ar;
            }



            $User->status = 1;
            // $User->updated_by = $id;
            $User->save();
            return redirect()->action('Wholesaler\UsersController@edit', $id)->with('doneMessage', 'Profile has been updated successfully');
        } else {
            return redirect()->action('Wholesaler\UsersController@index');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }


    /**
     * Update all selected resources in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param buttonNames , array $ids[]
     * @return \Illuminate\Http\Response
     */
    public function updateAll(Request $request)
    {
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function changePassword(Request $request)
    {


        return view("wholesaler.users.change_password");
    }


    //update password
    public function updatePassword(Request $request)
    {
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirmation');
        // $validatedData = $request->validate(
        //     [
        //         'current_password' => 'required',
        //         'password' => 'required|string|min:6|confirmed|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{6,}$/',
        //         'password_confirmation' => 'required',
        //     ],

        //     [
        //         'password.confirmed' => 'The new-password and confirm new-password field does not match.',
        //         'password.required' => 'The new password field is required.',
        //         'password_confirmation.required' => 'The confirm new password field is required.',
        //         'password.min' => 'The password must be 6 alphanumeric characters in length',
        //         'password.regex' => 'Your password must be have at least 1 uppercase & 1 lowercase character & 1 speacial character (#?!@$%^&*-) number'

        //     ]
        // );

        // if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
        //     // The passwords matches
        //     return redirect()->back()->with("errorMessage", "Your current password does not match. Please try again.");
        // }
        // if (strcmp($request->get('current_password'), $request->get('password')) == 0) {
        //     //Current password and new password are same
        //     return redirect()->back()->with("errorMessage", "New Password cannot be same as your current password. Please choose a different password.");
        // }
        // if ($password != $password_confirm) {
        //     return redirect()->back()->with("errorMessage", "Password do not match with comfirm password.");
        // }
        //Change Password
        if($request->current_password == ''){
            return redirect()->back()->with("errorMessageCurrentPassword", "Current Password is required"); 
        }

        if(!(Hash::check($request->get('current_password'), auth()->guard('main_user')->user()->password))) {
            // The passwords matches
            return redirect()->back()->with("errorMessageCurrentPassword", "Your current password is incorrect. Please try again.");
            }
        if($request->password == ''){
            return redirect()->back()->with("errorMessageNewPassword", "New Password is required"); 
        }
        if(strcmp($request->get('current_password'), $request->get('password')) == 0)
        {
        //Current password and new password are same
        return redirect()->back()->with("errorMessageNewPassword","New Password cannot be same as your current password. Please choose a different password.");
        }
        
        if($request->password_confirmation == ''){
            return redirect()->back()->with("errorMessageConformPassword", "Confirm Password is required"); 
        }
        
        if ($password != $password_confirm) {
            return redirect()->back()->with("errorMessageConformPassword", "New password and confirm password doesn't match.");
        }

        $user_id = auth()->guard('main_user')->user();
        $user = MainUser::find($user_id->id);
        // $user = auth()->guard('main_user')->user();
        $user->password = bcrypt($request->get('password'));
        $user->save();
        return redirect()->back()->with("doneMessage", "Password changed successfully !");
    }
}
