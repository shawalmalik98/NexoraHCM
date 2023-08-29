<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payslip || SourceCode</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://sourcecode.world/wp-content/uploads/2021/10/SourceCode-FavIcon.png"
        type="image/x-icon" />

    {{-- <link href="{{ url('/portal_assets/css/bootstrap.min.css') }}" rel="stylesheet" > --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        body {
            font-weight: 400 !important;
        }

        .content {
            background: url('http://localhost/hrmanagement//portal_assets/images/placeholder.png') no-repeat !important;
            background-position: center bottom !important;
            background-size: 100% !important;
        }

        .table {
            font-size: 14px !important;
            border: 1px solid #000000 !important;
        }

        .table thead tr td {
            text-align: center !important;
            border: 1px solid #000000 !important;
        }

        .table td.table-active {
            width: 100px;
            font-weight: 600 !important;
            background: #e7e6e6;
            box-shadow: none;
            border: 1px solid #000000 !important;
        }

        .table td.table-active2 {
            width: 100px;
            font-weight: 600 !important;
            background: #e7e6e6;
            box-shadow: none;
            border: 1px solid #000000 !important;
        }

        .table {
            border: 1px solid #000000 !important;
        }

        .table thead tr,
        .table thead tr th {
            border: 1px solid #000000 !important;
        }

        .table td {
            padding: 5px 10px !important;
            border: 1px solid #000000 !important;
            white-space: nowrap;
        }

        .extra-space {
            min-height: 150px !important;
        }

        .text-primary {
            color: #1274bb !important;
        }

        hr {
            margin: 2rem 0rem 0.3rem 0rem !important;
            opacity: 1 !important;
        }

        .button {
            background-color: #4CAF50;
            /* Green */
            border: none;
            color: white;
            padding: 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 12px;
            font-family: sans-serif;
        }

        .padding-img{

            margin-bottom: 12px; 

        }
        
    </style>
    
</head>




<body>
    
    @if ($download_button_show == false)
    <div class="container-fluid">
    @else
    <div class="container">
    @endif
        @if ($download_button_show == false)
            <img src="{{ public_path('portal_assets/images/header-bg.png') }}" draggable="false" class="img-fluid w-100 padding-img"
                alt="header" width="1200" height="277">
        @else
            <img src="{{ url('/portal_assets/images/header-bg.png') }}" draggable="false" class="img-fluid w-100 padding-img"
                alt="header" width="1200" height="277">
        @endif
        <div class="row no-guters content">
            <div class="col-lg-11 mx-auto">
                <div class="row pb-10" style="height:250px;">
                    <div class=" me-auto" style="width:25%  !important; float:left !important;">
                        @if ($download_button_show == false)
                            <img src="{{ public_path('portal_assets/images/logo.png') }}" draggable="false"
                                class="img-fluid w-100" alt="Source Code logo">
                        @else
                            <img src="{{ url('/portal_assets/images/logo.png') }}" draggable="false"
                                class="img-fluid w-100" alt="Source Code logo">
                        @endif
                    </div>
                    <div class=" ms-auto" style="width:25%  !important; float:right !important; padding-top:50px;">
                        @if (!empty($payroll) && !empty($payroll[0]['pc_created_at']))
                            Date: {{ $payroll[0]['pc_created_at'] }}
                        @else
                            Date: --/--/--
                        @endif

                    </div>
                </div>
                <div class="row my-3">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered bg-white">
                                <thead>
                                    <tr>
                                        <th colspan="6" class="table-active text-center text-primary">Pay Slip</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="table-active">Employee Code</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['employee_code']))
                                                {{ $payroll[0]['employee_code'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active">Gender</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['employee_gender']))
                                                {{ $payroll[0]['employee_gender'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active">Payroll Days</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_total_working_days']))
                                                {{ $payroll[0]['pc_total_working_days'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="table-active">Employee Name</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['employee_name']))
                                                {{ $payroll[0]['employee_name'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active">Status</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['employee_status']))
                                                {{ $payroll[0]['employement_status_name'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active"></td>
                                        <td>
                                        </td>

                                    </tr>
                                    <tr>
                                        <td class="table-active">Designation</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_designation']))
                                                {{ $payroll[0]['pc_employee_designation'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active">Bank Name</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_bank_name']))
                                                {{ $payroll[0]['pc_employee_bank_name'] }}
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <td class="table-active"></td>
                                        <td></td>

                                    </tr>
                                    <tr>
                                        <td class="table-active">Department</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_department']))
                                                {{ $payroll[0]['pc_employee_department'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active">Bank Account No</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_bank_account']))
                                                {{ $payroll[0]['pc_employee_bank_account'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active"></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="table-active">Location</td>
                                        <td>
                                            Karachi, Pakistan
                                        </td>
                                        <td class="table-active">Monthly Salary</td>
                                        <td>
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_month']) && !empty($payroll[0]['pc_year']))
                                                {{ date('F', mktime(0, 0, 0, $payroll[0]['pc_month'], 10)) }}-{{ $payroll[0]['pc_year'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active"></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="table-responsive mt-4 ">
                            <table class="table table-bordered ">
                                <thead>
                                    <tr>
                                        <th colspan="4" class="table-active text-center text-primary">Details of
                                            Earning</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="table-active2 text-center">Earnings</td>
                                        <td class="table-active2 text-center">Amount</td>
                                        <td class="table-active2 text-center text-center">Deductions</td>
                                        <td class="table-active2 text-center">Amount</td>
                                    </tr>
                                    <tr>
                                        <td class="table-active2">{{$salary_division[0]['salary_division_title'] ?? 'Gross Salary'}}</td>

                                        <td class="text-center">
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_gross_salary']))
                                                {{ $payroll[0]['pc_employee_gross_salary'] * ($salary_division[0]['salary_division_percentage'] ?? 1) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active2">Late</td>
                                        <td class="text-center">
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_late_early_deduction']))
                                                {{ $payroll[0]['pc_late_early_deduction'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="table-active2">{{$salary_division[1]['salary_division_title'] ?? ''}}</td>
                                        <td class="text-center">
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_gross_salary']) && !empty($salary_division[1]['salary_division_percentage']) )
                                                {{ $payroll[0]['pc_employee_gross_salary'] * ($salary_division[1]['salary_division_percentage'] ?? 0) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active2">Absent</td>
                                        <td class="text-center">
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_absent_deduction']))
                                                {{ $payroll[0]['pc_absent_deduction'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="table-active2">{{$salary_division[2]['salary_division_title'] ?? ''}}</td>
                                        <td class="text-center">
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_gross_salary']) && !empty($salary_division[2]['salary_division_percentage']))
                                                {{ $payroll[0]['pc_employee_gross_salary'] * ($salary_division[2]['salary_division_percentage'] ?? 0) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active2">Half Days</td>
                                        <td class="text-center">
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_half_day_deduction']))
                                                {{ $payroll[0]['pc_half_day_deduction'] }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @for($i=3;$i<count($salary_division);$i++)
                                        <td class="table-active2">{{$salary_division[$i]['salary_division_title'] ?? ''}}</td>
                                        <td class="text-center">
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_gross_salary']) && !empty($salary_division[$i]['salary_division_percentage']) )
                                                {{ $payroll[0]['pc_employee_gross_salary'] * ($salary_division[$i]['salary_division_percentage'] ?? 0) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="table-active2"></td>
                                        <td class="text-center">
                                                -
                                        </td>
                                    @endfor
                                    @if (!empty($max_loop))
                                        @for ($i = 0; $i < $max_loop; $i++)
                                            <tr>
                                                <td class="table-active2">
                                                    @if (!empty($extra_earning) && !empty($extra_earning[$i]['pc_earning_title']))
                                                        {{ $extra_earning[$i]['pc_earning_title'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (!empty($extra_earning) && !empty($extra_earning[$i]['pc_earning_amount']))
                                                        {{ $extra_earning[$i]['pc_earning_amount'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="table-active2">
                                                    @if (!empty($extra_deduction) && !empty($extra_deduction[$i]['pc_deduction_title']))
                                                        {{ $extra_deduction[$i]['pc_deduction_title'] }}
                                                    @else
                                                    @endif

                                                </td>
                                                <td class="text-center">
                                                    @if (!empty($extra_deduction) && !empty($extra_deduction[$i]['pc_deduction_amount']))
                                                        {{ $extra_deduction[$i]['pc_deduction_amount'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            </tr>
                                        @endfor
                                    @endif

                                    <tr>
                                        <td class="table-active2">
                                            Total Earning
                                        </td>
                                        <td class="text-center">
                                            {{$total_earning}}
                                        </td>
                                        <td class="table-active2">
                                            Total Deduction
                                        </td>
                                        <td class="text-center">
                                            {{$total_deduction}}
                                        </td>
                                    </tr>


                                    <tr>
                                        <td class="table-active2 text-center" colspan="2" >Net Salary</td>
                                        <td class="text-center" colspan="2" >
                                            @if (!empty($payroll) && !empty($payroll[0]['pc_employee_net_salary']))
                                               <span id="netTotal" >{{ $payroll[0]['pc_employee_net_salary']}}</span> 
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @if(!empty($download_button_show) && $download_button_show == true)
                                    <tr>
                                        <td class="text-center" colspan="4" >
                                            <b>Total Amount (in words) : </b><span id="totalWordsV" ></span>
                                        </td>
                                    </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if (!empty($setting) && !empty($setting[0]['signature_on_payslip']))
                    <div class="row mt-2">
                        <div class="col-lg-3">
                            @if ($download_button_show == false)
                                <img src="{{ public_path('portal_assets/setting/images/'.$setting[0]['signature_on_payslip']) }}" draggable="false"
                                    class="img-fluid" alt="Source Code logo" width="81" height="49">
                            @else
                                <img src="{{ url('portal_assets/setting/images/'.$setting[0]['signature_on_payslip']) }}" draggable="false" class="img-fluid"
                                    alt="Source Code logo" width="81" height="49">
                            @endif
                        </div>
                    </div>
                @endif


                
                <div class="row">
                    <div style="width:70%  !important; float:left !important;">
                        @if (!empty($finance_name))
                            <p class="mb-0">{{$finance_name[0]['employee_name']}},</p>
                            <p class="mb-0">{{$finance_name[0]['designation_name']}},</p>
                            <p>{{$finance_name[0]['department_name']}}.</p>
                        @endif
                    </div>
                    <div class=" text-center" style="width:30%  !important; float:right !important">
                        <hr>
                        Signature of the Employee
                    </div>
                </div>
                <div class="extra-space"></div>

            </div>
        </div>

        @if ($download_button_show == false)
            <img src="{{ public_path('portal_assets/images/footer-bg.png') }}" draggable="false"
                class="img-fluid  w-100" alt="header" width="1200" height="100">
        @else
            <img src="{{ url('/portal_assets/images/footer-bg.png') }}" draggable="false" class="img-fluid  w-100"
                alt="header" width="1200" height="100">
        @endif


        <p class="text-center mt-2">This is a computer generated pay slip and it doesn't require any stamp. <br> Any
            enquiry regarding payroll, please contact Email: sourcecode.dhr@gmail.com</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

    <script type="text/javascript" src="{{ url('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ url('assets/js/num2words.js') }}?js_v=2"></script>

    <script>
        // Net Total in Words
    
        var nTotal = $('#netTotal').text();
        var getSign = $('#netTotal').text().indexOf('$');
    
        nTotal = $('#netTotal').text().slice(getSign + 1);
        nTotal = parseFloat(nTotal.replace(/,/g, ''));
        nTotal = parseInt(nTotal);
        nTotalCents = $('#netTotal').text().slice(getSign + 1).split('.');
    
        if (nTotalCents.length == 1) {
            nTotalCents = '0';
        } else {
            nTotalCents = nTotalCents[1];
        }
    
        nTotalCents = parseInt(nTotalCents);
        var converter = new T2W("EN_US");
        var totalWords = converter.toWords(nTotal);
        var totalCents = converter.toWords(nTotalCents);
    
        totalWords = totalWords.trim().toLowerCase().replace(/\w\S*/g, (w) => (w.replace(/^\w/, (c) => c.toUpperCase())));
        totalCents = totalCents.trim().toLowerCase().replace(/\w\S*/g, (w) => (w.replace(/^\w/, (c) => c.toUpperCase())));
    
        let currency_text = 'PKR';
        let currency_title = 'Rupees';
    
        let sub_currency = {
            'PKR': 'Paisa',
            'USD': 'Cents',
            'AED': 'fils'
        };
    
        let cent_text = sub_currency[currency_text];
    
        document.querySelector('#totalWordsV').textContent += totalWords + ' ' + currency_title + ' and ' + totalCents + ' ' + cent_text;
    </script>
    <script>
        $(document).ready(function(){
           $('head').append('<style type="text/css" media="print"> body { visibility: hidden; display: none; }</style>');
        })
        $(document).bind("keyup keydown", function(e){
            if(e.ctrlKey && e.keyCode == 80){
                return false;
            }
        });
        </script>
    
</body>

</html>
