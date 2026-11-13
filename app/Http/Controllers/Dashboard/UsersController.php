<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
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

    public function __construct()
    {

        $this->middleware('auth');

        // Check Permissions

    }

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
        // echo "string";exit();
        $Users = User::find($id);

        if (!empty($Users)) {
            return view("dashboard.users.edit", compact("Users"));
        } else {
            return redirect()->action('adminHome');
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
        // $this->validate($request, [
                    
        //             'profile_picture' => 'required|mimes:png,jpeg,jpg|max:2048',
        //             'name' => 'required|max:40',
        //             'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email',

        //         ],

        //         [
        //             'name.max' => 'The name should not be greater than 40 characters length.',
        //             'email.regex' => 'Please enter valid email',    
        //             'profile_picture.max'=>'The profile photo should be less than 2 MB .',
        //             'profile_picture.mimes'=>'The profile photo must be in .png,.jpg or.jpeg format.',
        //             'profile_picture.required'=>'Profile photo field is required.',
        //         ]
        //     );
            // If user type country admin or sub admin then phone number validation will execute. 
            
        $User = User::find($id);
        if (!empty($User)) {            

            $this->validate($request, [
                    
                    'profile_picture' => 'mimes:png,jpeg,jpg|max:2048',
                    'name' => 'required|max:40',
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email',

                ],

                [
                    'name.max' => 'The name should not be greater than 40 characters length.',
                    'email.regex' => 'Please enter valid email',
                    'profile_picture.max'=>'The profile photo should be less than 2 MB.',
                    'profile_picture.mimes'=>'The profile photo must be in .png,.jpg or.jpeg format.',
                    
                ]
            );

            if ($request->email != $User->email) {
                $this->validate($request, [
                    'email' => 'required|regex:/(.+)@(.+)\.(.+)/i|email',
               ],

                [
                    'email.regex' => 'Please enter valid email'
                ]
              );
            }

            if(Auth::user()->user_type!=1){
                $this->validate($request, [                    
                    'phone_number' => 'required|digits_between:8,15',
                ],
                [
                    'phone_number.required'=> 'The phone number field is required',
                    'phone_number.digits_between'=>'Phone number field should allow 8 to 15 digits ',
                ]
                );
            }  
            if ($request->photo_delete == 1 && $request->profile_picture != "") {
                $this->validate($request, [                    
                    'profile_picture' => 'required',
                ],
                [
                    'profile_picture.required'=>'Profile photo field is required.',
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

            //if ($id != 1) {
            $User->name = $request->name;
            $User->email = $request->email;
            if ($request->password != "") {
                $User->password = bcrypt($request->password);
            }
            $User->permissions_id = isset($request->permissions_id) ? $request->permissions_id : 1;
            //}
            if ($request->photo_delete == 1) {
                // Delete a User file
                if ($User->photo != "") {
                    File::delete($this->getUploadPath() . $User->photo);
                }

                $User->photo = "";
            }
            if ($fileFinalName_ar != "") {
                // Delete a User file
                if ($User->photo != "") {
                    File::delete($this->getUploadPath() . $User->photo);
                }

                $User->photo = $fileFinalName_ar;
            }

            // If user type country admin or sub admin then phone number validation will execute. 
            if(Auth::user()->user_type!=1){
                $User->phone = $request->phone_number;
            }  

            $User->status = 1;
            $User->updated_by = Auth::user()->id;
            $User->save();
            return redirect()->action('Dashboard\UsersController@edit', $id)->with('doneMessage', 'Profile has been updated successfully');
        } else {
            return redirect()->action('Dashboard\UsersController@index');
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


        return view("dashboard.users.change_password");
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

        if(!(Hash::check($request->get('current_password'), Auth::user()->password))) {
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
            return redirect()->back()->with("errorMessageConformPassword", "New password and confirm password do not match.");
        }

        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->save();
        return redirect()->back()->with("doneMessage", "Password changed successfully !");
    }
}
