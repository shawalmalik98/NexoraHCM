<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
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
                <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo">
            @else
                <img src="{{ public_path('/portal_assets/images/logo.png') }}" draggable="false" class="img-fluid" >
            @endif
            
            <h1 class="text-center" style="margin-top:1%">{{ $companySetting['company_name']}} </h1>

            <h2 class="text-center" style="margin-top:1%">Overtime Management Sheet</h2>

            <h4 class="text-center" style="margin-top:1%">
                @if (empty($month) && empty($year))
                    For the Period of ({{ date('F') . '-' . date('Y') }})
                @else
                    For the Period of ({{ date("F", mktime(0, 0, 0, $month, 10)) }}-{{ $year }})
                @endif
            </h4>

        </div>  
        
        <div class="col-10" style="margin-left:10%;margin-top:3%">
            
            <div class="row">
                <table>
                    <thead>
                        <tr style="background:lightgray">
                            <th class="text-center td-border">Employee Code</th>
                            <th class="text-center td-border">Employee Name</th>
                            <th class="text-center td-border">Shift Duration</th>
                            <th class="text-center td-border">Extra Time Worked</th>
                            <th class="text-center td-border">Overtime Period</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        
                        @if (!empty($all_attendaces))
                            @foreach (array_reverse($all_attendaces) as $all_attendance)
                                @if(!empty($all_attendance['attendance']))
                                    @foreach ($all_attendance['attendance'] as $key=>$atty)
                                        @php 
                                            
                                            $employee_shift_times =  CustomHelper::calculate_shift_timings_shift_management($atty['att_shift_start_time'],$atty['att_shift_end_time'],$atty['att_check_in_status'],$atty['att_check_out_status']);
                                            $employee_working_hours =  CustomHelper::calculate_working_hour($atty['att_check_out_status'],$atty['att_check_in_time'],$atty['att_check_out_time'],$atty['att_created_at'],$atty['att_updated_at']);
                                        
                                        @endphp
                                        @if( $employee_working_hours > $employee_shift_times)
                                                
                                            <tr>
                                                <td  class="text-center td-border">   {{$all_attendance['employee_code'] }}</td>
                                                <td  class="text-center td-border">   {{$all_attendance['employee_name'] }}</td>
                                                <td  class="text-center td-border">   {{ $employee_shift_times }}</td>
                                                <td  class="text-center td-border">   {{CustomHelper::calculate_total_overtime_monthly($all_attendance['attendance'])}}</td>
                                                <td  class="text-center td-border">   {{date('F-Y', strtotime($atty['att_for_year'].'-'.$atty['att_for_month'] ))}}</td>
                                            </tr>
                                            @php break; @endphp;
                                        @endif
                                        
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                       
                    </tbody>                     
                </table>
    
            </div>
        </div>
       
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
