<head>
    <style>
        #system_datatables {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
        }
        
        #system_datatables td, #system_datatables th {
        border: 1px solid #ddd;
        padding: 8px;
        }
        
        #system_datatables tr:nth-child(even){background-color: #f2f2f2;}
        
        #system_datatables tr:hover {background-color: #ddd;}
        
        #system_datatables th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
        }
    </style>
</head>
@if(!empty($emailError))
    <p>{{ $emailError[0]['errormsg'] }}</p>
@endif

@if(!empty($emailData))
    <p>Greetings,<br><br>
    <p>Some of our newly joined employee(s) accounts are created in Nexora HCM from SAP – Nexora HCM integration. Please verify and add the appropriate information for their attendance marking purpose.</p>
    <p>Thanks!</p>
    
    {{-- <p>Portal Link : {{ url('/employees/editemployee/' . $employee_registration_id) }}</p>
    <p>ID :{{$employee_code}}</p>
    <p>Name :{{$employee_name}}</p>
    <p>Password : {{$employee_password}}</p> --}}

        <table id="system_datatables" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    {{-- <th>Password</th> --}}
                    <th>Department</th>
                    <th>Designation</th>
                    <th>Portal Link</th>
                </tr>
            </thead>
            <tbody>
                
                @foreach($emailData as $data)
                    <tr>
                        
                        <td> {{ $data['employee_code'] }}</td>
                        <td> {{ $data['employee_name'] }}</td>
                        {{-- <td> {{ $data['employee_password'] }}</td> --}}
                        <td> {{ $data['employee_department'] }}</td>
                        <td> {{ $data['employee_designation'] }}</td>
                        <td> <a href="{{ url('/employees/editemployee/' . $data['employee_registration_id']) }}">Edit Employee Profile</a></td>

                    </tr>
                @endforeach    
                    
            </tbody>

        </table><br>
@endif
    {{-- <p style="font-size:12px " >The information contained in this email and any attachments ("Message") may be confidential. If you are not the intended recipient, please immediately notify the sender, delete this Message and do not disclose, distribute, or copy to any third party or otherwise use this Message. Electronic messages are not secure or error free and can contain viruses or may be delayed. The sender reserves the right to monitor, record, and retain electronic messages.</p> --}}
    