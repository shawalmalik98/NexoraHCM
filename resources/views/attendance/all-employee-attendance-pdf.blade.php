<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
    <title></title>
    <style>
    .table {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    .table th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    /* .table tr:nth-child(even){background-color: #f2f2f2;} */

    /* .table tr:hover {background-color: #ddd;} */

    .table th {
        padding-top: 12px;
        padding-bottom: 12px;
        border:1px solid;
      
       
    }
    .td-border{
        border: 1px solid;
        font-size: 15px;
        min-width: 300px;
        min-height: 460px;
        padding: 10px;
    }

    .text-center {
            text-align: center !important;
        }

    .button {
        background-color: #4CAF50; /* Green */
        border: none;
        color: white;
        padding: 15px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 13px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 12px;
        font-family: sans-serif;
    }
    </style>
</head>
<body>

    <div class="col-2" style="float:right">
        <span style="font-size:15px">Report Date: {{date('d M, Y')}}</span>
    </div>

    @php
        $companySetting = CustomHelper::hcm_default_settings();
    @endphp
    <div class="container mt-3">
       
        <div class="col-12 text-center" style="clear:right">

            @php $apply_month_year =  $month .'-'. $year; @endphp

            @if(!empty($companySetting['company_logo']))
                <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo" style="width:10%">
            @else
                <img src="{{ url('/portal_assets/images/logo.png') }}" draggable="false" class="img-fluid" >
            @endif
            
            <h1 class="text-center" style="margin-top:1%">{{ $companySetting['company_name']}} </h1>

            <h2 class="text-center" style="margin-top:1%">Employees Attendance Sheet ( {{\Carbon\Carbon::createFromFormat('m-Y', $apply_month_year)->format('F-Y') }} )</h2>

        </div>  
    </div>
    <table class="table" >
        <thead>
        
            <tr  class="fw-bold text-muted bg-light">
                <th>Employee</th>
                @for ($month_count = 1; $month_count <= $month_days; ++$month_count)
                    @php
                    
                    $date = '' . $month_count . '-' . $month . '-' . $year . '';
                    @endphp
                    <th>{{ $month_count }} </th>
                @endfor
                    <th>Total On Time</th>
                    <th>Total Absent</th>
                    <th>Total Half Day</th>
                    <th>Total Late Coming</th>
                    <th>Total Leaves</th>
                    <th>Total Work Hours</th>
                    
            </tr>
            <tr class="fw-bold text-muted bg-light">
                <th></th>
                @for ($month_count = 1; $month_count <= $month_days; ++$month_count)
                    @php
                    
                    $date = '' . $month_count . '-' . $month . '-' . $year . '';
                    @endphp
                    <th style="" >{{ substr(date('D',strtotime($date)),0,3) }} </th>
                @endfor
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                
            </tr>
        </thead>
        <tbody>
            @if (!empty($all_attendances))
                @foreach ($all_attendances as $all_attendance)
                    <tr>
                        @php $calculate_absent_shift = 0;
                            $calculate_absent_shift += CustomHelper::employee_shift_time_calculation_absent($all_attendance['employee_id'],$month,$year,$from_date=NUll,$to_date=Null);  @endphp
                        <td class="td-border">{!! CustomHelper::getEmpProfileDiv($all_attendance['employee_id']) !!}</td>
                        

                        @php 
                        
                            $present_count = 0;
                            $absent_count = 0;
                            $halfday_count = 0;
                            $late_count = 0;
                            $leave_count = 0;
                            $total_working_hours = 0;
                            $total_working_hours_actual = 0;
                            $in_day_time_actual = $out_day_time_actual = 0 ;
                            $day = 1;

                            $attendance_total_count = count($all_attendance['attendance']);

                            if($attendance_total_count == 0){
                                
                                // $calculate_working_hour['hours_actual'] = "00:00";
                                // $calculate_working_hour['hours_worked'] = "00:00";
                            }
                            
                            
                        
                        @endphp
                        @php $all_attendance['employee_offday1'] = (!empty($all_attendance['employee_offday1'])) ? explode(",",$all_attendance['employee_offday1']):[]; @endphp
                        @for ($month_count = 1; $month_count <= $month_days; ++$month_count)
                            @php
                                
                                $date = '' . $month_count . '-' . $month . '-' . $year . '';
                                $name_of_day = date('l', strtotime($date));
                                $absent = true;
                                $helper_absent = false;
                                $total_working_hours_actual = 0;
                                $total_working_hours = 0;
                                $loop_count = 0;

                            @endphp
                            
                            <td class="td-border">
                             
                                @foreach ($all_attendance['attendance'] as $key=>$atty)

                                    @php

                                    $calculate_shift_timing = CustomHelper::calculate_shift_timings_shift_management($atty['att_shift_start_time'],$atty['att_shift_end_time'],$atty['att_check_in_status'],$atty['att_check_out_status']);
                                    $calculate_working_hour = CustomHelper::calculate_working_hour($atty['att_check_out_status'],$atty['att_check_in_time'],$atty['att_check_out_time'],$atty['att_created_at'],$atty['att_updated_at']);
                                
                                    $shift_components = explode(":", $calculate_shift_timing);
                                    $shift_seconds = $shift_components[0]*60*60 + $shift_components[1]*60;
                                    $total_working_hours_actual +=$shift_seconds;

                                    $components = explode(":",  $calculate_working_hour);
                                    $seconds = $components[0]*60*60 + $components[1]*60;
                                    $total_working_hours +=$seconds;

                                    @endphp
                                    @if ($atty['att_for_date'] != '' && $atty['att_for_date'] == $month_count)  
                                        @php $absent = false; 
                                        @endphp                                                                     
                                        
                                        @if ($atty['att_check_overall_status'] == 'Present')
                                            @php $present_count++; @endphp
                                            <i class="fa-solid fa-calendar-check text-success"
                                           ></i>
                                        @elseif($atty['att_check_overall_status'] == 'Absent')
                                            @php $absent_count++; @endphp
                                            <i class="fa-solid fa-xmark text-danger"
                                           ></i>
                                        @elseif($atty['att_check_overall_status'] == 'Half day')
                                            @php $halfday_count++; @endphp
                                            <i class="fa-solid fa-calendar-check text-danger"
                                            ></i>
                                        @elseif($atty['att_check_overall_status'] == 'Late Coming')
                                            @php $late_count++; @endphp
                                            <i class="fa-solid fa-calendar-check text-gray"
                                               ></i>
                                        @elseif(in_array($atty['att_check_overall_status'],$manage_leaves))
                                               @php $leave_count++;
                                               @endphp
                                               <i class="fa-solid fa-user-large-slash text-info"
                                              ></i>
                                           
                                        
                                        @elseif($atty['att_check_overall_status'] == 'Early out')
                                            <i class="fa-solid fa-clock text-info"
                                           ></i>
                                        @elseif(substr($atty['att_check_overall_status'] , 0, 7) == 'Holiday')

                                            <i class="fa-solid fa-umbrella-beach text-warning"
                                               ></i>
                                            
                                        @endif

                                        @if(substr($atty['att_check_overall_status'] , 0, 7) == 'Holiday' || in_array($atty['att_check_overall_status'], $manage_leaves) ) 
                                            {{ $atty['att_check_overall_status'] }}
                                        @else
                                            CI: {{ $atty['att_check_in_time'] }} | CO: {{ $atty['att_check_out_time'] }}  | {{ $atty['att_check_overall_status'] }}
                                        @endif 
                                        
                                    @endif
                                @endforeach
                                @php
                                    
                                    $date_filter = strtotime(date("Y-m-d", strtotime($year . "-" . $month . "-" . $month_count )));
                                    $name_of_day = date('l' , $date_filter);
                                    $today = strtotime(date("Y-m-d"));
                                @endphp
                                
                                @if($absent )
                
                                    @if(in_array($name_of_day,$all_attendance['employee_offday1']) )
                                        <i class="fa-solid fa-sun text-warning"
                                        ></i>
                                        Off Day
                                        
                                    @elseif($date_filter < $today)
                                        @php $absent_count++; 
                                        @endphp
                                        <i class="fa-solid fa-xmark text-danger"
                                        ></i>
                                        Absent
                                    @endif
                                @endif
                                
                            </td>
                            

                            
                        @endfor
                        @php $total_shift_calculation =  $calculate_absent_shift + $total_working_hours_actual;
                        $total_working_hours_actual = CustomHelper::calculate_total_overtime($total_shift_calculation); @endphp
                            <td class="td-border">{{ $present_count }}</td>
                            <td class="td-border">{{ $absent_count }}</td>
                            <td class="td-border">{{ $halfday_count }}</td>
                            <td class="td-border">{{ $late_count }}</td>
                            <td class="td-border">{{ $leave_count }}</td>
                            <td class="td-border"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            data-bs-title="Actual Hours : 
                            {{$total_working_hours_actual['overtime']}}
                            ">
                            @php $total_working_hours = CustomHelper::calculate_total_overtime($total_working_hours); @endphp
                            {{$total_working_hours['overtime'] }}

                            </td>
                            
                        
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</body>
</html>