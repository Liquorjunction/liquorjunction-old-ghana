<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\MainUser;

class headerauth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // echo "<pre>";print_r($_SERVER);exit;
        if(isset($_SERVER['HTTP_USERNAME']) && isset($_SERVER['HTTP_PASSWORD'])){
            
             $username=$_SERVER['HTTP_USERNAME'];
             $password=$_SERVER['HTTP_PASSWORD'];
        }else{
            $username='';
            $password='';
        }
       
        
        //Comment Shubhan Here
        // if($username=='quickdrop@123' && $password=='12345'){
        //     return $next($request);
        // }else{
        //      $result['code']     =   -2;
        //     $result['message']  =   'Access Denied';
        //     $result['data']     =   [];
        //     $mainResult[]=$result;
        //     return response()->json($mainResult); 
        // }
       
       //New Api Version Flow Shubham Comment 
        if($username=='liquor' && $password=='liquor@123'){

            $user_id = $request->user_id;
            $token = $request->token;



            if($user_id != '' && $token != ''){

                $user_check = MainUser::where('status','!=',2)->where('user_type','=',1)->where('id',$user_id)->where('remember_token','=',$token)->first();
                
                if(!empty($user_check)){
                    if($user_check->status == 0)
                    {
                        $result['code']     =   strval(-3);
                        $result['message']  =   'profile_deleted_inactive';
                        $result['data']     =   [];
                        $mainResult[]   =   $result;
                        return response()->json($mainResult);  
                    }else{
                        return $next($request);
                    }
                       
                 }else{
                    $result['code']     =   strval(-2);
                    $result['message']  =   'profile_deleted_inactive';
                    $result['data']     =   [];
                    $mainResult[]=$result;
                    return response()->json($mainResult);
                    
                 }        
    
            }else{
    
                $result['code']     =   strval(-1);
                $result['message']  =   'user_not_register_our_system';
                $result['data']     =   [];
                $mainResult[]=$result;
                return response()->json($mainResult); 
            }

       }else{
           $result['code']     =   -1;
           $result['message']  =   'access_denied';
           $result['data']     =   [];
           $mainResult[]=$result;
           return response()->json($mainResult); 
       }
        
    }


}
