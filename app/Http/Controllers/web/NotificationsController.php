<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Notification, NotificationStatus, Employees, Interview, Meeting, Resignation, OnBoardingEmployee};
use App\Traits\{LoginEmployee, LogGenerateTrait, NotificationTrait};
use App\Helpers\ActivityTracker;

class NotificationsController extends Controller
{
    use LoginEmployee, LogGenerateTrait , NotificationTrait;
    const MAIN_TITLE = "";
    const TITLE_GLOBAL = "alerts-notifications";

    protected function view_all(Request $request){

        try{

            $title = Self::MAIN_TITLE;

            $login_user = $this->get_login_user_detail();

            $notifications = $this->get_all_notifications();
    
            $send_data = compact('title','login_user','notifications');

            return view('notification.all-notifications', $send_data);

        } catch(\Exception $e){
            $this->create_error_log('View All Notifications', $e);
            $send_data = compact('title');
            return view('errors.500');
        }finally{

            ActivityTracker::track_activity('Access All Notifications Module');
          
        }

    }

    protected function read_notification(Request $request){

        try{

            $notification = $request->input('notificationid');
      
            $user_id = session('user_session');
            if(!empty($user_id) && !empty($notification)){
                // once per employee and notification
                NotificationStatus::firstOrCreate(['viewed_notification' => (int) $notification, 'viewed_by' => (int) $user_id]);
            }else{
                return redirect('');
            }
        }catch(\Exception $e){
            $this->create_error_log("read_notification", $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Something went wrong. Please try again.",
            ]);
        }finally{
            ActivityTracker::track_activity('read_notification');  
        }   

    }

    protected function get_pushnotification(Request $request){

        try{

            $notifications = $this->get_all_notifications();
            if(!empty($notifications['notification'])){
                $unread_count = 0;
                foreach($notifications['notification'] as $mynotifications){
                    $nots[] = '<a href="'.$mynotifications['notification_url'].'" class="dropdown-item '.(($mynotifications['viewer_history'])?'':"notification-dark notification-open").'" '.(($mynotifications['viewer_history'])?'':"data-notificationid='".$mynotifications['notification_id']."'").'><div class="media notification-count-div" data-total_notifications="'.count($notifications['notification']).'"><div class="media-body"><h5 class="" style="margin-bottom:0px;">'. $mynotifications['notification_title'] .'</h5><p class="text-sm">'. $mynotifications['notification_message'] .'</p><p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> '. $mynotifications['notification_time'] .'</p></div></div></a><div class="dropdown-divider"></div>';
                    
                    if(empty($mynotifications['viewer_history'])){
                        $unread_count += 1;
                    }
                }
                if(count($notifications['notification']) > $request->input('notification_count')){

                    return response()->json([
                        'not' => $nots,
                        'notification_checker' => 'yes',
                        'unread_count' => $unread_count
                    ]);

                }else{

                    return response()->json([
                        //'not' => $nots,
                        'notification_checker' => 'no',
                        'unread_count' => $unread_count
                    ]);

                }

            }
        }catch(\Exception $e){
            $this->create_error_log("get_pushnotification", $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Something went wrong. Please try again.",
            ]);
        }
        finally{
            ActivityTracker::track_activity('get_pushnotification');  
        }   


    }

    protected function read_all_notification(){

        try{

            // every unread notification of the employee, not only the 100 listed
            $employee_id = (int) session('user_session');
            foreach(array_chunk($this->unread_notification_ids($employee_id), 1000) as $ids){
                NotificationStatus::insert(array_map(function($id) use ($employee_id){
                    return ['viewed_notification' => $id, 'viewed_by' => $employee_id];
                }, $ids));
            }

        }catch(\Exception $e){
            $this->create_error_log("read_all_notification", $e);
            return response()->json([
                'status' => 'FALSE',
                'msg' => "Something went wrong. Please try again.",
            ]);
        }
        finally{
            ActivityTracker::track_activity('read_all_notification');  
            return redirect('all-notifications');
        }      
           

	}

    protected function notification_alert_detail(){

        try{
            $title = SELF::TITLE_GLOBAL;

            $login_user = $this->get_login_user_detail();

            $interviews =  Interview::join('tbl_interview_candidate','tbl_interviews.interview_candidate_id','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_interviews.interview_department','tbl_departments.department_id')
            ->join('tbl_interview_status','tbl_interview_status.main_interview_id','tbl_interviews.interview_id')
            ->join('tbl_interview_status_level','tbl_interview_status_level.interview_status_level_id','tbl_interview_status.interview_status_level_id')
            ->whereIn('interview_status_id',function($query){
                $query->from('tbl_interview_status')
                ->selectRaw('Max(interview_status_id)')
                ->groupBy('main_interview_id');
            })->where('interview_date', date('Y-m-d'))
            ->get(['interview_id','candidate_name','interview_interviewer1','interview_interviewer2','interview_time','department_name','interview_interviewer_remarks','interview_date','department_id','interview_status_level','interview_type'])->toArray();

            $meetings = Meeting::join('tbl_employees','tbl_meetings.host_id','=','tbl_employees.employee_id')
            ->where('meeting_time','like' ,'%'.date('Y-m-d').'%')
            ->get(['meeting_id','meeting_title','employee_id','meeting_time'])->toArray();

            $birthdays = Employees::join('tbl_personal_informations', 'tbl_employees.employee_id', '=','tbl_personal_informations.employeeid')
            ->join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')
            ->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_isactive',1)
            ->whereRaw('MONTH(dob) = '.date('m'))
            ->whereRaw('LEFT(dob, 2) = '.date('d'))
            // ->whereRaw('date_format(dob,"%m") = '.date('m'))
            // ->whereRaw('date_format(dob,"%d") = '.date('d'))
            ->get(['employee_id','department_name','designation_name','dob'])
            ->toArray();

            $employee_work_ceremony = Employees::join('tbl_departments','tbl_departments.department_id','=','tbl_employees.employee_department')->join('tbl_designations','tbl_designations.designation_id','=','tbl_employees.employee_designation')
            ->where('employee_isactive', 1)
            // ->whereRaw('TIMESTAMPDIFF(YEAR, employee_doj, CURDATE()) >= 1')
            ->whereRaw('TIMESTAMPDIFF(YEAR, employee_doj, NOW()) >= 1')
            ->whereRaw('MONTH(employee_doj) = MONTH(NOW())')
            ->whereRaw('DAY(employee_doj) = DAY(NOW())')
            // ->whereRaw('date_format(employee_doj,"%m") = '.date('m'))
            // ->whereRaw('date_format(employee_doj,"%d") = '.date('d'))
            ->get(['employee_id','employee_code','department_name','designation_name','employee_doj'])->toArray();
          
            $probation_completion = Employees::where('employee_isactive', 1)->where('employee_status','2')
            // ->whereRaw('DATE_ADD(employee_doj, INTERVAL +3 MONTH) = "'.date('Y-m-d').'"')
            ->whereRaw('DATE_ADD(employee_doj, INTERVAL 3 MONTH) = NOW()')
            ->get(['employee_id','employee_code','employee_name','employee_doj'])->toArray();
    
            $notice_period_ending = Resignation::where('resignation_status', '=', 2)->where('resignation_to_date', date('Y-m-d'))->get(['resignation_employee', 'resignation_from_date','resignation_to_date'])->toArray();

            $onboard_employees = OnBoardingEmployee::join('tbl_interview_candidate','tbl_employee_onboarding.onboarding_candidate_id','tbl_interview_candidate.candidate_id')
            ->join('tbl_departments','tbl_employee_onboarding.department_name','=','tbl_departments.department_id')->where('onboard_status',0)->where('doj', date('Y-m-d'))->get(['onboarding_id','candidate_name','tbl_departments.department_name','doj'])->toArray();
        
            $send_data = compact('title', 'login_user', 'interviews', 'meetings','birthdays', 'employee_work_ceremony','probation_completion','notice_period_ending','onboard_employees');

        }
        catch(\Exception $e){
            $this->create_error_log('View notifications alert Detail', $e);
            $send_data = compact('title');
        }
        finally{

            ActivityTracker::track_activity('View notifications alert Detail');

            return view('notification.notification-alert-detail', $send_data);
        }
    }

}
