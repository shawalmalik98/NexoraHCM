<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\web\{LoginController, EmployeeController, HomeController, GadgetController, AttendanceController, MarkAttendanceController, AttendanceRequestController, WFHController, LeavesController, LeavesRequestController, InterviewsController, HolidayController, DepartmentController, DesignationController, VehicleController, LoanController, PersonalInformationController, NotificationController, VehicleAllotmentController, MeetingsController, HelpdeskController, HrmSettingController, ExpenseSheetController, NotificationsController, PettyCashController, ErrorLogController, CustomController, ResignationController, AlertsController, LettersController, RequisitionController, CommandController, PayrollController, AdvanceSalaryController, RoomScreenController, OfficeLocationsController, CredentialsController, OvertimeController, ShiftController, OrganogramController, MachineAttendanceController, TaxSlabController, EmployeeExpenseController, ReportsController, PollingsController, AttendanceDeviceController, LeavesPolicyControlller,RplController, CompanyController, FundController,AppFlowController};
use App\Http\Controllers\api\EmployeeApiController;




 

  Route::middleware(['userAuthentication','userPermission','userStatusChecker'])->group(function (){

      Route::prefix('/login-history')->group(function () {

        Route::get('/', [LoginController::class, 'login_history']);
        Route::get('/login-activity/{id}', [LoginController::class, 'login_user_activity']);

      });

      // Employee Controller Start

      Route::prefix('/employees')->group(function(){

        Route::get('/', [EmployeeController::class, 'index']);
        Route::get('/addemployee', [EmployeeController::class, 'add_employee']);
        Route::get('/editemployee/{employee_id}', [EmployeeController::class, 'edit_employee']);
        Route::get('/employee-documents/{employee_id}', [EmployeeController::class, 'employee_documents']);
        Route::get('/check-candidate-name-list', [EmployeeController::class, 'get_on_board_employees']);
        Route::post('/check-employee-name-list', [EmployeeController::class, 'get_reporting_employees']);

        Route::get('/employee-allotment/{employee_id}', [EmployeeController::class , 'employee_allotment']);
        Route::get('/get_desinations/{departmentid}', [DesignationController::class, 'get_designations']);
        Route::get('/download-employee-document', [EmployeeController::class, 'download_employee_document']);
        
        Route::post('/upload_document', [EmployeeController::class , 'store_employee_documents']);
        Route::post('/edit_upload_document', [EmployeeController::class , 'edit_upload_document']);
        Route::post('/update_employee', [EmployeeController::class, 'update_employee']);
        Route::post('/create_employee', [EmployeeController::class, 'create_employee']);
        Route::post('/submit_edit_salary_request', [EmployeeController::class, 'update_employee_salary']);
        Route::post('/update-onboarding-employee-status', [EmployeeController::class, 'update_onboarding_employee_status']);
        Route::post('/discard_document', [EmployeeController::class, 'delete_employee_document']);
        Route::post('/update-employee-can-ra', [EmployeeController::class, 'update_employee_can_ra']);

        Route::post('/employee-information-download', [EmployeeController::class, 'download_employee_detail']);
        Route::post('/upload-employee-information',[EmployeeController::class, 'upload_employee_record']);

        Route::get('/check-validation', [EmployeeController::class,'checkValidationExists']);

      });



      Route::prefix('/employees-managers')->group(function (){

        Route::get('/', [EmployeeController::class, 'employees_managers']);

        Route::post('/seniormanager', [EmployeeController::class, 'assign_manager']);
        Route::post('/employeemanager', [EmployeeController::class, 'assign_bulk_employee_manager']);
        Route::post('/employees-managers-download',[EmployeeController::class, 'download_employees_managers']);
      });

      Route::prefix('/employee-birthdays')->group(function (){

        Route::get('/', [EmployeeController::class, 'employees_birthdays']);
        Route::post('/employee-birthdays-download',[EmployeeController::class, 'download_employees_birthdays']);

      });

      // Route::prefix('/employee-mobile-access')->group(function (){

      //   Route::get('/', [EmployeeController::class, 'employee_mobile_access']);

      // });

      Route::prefix('/employee-work-anniversay')->group(function (){

        Route::get('/', [EmployeeController::class, 'employee_anniversary']);
        Route::post('/employee-work-anniversay-download',[EmployeeController::class, 'download_employee_anniversary']);


      });

      Route::prefix('/employee-devices')->group(function (){

        Route::get('/', [EmployeeController::class, 'employee_devices']);
        Route::post('/emp-approve-request', [EmployeeController::class, 'approve_request']);
        Route::post('/emp-disapprove-request', [EmployeeController::class, 'disapprove_request']);

      });


      Route::prefix('/employee-onboarding')->group(function (){

        Route::get('/', [EmployeeController::class, 'get_employee_onboading']);
        Route::post('update-employee-onboarding', [EmployeeController::class , 'update_employee_onboarding']);
        Route::get('/discard_boarding_employee/{boarding_id}', [EmployeeController::class, 'delete_employee_on_boarding']);

      });

      // Route::prefix('/employee-location')->group(function (){

      //   Route::get('/', [EmployeeController::class, 'employees_location_detail']);
      // });

      //EmployeeController End

      //GadgetController Start

      Route::prefix('/gadgets-inventory')->group(function () {

        Route::get('/', [GadgetController::class, 'index']);
        Route::get('/add-new-gadget', [GadgetController::class, 'add_new_gadget_form']);
        Route::get('/gadget-detail/{gadget_id}', [GadgetController::class, 'view_gadget']);
        Route::get('/discard_gadget/{gadget_id}', [GadgetController::class, 'delete_gadget']);

        Route::post('/assign_gadget', [GadgetController::class, 'assign_gadget_employee']);
        Route::post('/update_gadget', [GadgetController::class, 'update_gadget_details']);
        Route::post('/assign_form_submit', [GadgetController::class, 'assign_post_request']);
        Route::post('/update_assinged_gadget', [GadgetController::class, 'assign_update_request']);

      });

      Route::prefix('/gadgets-allotment')->group(function () {

        Route::get('/', [GadgetController::class, 'gadget_allotment']);

      });

      //GadgetController End

      //AttendanceController Start

      Route::prefix('/attendance')->group(function () {

        Route::get('/', [AttendanceController::class, 'all_attendance']);
        Route::get('/get-department-managers', [AttendanceController::class, 'get_department_managers']);
        Route::get('/attendance-report', [AttendanceController::class, 'attendance_report']);
        Route::get('/attendance-calender', [AttendanceController::class, 'attendance_calender']);

        Route::post('/update_attendance', [AttendanceController::class, 'update_employee_attendance']);
        Route::post('/update_marked_attendance', [AttendanceController::class, 'update_marked_attendance']);
        Route::post('/update_marked_attendance_absent', [AttendanceController::class, 'update_marked_attendance_absent']);

        Route::post('/share-employee-attendance-report', [AttendanceController::class, 'share_employee_attendance_report']);



      });

      Route::prefix('/daily-attendance-report')->group(function () {

        Route::get('/',[AttendanceController::class, 'get_employee_daily_attendance']);
        Route::post('/upload-attendance-report',[AttendanceController::class, 'upload_attendance_record']);
        Route::post('/export-attendance-data', [AttendanceController::class , 'export_data_into_dat_file']);
        Route::get('/download-daily-attendance', [AttendanceController::class, 'download_daily_attendance_all_employee']);
        Route::post('/delete-attendance-record', [AttendanceController::class, 'delete_attendance_record']);

      });

      //AttendanceController End

      //AttendanceRequestController Start

      Route::prefix('/attendance-requests')->group(function () {

        Route::get('/', [AttendanceRequestController::class, 'index']);

        Route::post('attendanceapproverequest', [AttendanceRequestController::class, 'attendance_request']);
        Route::post('attendance-check-in-time', [AttendanceRequestController::class, 'attendance_check_in_time']);
        Route::post('attendance-check-out-time', [AttendanceRequestController::class, 'attendance_check_out_time']);


      });

      //AttendanceRequestController End

      //Leave Controller Start


      Route::prefix('/manage-leaves')->group(function () {

        Route::get('/', [LeavesController::class, 'index']);
        Route::get('/leave-detail/{leave_id}', [LeavesController::class,'leave_entitlement_detail']);

        Route::post('/update-employee-leave-entitlement',[LeavesController::class, 'update_employee_leave_entitlement']);

        Route::post('manage-leaves-download',[LeavesController::class, 'download_manage_leaves']);

        Route::post('/upload-employee-leave',[LeavesController::class, 'upload_leave_record']);
      });

      Route::prefix('/leave-management')->group(function () {

        Route::get('/', [LeavesController::class, 'leave_management']);
        Route::post('/add-leave', [LeavesController::class, 'add_leave']);

        Route::get('/discard_leave/{leave_id}', [LeavesController::class, 'delete_leave_management']);

        Route::post('/update-leave-management', [LeavesController::class, 'update_leave_management']);

      });

      //Leave Controller End

      //Leave Request Controller Start

      Route::prefix('/employees-leaves')->group(function () {

        Route::get('/', [LeavesRequestController::class, 'index']);
        Route::get('/download-leave-file', [LeavesRequestController::class, 'download_leave_file']);
        Route::post('/leaveapproverequest', [LeavesRequestController::class, 'leave_request']);

      });

      //Leave Request Controller End

      

      //Work From Home Request Start

      Route::prefix('/wfh-requests')->group(function () {

        Route::get('/', [WFHController::class, 'index']);
        Route::post('all-wfh-request', [WFHController::class, 'all_request_wfh']);
        Route::post('wfhapproverequest', [WFHController::class, 'wfh_request']);

      });

      //Work From Home Request End

      //Adavnce Salary Request Start

      Route::prefix('/all-salary-request')->group(function () {

        Route::get('/', [AdvanceSalaryController::class, 'index']);
        Route::post('advancesalaryapproverequest', [AdvanceSalaryController::class, 'advance_salary_request_approval']);

      });

      //Adavnce Salary Request End


      // Interviews Controller Start

      Route::prefix('/interviews')->group(function () {

        Route::get('/', [InterviewsController::class, 'manage_all']);
        Route::get('/interviewdetail/{interviewid}', [InterviewsController::class, 'detail']);

        Route::post('/scheduleinterview', [InterviewsController::class, 'schedule_interview']);
        Route::post('/modify_interview', [InterviewsController::class, 'update_interview_details']);
        Route::post('/interviewer-hired', [InterviewsController::class,'interviewer_employee_hired']);

        Route::post('/interviewer-status', [InterviewsController::class,'interviewer_employee_status']);

      });

      Route::prefix('/candiddate-interviews')->group(function () {

        Route::get('/', [InterviewsController::class, 'candidate_interviews_list']);
        Route::get('candidate-detail/{id}', [InterviewsController::class, 'interview_candidate_detail']);


      });

      //Interviews Controller End

      //Holiday Controller Start
      Route::prefix('/holidays')->group(function () {

        Route::get('/', [HolidayController::class, 'index']);
        Route::post('/addholiday', [HolidayController::class , 'add_holiday_request']);
        Route::post('/holidays-download', [HolidayController::class , 'holidays_download']);

      });

      //Holiday Controller End

      //Department Controller Start
      Route::prefix('/departments')->group(function () {

        Route::get('/', [DepartmentController::class, 'department']);
        Route::get('/discard_department/{department_id}', [DepartmentController::class, 'delete_department']);

        Route::get('/department_employees', [DepartmentController::class, 'department_managers']);

        Route::post('/add_department', [DepartmentController::class, 'add_department']);
        Route::post('/update_department', [DepartmentController::class, 'update_department']);

      });

      //Department Controller End

      //Designation Controller Start

      Route::prefix('/designations')->group(function () {

        Route::get('/', [DesignationController::class, 'designation']);
        Route::get('/discard_designation/{designation_id}', [DesignationController::class, 'delete_designation']);

        Route::post('/add_designation', [DesignationController::class, 'add_designation']);
        Route::post('/update_designation', [DesignationController::class, 'update_designation']);

      });

      //Designation Controller End

      //vechile Controller Start

      Route::prefix('/vehicles')->group(function () {

        Route::get('/',[VehicleController::class, 'index']);
        Route::get('/add-vehicle',[VehicleController::class, 'add_vehicle_form']);
        Route::get('/vehicles-allotment',[VehicleController::class, 'vehicles_allotment']);
        Route::get('/vehicle-detail/{vehicle_id}',[VehicleController::class, 'vehicle_detail']);
        Route::get('/delete_maintenance_detail/{maintenance_id}', [VehicleController::class, 'delete_maintenance']);
        Route::get('/delete_allotment_detail/{maintenance_id}', [VehicleController::class, 'delete_allotment']);

        Route::post('/create_maintenance_detail', [VehicleController::class, 'create_maintenance']);
        Route::post('/update_maintenance_detail', [VehicleController::class, 'update_maintenance']);
        Route::post('/addvehicledata', [VehicleController::class, 'create_vehicle_function']);
        Route::post('/updatevehicledata', [VehicleController::class, 'update_vehicle_function']);
        Route::post('/upload_vehicles_images', [VehicleController::class , 'store_vehicle_images']);
        Route::post('/create_allotment_detail', [VehicleController::class , 'create_allotment']);
        Route::post('/update_allotment_detail', [VehicleController::class , 'update_allotment']);

        Route::post('/vehicles-download', [VehicleController::class , 'download_vehicles']);

        Route::get('/download-vehicle-document', [VehicleController::class , 'download_vehicle_document']);


      });

      //Vechile Controller End

      //Loan Controller Start

      Route::prefix('/loans')->group(function () {

        Route::get('/', [LoanController::class, 'index']);
        Route::get('/loan-detail/{loan_id}', [LoanController::class, 'loan_detail']);

        Route::post('/add-new-loan', [LoanController::class, 'grant_new_loan']);
        Route::post('/update_loan', [LoanController::class, 'update_loan_details']);
        Route::post('/payinstallment', [LoanController::class, 'pay_installment']);
        Route::post('delete-loan', [LoanController::class, 'delete_loan']);
        Route::get('/get_loan_installement_source/{id}', [LoanController::class, 'get_loan_installement_source']);
        Route::post('/employee-loan-download', [LoanController::class, 'download_loan_details']);


      });
      //Loan Controller End

        //Loanrequest Controller Start

      Route::prefix('/all-loan-request')->group(function () {

        Route::get('/', [LoanController::class, 'all_loan_request']);
        Route::post('loanapproverequest', [LoanController::class, 'loan_request_approval']);

      });
       //Loanrequest Controller end

      //PersonalInfromation Controller Start

      // Route::prefix('/personal-information')->group(function () {

      //   Route::get('/personal-information', [PersonalInformationController::class, 'index']);
      //   Route::get('/view-employee-information-form/{employeeid}',[PersonalInformationController::class, 'view_employee_info_form']);
      //   Route::get('/print-employee-information-form/{employeeid}',[PersonalInformationController::class, 'print_employee_info_form']);

      //   Route::post('/savemypersonalinformation', [PersonalInformationController::class, 'submit_information']);

      // });

      //personal Infromation Controller End

      //Hrm Setting Controller Start

      Route::prefix('/system-settings')->group(function () {

        Route::get('/', [HrmSettingController::class , 'index']);
        Route::post('/discard_mac_address', [HrmSettingController::class, 'delete_mac_address']);
        Route::post('/discard_salary_division', [HrmSettingController::class, 'delete_salary_division']);
        Route::post('/discard_email_access', [HrmSettingController::class, 'delete_email_access_module_employee']);

        Route::post('/update-hrm-settings', [HrmSettingController::class , 'create']);
        Route::post('/send-test-mail', [HrmSettingController::class , 'send_test_mail']);
        Route::post('/add-email', [HrmSettingController::class , 'add_email']);

        Route::post('/download-system-setting', [HrmSettingController::class , 'download_system_setting']);

        

      });

      //Hrm Setting Controller End


      //System Error Log Controller start

      Route::prefix('/system-error-logs')->group(function () {

        Route::get('/', [ErrorLogController::class, 'index']);

      });

      //System Error Log Controller start end


      //All meetings start

      Route::prefix('/meetings')->group(function () {

        Route::get('/', [MeetingsController::class, 'index']);

      });
      //All meetings end

      Route::prefix('all-tickets')->group(function () {

        Route::get('/', [HelpdeskController::class, 'all_help_desks']);
        Route::get('download-help-desk-file', [HelpdeskController::class, 'download_help_desk_attachment']);

      });


      //Expensesheet Controller start

      Route::prefix('/daily-expense')->group(function () {

        Route::get('/', [ExpenseSheetController::class, 'index']);

        Route::post('addexpense', [ExpenseSheetController::class , 'add_expense']);
        Route::post('/updateexpense', [ExpenseSheetController::class , 'update_expense']);
        Route::get('download-expense-file', [ExpenseSheetController::class, 'download_expense_file']);
        Route::post('/daily-expense-download', [ExpenseSheetController::class, 'download_expense']);
        Route::post('/expenselock', [ExpenseSheetController::class, 'lock_expense']);

      });

      //Expensesheet Controller End

      //Pettycash Controller start

      Route::prefix('/petty-cash')->group(function () {

        Route::get('/', [PettyCashController::class, 'index']);

        Route::post('addcash', [PettyCashController::class , 'add_cash']);
        Route::post('/updatecash', [PettyCashController::class , 'update_cash']);
        Route::get('download-cash-file', [PettyCashController::class, 'download_cash_file']);
        Route::post('/petty-cash-download', [PettyCashController::class , 'download_pettycash']);

      });

      //Pettycash Controller End


      //Alerts Controller start

      Route::prefix('/alerts')->group(function () {

        Route::get('/', [AlertsController::class, 'index']);

        Route::post('discard_alert', [AlertsController::class, 'delete_alert']);
        Route::post('addalert', [AlertsController::class , 'add_alert']);
        Route::post('updatealert', [AlertsController::class , 'update_alert']);

      });

      //Alerts Controller End

      //Letters Controller start

      Route::prefix('/letters')->group(function () {

        Route::get('/', [LettersController::class, 'index']);
        Route::post('addletter', [LettersController::class , 'add_letter']);
        Route::post('updateletter', [LettersController::class , 'update_letter']);
        Route::get('download-employee-letter', [LettersController::class, 'download_employee_letter']);
      });

      Route::prefix('/letter-template')->group(function () {
        Route::get('', [LettersController::class , 'letter_template']);
        Route::post('employee-letter-template', [LettersController::class , 'employee_letter_template']);
        Route::post('add-new-letter-template', [LettersController::class , 'add_new_letter_template']);
        Route::get('letter-pdf/{letter_id}',[LettersController::class , 'letter_pdf']);
      });

      Route::prefix('/employee-letter')->group(function () {
        Route::get('', [LettersController::class , 'employee_letter']);
        Route::get('employee-letter-pdf/{letter_id}',[LettersController::class , 'employee_letter_pdf']);
        Route::post('insert-template-in-document',[LettersController::class , 'insert_template_in_document']);
      });

      //Letters Controller End

      //requisition Controller start

      Route::prefix('/requisition')->group(function () {

        Route::get('/', [RequisitionController::class, 'index'])->name('req.index');
        Route::get('/requisitiondetail/{requisition_id}', [RequisitionController::class, 'requisition_detail']);
        Route::post('/requisition-status', [RequisitionController::class,'requisition_status']);


      });

      Route::prefix('/my-requisition')->group(function () {

        Route::get('/', [RequisitionController::class , 'my_requisition']);
        Route::get('/myrequisitiondetail/{requisition_id}', [RequisitionController::class, 'my_requisition_detail']);

      });

      //Requisition Controller End


      //Resignation Controller start

      Route::prefix('/resignations')->group(function () {

        Route::get('/', [ResignationController::class , 'all_employee_resignation']);
        Route::post('resignationapproverequest', [ResignationController::class, 'resignation_request_approval']);
        Route::post('/resignation-reject', [ResignationController::class, 'reject_resignation']);

      });


      //Resignation Controller end

    
      // payroll routes

      // Route::prefix('/payroll')->group(function () {
      Route::group(['prefix' => 'payroll','middleware' => ['payrollVerification']], function() {

        Route::get('/',[PayrollController::class,'index']);
        Route::get('/edit-payroll/{payroll_id}',[PayrollController::class,'edit_payroll']);
        Route::post('/generate-payroll',[PayrollController::class,'generate_payroll']);
        Route::post('/get-employee-payroll',[PayrollController::class,'get_employee_payroll']);
        Route::post('/edit-individaul-employee-payroll',[PayrollController::class,'edit_individaul_employee_payroll']);
        Route::get('/download-payroll-pdf',[PayrollController::class,'download_payroll_pdf']);
        Route::post('/get-employee-payroll-month',[PayrollController::class,'get_employee_payroll_month']);
        Route::post('/payroll-disbursement',[PayrollController::class,'payroll_disbursement']);
        Route::post('/payroll-delete',[PayrollController::class,'payroll_delete']);
        Route::post('/payroll-delete-by-department',[PayrollController::class,'payroll_delete_by_department']);

      });

      Route::prefix('/tax-slab')->group(function () {

        Route::get('/',[TaxSlabController::class,'index']);
        Route::get('/download-tax-collection-pdf',[TaxSlabController::class,'download_tax_collection_pdf']);
        Route::post('/add-tax-slab',[TaxSlabController::class,'add_tax_slab']);
        Route::post('/edit-tax-slab',[TaxSlabController::class,'edit_tax_slab']);

      });

      Route::prefix('/tax-collection')->group(function () {

        Route::get('/',[TaxSlabController::class,'tax_collection']);

      });



      Route::prefix('/rooms')->group(function () {

        Route::get('/',[RoomScreenController::class,'index']);
        Route::post('/addroom',[RoomScreenController::class,'add_room']);
        Route::post('/updateroom',[RoomScreenController::class,'update_room']);
        Route::post('/deleteroom',[RoomScreenController::class,'delete_room']);

      });

      Route::prefix('/locations')->group(function () {

        Route::get('/',[OfficeLocationsController::class,'index']);
        Route::post('/addlocation',[OfficeLocationsController::class,'add_location']);
        Route::post('/updatelocation',[OfficeLocationsController::class,'update_location']);
        Route::post('/deletelocation',[OfficeLocationsController::class,'delete_location_request']);
        Route::post('/upload-location-information',[OfficeLocationsController::class, 'update_employee_location_record']);
        Route::post('/upload-office-location',[OfficeLocationsController::class, 'upload_employee_location_record']);
        

      });

      Route::prefix('/attendance-devices')->group(function () {

        Route::get('/',[AttendanceDeviceController::class,'index']);
        Route::post('/adddevice',[AttendanceDeviceController::class,'add_device']);
        Route::post('/updatedevice',[AttendanceDeviceController::class,'update_device']);
        Route::post('/deletedevice',[AttendanceDeviceController::class,'delete_device']);

      });

      Route::prefix('/linked-in-post')->group(function () {

        Route::get('/',[HomeController::class,'linked_post_index']);
        Route::post('/add-linked-post',[HomeController::class,'linked_post_add']);


      });

      Route::prefix('/shift-management')->group(function (){

        Route::get('/', [ShiftController::class, 'index']);
        Route::get('add-shift',[ShiftController::class, 'add_shift']);
        Route::post('get-employee-shift',[ShiftController::class, 'get_employee_shift']);
        Route::post('add-employee-shift',[ShiftController::class, 'add_employee_shift']);
        Route::post('shift-management-download',[ShiftController::class, 'download_shift_management']);


      });

      Route::prefix('/employee-expense')->group(function(){
        Route::get('/',[EmployeeExpenseController::class,'index']);
        Route::post('/approved-expense',[EmployeeExpenseController::class,'approved_expense']);
        Route::post('/disapproved-expense',[EmployeeExpenseController::class,'disapproved_expense']);
        Route::post('/approved-expense-manager',[EmployeeExpenseController::class,'approved_expense_manager']);
        Route::post('/disapproved-expense-manager',[EmployeeExpenseController::class,'disapproved_expense_manager']);
      });

      Route::prefix('/overtime-management')->group(function () {

        Route::get('/',[OvertimeController::class,'index']);
        Route::get('/overtime-detail/{id}', [OvertimeController::class, 'overtime_detail']);
        Route::post('/overtime-record-insert', [OvertimeController::class, 'overtime_record_insert']);
        Route::post('/multiple-overtime-record-insert-or-remove', [OvertimeController::class, 'multiple_overtime_record_insert_or_remove']);
        Route::post('/delete-overtime-record-insert', [OvertimeController::class, 'delete_overtime_record_insert']);
        Route::post('/edit-overtime-record', [OvertimeController::class, 'edit_overtime_record']);
        Route::post('/overtime-management-download', [OvertimeController::class, 'download_overtime_report']);
        Route::post('/overtime-detail/overtime-detail-download', [OvertimeController::class, 'overtime_detail_download']);

      });

      Route::prefix('/organogram')->group(function(){
        Route::get('/', [OrganogramController::class, 'index']);
      });


      Route::prefix('/machine-attendance-logs')->group(function () {
        Route::get('/', [MachineAttendanceController::class, 'index']);
      });

      Route::prefix('/shift-time-request')->group(function(){
        Route::get('',[ShiftController::class, 'shift_time_request']);
        Route::post('disapproved-shift-time-request',[ShiftController::class, 'disapproved_shift_time_request']);
        Route::post('approved-shift-time-request',[ShiftController::class, 'approved_shift_time_request']);
      });

      Route::prefix('/polling')->group(function(){
        Route::get('/', [PollingsController::class, 'index']);
        Route::get('pollingdetail/{polling_id}', [PollingsController::class, 'polling_detail']);
        Route::post('addpolling',[PollingsController::class, 'add_polling']);
        Route::post('updatepolling',[PollingsController::class, 'update_polling']);
        Route::post('discard-polling',[PollingsController::class, 'delete_polling']);
        Route::post('polling-options-log',[PollingsController::class,'polling_options_log']);

      });

      Route::prefix('/employee-bank')->group(function (){

        Route::get('/', [EmployeeController::class, 'employees_bank_detail']);
        Route::post('/employee-bank-download',[EmployeeController::class, 'download_employees_bank_detail']);


      });

      Route::prefix('/monthly-attendance')->group(function () {

        Route::get('/', [AttendanceController::class, 'monthly_attendance']);
        Route::post('/download-monthly-attendance',[AttendanceController::class, 'download_monthly_attendance']);


      });

      Route::prefix('/employment-status')->group(function (){

        Route::get('/', [EmployeeController::class, 'employment_status']);
        Route::post('/add-employment-status', [EmployeeController::class, 'add_employment_status']);
        Route::post('/update-employment-status', [EmployeeController::class, 'update_employment_status']);
        Route::get('/discard-employee-status/{employment_id}', [EmployeeController::class, 'delete_employee_status']);

      });

      
        Route::prefix('/funds')->group(function (){

          Route::get('/', [FundController::class, 'index']); 
          Route::post('/add-fund', [FundController::class, 'add_fund']);
          Route::post('/update-fund', [FundController::class, 'update_fund']);
          Route::post('/discard-fund', [FundController::class, 'delete_fund']);

        });

        Route::prefix('/employee-fund')->group(function (){
          Route::get('/', [FundController::class, 'employee_fund']);
          Route::get('/get-source-data', [FundController::class, 'get_employee_fund_data']);

        });



      Route::prefix('/reports')->group(function (){
        Route::get('/employee-report',[ReportsController::class, 'employee_demographics']);
      });

      
      Route::prefix('/attendance-locker')->group(function () {

        Route::get('/', [AttendanceController::class, 'attendance_locker_sheet']);
        Route::post('/lock-attendance-sheet', [AttendanceController::class, 'lock_attendance_sheet']);

      });

      Route::group(['prefix' => 'daily-wages-payroll','middleware' => ['payrollVerification']], function() {

        Route::get('',[PayrollController::class, 'daily_wages_payroll']);
        Route::get('/employee',[PayrollController::class, 'daily_wages_employee']);
        Route::post('individual-generate-daily-wages',[PayrollController::class, 'individual_generate_daily_wages']);
        Route::post('get-employee-daily-wages',[PayrollController::class, 'get_employee_daily_wages']);
        Route::post('delete-daily-wages',[PayrollController::class, 'delete_daily_wages']);
        Route::get('edit-daily-wages/{wage_id}',[PayrollController::class, 'edit_daily_wages']);
        Route::post('update-daily-wages',[PayrollController::class, 'update_daily_wages']);
        Route::post('wages-disbursement',[PayrollController::class, 'wages_disbursement']);
        Route::post('wages-delete-by-department',[PayrollController::class, 'wages_delete_by_department']);
        Route::post('delete-selected-daily-wages',[PayrollController::class, 'delete_selected_daily_wages']);
        Route::get('download-daily-wages-pdf',[PayrollController::class,'download_daily_wages_pdf']);
        Route::post('calculate-daily-wages',[PayrollController::class,'calculate_daily_wages']);
        

      });

      Route::withoutMiddleware(['userPermission'])->group(function () {

          Route::post('authenticate', [LoginController::class, 'login_process_sap'])->withoutMiddleware('userAuthentication');

          Route::get('/employee-location', [EmployeeController::class, 'employees_location_detail']);

          Route::get('/',[HomeController::class, 'index']);

          Route::get('/user-detail/{id}', [HomeController::class , 'get_user_detail']);

          Route::get('/my-loan', [LoanController::class, 'my_loan']);
          Route::post('/loan-request', [LoanController::class, 'new_loan_request']);
          Route::get('/my-loan-request-detail', [LoanController::class, 'my_loan_request_detail']);

          Route::post('/expense-graph',[HomeController::class, 'expense_graph']);
          Route::post('/department-employee-graph',[HomeController::class, 'department_employee_graph']);

          Route::post('/remove_alert/{alert_id}', [AlertsController::class, 'alert_remove']);
          Route::get('/alert-employee-mark-as-read', [AlertsController::class, 'alert_mark_as_read']);

          Route::post('/addrequisition', [RequisitionController::class , 'add_requisition']);
          Route::post('/updaterequisition', [RequisitionController::class , 'update_requisition']);
          Route::post('/delete-requisition-request', [RequisitionController::class, 'delete_requisition_request']);

          Route::get('/my-comapny-letters', [LettersController::class, 'personal_letter']);
          Route::get('/download-personal-letter', [LettersController::class, 'download_personal_letter']);

          Route::get('/employee-company-assets', [EmployeeController::class, 'my_personal_company_asset']);
          Route::post('/upload_personal_document', [EmployeeController::class , 'store_employee_personal_documents']);
          Route::post('/get_employees', [EmployeeController::class, 'get_employee_location']);

          // Route::post('/authenticate',[LoginController::class, 'login_process'])->withoutmiddleware('userAuthentication');
          Route::post('/proxy-user',[LoginController::class, 'proxy_user']);


          Route::get('/my-attendance', [AttendanceController::class, 'personal_attendace']);
          Route::get('/share-attendance-report/{report_id}', [AttendanceController::class, 'access_attendance_sheet_report']);

          Route::get('/sap-attendance',[AttendanceController::class,'get_sap_employee_monthly_attendance']);

          Route::get('/my-attendance-request', [AttendanceRequestController::class, 'my_requests']);
          Route::post('/submit_attendance_request', [AttendanceRequestController::class, 'requestfor_attendance']);

          Route::get('/my-leaves', [LeavesRequestController::class, 'my_leaves']);
          Route::get('/download-leave-file', [LeavesRequestController::class, 'download_leave_file']);
          Route::post('/applyfor_leave', [LeavesRequestController::class, 'applyfor_personalleave']);

          Route::get('/my-salary-request', [AdvanceSalaryController::class, 'my_salary_request']);
          Route::post('/submit_salary_request', [AdvanceSalaryController::class, 'requestfor_advance_salary']);

          Route::get('/my-wfh-requests', [WFHController::class, 'my_wfh_requests']);
          Route::post('/submit_wfh_request', [WFHController::class, 'requestfor_wfh']);

          Route::get('/read-notification', [NotificationsController::class, 'read_notification']);
          Route::get('/fetch_notifications', [NotificationsController::class, 'fetch_notifications']);
          Route::get('/all-notifications', [NotificationsController::class, 'view_all']);
          Route::get('/push-notification', [NotificationsController::class, 'get_pushnotification']);
          Route::get('/read-my-notifications', [NotificationsController::class, 'read_all_notification']);

          Route::get('/alerts-notifications', [NotificationsController::class, 'notification_alert_detail']);

          Route::get('my-interviews', [InterviewsController::class, 'my_interviews']);
          Route::get('/myinterviewdetail/{interviewid}', [InterviewsController::class, 'mydetail']);
          Route::post('/submit_interviewer_remarks', [InterviewsController::class, 'submit_interviewer_remarks']);
          Route::post('/submit_second_interviewer_remarks', [InterviewsController::class, 'submit_second_interviewer_remarks']);
          Route::post('/assign_second_interviewer', [InterviewsController::class, 'changesecond_interviewer']);
          Route::post('/submit_interviewer_chat', [InterviewsController::class, 'submit_interviewer_chat']);
          Route::get('/download-candidate-cv', [InterviewsController::class, 'download_candidate_cv']);

          Route::post('/change_password_method', [EmployeeController::class, 'change_password_function']);

          Route::get('/employee-personal-documents', [EmployeeController::class, 'employee_personal_documents']);

          Route::get('/download-personal-document', [EmployeeController::class, 'download_personal_document']);

          Route::get('/my-meetings', [MeetingsController::class, 'my_meetings']);
          Route::get('/meeting-detail/{id}', [MeetingsController::class, 'detail']);
          Route::get('/my-meeting-detail/{id}', [MeetingsController::class, 'my_meeting_detail']);
          Route::post('/schedule_meeting', [MeetingsController::class, 'create_meeting']);
          Route::post('/send-chat-message', [MeetingsController::class , 'meeting_chat']);


          Route::get('/request-manager-status', [CustomController::class, 'request_manager_status']);

          Route::get('/request-check-in-time-log', [AttendanceRequestController::class, 'request_check_in_time_log']);
          Route::get('/request-check-out-time-log', [AttendanceRequestController::class, 'request_check_out_time_log']);

          Route::get('/employee-resignation/apply-resignation', [ResignationController::class , 'employee_resignation']);
          Route::get('/employee-resignation', [ResignationController::class , 'my_resignation']);
          Route::get('/employee-resignation/resignation-detail/{resignation_id}', [ResignationController::class , 'resignation_detail']);

          Route::get('/my-tickets', [HelpdeskController::class, 'my_help_desks']);
          Route::get('/get-employee-department-hd', [HelpdeskController::class, 'get_employee_department']);
          Route::get('/help-desk-detail/{id}', [HelpdeskController::class, 'help_desk_detail']);
          Route::get('/my-help-desk-detail/{id}', [HelpdeskController::class, 'my_help_desk_detail']);
          Route::get('/download-my-help-desk-file', [HelpdeskController::class, 'download_my_help_desk_attachment']);

          Route::post('/add-help-desk', [HelpdeskController::class, 'add_help_desk']);
          Route::post('/ticket-status', [HelpdeskController::class, 'ticket_status']);
          Route::post('/update-help-desk-employee', [HelpdeskController::class, 'update_help_desk_employees']);
          Route::post('/update-help-desk-department', [HelpdeskController::class, 'update_help_desk_departments']);
          Route::post('/send-help-desk-chat-message', [HelpdeskController::class , 'help_desk_chat']);

          Route::post('/add-employee-resignation', [ResignationController::class, 'add_employee_resignation']);

          Route::get('/personal-information', [PersonalInformationController::class, 'index']);
          Route::get('/view-employee-information-form/{employeeid}',[PersonalInformationController::class, 'view_employee_info_form']);
          Route::get('/print-employee-information-form/{employeeid}',[PersonalInformationController::class, 'print_employee_info_form']);

          Route::post('/savemypersonalinformation', [PersonalInformationController::class, 'submit_information']);

          Route::post('/update-attendance-request', [AttendanceRequestController::class, 'update_attendance_request']);
          Route::post('/update-leave-request', [LeavesRequestController::class, 'update_leave_request']);
          Route::post('/update-wfh-request', [WFHController::class, 'update_wfh_request']);
          Route::post('/update-adv-request', [AdvanceSalaryController::class, 'update_advance_salary_request']);
          Route::post('/update-loan-request', [LoanController::class, 'update_loan_request']);


          Route::post('/delete-attendance-request', [AttendanceRequestController::class, 'delete_attendance_request']);
          Route::post('/delete-leave-request', [LeavesRequestController::class, 'delete_leave_request']);
          Route::post('/delete-wfh-request', [WFHController::class, 'delete_wfh_request']);
          Route::post('/delete-adv-request', [AdvanceSalaryController::class, 'delete_advance_salary_request']);
          Route::post('/delete-loan-request', [LoanController::class, 'delete_loan_request']);


          Route::get('/my-attendance-report', [AttendanceController::class, 'my_attendance_report']);
          Route::get('/download-attendance-report', [AttendanceController::class, 'download_attendance_report']);


          Route::post('/add_another_user_schedule_meeting', [MeetingsController::class, 'add_another_user_schedule_meeting']);
          Route::post('/cancel-schedule-meeting', [MeetingsController::class, 'cancel_schedule_meeting']);

          Route::get('/fetch-firebase-configuration', [CredentialsController::class , 'firebase_config']);

          // payroll and daily wages
          Route::get('/my-payroll',[PayrollController::class,'my_payroll']);
          Route::post('/account-identification',[PayrollController::class,'account_identification']);
          Route::post('/payroll-identification',[PayrollController::class,'payroll_identification']);
          Route::get('/individual-payroll-pdf/{payroll_id}',[PayrollController::class,'individual_payroll_pdf']);
          Route::post('/calculate-my-payroll',[PayrollController::class,'calculate_my_payroll']);
          Route::post('/calculate-my-wages',[PayrollController::class,'calculate_my_wages']);
          Route::get('/my-wages',[PayrollController::class,'my_wages']);
          Route::get('pdf-daily-wages/{wage_id}',[PayrollController::class, 'pdf_daily_wages']);


          // employee expense
          Route::get('my-expense',[EmployeeExpenseController::class,'my_expense']);
          Route::post('/add-my-expense',[EmployeeExpenseController::class,'add_my_expense']);
          Route::post('/delete-my-expense',[EmployeeExpenseController::class,'delete_my_expense']);
          Route::post('/update-my-expense',[EmployeeExpenseController::class,'update_my_expense']);
          Route::post('/expense-amount-log',[EmployeeExpenseController::class,'expense_amount_log']);
          Route::get('/download-employee-expense-file',[EmployeeExpenseController::class,'download_employee_expense_file']);

          // employee

          Route::get('/get-employee-department',[CustomController::class,'get_employee_department']);
          Route::get('/get_employees_proxy/{status}', [EmployeeController::class, 'get_employees_proxy']);


          Route::post('/leave-request-graph',[HomeController::class, 'leave_request_graph']);
          Route::post('/wfh-request-graph',[HomeController::class,'wfh_request_graph']);
          Route::post('/interview-graph',[HomeController::class, 'interview_graph']);
          Route::post('/employee-monthly-strength',[HomeController::class, 'employee_monthly_strength']);

          Route::get('/sign-out', function (Request $request) {
            $request->session()->flush();
            return redirect('/');
          });

          // shift time start
          Route::get('/my-shift-time',[ShiftController::class, 'my_shift_time']);
          Route::post('/add-shift-time-request',[ShiftController::class, 'add_shift_time_request']);
          Route::post('/delete-shift-time-request',[ShiftController::class, 'delete_shift_time_request']);
          Route::post('/update-shift-time-request',[ShiftController::class, 'update_shift_time_request']);
          Route::post('/log-shift-time',[ShiftController::class, 'log_shift_time']);
          // shift time end

          //employee polling
          Route::post('/polling/employee-polling',[PollingsController::class,'polling_employee']);
          //employee polling

          Route::prefix('/employee-mobile-access')->group(function (){

            Route::get('/', [EmployeeController::class, 'employee_mobile_access']);
            Route::post('/add-button', [EmployeeController::class, 'add_mobile_access_button']);
            Route::post('/update-button', [EmployeeController::class, 'update_mobile_access_button']);
    
          });

          
          //employee api controller start

          Route::get('/get-employee-location', [EmployeeApiController::class, 'employees_location_api']);
          Route::get('/get-all-employees', [EmployeeApiController::class, 'get_all_employees_api']);
          Route::get('/get-all-employees-devices', [EmployeeApiController::class, 'employees_devices_api']);
          

          //employee api controller end

      });

      Route::prefix('/leave-policy')->group(function () {

        Route::get('/', [LeavesPolicyControlller::class, 'index']);
        Route::get('/add', [LeavesPolicyControlller::class, 'add_new_leave_policy']);
        // Route::post('/leaveapproverequest', [LeavesRequestController::class, 'leave_request']);
    
      });
      //Leave Policy Controller End
      Route::prefix('/employee-group')->group(function () {

        Route::get('/', [EmployeeController::class, 'employee_groups']);
        Route::post('/store-employee-group', [EmployeeController::class, 'add_employee_group']);
        Route::post('/update-employee-group', [EmployeeController::class, 'update_employee_group']);
        Route::get('/discard-employee-group/{employee_group_id}', [EmployeeController::class, 'delete_employee_group']);
    
      });


      Route::prefix('/deduction-policy')->group(function () {

        Route::get('/', [PayrollController::class, 'deduction_policy']);
        Route::post('/get-source-data', [PayrollController::class, 'get_deduction_source_data']);
        Route::post('/update-deduction-policy', [PayrollController::class, 'update_deduction_policy']);
      });

      Route::prefix('/companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/add-company', [CompanyController::class, 'add_company']);
        Route::post('/update-company', [CompanyController::class, 'update_company']);
        Route::get('/discard-company/{company_id}', [CompanyController::class, 'delete_company']);
      });
    
  });

  Route::prefix('/appflow')->group(function () {

    Route::get('/', [AppFlowController::class, 'appflow_modules']);
    Route::get('/detail/{id}', [AppFlowController::class, 'appflow_detail']);
    Route::post('/update-appflow', [AppFlowController::class, 'update_appflow_detail']);
    // Route::post('/update-deduction-policy', [PayrollController::class, 'update_deduction_policy']);
  });

  

    // Route::get('/employee-fund', [FundController::class, 'employee_fund']);
    // Route::post('employee-fund/get-source-data', [FundController::class, 'get_employee_fund_data']);
 

  // Route::prefix('/rpl-leaves')->group(function () {

  //   Route::get('/', [RplController::class, 'index']);
  //   Route::post('/rplleaveapproverequest', [RplController::class, 'rpl_leave_request']);
   
  // });
  // Route::get('/my-rpl-leave', [RplController::class, 'my_rpl_leave']);
  // Route::post('/apply-rpl-leave', [RplController::class, 'apply_rpl_leave_request']);
  // Route::post('/update-rpl-leave-request', [RplController::class, 'update_rpl_leave_request']);
  // Route::post('/delete-rpl-leave-request', [RplController::class, 'delete_rpl_leave_request']);
  
  //Leave Request Controller End

  Route::get('reports/attendance-report',[ReportsController::class, 'attendance_report']);

