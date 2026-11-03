<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
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
       
        .table-data{
            padding:10px;
            font-size: 20px;
            
        }
        
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
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

            <h1 class="text-center" style="margin-top:1%">System Settings</h1>

        </div>  

        <div class="col-12">
            <h2>General Setting</h2>
            <table class="col-12">
                <thead>
                    <tr style="background:lightgray" class="col-12">
                        <th  class="table-data">Title</th>
                        <th class="table-data">Value </th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($setting))
                        <tr >
                            <td class="table-data col-3" >Company Name</td>
                            <td class="table-data" >{{$setting[0]['company_name'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Company Primary Email</td>
                            <td class="table-data" >{{$setting[0]['company_email'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Company Address</td>
                            <td class="table-data " >{{$setting[0]['company_address'] ?? ''}}</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Representative HR Employee</td>
                            <td class="table-data " >
                                @if(!empty($hr_employees))
                                    @foreach($hr_employees as $hr)
                                        @if(!empty($setting) && $setting[0]['onboarding_email_name'] == $hr['employee_name'])
                                            {{$hr['employee_name']}}
                                        @endif
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Representative Finance Employee</td>
                            <td class="table-data " >
                                @if(!empty($finance_employees))
                                    @foreach($finance_employees as $finance)
                                        @if(!empty($setting) && $setting[0]['name_on_payslip'] == $finance['employee_id'])
                                            {{$finance['employee_name'] }}
                                        @endif
                                       
                                    @endforeach
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >System Currency</td>
                            <td class="table-data">
                               
                                @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'PKR')  Pakistani Rupee  @endif
                                @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'AUD')  Australian Dollar @endif
                                @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'CAD')  Canadian Dollar  @endif
                                @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'USD')  United States Dollar @endif
                                @if (!empty($setting[0]['system_currency'] ) && $setting[0]['system_currency'] == 'GBP')  UNITED KINGDOM @endif
                            </td>
                        </tr>
                    @endif
                  
                </tbody>
            </table>

        </div>
        <div style="margin-top: 80px;"></div>
        <div class="col-12">
            <h2>Attendance Devices</h2>
            <table  class="col-12" >
                <thead>
                    <tr style="background:lightgray" class="col-12">
                        <th  class="table-data">Title</th>
                        <th class="table-data">Value </th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($setting))
                        @if(!empty($mac_address))
                            @foreach($mac_address as $key=>$mac)
                                <tr>
                                   
                                    <td class="table-data col-3" >IP Address {{$key + 1}}</td>
                                    <td class="table-data" >{{$mac['mac_address'] ?? ''}}</td>
                                    
                                </tr>
                            @endforeach
                        @endif
                        <tr>
                            <td class="table-data col-3" >Web Attendance Permission</td>
                            <td class="table-data" >@if(!empty($setting[0]['web_attendance']) && $setting[0]['web_attendance'] == 1)  Allow @else Not Allowed @endif</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Mobile Attendance Permission</td>
                            <td class="table-data " >@if(!empty($setting[0]['mobile_attendance']) && $setting[0]['mobile_attendance'] == 1)  Allow @else Not Allowed @endif</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Check In Grace Time</td>
                            <td class="table-data " >
                                {{$setting[0]['check_in_flexibility_time'] ?? ''}} Minutes
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Check Out Grace Time</td>
                            <td class="table-data " >
                                {{$setting[0]['check_out_flexibility_time'] ?? ''}} Minutes
                            </td>
                        </tr>
                        
                    @endif
                  
                </tbody>
            </table>

        </div>
        <div style="margin-top: 120px;"></div>
        <div class="col-12">
            <h2 style="margin-top: 25px;">Payroll Settings</h2>
            <table class="col-12">
                <thead>
                    <tr style="background:lightgray" class="col-12">
                        <th  class="table-data">Title</th>
                        <th class="td-border">Value </th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($setting))
                        <tr>
                            
                            <td class="table-data col-3" >Payroll Start Period</td>
                            <td class="table-data" >{{$setting[0]['payroll_start_date']}}</td>
                            
                        </tr>
                          
                        <tr>
                            <td class="table-data col-3" >Tax Fiscal Start Date</td>
                            <td class="table-data" >{{$setting[0]['fiscal_date']}}</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Tax Fiscal End Date</td>
                            <td class="table-data " >{{date('Y-m-d', strtotime('+1 year -1 day', strtotime($setting[0]['fiscal_date'])) )}}</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Club Rule Implentation on Payroll</td>
                            <td class="table-data " >
                                @if(!empty($setting[0]['club_rule_apply']) && $setting[0]['club_rule_apply'] == 1) Yes @else No  @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Tax Deduction on Payroll</td>
                            <td class="table-data " >
                                @if(!empty($setting[0]['tax_deduction_apply']) && $setting[0]['tax_deduction_apply'] == 1) Yes @else No  @endif 
                            </td>
                        </tr>
                        
                    @endif
                  
                </tbody>
            </table>

        </div>

        <div class="col-12">
            <h2 style="margin-top: 25px;">Email Configuration</h2>
            <table  class="col-12">
                <thead>
                    <tr style="background:lightgray" class="col-12">
                        <th  class="table-data">Title</th>
                        <th class="table-data">Value </th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($email_config))
                        <tr>
                            
                            <td class="table-data col-3" >Email</td>
                            <td class="table-data" >@if(!empty($email_config)){{$email_config[0]['email_config_email']}}@endif</td>
                            
                        </tr>
                          
                        <tr>
                            <td class="table-data col-3" >Password</td>
                            <td class="table-data" >@if(!empty($email_config)){{$email_config[0]['email_config_pass']}}@endif</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Mailer</td>
                            <td class="table-data " >@if(!empty($email_config)){{$email_config[0]['email_config_mailer']}}@endif</td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Host</td>
                            <td class="table-data " >
                                @if(!empty($email_config)){{$email_config[0]['email_config_host']}}@endif
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Port</td>
                            <td class="table-data " >
                                @if(!empty($email_config)){{$email_config[0]['email_config_port']}}@endif
                               
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Encryption</td>
                            <td class="table-data " >
                                @if(!empty($email_config)){{$email_config[0]['email_config_encryption']}}@endif
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >From Address</td>
                            <td class="table-data " >
                                @if(!empty($email_config)){{$email_config[0]['email_config_from_address']}}@endif
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >From Name</td>
                            <td class="table-data " >
                                @if(!empty($email_config)){{$email_config[0]['email_config_from_name']}}@endif
                            </td>
                        </tr>
                        <tr>
                            <td class="table-data col-3" >Use this email for email alerts and notifications</td>
                            <td class="table-data " >
                                @if(!empty($setting[0]['company_email_permission']) && $setting[0]['company_email_permission'] == 1) Yes @else No   @endif

                            </td>
                        </tr>
                         <tr>
                            <td class="table-data col-3" >Fcm Enability</td>
                            <td class="table-data " >
                                 @if(!empty($setting[0]['push_notification_permission']) && $setting[0]['push_notification_permission'] == 1) Yes @else No   @endif

                            </td>
                        </tr>
                        
                    @endif
                  
                </tbody>
            </table>

        </div>
       
        <div class="col-12">
            <h2>Leaves Setting</h2>
            <table  class="col-12">
                <thead>
                    <tr style="background:lightgray" class="col-12">
                        <th  class="table-data">Title</th>
                        <th class="table-data">Value </th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($get_leave_managements))
                        @foreach ($get_leave_managements as $manage_leave)
                            <tr>
                                
                                <td class="table-data col-3" >{{$manage_leave['leave_name']}}</td>
                                <td class="table-data" >{{$manage_leave['leave_yearly_allotment'] ?? 0}}</td>
                                
                            </tr>
                        @endforeach
                           
                    <tr>
                        <td class="table-data col-3" >Show <b>Annual Leave(s)</b> to probation employees</td>
                        <td class="table-data " >
                            @if(!empty($setting[0]['annual_leave_display']) && $setting[0]['annual_leave_display'] == 1) Yes @else No  @endif 
                        </td>
                    </tr>
                    <tr>
                        <td class="table-data col-3" >Show total Leaves / Show at the moment count of leaves</td>
                        <td class="table-data " >
                            @if(!empty($setting[0]['total_leave_all_leaves_visibility']) && $setting[0]['total_leave_all_leaves_visibility'] == 1) Yes @else No  @endif
                        </td>
                    </tr>
                        
                    @endif
                  
                </tbody>
            </table>

        </div>
        <div style="margin-top: 30px;"></div>
        <div class="col-12">
            <h2 >Salary Division</h2>
            <table  class="col-12">

                <thead>
                    <tr style="background:lightgray" class="col-12">
                        <th  class="table-data">Title</th>
                        <th class="table-data">Value </th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($salary_division))
                        @foreach ($salary_division as $division)
                            <tr>
                                
                                <td class="table-data col-3" >Salary Division Title</td>
                                <td class="table-data" >{{$division['salary_division_title']}}</td>
                                
                            </tr>
                            <tr>
                                
                                <td class="table-data col-3" >Salary Division Percentage</td>
                                <td class="table-data" >{{$division['salary_division_percentage']}}</td>
                                
                            </tr>
                        @endforeach
                           
                  
                        
                    @endif
                  
                </tbody>
               
            </table>

        </div>
        <div ></div>
        <div class="col-12">
            <h2 style="margin-top: 25px;">Email Recipients</h2>
            <table  class="col-12">

                <thead>
                    <tr style="background:lightgray" class="col-12">
                        <th  class="table-data">Employee</th>
                        <th class="table-data">Recipients Type</th>
                        <th class="table-data">Endpoint</th>
                    </tr>
                </thead>
                <tbody>
                    @if(!empty($email_recipient))
                    @foreach($email_recipient as $key=>$recipient)
                        <tr>
                            <td class="table-data" >{{$recipient['employee_name']}} </td>
                            <td class="table-data" > {{$recipient['recipient_type'] }} </td>    
                            <td class="table-data" >{{$recipient['endpoint_desc'] }}</td>     
                          
                        </tr>
                    @endforeach
                @endif
                  
                </tbody>
               
            </table>

        </div>
    </body>
</html>


