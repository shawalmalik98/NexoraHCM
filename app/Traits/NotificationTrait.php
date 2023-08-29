<?php

namespace App\Traits;

use App\Models\Notification;
use App\Models\{Employees,HrmSetting};
use App\Models\NotificationStatus;
use App\Jobs\{NotificationJob, PushNotificationJob};

trait NotificationTrait {

    public function send_notification($title , $msg , $url , $to_employee = null , $to_manager1 = null , $to_manager2 = null , $to_hr){
		
		// dispatch(new NotificationJob($title, $msg, $url, $to_employee, $to_manager1, $to_manager2, $to_hr,session('user_session'),date('h:i:s a d F, Y')));
		$notify = new Notification();
		$notify->notification_title = $title;
		$notify->notification_message = $msg;
		$notify->notification_url = $url;
		$notify->notification_to_employee = $to_employee;
		$notify->notification_to_manager1  = $to_manager1;
		$notify->notification_to_manager2  = $to_manager2;
		$notify->notification_from = session('user_session');
		$notify->notification_to_hr = $to_hr;
		$notify->notification_time = date('h:i:s a d F, Y');
		$process = $notify->save();
       
	}

	public function send_notification_api($title , $msg , $url , $to_employee = null , $to_manager1 = null , $to_manager2 = null , $to_hr, $login_user){

		// dispatch(new NotificationJob($title, $msg, $url, $to_employee, $to_manager1, $to_manager2, $to_hr,$login_user,date('h:i:s a d F, Y')));
		$notify = new Notification();
		$notify->notification_title = $title;
		$notify->notification_message = $msg;
		$notify->notification_url = $url;
		$notify->notification_to_employee = $to_employee;
		$notify->notification_to_manager1  = $to_manager1;
		$notify->notification_to_manager2  = $to_manager2;
		$notify->notification_from = $login_user;
		$notify->notification_to_hr = $to_hr;
		$notify->notification_time = date('h:i:s a d F, Y');
		$process = $notify->save();

	}


	public static function get_all_notifications($limit = NULL, $login_user = NULL){

		if(empty($login_user)){
			$login_employee_id = session('user_session');
		}else{
			$login_employee_id = $login_user;
		}
	  
		if(!empty($login_employee_id) || $login_employee_id != ''){

			$login_user_detail = Employees::where('employee_id',$login_employee_id)->get(['employee_department','employee_can_ra','employee_doj'])->toArray();
			
			if(!empty($login_user_detail)){

				if($login_user_detail[0]['employee_department'] == 2 || $login_user_detail[0]['employee_department'] == 1){
					
					// $wherquery = "CONVERT(DATE , notification_time, 100) >= '".$login_user_detail[0]['employee_doj']."'";

					$wherquery = "CONVERT(VARCHAR(10), CONVERT(DATETIME, SUBSTRING(notification_time, CHARINDEX(' ', notification_time) + 3, LEN(notification_time)), 109), 23) > = '".$login_user_detail[0]['employee_doj']."'";

					$my_notifications = Notification::where('notification_to_hr','yes')->whereRaw($wherquery)->orWhere('notification_to_employee', $login_employee_id)->orWhere('notification_to_employee', '*')->limit($limit)->orderBy('notification_id','DESC')->get()->toArray();

					foreach($my_notifications as $notification_loop){

						$notifications_status['viewer_history'] = NotificationStatus::where('viewed_notification', $notification_loop['notification_id'])->where('viewed_by', $login_employee_id)->limit(1)->get()->toArray();

						$notificationResult[] = array_merge( $notification_loop , $notifications_status );

					}
					
					$notification_count = Notification::where(function ($query) use ($login_employee_id,$wherquery) {
						$query->where('notification_to_hr','yes')
						->whereRaw($wherquery)
						->orWhere('notification_to_employee', $login_employee_id);

					})->whereNotIn('notification_id',function($query) use ($login_employee_id){
						$query->select('viewed_notification')
						->from('tbl_notification_status')
						->where('viewed_by', $login_employee_id);
					})->count();

				}else{
				   
					if($login_user_detail[0]['employee_can_ra'] == 1){
					
					   $get_notifications = Notification::where(function ($query) use ($login_employee_id) {
							$query->where('notification_to_manager1' , $login_employee_id)
							->orWhere('notification_to_manager2' , $login_employee_id)
							->orWhere('notification_to_employee', $login_employee_id)
							->orWhere('notification_to_employee', '*');

						})->limit($limit)->orderBy('notification_id','DESC')->get()->toArray();

						foreach($get_notifications as $notification_loop){

							$notifications_status['viewer_history'] = NotificationStatus::where('viewed_notification', $notification_loop['notification_id'])->where('viewed_by', $login_employee_id)->get()->toArray();

							$notificationResult[] = array_merge( $notification_loop , $notifications_status );

						}

						$notification_count = Notification::where(function ($query) use ($login_employee_id) {
							$query->where('notification_to_manager1' , $login_employee_id)
							->orWhere('notification_to_manager2' , $login_employee_id)
							->orWhere('notification_to_employee', $login_employee_id);
	
						})->whereNotIn('notification_id',function($query) use ($login_employee_id){
							$query->select('viewed_notification')
							->from('tbl_notification_status')
							->where('viewed_by', $login_employee_id);
						})->count();
	
					}else{
					
					  $get_notifications = Notification::where('notification_to_hr','no')
					  ->where('notification_to_employee',$login_employee_id)
					  ->orWhere('notification_to_employee', '*')
					  ->limit($limit)
					  ->orderBy('notification_id','DESC')
					  ->get()->toArray();

						foreach($get_notifications as $notification_loop){

							$notifications_status['viewer_history'] = NotificationStatus::where('viewed_notification', $notification_loop['notification_id'])->where('viewed_by', $login_employee_id)->get()->toArray();

							$notificationResult[] = array_merge( $notification_loop , $notifications_status );

						}

						$notification_count = Notification::where(function ($query) use ($login_employee_id) {
							$query->where('notification_to_hr','no')
							->where('notification_to_employee',$login_employee_id);
	
						})->whereNotIn('notification_id',function($query) use ($login_employee_id){
							$query->select('viewed_notification')
							->from('tbl_notification_status')
							->where('viewed_by', $login_employee_id);
						})->count();

					}

				}

				if(!empty($notificationResult)){
					return ['notification' => $notificationResult ,'notification_count' => $notification_count];
				}else{
					return array();
				}
			}else{
				return array();
			}
		}else{
			return array();
		}
	}

	public function send_push_notification_mobile_app($employee_id, $title, $msg){

		$push_notification = HrmSetting::get('push_notification_permission')->toArray();
        if(!empty($push_notification) && $push_notification[0]['push_notification_permission'] == 1){
			if(!empty($employee_id)){
				$fcm_token = Employees::where('employee_isactive', 1)->where('employee_id', $employee_id)->value('employee_fcm_token');
				if(!empty($fcm_token)){
					dispatch(new PushNotificationJob($fcm_token, $title, $msg));
				}
			}
		}
    }
	
}