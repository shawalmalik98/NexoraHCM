<?php

namespace App\Helpers;
use Request;
use App\Models\ActivityLog;


class ActivityTracker
{
    public static function track_activity($activity_detail){
		$add_log = new ActivityLog();
		$add_log->activity_login_history_id = session('token');
		$add_log->activity_detail = $activity_detail;
		$add_log->activity_request_method = Request::method();
		$add_log->activity_url = Request::fullUrl();
		$add_log->activity_employee_ip_address = Request::ip();
		$add_log->activity_created = date('Y-m-d H:i:s');
		$add_log->save();
    }


	public static function track_activity_from_api($activity_detail, $header_token, $app_version = NULL){
		$add_log = new ActivityLog();
		$add_log->activity_login_history_id = $header_token;
		$add_log->activity_detail = $activity_detail . ' -- ' . $app_version;
		$add_log->activity_request_method = Request::method();
		$add_log->activity_url = Request::fullUrl();
		$add_log->activity_employee_ip_address = Request::ip();
		$add_log->activity_created = date('Y-m-d H:i:s');
		$add_log->save();
    }
}