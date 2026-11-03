@php $payslipCompany = CustomHelper::hcm_default_settings(($download_button_show ?? true) == false ? 'pdf' : null); @endphp
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payslip || {{ $payslipCompany['company_name'] ?? 'Nexora HCM' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ $payslipCompany['company_favicon'] ?? '' }}"
        type="image/x-icon" />

    {{-- <link href="{{ url('/portal_assets/css/bootstrap.min.css') }}" rel="stylesheet" > --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        body {
            font-weight: 400 !important;
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
            color: #0d9488 !important;
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

        .slip-header {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 18px;
        }

        .slip-header td {
            vertical-align: middle;
            padding: 0;
            border: none !important;
        }

        .slip-header-logo {
            width: 86px;
        }

        .slip-header-logo img {
            width: 74px;
            height: auto;
        }

        .slip-header-company {
            padding-left: 14px !important;
        }

        .slip-company-name {
            font-size: 34px;
            line-height: 1.1;
            white-space: nowrap;
        }

        .slip-name-main {
            color: #0b1533;
            font-weight: 800;
            letter-spacing: .5px;
        }

        .slip-name-rest {
            color: #0d9488;
            font-weight: 600;
            letter-spacing: 4px;
            margin-left: 8px;
        }

        .slip-company-address {
            margin-top: 6px;
            font-size: 13px;
            color: #5e6278;
        }

        .slip-header-title {
            text-align: right;
            width: 30%;
        }

        .slip-title {
            font-size: 22px;
            font-weight: 800;
            color: #0d9488;
            letter-spacing: 3px;
        }

        .slip-month {
            font-size: 15px;
            font-weight: 600;
            color: #0b1533;
            margin-top: 2px;
        }

        .slip-date {
            font-size: 13px;
            color: #5e6278;
            margin-top: 2px;
        }

        .slip-issued-by {
            margin-top: 18px;
            font-size: 16px;
            font-weight: 700;
            color: #0b1533;
        }

        /* pay statement layout (tables only, so the downloaded PDF looks the same) */
        .slip-info { width: 100%; border-collapse: separate; border-spacing: 14px 6px; margin: 0 -14px 6px; }
        .slip-info td { width: 33.33%; vertical-align: top; padding: 0; border: none !important; }
        .slip-info-label { font-size: 13px; color: #5e6278; }
        .slip-info-value { font-size: 14px; color: #0b1533; background: #eef7f6; padding: 3px 8px; margin-top: 2px; min-height: 22px; }
        .slip-section { font-size: 15px; font-weight: 700; color: #0b1533; margin: 16px 0 4px; }
        .slip-two { width: 100%; border-collapse: collapse; }
        .slip-two td.slip-two-left, .slip-two td.slip-two-right { width: 50%; vertical-align: top; padding: 0; border: none !important; }
        .slip-two td.slip-two-left { padding-right: 14px; }
        .slip-two td.slip-two-right { padding-left: 14px; }
        .slip-grid { width: 100%; border-collapse: collapse; border: 1px solid #b5b5c3; font-size: 14px; }
        .slip-grid th { background: #e6f4f1; color: #0b1533; font-weight: 600; padding: 5px 10px; border-bottom: 1px solid #b5b5c3; text-align: left; }
        .slip-grid td { padding: 4px 10px; color: #3f4254; height: 22px; }
        .slip-grid .amount { text-align: right; width: 32%; }
        .slip-center th, .slip-center td { text-align: center; border: 1px solid #d5d5de; }
        .slip-total { width: 100%; border-collapse: collapse; font-size: 14px; font-weight: 700; color: #0b1533; }
        .slip-total td { padding: 5px 10px; background: #f3faf9; }
        .slip-total .amount { text-align: right; color: #0d9488; }
        .slip-words { font-size: 13px; color: #3f4254; margin-top: 4px; }
        .slip-note { font-size: 12px; color: #7e8299; margin-top: 3px; }
        .slip-note-title { width: 180px; font-weight: 600; color: #0b1533 !important; vertical-align: top; }

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
            <img src="{{ public_path('portal_assets/images/header-bg.png') }}" draggable="false" class="img-fluid w-100 padding-img" style="height:150px"
                alt="header" width="1200" height="277">
        @else
            <img src="{{ url('/portal_assets/images/header-bg.png') }}" draggable="false" class="img-fluid w-100 padding-img"
                alt="header" width="1200" height="277">
        @endif
        <div class="row no-guters content">
            <div class="col-lg-11 mx-auto">
                {{-- company: logo + name (first word navy, the rest teal) + address; slip title, month and date on the right.
                     A table, so the downloaded PDF (dompdf) lays it out the same way --}}
                @php
                    $slipCompanyName = trim($payslipCompany['company_name'] ?? '') ?: 'Company';
                    $slipNameParts = explode(' ', $slipCompanyName, 2);
                @endphp
                <table class="slip-header">
                    <tr>
                        <td class="slip-header-logo">
                            <img src="{{ $payslipCompany['company_logo'] ?? '' }}" draggable="false" alt="{{ $slipCompanyName }} logo">
                        </td>
                        <td class="slip-header-company">
                            <div class="slip-company-name"><span class="slip-name-main">{{ $slipNameParts[0] }}</span>@if (!empty($slipNameParts[1]))<span class="slip-name-rest">{{ $slipNameParts[1] }}</span>@endif</div>
                            @if (!empty($payslipCompany['company_address']))
                                <div class="slip-company-address">{{ $payslipCompany['company_address'] }}</div>
                            @endif
                        </td>
                        <td class="slip-header-title">
                            <div class="slip-title">SALARY SLIP</div>
                            @if (!empty($payroll) && !empty($payroll[0]['pc_month']) && !empty($payroll[0]['pc_year']))
                                <div class="slip-month">{{ date('F', mktime(0, 0, 0, $payroll[0]['pc_month'], 10)) }} {{ $payroll[0]['pc_year'] }}</div>
                            @endif
                            <div class="slip-date">Date: {{ (!empty($payroll) && !empty($payroll[0]['pc_created_at'])) ? date('d M Y', strtotime($payroll[0]['pc_created_at'])) : '--' }}</div>
                        </td>
                    </tr>
                </table>
                @php
                    $slip = $payroll[0];
                    $money = fn($v) => number_format((float) $v, fmod(round((float) $v, 2), 1) == 0 ? 0 : 2);
                    $infoRows = [
                        [['Employee Name', $slip['employee_name'] ?? '-'], ['Employee Code', $slip['employee_code'] ?? '-'], ['Employment Type', $slip['employement_status_name'] ?? '-']],
                        [['CNIC', $slip['employee_cnic'] ?? '-'], ['Date of Joining', !empty($slip['employee_doj']) ? date('d M Y', strtotime($slip['employee_doj'])) : '-'], ['Designation', $slip['pc_employee_designation'] ?? '-']],
                        [['Grade', $slip['employee_grade'] ?? '-'], ['Department', $slip['pc_employee_department'] ?? '-'], ['Location', $slip['location_title'] ?? '-']],
                        [['Bank', $slip['pc_employee_bank_name'] ?? '-'], ['Account No.', $slip['pc_employee_bank_account'] ?? '-'], ['Service Period', $service_period]],
                        [['Pay Period', date('d M', strtotime($slip['pc_date_from'])).' - '.date('d M Y', strtotime($slip['pc_date_to']))], ['Payroll Days', (float) ($slip['pc_total_working_days'] ?? 0)],
                         ['Attendance', (float) $slip['pc_present'].' Present, '.(float) $slip['pc_absent'].' Absent, '.(float) $slip['pc_leave'].' Leave, '.(float) $slip['pc_holiday'].' Holiday, '.(float) $slip['pc_late_early'].' Late, '.(float) $slip['pc_half_day'].' Half Day']],
                    ];
                    $lineCount = max(count($earning_lines), count($deduction_lines));
                    $netSalary = max(0, (float) $slip['pc_employee_net_salary']);
                @endphp

                {{-- employee --}}
                <table class="slip-info">
                    @foreach ($infoRows as $infoRow)
                        <tr>
                            @foreach ($infoRow as [$infoLabel, $infoValue])
                                <td>
                                    <div class="slip-info-label">{{ $infoLabel }} :</div>
                                    <div class="slip-info-value">{{ $infoValue !== '' && $infoValue !== null ? $infoValue : '-' }}</div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </table>

                {{-- earnings | deductions --}}
                <table class="slip-two">
                    <tr>
                        <td class="slip-two-left">
                            <div class="slip-section">Earning</div>
                            <table class="slip-grid">
                                <tr><th>Earnings</th><th class="amount">Amount</th></tr>
                                @for ($i = 0; $i < $lineCount; $i++)
                                    <tr>
                                        <td>{{ $earning_lines[$i]['title'] ?? '' }}</td>
                                        <td class="amount">{{ isset($earning_lines[$i]) ? $money($earning_lines[$i]['amount']) : '' }}</td>
                                    </tr>
                                @endfor
                            </table>
                            <table class="slip-total"><tr><td>Total Earning</td><td class="amount">{{ $money($total_earning) }}</td></tr></table>
                        </td>
                        <td class="slip-two-right">
                            <div class="slip-section">Deductions</div>
                            <table class="slip-grid">
                                <tr><th>Deductions</th><th class="amount">Amount</th></tr>
                                @for ($i = 0; $i < $lineCount; $i++)
                                    <tr>
                                        <td>{{ $deduction_lines[$i]['title'] ?? '' }}</td>
                                        <td class="amount">{{ isset($deduction_lines[$i]) ? $money($deduction_lines[$i]['amount']) : '' }}</td>
                                    </tr>
                                @endfor
                            </table>
                            <table class="slip-total"><tr><td>Total Deductions</td><td class="amount">{{ $money($total_deduction) }}</td></tr></table>
                        </td>
                    </tr>
                </table>

                {{-- net salary --}}
                <div class="slip-section">Net Salary For The Month</div>
                <table class="slip-grid">
                    <tr><th>Net Salary</th><th class="amount">Amount</th></tr>
                    <tr><td>Net Salary</td><td class="amount">{{ $money($netSalary) }}</td></tr>
                </table>
                <table class="slip-total"><tr><td>Total Net Salary For The Month</td><td class="amount"><span id="netTotal">{{ $money($netSalary) }}</span></td></tr></table>
                @if(!empty($download_button_show) && $download_button_show == true)
                    <div class="slip-words"><b>In words:</b> <span id="totalWordsV"></span></div>
                @endif

                {{-- income tax --}}
                @if (!empty($tax_details))
                    <div class="slip-section">Income Tax Details</div>
                    <table class="slip-grid slip-center">
                        <tr><th>Taxable Income</th><th>Tax Slab %</th><th>Tax Credit | Adj</th><th>Tax Chargeable</th><th>Tax Deducted</th><th>Tax Payable</th></tr>
                        <tr>
                            <td>{{ $money($tax_details['taxable_income']) }}</td>
                            <td>{{ $tax_details['slab_percentage'] + 0 }}</td>
                            <td>{{ $money($tax_details['tax_credit']) }}</td>
                            <td>{{ $money($tax_details['tax_chargeable']) }}</td>
                            <td>{{ $money($tax_details['tax_deducted']) }}</td>
                            <td>{{ $money($tax_details['tax_payable']) }}</td>
                        </tr>
                    </table>
                    <div class="slip-note">Yearly taxable income on the monthly gross salary; tax deducted = earlier payslips of this tax year; tax payable = this month.</div>
                @endif

                {{-- provident fund balance up to this payslip --}}
                @if (!empty($pf_summary))
                    <div class="slip-section">Provident Fund</div>
                    <table class="slip-grid slip-center">
                        <tr><th>Member Contribution</th><th>Company Contribution</th><th>Member Profit</th><th>Company Profit</th><th>Permanent Withdrawals</th><th>Temporary Withdrawals</th><th>Temp. Withdrawal Recovery</th><th>Available Provident Fund</th></tr>
                        <tr>
                            <td>{{ $money($pf_summary['member_contribution']) }}</td>
                            <td>{{ $money($pf_summary['company_contribution']) }}</td>
                            <td>{{ $money($pf_summary['member_profit']) }}</td>
                            <td>{{ $money($pf_summary['company_profit']) }}</td>
                            <td>{{ $money($pf_summary['permanent_withdrawals']) }}</td>
                            <td>{{ $money($pf_summary['temporary_withdrawals']) }}</td>
                            <td>{{ $money($pf_summary['withdrawal_recovery']) }}</td>
                            <td><b>{{ $money($pf_summary['available']) }}</b></td>
                        </tr>
                    </table>
                @endif

                {{-- leaves used for absent / late / half days, loan not covered this month --}}
                @if (!empty($leave_deductions) || (!empty($loan_carried) && $loan_carried > 0))
                    <div class="slip-section">Notes</div>
                    <table class="slip-grid">
                        @if (!empty($leave_deductions))
                            <tr>
                                <td class="slip-note-title">Leaves Adjusted</td>
                                <td>
                                    @foreach ($leave_deductions as $leave_deduction)
                                        @php
                                            $ld_reason = ['Absent' => 'Absent Day', 'Half day' => 'Half Day', 'Late Coming' => 'Late Coming'][$leave_deduction['ld_reason']] ?? $leave_deduction['ld_reason'];
                                            $ld_count = (int) $leave_deduction['total'];
                                            $ld_leave = (float) $leave_deduction['leave_days'];
                                        @endphp
                                        <div>
                                            @if (!empty($leave_deduction['leave_name']))
                                                {{ $ld_count }} {{ $ld_reason }}{{ $ld_count > 1 ? 's' : '' }}: {{ $ld_leave + 0 }} {{ $leave_deduction['leave_name'] }} used (no salary cut)
                                            @else
                                                {{ $ld_count }} {{ $ld_reason }}{{ $ld_count > 1 ? 's' : '' }}: deducted from salary (no leave balance)
                                            @endif
                                        </div>
                                    @endforeach
                                    <div class="slip-note">Absent day = 1 leave. 2 late comings and 1 half day a month are free; each extra one = half a leave. Annual Leave first, then Casual, then Sick; with no leave left, salary is deducted.</div>
                                </td>
                            </tr>
                        @endif
                        @if (!empty($loan_carried) && $loan_carried > 0)
                            <tr>
                                <td class="slip-note-title">Loan Carried Forward</td>
                                <td>{{ number_format($loan_carried, 2) }}: salary not enough this month, will be deducted from next month's salary</td>
                            </tr>
                        @endif
                    </table>
                @endif
                {{-- issued by: the company name only (no signatures) --}}
                <div class="slip-issued-by">{{ $slipCompanyName }}</div>

            </div>
        </div>

        @if ($download_button_show == false)
            <img src="{{ public_path('portal_assets/images/footer-bg.png') }}" draggable="false" style="height:60px"
                class="img-fluid  w-100" alt="header" width="1200" height="100">
        @else
            <img src="{{ url('/portal_assets/images/footer-bg.png') }}" draggable="false" class="img-fluid  w-100"
                alt="header" width="1200" height="100">
        @endif


        <p class="text-center mt-2">This is a computer generated pay slip and it doesn't require any stamp. <br> Any
            enquiry regarding payroll, please contact {{ $payslipCompany['company_email'] ?? 'HR' }}</p>
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
