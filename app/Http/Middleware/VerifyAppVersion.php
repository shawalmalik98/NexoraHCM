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
                    // allowed versions from System Settings (comma separated); an empty list allows every version
                    $allowed_versions = array_values(array_filter(array_map('trim', explode(',', (string) HrmSetting::value('mobile_app_version')))));
                    if(!empty($allowed_versions)){
                        if(!in_array(trim($request->header('apk-version')), $allowed_versions)){
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
