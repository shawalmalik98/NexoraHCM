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
            font-size: 18px;
            padding:5px;
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
                 <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo" style="width:10%">
            @else
                <img src="{{ CustomHelper::hcm_default_settings('pdf')['company_logo'] ?? '' }}" draggable="false" class="img-fluid" >
            @endif
            
            <h1 class="text-center" style="margin-top:1%">{{ $companySetting['company_name']}} </h1>

            <h2 class="text-center" style="margin-top:1%">Employee(s) Work Anniversary Report </h2>

        </div>    

        <div class="col-10" style="margin-left:10%;margin-top:3%">

            <div class="row">
                <table>
                    <thead>
                        <tr style="background:lightgray">

                            <th class="text-center td-border">Employee Code & Name</th>
                            <th class="text-center td-border">Department Name</th>
                            <th class="text-center td-border">Designation Name</th>
                            <th class="text-center td-border">Employee DOJ</th>
                            <th class="text-center td-border">Duration</th>

                        </tr>
                    </thead>

                    <tbody>
                        @if(!empty($employees))
                            @foreach($employees as $employee)
                                <tr>
                                    <td class="td-border" style="text-align:left;">{{$employee['employee_code']}} - {{$employee['employee_name']}}</td>
                                    <td class="text-center td-border">{{$employee['department_name']}}</td>
                                    <td class="text-center td-border">{{$employee['designation_name']}}</td>
                                    <td class="text-center td-border">{{ date('d, F Y' ,strtotime($employee['employee_doj'])) }}</td>
                                    <td class="text-center td-border">{{CustomHelper::calculate_employeement_duration_period($employee['employee_doj']  , date('Y-m-d'))}}</td>
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
