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

        <div class="col-2" style="float:right"  >
            <span style="font-size:20px">Report Date: {{date('d M, Y')}}</span>
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

            <h2 class="text-center" style="margin-top:1%">Office Expenses</h2>

        </div>

        <div class="col-10" style="margin-left:10%;margin-top:3%">

            <div class="row">
                <table>
                    <thead>
                        <tr style="background:lightgray">

                            <th class="text-center td-border">Ref ID</th>
                            <th class="text-center td-border">Date</th>
                            <th class="text-center td-border">Reason</th>
                            <th class="text-center td-border">Source</th>
                            <th class="text-center td-border">Amount</th>
                            <th class="text-center td-border">Created By</th>
                            <th class="text-center td-border">Created At</th>

                        </tr>
                    </thead>

                    <tbody>
                        @if(!empty($expenses))
                            @foreach($expenses as $expense)
                                <tr>
                                    <td class="text-center td-border">{{$expense['expense_id'] }}</td>
                                    <td class="text-center td-border">{{ $expense['expense_date'] }}</td>
                                    <td class="text-center td-border">{{ $expense['expense_reason'] }}</td>
                                    <td class="text-center td-border">{{ $expense['expense_source'] }}</td>
                                    <td class="text-center td-border">{{ $expense['expense_amount'] }}</td>
                                    <td class="text-center td-border">{{$expense['employee_code']}} - {{$expense['employee_name']}}</td>
                                    <td class="text-center td-border">{{ $expense['expense_created_at'] }}</td>
                                </tr>
                            @endforeach
                        @endif

                    </tbody>

                     <tfoot style="background:lightgray">
                        <tr>
                            <th class="text-center td-border" colspan="7">Total Expense</th>
                           
                        </tr>
                        <tr>
                            <td class="text-center td-border" colspan="7">{{$total_expense}}</td>
                           
                        </tr>
                    </tfoot>

                </table>

            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    </body>
</html>
