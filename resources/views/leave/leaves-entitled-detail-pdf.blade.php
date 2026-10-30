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
                <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo" style="width:10%">
            @else
                <img src="{{ CustomHelper::hcm_default_settings('pdf')['company_logo'] ?? '' }}" draggable="false" class="img-fluid" >
            @endif
            
            <h1 class="text-center" style="margin-top:1%">{{ $companySetting['company_name']}} </h1>

            <h2 class="text-center" style="margin-top:1%">Leave Balance Report</h2>

        </div>  

        <table class="col-12">
            <tbody>
                <tr>
                    <td class="td-border-main table-data"><b>Employee</b></td>
                    <td class="td-border-main table-data" >{{!empty($employee_detail) ? $employee_detail[0]['employee_name']. ' - ' .$employee_detail[0]['employee_code'] : '' }}</td>
                    <td class="td-border-main table-data"> <b>Report ID</b></td>
                    <td class="td-border-main table-data">{{!empty($employee_detail) ? 'LVR-' . $employee_detail[0]['employee_code'] : '' }}</td>
                </tr>
                {{-- <tr>
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
                </tr> --}}
                <tr>
                    <td class="td-border-main table-data"><b>Report Type</b></td>
                    <td class="td-border-main table-data" >Leave Balance Report</td>
                    <td class="td-border-main table-data"><b>Report Date</b></td>
                    <td class="td-border-main table-data">{{date('d M, Y')}}</td>
                </tr>
            </tbody>
        </table>
        <div class="row">
            <table class="col-12">
                <thead>
                    <tr style="background:lightgray">
                        <th class="text-center td-border">S.No</th>
                        <th class="text-center td-border">Employee ID</th>
                        <th class="text-center td-border">Employee Name</th>
                        <th class="text-center td-border">Designation</th>
                        <th class="text-center td-border">Leave Type</th>
                        <th class="text-center td-border">Total Entitlement</th>
                        <th class="text-center td-border">Total Availed</th>
                        <th class="text-center td-border">Remaining Balance</th>
                    </tr>
                </thead>

                <tbody>
                    @php $val = 0; @endphp
                    @foreach ($leaves_entitled as $leaves_entitled_employee)
                        <tr style="border:1px solid" >
                            <td class="text-center td-border" >
                                {{ ++$val }}
                            </td>
                            <td class="text-center td-border">
                                {{ $employee_detail[0]['employee_code'] }}
                            </td>
                            <td class="text-center td-border">
                                {{ $employee_detail[0]['employee_name'] }}
                            </td>
                            <td class="text-center td-border">
                                {{ $employee_detail[0]['designation_name'] }}
                            </td>
                            <td class="text-center td-border">
                                {{ $leaves_entitled_employee['leave_name'] }}
                            </td>
                            <td class="text-center td-border">
                                {{ $leaves_entitled_employee['leave_allotement'] ?? 0 }}
                            </td>
                            <td class="text-center td-border">
                                {{ $leaves_entitled_employee['employee_availed_leaves'] }}
                            </td>
                            <td class="text-center td-border">
                                {{ $leaves_entitled_employee['leave_allotement'] - $leaves_entitled_employee['employee_availed_leaves'] }}
                            </td>
                        </tr>
                        @endforeach
                </tbody>
            </table>

        </div>
    
       
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>