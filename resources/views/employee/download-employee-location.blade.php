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

<table class="table">
  <thead>
    <tr>
        <th class="text-center" >Sr No.</th>
        <th>Employee code</th>
        <th>Employee Name</th>
        <th>Department</th>
        <th>Designation</th>
        <th>Business Unit</th>
        <th>Location Title</th>
        <th>Location Latitude</th>
        <th>Location Longitude</th>

    </tr>
  </thead>
  <tbody>
    @if (!empty($employee_location_details))
        @php $i = 0; @endphp
        @foreach ($employee_location_details as $employee_location)
        <tr>
            <td> {{++$i}}</td>  
            <td> {{$employee_location['Employee ID']}} </td> 
            <td> {{ $employee_location['Employee Name'] }} </td> 
            <td> {{$employee_location['Department']}} </td>
            <td>{{$employee_location['Designation']}}</td>
            <td>{{ $employee_location['Business unit'] }}</td>
            <td>{{ $employee_location['Location title'] }}</td>
            <td>{{ $employee_location['Location latitude'] }}</td>
            <td>{{ $employee_location['Location longitude'] }}</td>
        </tr>                          
        @endforeach                        
    @endif
  </tbody>
</table>
</body>
</html>