Route::get('/procced-to-dashboard', [HomeController::class, 'procced_to_dashboard']);
Route::get('/mark-attendance', [MarkAttendanceController::class, 'index']);
Route::post('/checkinattendance', [MarkAttendanceController::class, 'check_in'])->middleware('WebAttendancePermission');
Route::post('/checkoutattendance', [MarkAttendanceController::class, 'check_out'])->middleware('WebAttendancePermission');
Route::get('/payslip-pdf',[PayrollController::class,'payslip_pdf']);

Route::get('/employee-checkout-missing',[CustomController::class,'earlyout_of_missing_checkout_attendance']);
Route::get('/set-employee-permissions', [CustomController::class, 'fetch_all_employees_permission']);
Route::get('/set-attendance-access', [CustomController::class, 'add_attendance_access_data']);
Route::get('/set-recipient-type', [CustomController::class, 'add_recipient_type']);
Route::get('/set-room-location', [CustomController::class, 'add_room_location']);

Route::get('/employee-access-button', [CustomController::class, 'add_employee_button_access']);


Route::get('/clear-all-cache', [CommandController::class , 'clear_all_cache']);
Route::get('/clear-all-config', [CommandController::class , 'clear_all_config']);
Route::get('/clear-all-route', [CommandController::class , 'clear_all_route']);
Route::get('/clear-all-view', [CommandController::class , 'clear_all_view']);
Route::get('/truncate-migration', [CommandController::class , 'truncate_migration']);
Route::get('/privacy-policy', [HomeController::class , 'privacy_policy']);
Route::get('/bisconni-attendance-report',[AttendanceController::class, 'bisconni_attendace_report']);
Route::get('/bisconni-employee-report',[AttendanceController::class, 'bisconni_employee_report']);
Route::post('update-bisconni-employee-report',[AttendanceController::class, 'update_bisconni_employee_attendance']);


Route::get('/forgot-password',[LoginController::class, 'forgot_password']);
Route::post('/reset-password',[LoginController::class, 'reset_password']);
Route::get('/reset-password-link',[LoginController::class, 'reset_password_page']);
Route::post('/reset-update-password',[LoginController::class, 'reset_update_password']);

//attendance sap api start

// Route::get('/get-employee-attendance-sap-api', [AttendanceController::class, 'get_employee_monthly_attendance']);

//attendance sap api end


Route::middleware(['sapuserAuthentication'])->group(function (){
  Route::get('/sap-login', [AttendanceController::class, 'get_employee_monthly_attendance']);

  Route::post('/sap-login/authenticate', [LoginController::class, 'login_process_sap'])->withoutMiddleware('sapuserAuthentication');

    Route::get('/sap-login/sign-out', function (Request $request) {
     
      $request->session()->flush();
      return redirect('/sap-login');
      
    });
});    