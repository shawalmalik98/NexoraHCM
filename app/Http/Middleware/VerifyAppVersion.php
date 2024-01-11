<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\HrmSetting;
use Illuminate\Support\Facades\Schema;

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
        if(!empty($request->header('apk-version'))){
            if(Schema::hasTable('tbl_hrm_settings')){
                if (Schema::hasColumn('tbl_hrm_settings', 'mobile_app_version')) {
                    $get_app_version = HrmSetting::get(['mobile_app_version'])->toArray();
                    if(!empty($get_app_version) || $get_app_version[0]['mobile_app_version'] != '' || $get_app_version[0]['mobile_app_version'] != NULL){
                        $apk_version = explode(",",$get_app_version[0]['mobile_app_version']);
                        if(!in_array($request->header('apk-version'), $apk_version)){
                            return response()->json([
                                'msg' => 'Your current application version doesn\'t support this operation. Please update the mobile app.',
                                'status' => FALSE,
                            ], 403);
                        }
                    }
                }
                return $next($request);
            }
            return $next($request);
        }else{
            return response()->json([
                'msg' => 'Your current application version doesn\'t support this operation. Please update the mobile app.',
                'status' => FALSE,
            ], 403);
        }
    }
}
