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
        <th class="text-center" >Slip No.</th>
        <th>Employee Name</th>
        <th>Pay Period</th>
        <th>Net Salary</th>
        <th>Tax Amount</th>
        <th>Generation Date</th>
    </tr>
  </thead>
  <tbody>
    @if(!empty($fetch_employee_tax))
        @foreach ($fetch_employee_tax as $employee_tax)
            <tr>
                <td class="ps-8">{{ $employee_tax['pc_id'] }}</td>
                <td>{!! CustomHelper::getEmpProfileDiv($employee_tax['pc_employee_id']) !!}</td>
                <td>{{ date('M-Y',strtotime($employee_tax['pc_year'].'-'.$employee_tax['pc_month'].'-'.'01')) }} </td>
                
                <td>{{ $employee_tax['pc_employee_net_salary'] }}</td>
                <td>{{ $employee_tax['total_tax_collection'] }}</td>

                <td>{{ date('d-M-Y',strtotime($employee_tax['pc_created_at'])) }}</td>
                
            </tr>
        @endforeach
    @endif
  </tbody>
</table>
</body>
</html>