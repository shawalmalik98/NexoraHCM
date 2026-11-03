<?php

namespace App\Traits;

use App\Helpers\CustomHelper;
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
		$notify->notification_from = session('user_session') ?? 1;
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


	// Notifications of one employee for the topbar / all notifications page / app, newest first:
	//  - sent to the employee (or to everyone: '*')
	//  - the employee is the approving manager (manager 1 / manager 2)
	//  - the employee did the action (notification_from), so people see what they did
	//  - office notifications (to_hr = yes) only for HR / Finance / Admin role holders who can open the module the
	//    notification links to (e.g. a loan request goes to Finance, a requisition to HR)
	// Returns ['notification' => rows (+ viewer_history), 'notification_count' => unread], or [] when there is none.
	public static function get_all_notifications($limit = NULL, $login_user = NULL){

		$employee_id = (int) (!empty($login_user) ? $login_user : session('user_session'));
		if(empty($employee_id)){
			return [];
		}
		$limit = !empty($limit) ? (int) $limit : 100;

		// newest visible rows, read in batches until the limit is filled
		$rows = [];
		$offset = 0;
		$batch = max(50, $limit * 2);
		while(count($rows) < $limit){
			$chunk = self::notification_candidates($employee_id)->orderBy('notification_id', 'DESC')->offset($offset)->limit($batch)->get()->toArray();
			if(empty($chunk)){
				break;
			}
			foreach($chunk as $row){
				if(self::notification_is_for($row, $employee_id)){
					$rows[] = $row;
					if(count($rows) >= $limit){
						break;
					}
				}
			}
			$offset += $batch;
		}
		if(empty($rows)){
			return [];
		}

		// read status in one query
		$read = NotificationStatus::where('viewed_by', $employee_id)->whereIn('viewed_notification', array_column($rows, 'notification_id'))->pluck('viewed_notification')->flip();
		foreach($rows as &$row){
			$row['viewer_history'] = isset($read[$row['notification_id']]) ? [['viewed_notification' => $row['notification_id']]] : [];
		}
		unset($row);

		return ['notification' => $rows, 'notification_count' => self::unread_notification_count($employee_id)];
	}

	// number of unread notifications of the employee (all of them, not only the listed ones)
	public static function unread_notification_count($employee_id){
		$employee_id = (int) $employee_id;
		$count = self::unread_scope(self::personal_notifications($employee_id), $employee_id)->count();

		// office notifications that are not personal: per module, for the modules the employee can open
		if(self::sees_office_notifications($employee_id)){
			$per_module = self::unread_scope(self::office_only_notifications($employee_id), $employee_id)
				->selectRaw("SUBSTRING_INDEX(SUBSTRING_INDEX(TRIM(LEADING '/' FROM notification_url), '?', 1), '/', 1) as module_path, COUNT(*) as total")
				->groupBy('module_path')->pluck('total', 'module_path');
			foreach($per_module as $module_path => $total){
				if(self::office_module_allowed($employee_id, '/'.$module_path)){
					$count += $total;
				}
			}
		}
		return $count;
	}

	// ids of every unread notification of the employee (for "mark all as read")
	public static function unread_notification_ids($employee_id){
		$employee_id = (int) $employee_id;
		$ids = self::unread_scope(self::personal_notifications($employee_id), $employee_id)->pluck('notification_id')->all();
		if(self::sees_office_notifications($employee_id)){
			foreach(self::unread_scope(self::office_only_notifications($employee_id), $employee_id)->get(['notification_id', 'notification_url']) as $row){
				if(self::office_module_allowed($employee_id, self::notification_module($row->notification_url))){
					$ids[] = $row->notification_id;
				}
			}
		}
		return $ids;
	}

	protected static function unread_scope($query, $employee_id){
		return $query->whereNotExists(function($sub) use ($employee_id){
			$sub->select(\DB::raw(1))->from('tbl_notification_status')
				->whereColumn('tbl_notification_status.viewed_notification', 'tbl_notifications.notification_id')
				->where('tbl_notification_status.viewed_by', $employee_id);
		});
	}

	// sent to the employee / everyone, the employee approves it, or the employee did it
	protected static function personal_notifications($employee_id){
		$me = (string) $employee_id;
		return Notification::where(function($query) use ($me){
			$query->where('notification_to_employee', $me)
				->orWhere('notification_to_employee', '*')
				->orWhere('notification_to_manager1', $me)
				->orWhere('notification_to_manager2', $me)
				->orWhere('notification_from', $me);
		});
	}

	// office notifications (to_hr = yes) that are not personal for the employee
	protected static function office_only_notifications($employee_id){
		$me = (string) $employee_id;
		return Notification::where('notification_to_hr', 'yes')
			->where(function($query) use ($me){ $query->whereNull('notification_to_employee')->orWhere(function($q) use ($me){ $q->where('notification_to_employee', '!=', $me)->where('notification_to_employee', '!=', '*'); }); })
			->where(function($query) use ($me){ $query->whereNull('notification_to_manager1')->orWhere('notification_to_manager1', '!=', $me); })
			->where(function($query) use ($me){ $query->whereNull('notification_to_manager2')->orWhere('notification_to_manager2', '!=', $me); })
			->where(function($query) use ($me){ $query->whereNull('notification_from')->orWhere('notification_from', '!=', $me); });
	}

	protected static function sees_office_notifications($employee_id){
		$roles = CustomHelper::employee_roles($employee_id);
		return in_array('admin', $roles) || in_array('hr', $roles) || in_array('finance', $roles);
	}

	protected static function notification_module($url){
		return '/'.strtok(ltrim((string) $url, '/'), '?#/');
	}

	// an office notification is for HR / Finance / Admin holders who can open its module (Admin: all)
	protected static function office_module_allowed($employee_id, $module){
		$roles = CustomHelper::employee_roles($employee_id);
		if(in_array('admin', $roles)){
			return true;
		}
		if(!in_array('hr', $roles) && !in_array('finance', $roles)){
			return false;
		}
		$map = CustomHelper::permission_map($employee_id);
		return isset($map[$module]) ? CustomHelper::can_access($module, $employee_id) : in_array('hr', $roles);
	}

	// rows that may be for the employee (one indexed query); notification_is_for() decides the office ones
	protected static function notification_candidates($employee_id){
		$me = (string) $employee_id;
		$roles = CustomHelper::employee_roles($employee_id);
		$office_roles = in_array('admin', $roles) || in_array('hr', $roles) || in_array('finance', $roles);

		return Notification::where(function($query) use ($me, $office_roles){
			$query->where('notification_to_employee', $me)
				->orWhere('notification_to_employee', '*')
				->orWhere('notification_to_manager1', $me)
				->orWhere('notification_to_manager2', $me)
				->orWhere('notification_from', $me);
			if($office_roles){
				$query->orWhere('notification_to_hr', 'yes');
			}
		});
	}

	protected static function notification_is_for(array $row, $employee_id){
		$me = (string) $employee_id;
		foreach(['notification_to_employee', 'notification_to_manager1', 'notification_to_manager2', 'notification_from'] as $column){
			if((string) ($row[$column] ?? '') === $me){
				return true;
			}
		}
		if(($row['notification_to_employee'] ?? '') === '*'){
			return true;
		}
		if(strtolower((string) ($row['notification_to_hr'] ?? '')) !== 'yes'){
			return false;
		}
		return self::office_module_allowed($employee_id, self::notification_module($row['notification_url'] ?? ''));
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