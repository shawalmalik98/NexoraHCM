<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Attendance Report</title>
		
	<meta name="description" content="More than five years back a group of few members started IIL Aiming to provide website solutions and solutions to businesses through daily needed performing software like HRM/ERP to ease management businesses.">


	<meta itemprop="name" content="Portal || IIL">
	<meta itemprop="description" content="More than five years back a group of few members started IIL Aiming to provide website solutions and solutions to businesses through daily needed performing software like HRM/ERP to ease management businesses.">


	<meta property="og:type" content="website">
	<meta property="og:title" content="Portal || IIL">
	<meta property="og:description" content="More than five years back a group of few members started IIL Aiming to provide website solutions and solutions to businesses through daily needed performing software like HRM/ERP to ease management businesses.">
    <link rel="icon" href="{{ url('portal_assets/images/hcm-img.png') }}" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <script src="https://unpkg.com/sweetalert2@11"></script>
	
	<style>
        .hr{
           border: 1px solid black;
           margin-top: 1px;
        }

        th,td{
            font-size: 12px;
        }
	
	</style>
</head>
 
<body>
    @php
        $companySetting = CustomHelper::hcm_default_settings();
    @endphp

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-2 mt-2">
           
                @if(!empty(Request::get('employee_id')))
                    <a href="{{ url('download-attendance-report?employee_id='.Request::get('employee_id').'&month='.$month.'&year='.$year) }}" class="btn btn-sm fw-bold btn-primary">Download Attendance</a>
                @else
                    @if(!empty($month) && !empty($year) && empty(Request::get('from_date')) && empty(Request::get('to_date')))
                        <a href="{{ url('download-attendance-report?month='.$month.'&year='.$year.'&from_date='.Request::get('from_date').'&to_date='.Request::get('to_date')) }}"  class="btn btn-sm fw-bold btn-primary" >Download Attendance</a>
                    @else
                        <a href="{{ url('download-attendance-report?month='.$month.'&year='.$year.'&from_date='.Request::get('from_date').'&to_date='.Request::get('to_date')) }}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >Download Attendance</a>
                    @endif
                   
                @endif
                
        
            </div>
            <div class="col-md-12 text-center">

                @if(!empty($companySetting['company_logo']))
                    <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo" style="width:10%">
                @else
                    <img src="{{ url('/portal_assets/images/logo.png') }}" draggable="false" class="img-fluid" >
                @endif
                
                <h1 class="text-center" style="margin-top:1%">{{$companySetting['company_name']}}</h1>

                <h5 class="text-center" style="margin-top:1%">Employee Monthly Attendance Sheet </h5>

            </div>
        </div>    
        <div class="row mt-5">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <td><b>Employee</b></td>
                        <td>{{!empty($employee) ? $employee[0]['employee_name']. ' - ' .$employee[0]['employee_code'] : '' }}</td>
                        <td><b>Reporting Authority</b></td>
                        <td>{{!empty($employee_senior_manager) ? $employee_senior_manager[0]['employee_name'] : '' }}</td>
                    </tr>
                    <tr>
                        <td><b>Department</b></td>
                        <td>{{!empty($employee) ? $employee[0]['department_name'] : '' }}</td>
                        <td><b>Attendance Period</b></td>
                        

                        <td>
                            {{ date('d-M', strtotime($from_date)) .' to ' .date('d-M-Y', strtotime($to_date)) }}
                        </td>
                        
                    </tr>
                    <tr>
                        <td><b>Designation</b></td>
                        <td>{{!empty($employee) ? $employee[0]['designation_name'] : '' }}</td>
                        <td><b>Report Date</b></td>
                        <td>{{date('d-M-Y')}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        @if(Session::has('error'))

        <script>
             swal.fire("Error", "{{ Session::get('error') }}", "error");
        </script>
        @endif
        <div class="row">
            <table class="table table-bordered">
                <thead>
                    <tr style="background:lightgray">
                        <th rowspan="2" class="text-center">Attendance Date </th>
                        <th colspan="2" class="text-center">Check In</th>
                        <th colspan="2" class="text-center">Check Out </th>
                        <th rowspan="2" class="text-center">Work Hours</th>
                        <th rowspan="2" class="text-center">Extra Hours</th>
                        <th rowspan="2" class="text-center">Late Minutes</th>
                        <th rowspan="2" class="text-center">Early Depar. Minutes</th>
                        <th rowspan="2" class="text-center">Leave/Deputation</th>
                        <th rowspan="2" class="text-center">Att Source</th>
                        <th rowspan="2" class="text-center">Status</th>
                    </tr>
                    <tr style="background:lightgray">
                    
                        <th class="text-center">Date</th>
                        <th class="text-center">Time</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Time</th>

                    </tr>
                </thead>

                <tbody>
                    @php $employee[0]['employee_offdays'] = (!empty($employee[0]['employee_offdays'])) ? explode(",",$employee[0]['employee_offdays']):[]; @endphp
                    @for ($month_count = $month_count; $month_count <= $month_days; $month_count++)
                    @php $absent = true; @endphp
                    <tr>
                        @php
                                               
                            $current_date = $dates[$month_count];
                            $name_of_day = date("l", strtotime($current_date ));	
                            $name_of_day_short = date("D", strtotime($current_date ));	
                        @endphp
                        
                        <td class="text-center">
                            {{ $current_date }} - {{  $name_of_day_short }}
                        </td>
                        <td class="text-center">
                            @if(!empty($attendances))
                                @foreach($attendances as $attendance)
                            
                                    @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if ($attendance['att_check_overall_status'] == 'Present')
                                            {{ $current_date }}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day')
                                            {{ $current_date }}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                            {{ $current_date }}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out')
                                            {{ $current_date }}
                                        @elseif($attendance['att_check_in_time'] == '' || $attendance['att_check_overall_status'] == 'Absent' || substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                            00:00:00
                                        @endif
   
                                        @if(in_array($attendance['att_check_overall_status'],$manage_leaves) || $attendance['att_check_overall_status'] == 'RPL')
                                            00:00:00
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($attendances))
                                @foreach($attendances as $attendance)
                            
                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if ($attendance['att_check_overall_status'] == 'Present')
                                            {{ $attendance['att_check_in_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day')
                                            {{ $attendance['att_check_in_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                            {{ $attendance['att_check_in_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out')
                                            {{ $attendance['att_check_in_time'] }}
                                        @elseif($attendance['att_check_in_time'] == '' || $attendance['att_check_overall_status'] == 'Absent' || substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                            00:00:00
                                        @endif
   
                                        @if(in_array($attendance['att_check_overall_status'],$manage_leaves) || $attendance['att_check_overall_status'] == 'RPL')
                                            00:00:00
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($attendances))
                                @foreach($attendances as $attendance)
                            
                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if ($attendance['att_check_overall_status'] == 'Present')
                                            {{ $current_date }}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day')
                                            {{ $current_date }}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                            {{ $current_date }}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out')
                                            {{ $current_date }}
                                        @elseif($attendance['att_check_in_time'] == '' || $attendance['att_check_overall_status'] == 'Absent' || substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                         00:00:00
                                        @endif

                                        @if(in_array($attendance['att_check_overall_status'],$manage_leaves) || $attendance['att_check_overall_status'] == 'RPL')
                                            00:00:00
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($attendances))
                                @foreach($attendances as $attendance)
                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if ($attendance['att_check_overall_status'] == 'Present')
                                            {{ $attendance['att_check_out_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day')
                                            {{ $attendance['att_check_out_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                            {{ $attendance['att_check_out_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out')
                                        {{ $attendance['att_check_out_time'] }}
                                        @elseif($attendance['att_check_out_time'] == '00:00:00')
                                           
                                        @elseif($attendance['att_check_in_time'] == '' || $attendance['att_check_overall_status'] == 'Absent' || substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                            00:00:00
                                        @endif

                                        @if(in_array($attendance['att_check_overall_status'],$manage_leaves) || $attendance['att_check_overall_status'] == 'RPL')
                                            00:00:00
                                        @endif
                                    @endif
                                @endforeach
                            @endif  
                        </td>
                        <td class="text-center">
                            @if(!empty($attendances))
                                @foreach($attendances as $attendance)

                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if ($attendance['att_check_overall_status'] == 'Present')
                                            {{ CustomHelper::calculate_working_hour($attendance['att_check_out_status'],$attendance['att_check_in_time'],$attendance['att_check_out_time'],$attendance['att_created_at'],$attendance['att_updated_at'])}}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day')
                                            {{ CustomHelper::calculate_working_hour($attendance['att_check_out_status'],$attendance['att_check_in_time'],$attendance['att_check_out_time'],$attendance['att_created_at'],$attendance['att_updated_at'])}}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                            {{ CustomHelper::calculate_working_hour($attendance['att_check_out_status'],$attendance['att_check_in_time'],$attendance['att_check_out_time'],$attendance['att_created_at'],$attendance['att_updated_at'])}}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out')
                                            {{ CustomHelper::calculate_working_hour($attendance['att_check_out_status'],$attendance['att_check_in_time'],$attendance['att_check_out_time'],$attendance['att_created_at'],$attendance['att_updated_at'])}}
                                        @elseif($attendance['att_check_in_time'] == '' || $attendance['att_check_overall_status'] == 'Absent' || substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                            00:00:00
                                        @endif

                                        @if(in_array($attendance['att_check_overall_status'],$manage_leaves) || $attendance['att_check_overall_status'] == 'RPL')
                                            00:00:00
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($attendances))

                                @foreach($attendances as $attendance)
                                    @php 
                                        $employee_shift_times =  CustomHelper::calculate_shift_timings_shift_management($attendance['att_shift_start_time'],$attendance['att_shift_end_time'],$attendance['att_check_in_status'],$attendance['att_check_out_status']);
                                        $employee_working_hours =   CustomHelper::calculate_working_hour($attendance['att_check_out_status'],$attendance['att_check_in_time'],$attendance['att_check_out_time'],$attendance['att_created_at'],$attendance['att_updated_at']);
                                    
                                    @endphp
                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        
                                        @if ($attendance['att_check_overall_status'] == 'Present' )
                                          
                                        {{ CustomHelper::extra_hours_working($employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day')
                                            {{CustomHelper::extra_hours_working($employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                            {{CustomHelper::extra_hours_working($employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out')
                                            {{CustomHelper::extra_hours_working($employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_out_time'] == '00:00:00')
                                            
                                        @elseif($attendance['att_check_in_time'] == '' || $attendance['att_check_overall_status'] == 'Absent' || substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                            00:00:00
                                        @endif

                                        @if(in_array($attendance['att_check_overall_status'],$manage_leaves) || $attendance['att_check_overall_status'] == 'RPL')
                                         00:00:00
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($employee) && $employee[0]['employee_shift_flexibilty'] != 1)
                                @if(!empty($attendances))

                                    @foreach($attendances as $attendance)
                                        @php 
                                            $employee_shift_times =  CustomHelper::calculate_shift_timings_shift_management($attendance['att_shift_start_time'],$attendance['att_shift_end_time'],$attendance['att_check_in_status'],$attendance['att_check_out_status']);
                                        @endphp
                                         @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                            @php $absent = false; @endphp
                                            
                                            @if ($attendance['att_check_overall_status'] == 'Present')
                                            
                                            {{ CustomHelper::late_checkIn_hour($attendance['att_shift_start_time'],$attendance['att_check_in_time'])}}
                                            @elseif($attendance['att_check_overall_status'] == 'Half day')
                                                {{CustomHelper::late_checkIn_hour($attendance['att_shift_start_time'],$attendance['att_check_in_time'])}}
                                            @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                                {{CustomHelper::late_checkIn_hour($attendance['att_shift_start_time'],$attendance['att_check_in_time'])}}
                                            @elseif($attendance['att_check_overall_status'] == 'Early out')
                                                {{CustomHelper::late_checkIn_hour($attendance['att_shift_start_time'],$attendance['att_check_in_time'])}}
                                            @elseif($attendance['att_check_out_time'] == '00:00:00')
                                                
                                            @elseif($attendance['att_check_in_time'] == '' || $attendance['att_check_overall_status'] == 'Absent' || substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                00:00:00
                                            @endif

                                            @if(in_array($attendance['att_check_overall_status'],$manage_leaves) || $attendance['att_check_overall_status'] == 'RPL')
                                                00:00:00
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                        </td>
                        <td class="text-center">
                            @if(!empty($attendances))

                                @foreach($attendances as $attendance)
                                    @php 
                                        $employee_shift_times =  CustomHelper::calculate_shift_timings_shift_management($attendance['att_shift_start_time'],$attendance['att_shift_end_time'],$attendance['att_check_in_status'],$attendance['att_check_out_status']);
                                        $employee_working_hours =   CustomHelper::calculate_working_hour($attendance['att_check_out_status'],$attendance['att_check_in_time'],$attendance['att_check_out_time'],$attendance['att_created_at'],$attendance['att_updated_at']);
                                    @endphp
                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if ($attendance['att_check_overall_status'] == 'Present')
                                            {{ CustomHelper::early_checkOut_hour($attendance['att_check_out_status'],$employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day')
                                            {{CustomHelper::early_checkOut_hour($attendance['att_check_out_status'],$employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                            {{CustomHelper::early_checkOut_hour($attendance['att_check_out_status'],$employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out')
                                            {{CustomHelper::early_checkOut_hour($attendance['att_check_out_status'],$employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_out_time'] == '00:00:00')
                                        @elseif($attendance['att_check_in_time'] == '' || $attendance['att_check_overall_status'] == 'Absent' || substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                            00:00:00
                                            @endif

                                        @if(in_array($attendance['att_check_overall_status'],$manage_leaves) || $attendance['att_check_overall_status'] == 'RPL')
                                            00:00:00
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center">
                            @if (!empty($attendances))
                                @foreach ($attendances as $attendance)
                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if($attendance['att_request_approval'] == 1)
                                            Attendance Request
                                        @elseif($attendance['att_request_approval'] == 2)
                                            Leave Request
                                      
                                        @elseif($attendance['att_request_approval'] == 3)
                                         RPL Leave Request
                                    @endif
                                    @endif 
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center"> 
                            @if (!empty($attendances))
                                @foreach ($attendances as $attendance)
                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if($attendance['att_check_in_source'] == 1)
                                            Web Application
                                        @elseif($attendance['att_check_in_source'] == 2)
                                            Mobile Application
                                        @elseif($attendance['att_check_in_source'] == 3)
                                        Updated Attendance
                                        @else
                                            Machine Attendance
                                        @endif
                                    @endif 
                                @endforeach
                            @endif
                        </td>
                        <td class="text-center"> 
                            @if (!empty($attendances))
                                @foreach ($attendances as $attendance)
                                     @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if ($attendance['att_check_overall_status'] == 'Present')
                                            On Time
                                        @elseif($attendance['att_check_overall_status'] == 'Half day')
                                            Half Day
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming')
                                            Late Coming
                                           
                                        @elseif(in_array($attendance['att_check_overall_status'],$manage_leaves))
                                            {{$attendance['att_check_overall_status']}}
                                                       
                                        @elseif($attendance['att_check_overall_status'] == 'Absent')
                                            Absent
                                        @elseif($attendance['att_check_overall_status'] == 'Early out')
                                            Early out
                                        @elseif($attendance['att_check_overall_status'] == 'RPL')
                                            RPL
                                        @elseif(substr($attendance['att_check_overall_status'] , 0, 7) == 'Holiday')
                                            Holiday
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                            @php
                              
                                $date_filter = strtotime(date("Y-m-d", strtotime($dates[$month_count])));
                                $today = strtotime(date("Y-m-d"));
                            @endphp
                            
                            @if($absent )
                                @if(in_array($name_of_day,$employee[0]['employee_offdays']) )
                                    Off Day

                                @elseif($date_filter< $today)
                                   Absent
                                @endif
                            @endif
                            
                            
                        </td> 
                    </tr>
                    @endfor
                </tbody>
            </table>

        </div>
    </div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>


{{-- Get Current Location --}}


</body>
</html>