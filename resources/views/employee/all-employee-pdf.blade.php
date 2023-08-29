<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
    <style>
    .table {
    font-family: Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    }

    .table td, .table th {
    border: 1px solid #ddd;
    padding: 8px;
    }

    .table tr:nth-child(even){background-color: #f2f2f2;}

    .table tr:hover {background-color: #ddd;}

    .table th {
    padding-top: 12px;
    padding-bottom: 12px;
    text-align: left;
    background-color: #212529;
    color: white;
    }
    .button {
    background-color: #4CAF50; /* Green */
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

    .text-center {
        text-align: center !important;
      }
    </style>
</head>
<body>
    @if($pdf_file_access == 'True')

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

        <h2 class="text-center" style="margin-top:1%">All Employee Information </h2>

      </div>  
    @endif   

<table class="table">
  <thead>
    <tr>
        <th class="text-center" >Sr No.</th>
        <th>Employee Name</th>
        <th>Employee ID</th>
        <th>Department</th>
        <th>Designation</th>
        <th>Employee Job Type</th>
        <th>Employee Status</th>
        <th>Employee DOJ</th>
        <th>Salary Increment Date</th>
        <th>Employee Nic</th>
        <th>Increment</th>
        <th>Gross Salary </th>
        <th>Resination Date </th>
        <th>Last Working Day </th>

    </tr>
  </thead>
  <tbody>
    @if (!empty($employees_details))
        @php $i = 0; @endphp
        @foreach ($employees_details as $employees_detail)
        <tr>
            <td> {{++$i}}</td>  
            <td> {{ $employees_detail['Employee Name'] }}</td> 
            <td> {{$employees_detail['Employee Code']}}</td> 
            <td> {{$employees_detail['Department']}} </td>
            <td>{{$employees_detail['Designation']}}</td>
            <td>{{ $employees_detail['Employee Job Type'] }}</td>
            <td>{{ $employees_detail['Employee Status'] }}</td>
            <td>{{ $employees_detail['Employee DOJ'] }}</td>
            <td>{{ $employees_detail['Salary Increment Date'] }}</td>
            <td>{{ $employees_detail['Employee Cnic'] }}</td>
            <td> {{ $employees_detail['Increment'] }}</td>
            <td>{{ $employees_detail['Gross Salary'] }} </td>
            <td> {{ $employees_detail['Resignation Date'] }} </td>
            <td>{{ $employees_detail['Last Working Day'] }} </td>
        </tr>                          
        @endforeach                        
    @endif
  </tbody>
</table>
</body>
</html>