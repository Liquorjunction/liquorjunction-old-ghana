<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RoleModulePermission;

class RoleBasedPageAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $user_type = Auth::user()->user_type;
        $roleInfo = Role::where('user_type',$user_type)->select('id')->first();
        $allowed_permissions = RoleModulePermission::where('role_id',$roleInfo->id)->first(); 
        if($allowed_permissions->read!=1 ){
            return redirect()->route('/404');
        }
        return $next($request);
    }
}
