<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\web\EmployeeController;
use App\Http\Controllers\api\LoginApiController as LoginController;
use App\Http\Controllers\api\MarkAttendanceApiController as MarkAttendanceController;
use App\Http\Controllers\api\HomeControllerApi as HomeController;
use App\Http\Controllers\api\AlertsApiController as AlertsController;
use App\Http\Controllers\api\WFHApiController as WFHController;
use App\Http\Controllers\api\LeaveRequestApiController as LeavesRequestController;
use App\Http\Controllers\api\AttendanceApiController as AttendanceController;
use App\Http\Controllers\api\AttendanceRequestApiController as AttendanceRequestController;
use App\Http\Controllers\api\LettersApiController as LettersController;
use App\Http\Controllers\api\ResignationApiController as ResignationController;
use App\Http\Controllers\api\RequisitionApiController as RequisitionController;
use App\Http\Controllers\api\AdvanceSalaryApiController as AdvanceSalaryController;
use App\Http\Controllers\api\LeavesApiController as LeavesController;
use App\Http\Controllers\api\EmployeeApiController;
use App\Http\Controllers\api\LoanApiController as LoanController;
use App\Http\Controllers\api\NotificationsApiController as NotificationsController;
use App\Http\Controllers\web\MachineAttendanceController as MachineAttendanceController;
use App\Http\Controllers\api\CustomControllerApi as CustomController;
use App\Http\Controllers\api\InterviewsApiController as InterviewsController;
use App\Http\Controllers\api\PayrollApiController as PayrollController;
use App\Http\Controllers\api\EmployeeExpenseApiController as EmployeeExpenseController;
use App\Http\Controllers\api\HelpdeskApiController as HelpdeskController;
use App\Http\Controllers\api\MeetingsApiController as MeetingsController;
use App\Http\Controllers\api\OfficeLocationControllerApi as OfficeLocationController;
use App\Http\Controllers\api\GeneralControllerApi as GerenalController;
use App\Http\Controllers\api\RplControllerApi as RplController;






