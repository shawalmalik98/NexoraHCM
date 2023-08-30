<!DOCTYPE html>
<html>
<head>
    <title>Employee Information Form</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="{{ url('assets/css/personal-form-style.css') }}" rel="stylesheet" type="text/css" />
    <style>
        img.logo {
            text-align: center;
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 20% !important;
        }
        .small-font{
            font-size: 12px;
        }
        .custom_table tr td{
            margin-top: 0px;
            margin-bottom: 0px;
            padding-top: 3px;
            padding-bottom:3px;
            font-size: 13px;
        }
       
    </style>
</head>
<body>
        <div class="row">
            <div class="col-md-12 text-center">
                <img src="{{ url(asset('portal_assets/form/img/SourceCode-logo.png')) }}" alt="logo" class="logo">
            </div>
        </div>

        <div class="row">
            <table class="table table-borderless mt-5">
                <tr>
                    <td class="small-font" style="width:60%"> Date: <b>{{ date('Y-m-d') }}</b></td>
                    <td class="small-font" style="width:20%;">
                        Progressive Center, <br> 
						Office no.603 6th Floor <br>
                        Shahrah-e-Faisal, <br>
						Block 6 PECHS <br>
                        Contact: 021-34540913 <br>
						www.sourcecode.world
                    </td>
                </tr>
            </table>  
            
            <div class="text-center">
                <h3>EMPLOYEE INFORMATION FORM</h3>
            </div>
			
			<div class="row my-5">
				<div style="width:50%;float:left;margin-left:2%;">
					<table class="table custom_table" style="width: 100%">
						<tr>
							<td width="25%"><b>Employee code</b></td>
							<td width="25%">{{ $employee_data[0]['employee_code'] }}</td>
						</tr>
						<tr>
							<td width="25%"><b>Name</b></td>
							<td width="25%">{{ $employee_data[0]['employee_name'] }}</td>
						</tr>
						<tr>
							<td width="25%"><b>Designation</b></td>
							<td width="25%">{{ $employee_data[0]['employee_designation'] }}</td>
						</tr>
						<tr>
							<td width="25%"><b>Department</b></td>
							<td width="25%">{{ $employee_data[0]['employee_department'] }}</td>
						</tr>
						
						<tr>
							<td width="25%"><b>Joining date</b></td>
							<td width="25%">{{ $employee_data[0]['employee_code'] }}</td>
						</tr>	
                    </table>
				</div>
				<div style="width:50%;float:right;">
					@if (empty($information_data[0]['profile_avatar']) || $information_data[0]['profile_avatar'] == '')
						<img src="{{ url(asset('portal_assets/form/img/profile.png')) }}" style="width: 150px; float:right;margin-right:90px;">
					@else
						<img src="{{ url(asset('portal_assets/profile_pictures')) . '/' . $information_data[0]['profile_avatar'] }}" style="width: 150px; float:right;margin-right:90px;height:160px;">
					@endif
				</div>
			</div>


            <table class="table custom_table table-bordered" style="width: 100%;margin-top:24%">
                <tr style="background:#0000ff4d;">
                    <th colspan="4" style="font-size:15px">PERSONAL INFORMATION</th>
                </tr>
                <tr>
                    <td width="20%">Father's Name</td>
                    <td width="30%">{{ $information_data[0]['fathername'] }}</td>
                    <td width="20%">Date of Birth</td>
                    <td width="30%">{{ $information_data[0]['dob'] }}</td>
                </tr>

                <tr>
                    <td width="20%">Gender</td>
                    <td width="30%">{{ $information_data[0]['gender'] }}</td>
                    <td width="20%">Marital status</td>
                    <td width="30%">{{ $information_data[0]['maritalstatus'] }}</td>
                </tr>

                <tr>
                    <td width="20%">CNIC#</td>
                    <td width="30%">{{ $information_data[0]['cnic'] }}</td>
                    <td width="20%">Date of expiry</td>
                    <td width="30%">{{ $information_data[0]['dateofexpiry'] }}</td>
                </tr>

                <tr>
                    <td width="20%">Birth place</td>
                    <td width="30%">{{ $information_data[0]['birthplace'] }}</td>
                    <td width="20%">Citizenship</td>
                    <td width="30%">{{ $information_data[0]['citizenship'] }}</td>
                </tr>
                <tr>
                    <td width="20%">Passport#</td>
                    <td width="30%">{{ $information_data[0]['passport'] }}</td>
                    <td width="20%">Religion </td>
                    <td width="30%">{{ $information_data[0]['religion'] }}</td>
                </tr>

                <tr>
                    <td width="20%" colspan="2">Permanent Address: {{ $information_data[0]['permanentaddress'] }}</td>
                    
                    <td width="20%" colspan="2">Current Address: {{ $information_data[0]['currentaddress'] }}</td>
                    
                </tr>
            </table>


            <table class="table custom_table table-bordered" style="width: 100%">
                <tr class="" style="background:#0000ff4d;">
                    <th colspan="4" style="font-size:15px">BANK DETAILS</th>
                </tr>

                <tr>
                    <td width="20%">Bank Name</td>
                    <td width="30%">{{ $information_data[0]['bankname'] }}</td>
                    <td width="20%">Bank code</td>
                    <td width="30%">{{ $information_data[0]['bankcode'] }}</td>
                </tr>
                <tr>
                    <td width="20%">Account#</td>
                    <td width="30%">{{ $information_data[0]['accountno'] }}</td>
                    <td width="20%">Account title</td>
                    <td width="30%">{{ $information_data[0]['accounttitle'] }}</td>
                </tr>
                <tr>
                    <td width="20%">IBAN number</td>
                    <td width="30%">{{ $information_data[0]['ibannumber'] }}</td>
                    <td width="20%"></td>
                    <td width="30%"></td>
                </tr>
            </table>

            @php
                $emphistory = $information_data[0]['employementhistory'] ? json_decode($information_data[0]['employementhistory']) : '';
                $empcount = $emphistory ? count($emphistory) : 1;
            @endphp

            <table class="table custom_table table-bordered" style="width: 100%">
                <tr class="" style="background:#0000ff4d;">
                    <th colspan="5" style="font-size:15px">EMPLOYEMENT HISTORY</th>
                </tr>
                <tr>
                    <th width="20%" style="font-size: 14px;">Employers's Name</th>
                    <th width="20%" style="font-size: 14px;">Designation</th>
                    <th width="20%" style="font-size: 14px;">Salary & Benefits</th>
                    <th width="20%" style="font-size: 14px;">Duration(Start/End date)</th>
                    <th width="20%" style="font-size: 14px;">Reason for leaving</th>
                </tr>

                @for ($i = 1; $i <= $empcount; $i++)
                    @php $temp =  ($emphistory) ? $emphistory[$i-1] : '' @endphp

                    <tr>
                        <td>{{ $emphistory ? $temp->employername : '-' }}</td>
                        <td>{{ $emphistory ? $temp->designation : '-' }}</td>
                        <td>{{ $emphistory ? $temp->salaryandbenefits : '-' }}</td>
                     
                        <td>
                            <table border="0" class="table-borderless">
                                <tr>
                                    <td>{{$emphistory ? $temp->durationstartdate : '-' }}</td>
                                    <td>{{ $emphistory ? $temp->durationenddate : '-' }}</td>
                                </tr>
                            </table>

                        </td>
                        <td>{{ $emphistory ? $temp->reasonforleaving : '-' }}</td>
                    </tr>
                @endfor
            </table>

            @php
                $acadcred = $information_data[0]['academiccredential'] ? json_decode($information_data[0]['academiccredential']) : '';
                $acadcount = $acadcred ? count($acadcred) : 1;
            @endphp


            <table class="table custom_table table-bordered" style="width: 100%">
                <tr style="background:#0000ff4d;">
                    <th colspan="5" style="font-size:15px">ACADEMIC CREDENTIALS</th>
                </tr>
                <tr>
                    <th width="20%" style="font-size: 14px;">Degree</th>
                    <th width="20%" style="font-size: 14px;">Institution</th>
                    <th width="20%" style="font-size: 14px;">Majors</th>
                    <th width="20%" style="font-size: 14px;">Passing year</th>
                    <th width="20%" style="font-size: 14px;">Grade/CGPA</th>

                </tr>
                @for ($i = 1; $i <= $acadcount; $i++)
                    @php $temp =  ($acadcred) ? $acadcred[$i-1] : '' @endphp
                    <tr>
                        <td>{{ $acadcred ? $temp->degree : '-' }}</td>
                        <td>{{ $acadcred ? $temp->intitution : '-' }}</td>
                        <td>{{ $acadcred ? $temp->majors : '' }}</td>
                        <td>{{ $acadcred ? $temp->passingyear : '-' }}</td>
                        <td>{{ $acadcred ? $temp->grade : '-' }}</td>
                    </tr>
                @endfor
            </table>

            @php
                $profqua = $information_data[0]['professionalqualification'] ? json_decode($information_data[0]['professionalqualification']) : '';
                $profcount = $profqua ? count($profqua) : 1;
            @endphp

            <table class="table custom_table table-bordered" style="width: 100%">
                <tr style="background:#0000ff4d;">
                    <th colspan="5" style="font-size:15px">PROFESSIONAL QUALIFICATION /
                        CERTIFICATIONS / TRAININGS</th>
                </tr>
                <tr>
                    <th width="20%" style="font-size: 14px;">Type</th>
                    <th width="20%" style="font-size: 14px;">Details</th>
                    <th width="20%" style="font-size: 14px;">Institute</th>
                    <th width="20%" style="font-size: 14px;">From</th>
                    <th width="20%" style="font-size: 14px;">Till</th>
                </tr>
                @for ($i = 1; $i <= $profcount; $i++)
                    @php $temp =  ($profqua) ? $profqua[$i-1] : '' @endphp

                <tr>
                    <td>{{ $profqua ? $temp->type : '-' }}</td>
                    <td>{{ $profqua ? $temp->details : '-' }}</td>
                    <td>{{ $profqua ? $temp->institute : '-' }}</td>
                    <td>{{ $profqua ? $temp->from : '-' }}</td>
                    <td>{{ $profqua ? $temp->till : '-' }}</td>
                </tr>

                @endfor
            </table>


            <table class="table custom_table table-bordered" style="width: 100%">
                <tr style="background:#0000ff4d;">
                    <th colspan="4" style="font-size:15px">DO YOU HAVE ANY RELATIVES AT ISMAIL INDUSTRIES PRIVATE LIMITED?</th>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center"><b>Relative 1</b></td>
                </tr>

                <tr>
                    <td width="20%">Name</td>
                    <td width="30%">{{ $information_data[0]['relativename1'] }}</td>
                    <td width="20%">Designation</td>
                    <td width="30%">{{ $information_data[0]['relativedesignation1'] }}</td>
                </tr>
                <tr>
                    <td width="20%">Department</td>
                    <td width="30%">{{ $information_data[0]['relativedepartment1'] }}</td>
                    <td width="20%">Relation type</td>
                    <td width="30%">{{ $information_data[0]['relativerelationtype1'] }}</td>
                </tr>

                <tr>
                    <td colspan="4" style="text-align: center"><b>Relative 2</b></td>
                </tr>

                <tr>
                    <td width="20%">Name</td>
                    <td width="30%">{{ $information_data[0]['relativename2'] }}</td>
                    <td width="20%">Designation</td>
                    <td width="30%">{{ $information_data[0]['relativedesignation2'] }}</td>
                </tr>
                <tr>
                    <td width="20%">Department</td>
                    <td width="30%">{{ $information_data[0]['relativedepartment2'] }}</td>
                    <td width="20%">Relation type</td>
                    <td width="30%">{{ $information_data[0]['relativerelationtype2'] }}</td>
                </tr>

            </table>


            <table class="table custom_table table-bordered" style="width: 100%">
                <tr style="background:#0000ff4d;">
                    <th colspan="4" style="font-size:15px">PREVIOUS APPLICATION IN ISMAIL INDUSTRIES LIMITED?</th>
                </tr>

                <tr>
                    <td colspan="2" width="50%">Have you applied in IIL before?</td>
                    <td colspan="2"  width="50%">{{ $information_data[0]['appliedbefore'] }}</td>
                </tr>
                <tr>
                    <td width="20%">What Position</td>
                    <td width="30%">{{ $information_data[0]['whatposition'] }}</td>
                    <td width="20%">Result of Application</td>
                    <td width="30%">{{ $information_data[0]['resultofapplication'] }}</td>
                </tr>


            </table>



        </div>

    

   
  
</body>
</html>