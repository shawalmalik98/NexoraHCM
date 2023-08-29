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
    </style>
</head>
<body>
<table class="table">
  <thead>
    <tr>
        <th class="text-center" >Ref ID</th>
        <th>Employee Name</th>
        <th>Pay Period</th>
        <th>Gross Salary</th>
        <th>Extra Earning</th>
        <th>Total Deduction</th>
        <th>Net Salary</th>
        <th>Generation Date</th>
    </tr>
  </thead>
  @if (!empty($fetch_employee_payroll))
    <tbody>
        @foreach ($fetch_employee_payroll as $employee_payroll)
        <tr>
            <td>{{ $employee_payroll['pc_id'] }}</td>  
            <td>{{ $employee_payroll['employee_name'] }} - ({{$employee_payroll['employee_code']}})</td> 
            <td>{{ date('M-Y',strtotime($employee_payroll['pc_year'].'-'.$employee_payroll['pc_month'].'-'.'01')) }} </td>
            <td>{{ number_format($employee_payroll['pc_employee_gross_salary'],2) }}</td>
            <td>{{ number_format($employee_payroll['pc_employee_extra_salary'],2) }}</td>
            <td>{{ number_format($employee_payroll['pc_employee_deduction'],2) }}</td>
            <td>{{ number_format($employee_payroll['pc_employee_net_salary'],2) }}</td>
            <td>{{ date('d-M-Y',strtotime($employee_payroll['pc_created_at'])) }}</td>
        </tr>                          
        @endforeach                        
    </tbody>
    <tfoot>
      <tr>
        <th class="text-center" >Total</th>
        <th colspan="2" > Employee(s) : {{count($fetch_employee_payroll)}}</th>
        <th>{{number_format(array_sum(array_column($fetch_employee_payroll,'pc_employee_gross_salary')),2)}}</th>
        <th>{{number_format(array_sum(array_column($fetch_employee_payroll,'pc_employee_extra_salary')),2)}}</th>
        <th>{{number_format(array_sum(array_column($fetch_employee_payroll,'pc_employee_deduction')),2)}}</th>
        <th>{{number_format(array_sum(array_column($fetch_employee_payroll,'pc_employee_net_salary')),2)}}</th>
        <th></th>
      </tr>
    </tfoot>
  @endif
</table>
</body>
</html>