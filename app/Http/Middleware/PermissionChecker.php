<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\SystemModule;

class PermissionChecker
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
        if(!empty(session('user_session'))){
            $url = '/';
          
            $first_URI = $request->segment(1);
            if($first_URI){
                $url .= $first_URI;
             
            }
            
            $route = $url;

            $get_permission_detail = SystemModule::join('tbl_employee_permission','tbl_system_modules.module_url','=','tbl_employee_permission.module_url')->where('employee_id', session('user_session'))->where('tbl_employee_permission.module_url', $route)->get(['permission_status','module_accessibility'])->toArray();
            if(empty($get_permission_detail) || $get_permission_detail[0]['permission_status'] === '0' || $get_permission_detail[0]['permission_status'] === 0 || $get_permission_detail[0]['module_accessibility'] === '0' || $get_permission_detail[0]['module_accessibility'] === 0){
                return response()->view('errors.403', [], 403);
            }
        }
        return $next($request);
    }
}
