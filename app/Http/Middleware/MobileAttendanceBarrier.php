<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\HrmSetting;

class MobileAttendanceBarrier
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
        $get_permission_detail = HrmSetting::get(['mobile_attendance'])->toArray();
        if(empty($get_permission_detail) || $get_permission_detail[0]['mobile_attendance'] == '0'){
            return response()->json([
                'message' => 'Mobile attendance is temporarily blocked.',
                'status' => FALSE,
            ], 403);
        }
        return $next($request);
    }
}
