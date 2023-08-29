<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <style>

        .header{
            margin-top: 5px;
        }
        .company-logo {

            margin-left: 10%;
            width: 15%;
            float: left;
            padding: 15px;
           
        }

        .company-name {
            top: 10px;
            width: 100%;
            float: left;
            padding: 15px;
          
        }
        .company-name span{
            font-size: 25px;
            font-weight: bold;
        }
       
        .td-border-main{
            border:1px solid;
            font-size: 15px;
        }
        .table-data{
            padding-left:10px;
            padding-top:10px;
           
        }
        .td-border{
            border:1px solid;
            font-size: 11px;
        }

        .text-center {
            text-align: center !important;
        }

        .col-1 {width: 8.33%;}
        .col-2 {width: 16.66%;padding-bottom:2%}
        .col-3 {width: 25%;}
        .col-4 {width: 33.33%;}
        .col-5 {width: 41.66%;}
        .col-6 {width: 50%;}
        .col-7 {width: 58.33%;}
        .col-8 {width: 66.66%;}
        .col-9 {width: 75%;}
        .col-10 {width: 83.33%;}
        .col-11 {width: 91.66%;}
        .col-12 {width: 100%;}
    </style>
    </head>
    <body>

        @php
         $companySetting = CustomHelper::hcm_default_settings($pdf = 'pdf');
        @endphp

        <div class="col-12 text-center">

            @if(!empty($companySetting['company_logo']))
                <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo">
            @else
                <img src="{{ public_path('/portal_assets/images/logo.png') }}" draggable="false" class="img-fluid" >
            @endif
            
            <h1 class="text-center" style="margin-top:1%">{{ $companySetting['company_name']}} </h1>

            <h2 class="text-center" style="margin-top:1%">Employee Attendance Sheet</h2>

        </div>  

        <table class="col-12">
            <tbody>
                <tr>
                    <td class="td-border-main table-data"><b>Employee</b></td>
                    <td class="td-border-main table-data" >{{!empty($employee) ? $employee[0]['employee_name']. ' - ' .$employee[0]['employee_code'] : '' }}</td>
                    <td class="td-border-main table-data"> <b>Reporting Authority</b></td>
                    <td class="td-border-main table-data">{{!empty($employee_senior_manager) ? $employee_senior_manager[0]['employee_name'] : '' }}</td>
                </tr>
                <tr>
                    <td class="td-border-main table-data"><b>Department</b></td>
                    <td class="td-border-main table-data" >{{!empty($employee) ? $employee[0]['department_name'] : '' }}</td>
                    <td class="td-border-main table-data"><b>Attendance Period</b></td>
                    <td class="td-border-main table-data">
                        @if(!empty($month) && !empty($year) && empty($from_date) && empty($to_date))

                            @php $apply_month_year =  $month .'-'. $year; @endphp
                            {{\Carbon\Carbon::createFromFormat('m-Y', $apply_month_year)->format('F-Y') }}
                        @else
                        
                            {{date('d-F', strtotime(Request::get('from_date'))) .' to ' .date('d-F-Y', strtotime(Request::get('to_date')))}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="td-border-main table-data"><b>Designation</b></td>
                    <td class="td-border-main table-data" >{{!empty($employee) ? $employee[0]['designation_name'] : '' }}</td>
                    <td class="td-border-main table-data"><b>Report Date</b></td>
                    <td class="td-border-main table-data">{{date('d-M-Y')}}</td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <table class="col-12">
                <thead>
                    <tr style="background:lightgray">
                        <th rowspan="2" class="text-center td-border">Attendance Date </th>
                        <th colspan="2" class="text-center td-border">Check In</th>
                        <th colspan="2" class="text-center td-border">Check Out </th>
                        <th rowspan="2" class="text-center td-border">Work Hours</th>
                        <th rowspan="2" class="text-center td-border">Extra Hours</th>
                        <th rowspan="2" class="text-center td-border">Late Minutes</th>
                        <th rowspan="2" class="text-center td-border">Early Depar. Minutes</th>
                        <th rowspan="2" class="text-center td-border">Leave/Deputation</th>
                        <th rowspan="2" class="text-center td-border">Att Source</th>
                        <th rowspan="2" class="text-center td-border">Status </th>
                    </tr>
                    <tr style="background:lightgray">
                        <th class="text-center">Date</th>
                        <th class="text-center">Time</th>
                        <th class="text-center">Date</th>
                        <th class="text-center">Time</th>
                    </tr>
                </thead>

                <tbody>
                    @php $employee[0]['employee_offday1'] = (!empty($employee[0]['employee_offday1'])) ? explode(",",$employee[0]['employee_offday1']):[]; @endphp
                    @for ($month_count = $month_count; $month_count <= $month_days; $month_count++)
                    @php $absent = true; @endphp
                    <tr style="border:1px solid" >
                        @php
                            $current_date = $dates[$month_count];
                            $name_of_day = date("l", strtotime($current_date ));	
                            $name_of_day_short = date("D", strtotime($current_date ));	
                        @endphp
                        
                        <td class="text-center td-border" >
                            {{ $current_date }}  - {{  $name_of_day_short }}
                        </td>
                        <td class="text-center td-border">
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
                        <td class="text-center td-border">
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
                        <td class="text-center td-border">
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
                        <td class="text-center td-border">
                            @if(!empty($attendances))
                                @foreach($attendances as $attendance)
                                      @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        @if ($attendance['att_check_overall_status'] == 'Present' && $attendance['att_check_out_time'] != '00:00:00')
                                            {{ $attendance['att_check_out_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day' && $attendance['att_check_out_time'] != '00:00:00')
                                            {{ $attendance['att_check_out_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming' && $attendance['att_check_out_time'] != '00:00:00')
                                            {{ $attendance['att_check_out_time'] }}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out' && $attendance['att_check_out_time'] != '00:00:00')
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
                        <td class="text-center td-border">
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
                        <td class="text-center td-border">
                            @if(!empty($attendances))

                                @foreach($attendances as $attendance)
                                    @php 
                                        $employee_shift_times =  CustomHelper::calculate_shift_timings_shift_management($attendance['att_shift_start_time'],$attendance['att_shift_end_time'],$attendance['att_check_in_status'],$attendance['att_check_out_status']);
                                        $employee_working_hours =   CustomHelper::calculate_working_hour($attendance['att_check_out_status'],$attendance['att_check_in_time'],$attendance['att_check_out_time'],$attendance['att_created_at'],$attendance['att_updated_at']);
                                    
                                    @endphp
                                      @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        
                                        @if ($attendance['att_check_overall_status'] == 'Present' && $attendance['att_check_out_time'] != '00:00:00')
                                        
                                        {{ CustomHelper::extra_hours_working($employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day' && $attendance['att_check_out_time'] != '00:00:00')
                                            {{CustomHelper::extra_hours_working($employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming' && $attendance['att_check_out_time'] != '00:00:00')
                                            {{CustomHelper::extra_hours_working($employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out' && $attendance['att_check_out_time'] != '00:00:00')
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
                        <td class="text-center td-border">
                            @if(!empty($employee) && $employee[0]['employee_shift_flexibilty'] != 1)
                                @if(!empty($attendances))

                                    @foreach($attendances as $attendance)
                                        @php 
                                            $employee_shift_times =   CustomHelper::calculate_shift_timings_shift_management($attendance['att_shift_start_time'],$attendance['att_shift_end_time'],$attendance['att_check_in_status'],$attendance['att_check_out_status']);
                                        @endphp
                                          @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                            @php $absent = false; @endphp
                                            
                                            @if ($attendance['att_check_overall_status'] == 'Present' && $attendance['att_check_out_time'] != '00:00:00')
                                            
                                            {{ CustomHelper::late_checkIn_hour($attendance['att_shift_start_time'],$attendance['att_check_in_time'])}}
                                            @elseif($attendance['att_check_overall_status'] == 'Half day' && $attendance['att_check_out_time'] != '00:00:00')
                                                {{CustomHelper::late_checkIn_hour($attendance['att_shift_start_time'],$attendance['att_check_in_time'])}}
                                            @elseif($attendance['att_check_overall_status'] == 'Late Coming' && $attendance['att_check_out_time'] != '00:00:00')
                                                {{CustomHelper::late_checkIn_hour($attendance['att_shift_start_time'],$attendance['att_check_in_time'])}}
                                            @elseif($attendance['att_check_overall_status'] == 'Early out' && $attendance['att_check_out_time'] != '00:00:00')
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
                        <td class="text-center td-border">
                            @if(!empty($attendances))

                                @foreach($attendances as $attendance)
                                    @php 
                                        $employee_shift_times =  CustomHelper::calculate_shift_timings_shift_management($attendance['att_shift_start_time'],$attendance['att_shift_end_time'],$attendance['att_check_in_status'],$attendance['att_check_out_status']);
                                        $employee_working_hours =   CustomHelper::calculate_working_hour($attendance['att_check_out_status'],$attendance['att_check_in_time'],$attendance['att_check_out_time'],$attendance['att_created_at'],$attendance['att_updated_at']);
                                    @endphp
                                      @if ($attendance['att_for_date'].'-'.$attendance['att_for_month'].'-'.$attendance['att_for_year'] == $dates[$month_count])
                                        @php $absent = false; @endphp
                                        
                                        @if ($attendance['att_check_overall_status'] == 'Present' && $attendance['att_check_out_time'] != '00:00:00')
                                        
                                        {{ CustomHelper::early_checkOut_hour($attendance['att_check_out_status'],$employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Half day' && $attendance['att_check_out_time'] != '00:00:00')
                                            {{CustomHelper::early_checkOut_hour($attendance['att_check_out_status'],$employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Late Coming' && $attendance['att_check_out_time'] != '00:00:00')
                                            {{CustomHelper::early_checkOut_hour($attendance['att_check_out_status'],$employee_shift_times,$employee_working_hours)}}
                                        @elseif($attendance['att_check_overall_status'] == 'Early out' && $attendance['att_check_out_time'] != '00:00:00')
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

                        <td  class="text-center td-border"> 
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

                        <td class="text-center td-border">
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

                        <td class="text-center td-border"> 
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
                                        @elseif($attendance['att_check_overall_status'] == 'RPL')
                                            RPL
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
                                @if(in_array($name_of_day,$employee[0]['employee_offday1']) )
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
    
       
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>


