<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\HrmSetting;

class VerifyAppVersion
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
        $get_app_version = HrmSetting::get(['mobile_app_version'])->toArray();
        if(empty($get_app_version) || $get_app_version[0]['mobile_app_version'] == '' || $get_app_version[0]['mobile_app_version'] == NULL || $get_app_version[0]['mobile_app_version'] != $request->header('apk-version')){
            return response()->json([
                'message' => 'This mobile app version doesn\'t support this opertaion. Please update the mobile app.',
                'status' => FALSE,
            ], 403);
        }
        return $next($request);
    }
}