/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['BearerTokenChecker','BearerTokenVerifier','DeviceVerification','VerifyAppVersion'])->group(function (){

    Route::prefix('v1/')->group(function(){

        //Mobile App Brust
        Route::post('generate-mobile-app-log', [GerenalController::class, 'get_error_log_mobile_application']);
        
        // login and basic
        Route::middleware('throttle:200,1')->group(function () {
            Route::post('login-process',[LoginController::class, 'login_process'])->withoutMiddleware(['BearerTokenChecker','BearerTokenVerifier','DeviceVerification']);
        });
  
        
        Route::get('request-manager-status', [CustomController::class, 'request_manager_status']);
        Route::post('store-employee-fcm-token', [LoginController::class, 'store_employee_fcm_token']);

        // attendance start
        Route::middleware(['MobileAttendanceBarrier'])->group(function (){
            Route::middleware('throttle:200,1')->group(function () {
                Route::post('checkinattendance', [MarkAttendanceController::class, 'check_in']);
                Route::post('checkoutattendance', [MarkAttendanceController::class, 'check_out']);
                Route::post('offline-attendance', [MarkAttendanceController::class, 'offline_attendance']);
            });
        });
        
        Route::get('my-attendance', [AttendanceController::class, 'personal_attendace']);
        Route::get('get-my-todays-attendance', [AttendanceController::class, 'get_todays_attendance']);
        // attendance end

        // attendance request start
        Route::get('my-attendance-request', [AttendanceRequestController::class, 'my_requests']);
        Route::post('submit-attendance-request', [AttendanceRequestController::class, 'requestfor_attendance']);
        Route::get('attendance-requests', [AttendanceRequestController::class, 'index']);
        Route::post('update-attendance-request', [AttendanceRequestController::class, 'update_attendance_request']);
        Route::post('delete-attendance-request', [AttendanceRequestController::class, 'delete_attendance_request']);
        // attendance request end

        // letters start
        Route::get('letters', [LettersController::class, 'index']);
        Route::post('add-letter', [LettersController::class , 'add_letter']);
        Route::post('update-letter', [LettersController::class , 'update_letter']);
        Route::get('my-comapny-letters', [LettersController::class, 'personal_letter']);
        Route::get('download-employee-letter', [LettersController::class, 'download_employee_letter']);
        Route::get('download-personal-letter', [LettersController::class, 'download_personal_letter']);
        // letters end

        // resignation start
        Route::get('employee-resignation', [ResignationController::class , 'employee_resignation']);
        Route::post('add-employee-resignation', [ResignationController::class, 'add_employee_resignation']);
        Route::get('resignations', [ResignationController::class , 'all_employee_resignation']);
        // resignation end


        // basic start
        Route::get('dashboard',[HomeController::class, 'index']);
        Route::get('sidebar',[HomeController::class, 'sidebar']);
        // basic end


        // alert start
        Route::post('remove-alert', [AlertsController::class, 'alert_remove']);
        Route::get('alert', [AlertsController::class, 'index']);
        Route::post('add-alert', [AlertsController::class , 'add_alert']);
        Route::post('discard-alert', [AlertsController::class, 'delete_alert']);
        Route::post('update-alert', [AlertsController::class , 'update_alert']);
        // alert end

        //Wfh Start
        Route::get('wfh-request', [WFHController::class, 'index']);
        Route::get('my-wfh-requests', [WFHController::class, 'my_wfh_requests']);
        Route::post('submit_wfh_request', [WFHController::class, 'requestfor_wfh']);
        Route::post('update-wfh-request', [WFHController::class, 'update_wfh_request']);
        Route::post('delete-wfh-request', [WFHController::class, 'delete_wfh_request']);
        Route::post('/wfh-approve-request-by-hr', [WFHController::class, 'approve_request']);
        Route::post('/wfh-disapprove-request-by-hr', [WFHController::class, 'disapprove_request']);
        Route::post('/wfh-approve-request-by-lead', [WFHController::class, 'approve_request_by_lead']);
        Route::post('/wfh-disapprove-request-by-lead', [WFHController::class, 'disapprove_request_by_lead']);
        Route::post('/wfh-hard-approve-request', [WFHController::class, 'hard_approve_request_by_hr']);
        Route::post('/wfh-hard-disapprove-request', [WFHController::class, 'hard_disapprove_request_by_hr']);
        Route::get('wfh-today', [WFHController::class, 'wfh_today']);
        //Wfh end

        // Leave Request start
        Route::get('employees-leaves', [LeavesRequestController::class, 'index']);
        Route::get('my-leaves', [LeavesRequestController::class, 'my_leaves']);
        Route::get('download-leave-file', [LeavesRequestController::class, 'download_leave_file']);
        Route::post('applyfor_leave', [LeavesRequestController::class, 'applyfor_personalleave']);
        Route::post('update-leave-request', [LeavesRequestController::class, 'update_leave_request']);
        Route::post('delete-leave-request', [LeavesRequestController::class, 'delete_leave_request']);
        // Leave Request end


        // Leave Entitlement start
        Route::get('/leave-management', [LeavesController::class, 'get_leave_names']);
        // Leave Entitlement end

        // Requisition start
        Route::get('/requisition', [RequisitionController::class, 'index']);
        Route::get('/my-requisition', [RequisitionController::class, 'my_requisition']);
        Route::get('/myrequisitiondetail/{requisition_id}', [RequisitionController::class, 'my_requisition_detail']);
        Route::post('addrequisition', [RequisitionController::class , 'add_requisition']);
        Route::post('updaterequisition', [RequisitionController::class , 'update_requisition']);
        Route::post('delete-requisition-request', [RequisitionController::class, 'delete_requisition_request']);
        // Requisition end

        // Interview start
        Route::get('my-interviews', [InterviewsController::class, 'my_interviews']);
        Route::get('myinterviewdetail/{interviewid}', [InterviewsController::class, 'mydetail']);
        Route::post('submit-interviewer-remarks', [InterviewsController::class, 'submit_interviewer_remarks']);
        Route::post('assign-second-interviewer', [InterviewsController::class, 'changesecond_interviewer']);
        // Interview end
        
        // Advance Salary start 
        Route::get('my-salary-request', [AdvanceSalaryController::class, 'my_salary_request']);
        Route::post('submit_salary_request', [AdvanceSalaryController::class, 'requestfor_advance_salary']);
        Route::post('update-adv-request', [AdvanceSalaryController::class, 'update_advance_salary_request']);
        Route::post('delete-adv-request', [AdvanceSalaryController::class, 'delete_advance_salary_request']);
        // Advance Salary end

        //employee start
        Route::post('change_password_method', [EmployeeApiController::class, 'change_password_function']);
        // employee end

        //notification start
        Route::get('all-notifications', [NotificationsController::class, 'view_all']);
        //notification end

        //loan request start
        Route::get('/my-loan', [LoanController::class, 'my_loan']);
        Route::get('my-loan-request-detail/{loan_request_id}', [LoanController::class, 'my_loan_request_detail']);
        Route::post('loan-request', [LoanController::class, 'new_loan_request']);
        Route::post('update-loan-request', [LoanController::class, 'update_loan_request']);
        Route::post('delete-loan-request', [LoanController::class, 'delete_loan_request']);
        //loan request end

        //payroll start
        Route::get('/my-payroll', [PayrollController::class, 'my_payroll']);
        Route::post('/calculate-my-payroll', [PayrollController::class, 'calculate_my_payroll']);
        Route::get('/individual-payroll-pdf/{payroll_id}',[PayrollController::class,'individual_payroll_pdf']);
        Route::post('/account-identification',[PayrollController::class,'account_identification']);
        //payroll end

        //help desk start
        Route::get('my-tickets', [HelpdeskController::class, 'my_help_desks']);
        Route::post('get-employee-department-hd', [HelpdeskController::class, 'get_employee_department']);
        Route::post('add-help-desk', [HelpdeskController::class, 'add_help_desk']);
        Route::get('my-help-desk-detail/{id}', [HelpdeskController::class, 'my_help_desk_detail']);
        Route::post('update-help-desk-employee', [HelpdeskController::class, 'update_help_desk_employees']);
        Route::post('update-help-desk-department', [HelpdeskController::class, 'update_help_desk_departments']);
        Route::post('ticket-status', [HelpdeskController::class, 'ticket_status']);


        // help desk end
        
        // employee expense start
        Route::get('my-expense',[EmployeeExpenseController::class,'my_expense']);
        Route::post('add-my-expense',[EmployeeExpenseController::class,'add_my_expense']);
        Route::post('delete-my-expense',[EmployeeExpenseController::class,'delete_my_expense']);
        Route::post('update-my-expense',[EmployeeExpenseController::class,'update_my_expense']);
        Route::post('expense-amount-log',[EmployeeExpenseController::class,'expense_amount_log']);
        Route::get('download-employee-expense-file',[EmployeeExpenseController::class,'download_employee_expense_file']);
        // employee expense end

        // meeting start
        Route::get('my-meetings', [MeetingsController::class, 'my_meetings']);
        Route::get('my-meeting-detail/{meeting_id}', [MeetingsController::class, 'my_meeting_detail']);
        Route::post('schedule_meeting', [MeetingsController::class, 'create_meeting']);
        Route::post('add_another_user_schedule_meeting', [MeetingsController::class, 'add_another_user_schedule_meeting']);
        Route::post('cancel-schedule-meeting', [MeetingsController::class, 'cancel_schedule_meeting']);
        // meeting end

        // employee api start
        Route::get('/get-employee-detail', [EmployeeApiController::class, 'get_employee_detail']);
        Route::get('/employee-personal-documents', [EmployeeApiController::class, 'employee_personal_documents']);
        Route::get('/download-personal-document', [EmployeeApiController::class, 'download_personal_document']);
        Route::post('/upload-personal-document', [EmployeeApiController::class , 'store_employee_personal_documents']);
        Route::get('/employee-mapped-location',[EmployeeApiController::class,'employee_office_location']);

        // employee api end

        //rpl api start

        Route::get('/my-rpl-leave', [RplController::class, 'my_rpl_leave']);
        Route::post('/apply-rpl-leave', [RplController::class, 'apply_rpl_leave_request']);
        Route::post('/update-rpl-leave', [RplController::class, 'update_rpl_leave_request']);
        Route::post('/delete-rpl-leave', [RplController::class, 'delete_rpl_leave_request']);
    
       
    });


});

Route::get('fetchallemployees', [EmployeeController::class, 'get_all_employees_for_crm_api']);
Route::get('fetchemployee', [EmployeeController::class, 'get_employee_by_id_for_crm_api']);
Route::get('authenticationprocess', [EmployeeController::class, 'login_process_for_crm_api']);
Route::post('insert-machine-attendance', [MachineAttendanceController::class, 'pull_attendance_from_machine_into_portal']);
