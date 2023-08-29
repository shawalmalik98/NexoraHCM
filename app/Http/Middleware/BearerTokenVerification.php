<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Employees;

class BearerTokenVerification
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
        $get_login_user = Employees::join('tbl_personal_informations', 'tbl_personal_informations.employeeid', '=', 'tbl_employees.employee_id')
		->join('tbl_employee_token','tbl_employee_token.employee_token_emp_id','tbl_employees.employee_id')
        ->where('employee_token',$request->header('Bearer-Token'))
        ->whereNull('employee_token_cancelled_at')
        ->where('employee_isactive',1)
		->get(['employee_id'])
		->toArray();
        if(empty($get_login_user)){
            return response()->json([
                'message' => 'Invalid token passed. No employee found.',
                'status' => FALSE,
            ], 401);
        }
        return $next($request);
    }
}
