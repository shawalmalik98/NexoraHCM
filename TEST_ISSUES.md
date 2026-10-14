# HCM test issues log
1. [FIXED] Add employee -> "Something went wrong": tbl_employees_location has no `created_by` column (code EmployeeController:523,1582). Added migration 2026_10_14_000001.
2. [OPEN] Every page: JS console errors "Unexpected token 'export'" / "Cannot use import statement outside a module" (some script loaded as classic).
3. [OPEN] Sara Khan test: dept 3 (Supply Chain) loads 0 designations -> designation required, can't submit (data issue or bug?).
4. [OPEN] DB admin email was old (updated to shawaltelecart@gmail.com manually).
5. [NOTE] tbl_error_logs id161: monthly-attendance SQL uses MySQL-only `master..spt_values` (SQL Server syntax) -> fails on MariaDB.
6. [OPEN] footer.blade.php:22-23 loads firebase 10.7.1 firebase-app.js/firebase-database.js as classic scripts (they are ES modules; need -compat.js or type=module) -> 'export'/'import' JS errors on every page.
7. [FIXED-by-migration] 3 test employees created OK after fix: Ali Raza(1000001), Sara Khan(1000002), Usman Ahmed(1000003). Note: failed attempts burned auto-increment ids 2,3.
## GET smoke test (148 pages as admin): 126 ok, 16 fail
8. /attendance/attendance-report 500 Undefined variable $from_date
9. /bisconni-employee-report 500 Undefined array key 0
10. /employee-fund/get-source-data 500 Class App\Http\Controllers\web\Redirect not found (missing `use Illuminate\Support\Facades\Redirect`)
11. /incentive + /get-incentive-details 500: table tbl_incentive doesn't exist (no migration for the new incentive feature)
12. /load-business-cards 500: column tbl_employees.emp_qr_svg missing (no migration for QR feature)
13. /monthly-attendance: SQL uses master..spt_values (SQL Server syntax) -> fails on MariaDB
14. JS: present_attendance not defined on /daily-attendance-report and /bisconni-attendance-report
15. JS: /personal-information addEventListener of null
16. JS: /vehicles/add-vehicle $(...).inputmask is not a function (plugin not loaded)
17. /vehicles/vehicles-allotment 500 (reason not captured yet)
18. /fetch_notifications 500 (reason not captured yet)
19. utility routes 500 (probably need params, check): /reset-password-link /set-employee-permissions /update_eid
## Form tests batch 1 (masters)
20. [FIXED] last_updated_by column missing in tbl_departments/tbl_designations/tbl_office_locations (Add Department/Designation/Location failed) -> migration 000002
21. [FIXED] tbl_incentive, sap_attendance_report tables, tbl_employees.emp_qr_svg missing -> migration 000002 (columns inferred from code)
22. [FIXED] GeneralController uses STRING_AGG (SQL Server only) -> GROUP_CONCAT
23. [OPEN] read_notification: tbl_notification_status.viewed_notification NOT NULL violated (err log 168/173)
24. [OPEN] personal-information save: cURL error 6 resolving host "api" for api/add-employee (relative/invalid URL, err 167)
25. [OPEN] "refresh Sap Attendance": Trying to access array offset on value of type int (err 169/171/174)
26. [NOTE] Room name validation regex only allows letters (test data issue, not bug)
27. [OPEN - SECURITY/CRITICAL] GET /truncate-migration (CommandController@truncate_migration, routes/web.php:1040) is reachable WITHOUT login and truncates the `migrations` table. My smoke test hit it and wiped the table (restored by re-inserting rows). Same for /clear-all-cache|config|route|view (unauthenticated GET). Remove these routes or protect them.
28. [NOTE] Older migrations (2023_11_20..2024_01_31) lack hasTable guards -> `php artisan migrate` fails if tables already exist.
29. [OPEN] /leave-policy/add is an unfinished clone of the Add Employee form: field names employee_name/employee_cnic/employee_gender are duplicated, there is no POST route (commented out in routes/web.php:961), so a Leave Policy cannot be saved.
30. [UX] Name-format validation messages ("The employee group name format is invalid") don't say letters only; names with digits are rejected for group/status/leave/room.
31. [NOTE] Holiday form: designations select empty until departments chosen (expected). Attendance locker from_date max = today (expected).
## Form tests batch 2/3 (self-service as Sara/Usman/Admin): PASS = leave, attendance request, WFH, loan (my + admin grant), advance salary, my expense, meeting, help-desk ticket, shift-time request, personal document, requisition, petty cash, daily expense, company, fund, letter template, tax slab, attendance device, alert, room, department, designation, location, mobile button, polling, linkedin post, gadget.
32. [NOTE] First login of a new employee shows welcome interstitial (/procced-to-dashboard) - expected.
33. [UX] Many validation messages are generic ("Something went wrong") when a DB error occurs: the controllers swallow the exception and only write tbl_error_logs, so schema errors are invisible to users.
## Approval flow (as admin) - PASS: attendance request, leave, loan, advance salary (finance), shift-time request, resignation, expense. Resignation as employee also OK (needs confirm).
34. [CONFIG/INCONSISTENT] tbl_system_modules.module_accessibility=0 for /wfh-requests, /my-wfh-requests, /employee-letter, /letter-template. Admin gets "Not Allowed To Access This Module" on /wfh-requests (returned as HTTP 200, not 403) while employees can still submit WFH requests from /my-wfh-requests, so WFH requests can never be approved (they get "Auto approved by system"/stay pending).
35. [BUG-minor] Permission denied page (errors.403) is returned with HTTP 200 (response()->view without status) - smoke tests/monitoring can't detect it. PermissionChecker.php
## Batch 4/5: PASS interview schedule, edit employee salary, system settings save, vehicle add (saves OK), holiday (works; business rule "Employee(s) not exist on this designation").
36. [OPEN] Vehicle add page: public/assets/js/vehicle/add-vehicle.js:4 uses $(...).inputmask but the inputmask plugin isn't loaded on the page (JS TypeError; masks don't apply).
37. [NOTE] Kiosk /mark-attendance: "You can't mark attendance from this device" (device/IP rule, needs tbl_mac_address/devices config) - not a bug.
## FIX ROUND 1 (done)
- [FIXED] #2/#6 firebase compat scripts in footer (firebase-app-compat/firebase-database-compat) - removes 'export'/'import' JS errors
- [FIXED] #8 attendance-report: controller now passes $from_date/$to_date
- [FIXED] #10 FundController: Redirect facade imported
- [FIXED] #11/#12 migrations 000002..000004: tbl_incentive, sap_attendance_report, emp_qr_svg, emp_qr_value, emp_unique_code (backfilled with uuid + Employees::creating hook), employee_alt_name, tbl_company.office_phone/office_address/office_latlong/website, last_updated_by x3
- [FIXED] #9 bisconni-employee-report: guarded $employee_data[0]
- [FIXED] #14 present_attendance JS: ints defaulted in 4 blades; defaults added to bisconni-attendance-report + daily-attendance-report2
- [FIXED] #15 personal-information main.js null input guard
- [FIXED] #16 vehicle add: inputmask plugin script added
- [FIXED] #17/#18 dead routes removed: /fetch_notifications, /vehicles-allotment (controller methods don't exist)
- [FIXED] #23 read_notification guards empty notification id
- [FIXED] #27 removed /truncate-migration route; /clear-all-* now require login
- [FIXED] SQL Server leftovers in AttendanceEmployee trait: master.dbo.spt_values x6 -> numbers table; CROSS APPLY STRING_SPLIT -> FIND_IN_SET
- [FIXED] business-card page: employee_isactive JS var
- [OPEN] #13 /monthly-attendance still fails on MariaDB: correlated alias `e` referenced inside derived tables (needs the query rewritten, SQL Server-port in progress by team)
- [OPEN] #24 personal-information save: HTTP call to relative URL 'api/add-employee' (curl error 6 host 'api') - needs a configured base URL
- [OPEN] #25 'refresh Sap Attendance' array offset on int
- [OPEN] #29 leave-policy/add unfinished; #34 WFH module disabled in tbl_system_modules; #35 403 view returns HTTP 200
- [OPEN] FundController source-data query uses SQL Server syntax CONVERT(CHAR(10),[col],112)
## FIX ROUND 2 (done)
- [FIXED] 12x `redirect(url())` TypeError in download endpoints (url() without args returns UrlGenerator) -> redirect('/')
- [FIXED] PersonalInformationController: external sync to APP_SC_URL is now best-effort (was turning a successful save into an error when APP_SC_URL unset)
- Regression smoke: 135 ok (was 126). Remaining FAIL: /monthly-attendance (SQL port), 3 utility routes needing params, 3 removed routes (404 now expected).
- [NOTE-security] PayrollController::verification_code is hardcoded '123789'; employee onboarding mail uses plain-text passwords; DB seeds contain a real SMTP password (tbl_email_configuration) - move to env/secrets.
## FIX ROUND 3 (done)
- [FIXED] /monthly-attendance: query rewritten for MySQL (flattened correlated derived tables, DATEDIFF arg order, GETDATE->CURDATE, FORMAT->DATE_FORMAT). Renders all 4 employees.
- [FIXED] refresh Sap Attendance: employee_code int indexed as string (CustomHelper + AttendanceApiController)
- [FIXED] overtime-management: array_reverse on string 'No Employee Found' when a filter returns no employees
- [FIXED] dashboard interview chart: null canvas guard
- [FIXED] funds delete: JS posted to '/funds/discard-fund/' (trailing slash -> 301 -> GET 405) so Delete Fund never worked
- [FIXED] Add Employee with matching on-board candidate: create_employee returned without DB::commit(), so the employee was silently NOT saved (and the follow-up 'Process & Remove From On Board' had nothing to process)
- [FIXED] Add Employee: candidate_name_list() sent the entire 121-field form as a GET query string -> HTTP 414; now sends only name+department
- [TEST CLEANUP] reverted ' upd' suffixes my edit-test appended to master names; restored tbl_hrm_settings values my System Settings test overwrote (logo/favicon/signature/linked posts/onboarding name/email permissions); removed test.png uploads; added '::1' to tbl_mac_address so kiosk check-in works on localhost.
- Verified OK: kiosk check-in/out, interviews (schedule, update, hire->onboarding->employee), meetings, requisition status, vehicles (update/maintenance/allotment), gadgets (update/assign), business card edit, employee docs upload, shift set, payroll calculate+generate (code 123789), deletes (department, designation, location, fund, attendance device).
- Final regression smoke done. [OPEN] POST employees/employee-information-download -> download_employee_detail() is commented out in EmployeeController (modal still exposed; GET /employees?download=TRUE xlsx export works). Proxy-login user select loads remotely (not exercised).
## FIX ROUND 4 (done)
- [FIXED] errors.403 now returned with HTTP 403 (PermissionChecker)
- [FIXED] one-off maintenance routes (/set-employee-permissions, /set-attendance-access, /set-recipient-type, /set-room-location, /update_eid) now require login
- [FIXED] /reset-password-link without/with bad token no longer 500s
- [FIXED] FundController source-data query: SQL Server CONVERT -> DATE_FORMAT
- [FIXED] "Download Employees Records" modal: download_employee_detail() restored as a gate that forwards to the working export (employees?download=TRUE)
- [FIXED] payroll verification code can be set with PAYROLL_VERIFICATION_CODE (default unchanged); seeder no longer contains the SMTP password (SEED_EMAIL_CONFIG_PASSWORD); default-signature file name case
- [DATA] WFH modules enabled in local DB (tbl_system_modules.module_accessibility=1) - decide for production
- [NOT FIXED - needs product decision] /leave-policy/add is a UI prototype with no storage/route; employee proxy login not exercised
