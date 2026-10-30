<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Monthly Attendance Report</title>
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
       
        .td-border{
            border:1px solid;
            font-size: 18px;
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
            <span style="font-size:20px">Report Date: {{date('d M, Y')}}</span>
        </div>

        @php
         $companySetting = CustomHelper::hcm_default_settings($pdf = 'pdf');
        @endphp

        <div class="col-12 text-center" style="clear:right">

            @if(!empty($companySetting['company_logo']))
                <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo" width="20%">
            @else
                <img src="{{ CustomHelper::hcm_default_settings('pdf')['company_logo'] ?? '' }}" draggable="false" class="img-fluid" >
            @endif
            
            <h1 class="text-center" style="margin-top:1%">{{ $companySetting['company_name']}} </h1>

            <h2 class="text-center" style="margin-top:1%">Employee Monthly Attendance Report</h2>

            <h4 class="text-center" style="margin-top:1%">For Period of ({{date("F", mktime(0, 0, 0, $month, 10))}} , {{$year}} )</h4>

        </div>  

        <div class="col-12" >
            
            <div class="row">
                <table>
                    <thead>
                        <tr style="background:lightgray">
                           
                            <th class="ps-3 td-border">Employee Name</th>
                            <th class="td-border text-center">Month Days</th>
                            <th class="td-border text-center">Present</th>
                            <th class="td-border text-center">Late / Early</th>
                            <th class="td-border text-center">Half Days</th>
                            <th class="td-border text-center">Absent</th>
                            <th class="td-border text-center">Leaves</th>
                            <th class="td-border text-center">Holidays</th>
                            <th class="td-border text-center">Off Days</th>
                        </tr>
                    </thead>
    
                    <tbody>
                        @if(!empty($all_attendances))
                            @foreach($all_attendances as $all_attendance)
                                <tr>
                                    <td class="ps-3 td-border">{{ $all_attendance['employee_name'] }} - {{ $all_attendance['employee_code'] }}</td>
                                    <td class="text-center td-border">{{$all_attendance['total_month_days']}}</td>
                                    <td class="text-center td-border">{{$all_attendance['total_present']}}</td>
                                    <td class="text-center td-border">{{$all_attendance['total_late_early']}}</td>
                                    <td class="text-center td-border">{{$all_attendance['total_half_day']}}</td>
                                    <td class="text-center td-border">{{$all_attendance['total_absent']}}</td>
                                    <td class="text-center td-border">{{$all_attendance['total_leave']}}</td>
                                    <td class="text-center td-border">{{$all_attendance['total_holiday']}}</td>
                                    <td class="text-center td-border">{{$all_attendance['total_offdays']}}</td>
                                
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
    
            </div>
        </div>
       
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
