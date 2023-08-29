<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeStatusChecker
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
            $get_employee_detail = DB::table('tbl_employees')->where('employee_id', session('user_session'))->get(['employee_isactive']);
            $get_employee_detail = json_decode($get_employee_detail, true);
            if($get_employee_detail[0]['employee_isactive'] == 0){
                $request->session()->flush();
                return redirect('/');
            }
            
        }
        return $next($request);
    }
}
