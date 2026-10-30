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
            font-size: 13px;
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

            <h2 class="text-center" style="margin-top:1%">Employee(s) Loan Details</h2>

        </div>  
        
        <div class="col-12" style="margin-top:3%">
            
            <div class="row">
                <table>
                    <thead>
                        <tr style="background:lightgray">
                    
                            <th class="text-center td-border">Ref ID</th>
                            <th class="text-center td-border">Employee Name</th>
                            <th class="text-center td-border">Department Name</th>
                            <th class="text-center td-border">Designation Name</th>
                            <th class="text-center td-border">Loan amount</th>
                            <th class="text-center td-border">Submitted amount</th>
                            <th class="text-center td-border">Remaining amount</th>
                            <th class="text-center td-border">Deduction amount</th>
                            <th class="text-center td-border">Deduction start</th>
                            <th class="text-center td-border">Loan status</th>
                            <th class="text-center td-border">Issued date</th>
                            
                        </tr>
                    </thead>
    
                    <tbody>
                        @if (!empty($all_loans))
                            @php  $currency = CustomHelper::hcm_system_currency(); @endphp   
                            @foreach ($all_loans as $all_loan)
                                <tr>
                                    <td class="text-center td-border">{{ $all_loan['loan_id'] }}</td>
                                    <td class="text-center td-border">{{$all_loan['employee_code']}}-{{$all_loan['employee_name']}}</td>
                                    <td class="text-center td-border">{{$all_loan['department_name']}}</td>
                                    <td class="text-center td-border">{{$all_loan['designation_name']}}</td>
                                    <td class="text-center td-border">{{ $all_loan['loan_given_amount'].' '.$currency[0]['system_currency'] }}</td>
                                    <td class="text-center td-border">{{ $all_loan['loan_submitted_amount'].' '.$currency[0]['system_currency'] }}</td>
                                    <td class="text-center td-border">{{ $all_loan['loan_remaining_amount'].' '.$currency[0]['system_currency'] }}</td>

                                    @if($all_loan['loan_deduction_type'] == 1)
                                        <td class="text-center td-border">{{ $all_loan['loan_deduction_amount'].' '.$currency[0]['system_currency'] }}</td>
                                    @else
                                        <td class="text-center td-border">{{ $all_loan['loan_deduction_amount'].  " %" }}</td>
                                    @endif
                                    <td class="text-center td-border">{{ $all_loan['loan_deduction_start'] ?? 'N/A'}}</td>

                                    <td class="text-center td-border">
                                        @if ($all_loan['loan_remaining_amount'] > '0')
                                            Unpaid
                                        @elseif($all_loan['loan_remaining_amount'] == '0')
                                            Paid
                                        @endif
                                    </td>
                                    <td class="text-center td-border">{{ $all_loan['loan_issuance_date'] }}</td>
                                
                                </tr>
                            @endforeach
                        @endif                             
                
                    </tbody> 
                    <tfoot style="background:lightgray">
                        <tr>
                            <th class="text-center td-border" colspan="5">Total employees</th>
                            <th class="text-center td-border" >Total Loan Given (By Company)</th>
                            <th class="text-center td-border" >Total Loan Return (By Employees)</th>
                            <th class="text-center td-border" >Total Loan Remaining (On Employees)</th>
                            <th class="text-center td-border" colspan="2">Paid</th>
                            <th class="text-center td-border">Unpaid</th>
                           
                        </tr>
                        <tr>
                            <td class="text-center td-border" colspan="5">{{$employee_loans}}</td>
                            <td class="text-center td-border">{{$total_loan}}</td>
                            <td class="text-center td-border">{{$loan_return}}</td>
                            <td class="text-center td-border">{{$loan_reamining}}</td>
                            <td class="text-center td-border" colspan="2">{{$loan_status_paid}}</td>
                            <td class="text-center td-border">{{ $loan_status_unpaid}}</td>
                        </tr>
                    </tfoot>
                    
                </table>
    
            </div>
        </div>
       
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>