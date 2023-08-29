<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Daily Attendance Report</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" >
    <style>

        .header{
            margin-top: 5px;
        }
       
        .td-border{
            border:1px solid;
            font-size: 12px;
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
        <div class="col-2" style="float:right">
            <span style="font-size:15px">Report Date: {{date('d M, Y')}}</span>
        </div>

       @php
         $companySetting = CustomHelper::hcm_default_settings($pdf = 'pdf');
        @endphp
        <div class="col-12 text-center" style="clear:right">

            @if(!empty($companySetting['company_logo']))
                <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo" width="20%">
            @else
                <img src="{{ public_path('/portal_assets/images/logo.png') }}" draggable="false" class="img-fluid" >
            @endif
            
            <h1 class="text-center" style="margin-top:1%">{{$companySetting['company_name']}}</h1>

            <h2 class="text-center" style="margin-top:1%">Employee(s) Daily Attendance Sheet </h2>
           
            @if(empty(Request::get('d')))
                <h4 class="text-center">For the Period of ({{ date('d F, Y')}})</h4>
            @else
                <h4 class="text-center">For the Period of ({{ date('d F, Y', strtotime(Request::get('d'))) }})</h4>
            @endif

        </div>
        
        <div class="col-10" style="margin-left:10%;margin-top:3%">
            
            <div class="row">
                <table>
                    <thead>
                        <tr style="background:lightgray">
                            <th class="td-border ps-3">Employee Name</th>
                            <th class="text-center td-border">Check In</th>
                            <th class="text-center td-border">Check Out </th>
                            <th class="text-center td-border">Hours Worked</th>
                            <th class="text-center td-border">Check In Source</th>
                            <th class="text-center td-border">Check Out Source</th>
                            <th class="text-center td-border">Status </th>
                        </tr>
                    </thead>
    
                    <tbody>
                        @php 
                            $present = 0;
                            $halfday = 0;
                            $late_coming = 0;
                            $early_out = 0;
                            $offdays = 0;
                            $absent_count = 0;
                            $employees = 0;
                            $holiday_count = 0;
                            if(!empty(Request::get('d'))){
                                $d = date('d', strtotime(Request::get('d')));
                                $m = date('m', strtotime(Request::get('d')));
                                $y = date('Y', strtotime(Request::get('d')));
                                $current_date = $d . '-' .  $m . '-' . $y;
                            }else{
                                $current_date = date('d') . '-' . date('m') . '-' . date('Y');
                            }
                            $name_of_day = date("l", strtotime($current_date));	
                        @endphp
                        @if (!empty($attendance))
                            @foreach ($attendance as $attendances)
                                @php $attendances['employee_offday1'] = (!empty($attendances['employee_offday1'])) ? explode(",",$attendances['employee_offday1']):[]; @endphp
                                @php $absent = true;@endphp
                                <tr>
                                    <td class="td-border ps-3">
                                        {{$attendances['employee_code']}} - {{$attendances['employee_name']}}
                                        @php $employees++; @endphp 
                                    </td>

                                    <td class="text-center td-border"> 
                                        
                                        @foreach ($attendances['attendance'] as $daily_atty)
                                                
                                            @php $absent = false; @endphp
                                        
                                            @if ($daily_atty['att_check_overall_status'] == 'Present')
                                                {{ $daily_atty['att_check_in_time'] }}
                                            @elseif($daily_atty['att_check_overall_status'] == 'Half day')
                                                {{ $daily_atty['att_check_in_time'] }}
                                            @elseif($daily_atty['att_check_overall_status'] == 'Late Coming')
                                                {{ $daily_atty['att_check_in_time'] }}
                                            @elseif(in_array($daily_atty['att_check_overall_status'],$manage_leaves) || $daily_atty['att_check_overall_status'] == 'RPL')
                                                    00:00:00       
                                            @elseif($daily_atty['att_check_overall_status'] == 'Absent')
                                                    00:00:00
                                            @elseif($daily_atty['att_check_overall_status'] == 'Early out')
                                                {{ $daily_atty['att_check_in_time'] }}
                                            @elseif(substr($daily_atty['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                    00:00:00
                                            @endif
                                            
                                        @endforeach
                                    
                                        @php
                                    
                                        $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                        $today = strtotime(date("Y-m-d"));
                                        @endphp
                                        
                                        @if($absent )
                                            @if(in_array($name_of_day,$attendances['employee_offday1']) )
                                                00:00:00
                                            @elseif($date_filter< $today)
                                                00:00:00
                                            @else
                                                00:00:00    
                                            @endif
                                        @endif
                                    
                                    </td>

                                    <td  class="text-center td-border"> 
                                        
                                        @foreach ($attendances['attendance'] as $today_atty)
                                    
                                            @php $absent = false; @endphp
                                            @if ($today_atty['att_check_overall_status'] == 'Present')
                                                {{ $today_atty['att_check_out_time'] }}
                                            @elseif($today_atty['att_check_overall_status'] == 'Half day')
                                                {{ $today_atty['att_check_out_time'] }}
                                            @elseif($today_atty['att_check_overall_status'] == 'Late Coming')
                                                {{ $today_atty['att_check_out_time'] }}
                                            @elseif(in_array($today_atty['att_check_overall_status'],$manage_leaves) || $today_atty['att_check_overall_status'] == 'RPL')
                                                    00:00:00    
                                            @elseif($today_atty['att_check_overall_status'] == 'Absent')
                                                    00:00:00
                                            @elseif($today_atty['att_check_overall_status'] == 'Early out')
                                                {{ $today_atty['att_check_out_time'] }}
                                            @elseif(substr($today_atty['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                    00:00:00
                                            @endif
                                            
                                        @endforeach
                                        
                                        @php
                                        
                                            $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                            $today = strtotime(date("Y-m-d"));
                                        @endphp
                                        
                                        @if($absent )
                                            @if(in_array($name_of_day,$attendances['employee_offday1']) )
                                                00:00:00
                                            @elseif($date_filter< $today)
                                                00:00:00
                                            @else    
                                                00:00:00
                                            @endif
                                        @endif
                                    
                                    </td> 

                                    <td class="text-center td-border">
                                        
                                        @foreach ($attendances['attendance'] as $atty_hours)
                                            
                                            @php $absent = false; @endphp
                                            @if ($atty_hours['att_check_overall_status'] == 'Present')
                                                {{ CustomHelper::calculate_working_hour($atty_hours['att_check_out_status'],$atty_hours['att_check_in_time'],$atty_hours['att_check_out_time'],$atty_hours['att_created_at'],$atty_hours['att_updated_at'])}}
                                            @elseif($atty_hours['att_check_overall_status'] == 'Half day')
                                                {{ CustomHelper::calculate_working_hour($atty_hours['att_check_out_status'],$atty_hours['att_check_in_time'],$atty_hours['att_check_out_time'],$atty_hours['att_created_at'],$atty_hours['att_updated_at'])}}
                                            @elseif($atty_hours['att_check_overall_status'] == 'Late Coming')
                                                {{ CustomHelper::calculate_working_hour($atty_hours['att_check_out_status'],$atty_hours['att_check_in_time'],$atty_hours['att_check_out_time'],$atty_hours['att_created_at'],$atty_hours['att_updated_at'])}}
                                            @elseif(in_array($atty_hours['att_check_overall_status'],$manage_leaves) || $atty_hours['att_check_overall_status'] == 'RPL' )
                                            00:00:00
                                            
                                            @elseif($atty_hours['att_check_overall_status'] == 'Absent')
                                            00:00:00
                                            @elseif($atty_hours['att_check_overall_status'] == 'Early out')
                                            {{ CustomHelper::calculate_working_hour($atty_hours['att_check_out_status'],$atty_hours['att_check_in_time'],$atty_hours['att_check_out_time'],$atty_hours['att_created_at'],$atty_hours['att_updated_at'])}}
                                            @elseif(substr($atty_hours['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                00:00:00
                                            @endif
                                        
                                        @endforeach
                                       
                                        @php
                                            $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                            $today = strtotime(date("Y-m-d"));
                                        @endphp
                                        
                                        @if($absent )
                                            @if(in_array($name_of_day,$attendances['employee_offday1']) )
                                                00:00:00
                                            @elseif($date_filter< $today)
                                                00:00:00
                                            @else
                                                00:00:00    
                                            @endif
                                        @endif

                                    </td>

                                    <td class="text-center td-border">
                                        
                                        @foreach ($attendances['attendance'] as $atty_hours)
                                            
                                            @php $absent = false; @endphp
                                            @if ($atty_hours['att_check_in_source'] == '1')
                                                Web Application
                                            @elseif($atty_hours['att_check_in_source'] == '2')
                                                Mobile Application
                                            @elseif($atty_hours['att_check_in_source'] == '3')
                                                Updated Attendance
                                            @else
                                                Machine Attendance
                                            @endif
                                        
                                        @endforeach
                                       
                                        @php
                                            $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                            $today = strtotime(date("Y-m-d"));
                                        @endphp
                                        
                                        @if($absent )
                                            -
                                        @endif

                                    </td>

                                    <td class="text-center td-border">
                                        
                                        @foreach ($attendances['attendance'] as $atty_hours)
                                            
                                            @php $absent = false; @endphp
                                            @if ($atty_hours['att_check_out_source'] == '1')
                                                Web Application
                                            @elseif($atty_hours['att_check_out_source'] == '2')
                                                Mobile Application
                                            @elseif($atty_hours['att_check_out_source'] == '3')
                                                Updated Attendance
                                            @else
                                                Machine Attendance
                                            @endif
                                        
                                        @endforeach
                                       
                                        @php
                                            $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                            $today = strtotime(date("Y-m-d"));
                                        @endphp
                                        
                                        @if($absent )
                                            -
                                        @endif

                                    </td>

                                    <td class="text-center td-border"> 
                                        @foreach ($attendances['attendance'] as $atty )
                                            
                                            @if ($atty['att_for_date'] !== '' )
                                                @php $absent = false;@endphp
                                                @if ($atty['att_check_overall_status'] == 'Present')
                                                    @php $present++; @endphp
                                                    On Time
                                                @elseif($atty['att_check_overall_status'] == 'Half day')
                                                    @php $halfday++; @endphp
                                                    Half Day
                                                @elseif($atty['att_check_overall_status'] == 'Late Coming')
                                                    @php $late_coming++; @endphp
                                                    Late Coming                                                
                                                @elseif(in_array($atty['att_check_overall_status'],$manage_leaves))
                                                    {{$atty['att_check_overall_status']}}                
                                                @elseif($atty['att_check_overall_status'] == "RPL")
                                                    {{$atty['att_check_overall_status']}}                                                
                                                @elseif($atty['att_check_overall_status'] == 'Absent')
                                                    Absent
                                                @elseif($atty['att_check_overall_status'] == 'Early out')
                                                    @php $early_out++; @endphp
                                                    Early out
                                                @elseif(substr($atty['att_check_overall_status'] , 0, 7) == 'Holiday')
                                                @php $holiday_count++; @endphp
                                                    Holiday
                                                @endif
                                                
                                            @endif
                                        
                                        @endforeach

                                        @php
                                            $date_filter = strtotime(date("Y-m-d", strtotime($current_date)));
                                            $today = strtotime(date("Y-m-d"));
                                        @endphp
                                
                                        @if($absent )
                                            @if(in_array($name_of_day,$attendances['employee_offday1']) )
                                                @php $offdays++; @endphp
                                                Off Day

                                            @elseif($date_filter < $today)
                                                @php $absent_count++; @endphp
                                                Absent
                                    
                                            @elseif($date_filter == $today)
                                                @php $absent_count++; @endphp
                                                Absent
                                            @endif
 
                                        @endif
                                    
                                    </td> 

                                </tr>   
                                
                            @endforeach
  
                        @endif
                
                    </tbody> 
                    <tfoot style="background:lightgray">
                        <tr>
                            <th class="text-center td-border">Total Employees</th>
                            <th class="text-center td-border">Total On Time</th>
                            <th class="text-center td-border">Total Early / Late</th>
                            <th class="text-center td-border">Total Half Day</th>
                            <th class="text-center td-border">Total Absent</th>
                            <th class="text-center td-border">Total Leave</th>
                            <th class="text-center td-border">Total Off Day</th>
                        </tr>
                        <tr>
                            <td class="text-center td-border">{{$employees}}</td>
                            <td class="text-center td-border">{{$present}}</td>
                            <td class="text-center td-border">{{$late_coming + $early_out}}</td>
                            <td class="text-center td-border">{{$halfday}} </td>
                            <td class="text-center td-border">{{$absent_count}}</td>
                            <td class="text-center td-border">{{$t_employee_leaves??''}}</td>
                            <td class="text-center td-border">{{$offdays + $holiday_count}}</td>
                        </tr>
                    </tfoot>
                    
                </table>
    
            </div>
        </div>
       
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
