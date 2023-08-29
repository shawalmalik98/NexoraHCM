<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SourceCode-HCM</title>
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
            font-size: 15px;
            padding: 5px;

        }
        .table-data{
            padding-left:40px;
            padding-top:15px;
           

        }

        .text-center {
            text-align: center !important;
        }

        /* .header-first-page { display: none; }
        @page {
            margin-top: 80px;
        }
        @page :first {
            margin-top: 0;
        }
        @page :first .header-first-page {
            display: table-header-group;
        } */


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
                <img src="{{$companySetting['company_logo']}}" draggable="false" alt="logo" class="logo">
            @else
                <img src="{{ public_path('/portal_assets/images/logo.png') }}" draggable="false" class="img-fluid" >
            @endif
            
            <h1 class="text-center" style="margin-top:1%">{{ $companySetting['company_name']}} </h1>

            <h2 class="text-center" style="margin-top:1%">Employee(s) Leave Entitlement</h2>

        </div>  

        <div class="col-10" style="margin-left:10%;margin-top:3%">
            
            <div class="row">
                <table>
                    <thead  class="header-first-page">
                        <tr style="background:lightgray">
                            <th  class="text-center td-border">Employee Code</th>
                            <th  class="text-center td-border">Employee Name</th>
                            <th  class="text-center td-border">Total Alloted Leaves</th>
                            <th  class="text-center td-border">Total Availed Leaves</th>
                        </tr>
                       
                    </thead>
    
                    <tbody>
                       
                        @if (!empty($leaves_entitled))
                            @foreach ($leaves_entitled as $leaves_entitlement)
                                <tr style="border:1px solid">
                                    <td class="text-center td-border"><b>{{$leaves_entitlement['employee_code']}} </b></td>
                                    <td class="text-center td-border"><b>{{$leaves_entitlement['employee_name']}}</b></td>
                                    <td class="text-center td-border">{{ $leaves_entitlement['total_alloted_leave'] }}</td>
                                    <td class="text-center td-border">{{ $leaves_entitlement['used_get_availed_leaves'] }}</td>
                                    
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


