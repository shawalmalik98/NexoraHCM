<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\EmployeeDevice;

class DeviceVerification
{
    /**1
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {   

        $employee_device = EmployeeDevice::join('tbl_employee_token','tbl_employee_token.employee_token_emp_id','=','tbl_employees_devices.device_employee_id')
        ->where('employee_token',$request->header('Bearer-Token'))
        ->where('device_status',1)->get(['employee_token_emp_id'])->toArray();

        if(empty($employee_device)){
            return response()->json([
                'message' => 'Invalid token passed. No employee found.',
                'status' => FALSE,
            ], 401);
        }

        // when the app sends the phone's id, the token works only from that approved phone
        if(!empty($request->header('Device-Token')) && !EmployeeDevice::where('device_employee_id', $employee_device[0]['employee_token_emp_id'])->where('device_token', $request->header('Device-Token'))->where('device_status', 1)->exists()){
            return response()->json([
                'message' => 'This phone is not approved for your account. Please login again.',
                'status' => FALSE,
            ], 401);
        }
        return $next($request);
    }
}
