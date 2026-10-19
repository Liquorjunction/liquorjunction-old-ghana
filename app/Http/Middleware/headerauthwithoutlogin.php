<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\Models\User;

class headerauthwithoutlogin
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


        if(isset($_SERVER['HTTP_USERNAME']) && isset($_SERVER['HTTP_PASSWORD'])){
            $username=$_SERVER['HTTP_USERNAME'];
            $password=$_SERVER['HTTP_PASSWORD'];
        }else{
           $username='';
           $password='';
        }
      
       

       if($username=='liquor' && $password=='liquor@123'){

           return $next($request);
           
       }else{
           $result['code']     =   -1;
           $result['message']  =   'Access Denied';
           $result['data']     =   [];
           $mainResult[]=$result;
           return response()->json($mainResult); 
       }

       
     
        
       
       
         
    }


}
