@extends('layout.main')

@section('main-section')

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

            <div class="app-container col-12 d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Notification Alerts
                    </h1>
                </div>

            </div>

        </div>

        @if(!empty($interviews))
            <section id="interviews">
                <div class="app-content flex-column-fluid">

                    <div class="app-container">

                        <div class="card mb-5 mb-xl-8">

                            <div class="card-header border-0 pt-5">
                            <h3 class="pt-2">Interviews</h3>
                                <div class="card-title">

                                    <div class="d-flex align-items-center position-relative my-1">

                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterInterviews">
                                    </div>
                                </div>

                            </div>


                            <div class="card-body py-3">

                                <div class="table-responsive ">

                                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="table-responsive popup-visible ">
                                        <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tableinterviews" aria-describedby="tableEmployee_info">
                                            <thead>
                                                <tr class="fw-bold text-muted bg-light">
                                                    <th class="ps-4">ID</th>
                                                    <th>Candidate Name</th>
                                                    <th>1st Interviewer</th>
                                                    <th>2nd Interviewer</th>
                                                    <th>Timing</th>
                                                    <th>Deptartment</th>
                                                    <th>Type</th>
                                                    <th>Status</th>
                                                    <th>Interviewer Remarks</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($interviews as $interview)
                                                    <tr>
                                                        <td class="ps-4">{{ $interview['interview_id'] }}</td>
                                                        <td>{{ $interview['candidate_name'] }}</td>
                                                        <td>{!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer1']); !!}
                                                        </td>
                                                        <td>@if(!empty($interview['interview_interviewer2']))
                                                            {!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer2']) !!}
                                                            @else
                                                                <span >N\A</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $interview['interview_date'] }} - {{ date('h:i A', strtotime($interview['interview_time'])) }}</td>
                                                        <td>{{ $interview['department_name'] }}</td>
                                                        <td>
                                                            {{$interview['interview_type']}}
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-light-success text-center d-inline-block font-weight-bold">{{$interview['interview_status_level']}}</span>
                                                        </td>
                                                        <td>
                                                            @if (empty($interview['interview_interviewer_remarks']) && $interview['interview_interviewer_remarks'] == '')
                                                                <span class="badge badge-light-danger text-center d-inline-block font-weight-bold">Not given</span>
                                                            @else
                                                                <span class="badge badge-light-success text-center d-inline-block font-weight-bold">Given</span>
                                                            @endif
                                                        </td>

                                                        <td>
                                                            <a href="{{ url('interviews/interviewdetail') . '/' . $interview['interview_id']}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Interview detail">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                                            </a>

                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </section>
        @endif

        @if (!empty($meetings))
            <section id="meetings">
                <div class="app-content flex-column-fluid">

                    <div class="app-container">

                        <div class="card mb-5 mb-xl-8">

                            <div class="card-header border-0 pt-5">
                                <h3 class="pt-2">Meetings</h3>
                                <div class="card-title">

                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterMeeting">
                                    </div>
                                </div>

                            </div>


                            <div class="card-body py-3">

                                <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tablemeeting" aria-describedby="tableEmployee_info">
                                            <thead>
                                                <tr class="fw-bold text-muted bg-light">
                                                <th class="ps-4">Ref. Code</th>
                                                <th>Meeting Title</th>
                                                <th>Meeting Host</th>
                                                <th>Meeting Time</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($meetings as $meeting)
                                                    <tr>
                                                        <td class="ps-4">
                                                            @if ($meeting['meeting_id'] != '')
                                                                {{ $meeting['meeting_id'] }}
                                                            @else
                                                                Not Available
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($meeting['meeting_title'] != '')
                                                                {{ $meeting['meeting_title'] }}
                                                            @else
                                                                Not Available
                                                            @endif
                                                        </td>
                                                        <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($meeting['employee_id']) !!}</td>
                                                        <td>
                                                            @if ($meeting['meeting_time'] != '')
                                                                {{ $meeting['meeting_time'] }}
                                                            @else
                                                                Not Available
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>


                                    </div>

                                    </div>

                            </div>

                        </div>

                    </div>

                </div>
            </section>
        @endif

        @if(!empty($birthdays))
            <section id="employee-birthday">
                <div class="app-content flex-column-fluid">

                    <div class="app-container">

                        <div class="card mb-5 mb-xl-8">

                            <div class="card-header border-0 pt-5">
                                <h3 class="pt-2">Birthdays</h3>
                                <div class="card-title">

                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterBirthday">
                                    </div>
                                </div>

                            </div>

                            <div class="card-body py-3">

                                <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tablebirthday" aria-describedby="tableEmployee_info">
                                            <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th class="ps-4">Employee Code & Name</th>
                                                <th>Employee Department</th>
                                                <th>Birthday</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($birthdays as $birthday)
                                                    <tr>
                                                        <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($birthday['employee_id']) !!}</td>
                                                        <td>
                                                            <span class="text-dark fw-bold mb-1 fs-6"> {{ $birthday['department_name'] }}</span> -
                                                            <span class="ps-2 text-muted fw-semibold text-muted fs-7">{{$birthday['designation_name'] }}</span>
                                                        </td>
                                                        <td>{{ date('d, F' , strtotime($birthday['dob'])) }}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </section>
        @endif

        @if(!empty($employee_work_ceremony))
            <section id="employee-work-aniversery">
                <div class="app-content flex-column-fluid">

                    <div class="app-container">

                        <div class="card mb-5 mb-xl-8">

                            <div class="card-header border-0 pt-5">
                                <h3 class="pt-2">Employee Work Ceremony</h3>
                                <div class="card-title">

                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterWorkCeremony">
                                    </div>
                                </div>

                            </div>

                            <div class="card-body py-3">

                                <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tableworkceremony" aria-describedby="tableEmployee_info">
                                            <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                    <th class="ps-4">Name</th>
                                                    <th>Employee Depart & Desig</th>
                                                    <th>Employee DOJ</th>
                                                    <th>Duration</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach ($employee_work_ceremony as $employee)
                                                    <tr>
                                                        <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($employee['employee_id']) !!}</td>
                                                        <td>{{ $employee['department_name'] }} - <b>{{ $employee['designation_name'] }}</b></td>
                                                        <td>{{ date('d, F Y' ,strtotime($employee['employee_doj'])) }}</td>
                                                        <td>
                                                            <span class="badge badge-light-success fs-7 fw-bold">{{ CustomHelper::calculate_employeement_duration_period($employee['employee_doj']  , date('Y-m-d')) }}</span>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </section>
        @endif

        @if(!empty($probation_completion))
            <section id="probation_completed">
                <div class="app-content flex-column-fluid">

                    <div class="app-container">

                        <div class="card mb-5 mb-xl-8">

                            <div class="card-header border-0 pt-5">
                                <h3 class="pt-2">Employee Probation Completion</h3>
                                <div class="card-title">

                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterProbation">
                                    </div>
                                </div>

                            </div>

                            <div class="card-body py-3">

                                <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive popup-visible ">
                                        <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tableprobation" aria-describedby="tableEmployee_info">
                                            <thead>
                                                <tr class="fw-bold text-muted bg-light">
                                                    <th class="ps-4">Employee Name</th>
                                                    <th>Probation Status</th>
                                                    <th>Employment Days</th>
                                                    <th>Date of Joining</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($probation_completion as $probation_employees_detail)
                                                    <tr>
                                                        <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($probation_employees_detail['employee_id']) !!}</td>
                                                        <td>
                                                            @php
                                                                $now = time(); // or your date as well
                                                                $your_date = strtotime($probation_employees_detail['employee_doj']);
                                                                $datediff = $now - $your_date;

                                                                $days = round($datediff / (60 * 60 * 24));
                                                            @endphp
                                                            @if($days >= 90)
                                                                <span class="badge badge-light-success font-weight-bold">Probation Completed</span>
                                                            @else
                                                                <span class="badge badge-light-danger font-weight-bold">Probation Incomplete</span>
                                                            @endif
                                                        </td>
                                                        <td>{{ $days }} Days</td>
                                                        <td>{{ date('d F, Y' , strtotime($probation_employees_detail['employee_doj'])) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </section>
        @endif

        @if(!empty($notice_period_ending))
            <section id="notice_period">
                <div class="app-content flex-column-fluid">

                    <div class="app-container">

                        <div class="card mb-5 mb-xl-8">

                            <div class="card-header border-0 pt-5">
                                <h3 class="pt-2">Employee Notice Period Ending</h3>
                                <div class="card-title">

                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterNoticePeriod">
                                    </div>
                                </div>

                            </div>

                            <div class="card-body py-3">

                                <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tablenoticeperiod" aria-describedby="tableEmployee_info">
                                            <thead>
                                                <tr class="fw-bold text-muted bg-light">
                                                    <th class="ps-4">Employee Name</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Total Day(s)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($notice_period_ending as $all_resignation)
                                                    <tr>
                                                        <td class="ps-6">{!! CustomHelper::getEmpProfileDiv($all_resignation['resignation_employee']) !!}</td>

                                                        <td>{{ $all_resignation['resignation_from_date'] }}</td>
                                                        <td>{{ $all_resignation['resignation_to_date'] }}</td>
                                                        <td class="ps-10">
                                                            @php
                                                                $startDate = new DateTime($all_resignation['resignation_from_date']);
                                                                $endDate = new DateTime($all_resignation['resignation_to_date']);

                                                                $total_days = $endDate->diff($startDate)->format("%a");
                                                                echo $total_days+1;

                                                            @endphp
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            <section>
        @endif

        @if(!empty($onboard_employees))
            <section id="onboard_employees">
                <div class="app-content flex-column-fluid">

                    <div class="app-container">

                        <div class="card mb-5 mb-xl-8">

                            <div class="card-header border-0 pt-5">
                                <h3 class="pt-2">Employee Onboard</h3>
                                <div class="card-title">

                                    <div class="d-flex align-items-center position-relative my-1">
                                        <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                                <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterOnboard">
                                    </div>
                                </div>

                            </div>

                            <div class="card-body py-3">

                                <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                    <div class="table-responsive">
                                        <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tableemployeesonboard" aria-describedby="tableEmployee_info">
                                            <thead>
                                                <tr class="fw-bold text-muted bg-light">
                                                    <th class="ps-4">Ref ID</th>
                                                    <th>Candidate Name</th>
                                                    <th>Depart Name</th>
                                                    <th>Employee DOJ</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                                @foreach ($onboard_employees as $boarding)
                                                                                    
                                                    <tr>
                                                        
                                                        <td class="ps-4">{{$boarding['onboarding_id']}}</td>
                                                    
                                                        <td class="ps-4">
                                                        {{$boarding['candidate_name']}}
                                                        </td>

                                                        <td class="ps-4">
                                                            {{$boarding['department_name']}}
                                                        </td>

                                                    
                                                        <td class="ps-4">{{date('d M, Y', strtotime($boarding['doj'])) }}</td>

                                                        <td>
                                                            <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#updateOnBoardingEmployee" onclick="editOnBoardingEmployee( '{{ $boarding['onboarding_id'] }}','{{ $boarding['candidate_name'] }}','{{ $boarding['department_name'] }}','{{ $boarding['doj'] }}')">
                                                                
                                                                <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Edit On Boarding Employee">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                    </svg>
                                                                </span>
                                                                
                                                            </a>
                                                            <a href="#" onclick="deactivate_confirm('{{ url('employee-onboarding/discard_boarding_employee') . '/' . $boarding['onboarding_id'] }}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="tooltip" title="Delete On Boarding Employee">
                                                                <span class="svg-icon svg-icon-3">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                        <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                        <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                    </svg>
                                                                </span>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                        
                                            </tbody>

                                        </table>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            <section>
        @endif

    </div>

@endsection

@push('js-link')
<script>
    $(function(){
		dataTable = $('#tableinterviews').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterInterviews').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
        dataTable = $('#tablemeeting').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterMeeting').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
        dataTable = $('#tablebirthday').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterBirthday').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
        dataTable = $('#tableworkceremony').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterWorkCeremony').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
        dataTable = $('#tableprobation').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterProbation').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
        dataTable = $('#tablenoticeperiod').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterNoticePeriod').keyup(function(){
			dataTable.search($(this).val()).draw();
		})

        dataTable = $('#tableemployeesonboard').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterOnboard').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
	});
</script>

@endpush
