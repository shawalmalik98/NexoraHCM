<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <title>IIL - HCM</title>
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:site_name" content="Keenthemes | Metronic" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="canonical" href="https://preview.keenthemes.com/metronic8" />
    <link rel="icon" href="{{ url('portal_assets/images/fav-icons.png') }}"/>
    <link href="{{ url('assets/css/personal-form-style.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
    
</head>
<body>
    
</body>
</html>

    @php
        $companySetting = CustomHelper::hcm_default_settings();
    @endphp
<section>

    <div class="container">
        <div class="row my-3">
            <div class="col-md-12">
                {{-- <a href="{{ url()->previous() }}" class="btn btn-sm fw-bold btn-primary" style="float:right">Go Back</a> --}}
                <a href="#" onclick="history.back()" class="btn btn-sm fw-bold btn-primary mt-2" style="float:right"><i class="fa-solid fa-arrow-left"></i> &nbsp; Go back</a>
                @if($disabled == 'TRUE')
                    <a href="{{ url('/') }}/print-employee-information-form/{{ $employee_data[0]['employee_id'] }}" target="_blank">
                        <button type="button" class="btn btn-sm fw-bold btn-primary m-2" style="float:right"><i class="fa fa-download"></i> &nbsp; Download Form</button>
                    </a>
                @endif
            </div>
        </div>
    </div>


    <div class="container mt-3">
        <div class="row">
            <div class="col-lg-3">
            </div>
            <div class="col-lg-6">
                @if(!empty($companySetting['company_logo']))
                    <img src="{{$companySetting['company_logo']}}"  alt="logo" class="logo">
                @else
                    <img src="{{asset('portal_assets/images/fav-icons.png')}}"  alt="logo" class="logo">
                @endif
            </div>
            <div class="col-lg-3">
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row sec1">
            <div class="col-lg-8">
                <label for="date">Date: <small><i>(Office use)</i></small></label>
                <input type="text" id="date" name="date" value="{{date('Y-m-d')}}" readonly>

            </div>
            <div class="col-lg-4">
                <p>{{ $companySetting['company_address']}}</p>
                <p><a href="tel:021-34540913">Contact: 021-34540913</a></p>
                <p><a href="https://sourcecode.world//">sourcecode.world</a></p>
            </div>
        </div>
    </div>
</section>

<form id="personal_information_form">
    <input type="hidden" name="_token" value="GR7zdr6LIVB0PpYOlMuJmYAvQLSN3uA03YAjkELH">        
    <div class="container mt-3">
        <div class="row sec2">
            <div class="col-md-2">
            </div>
            <div class="col-md-8">
                <h1>EMPLOYEE INFORMATION FORM</h1>
            </div>
            <div class="col-md-2">
            </div>
        </div>


        <div class="row sec3 mt-3">
            <div class="col-md-6">


                <div class="form-group row">
                    <label class="col-sm-6 col-form-label">Employee code: <small><i>(Office use)</i></small>
                    </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="rm" placeholder="0000"
                            value="{{ $employee_data[0]['employee_code'] }}" readonly="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-6 col-form-label">Name: <small><i>(As per cnic)</i></small></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="name" placeholder="Name"
                            value="{{ $employee_data[0]['employee_name'] }}" readonly="">
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-6 col-form-label">Designation: <small><i>(Office use)</i></small> </label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="designation" placeholder="designation"
                            value="{{ $employee_data[0]['designation_name'] }}" readonly="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-6 col-form-label">Department: <small><i>(Office use)</i></small></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="dept" placeholder="department"
                            value="{{ $employee_data[0]['department_name'] }}" readonly="">
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-6 col-form-label">Joining date: <small><i>(Office use)</i></small></label>
                    <div class="col-sm-6">

                        <input type="date" id="birthday" name="birthday"
                            value="{{$employee_data[0]['employee_doj'] }}" readonly="">

                    </div>

                </div>



            </div>

            {{-- src="{{ url('/form/img/profile.png') }}" --}}
            <div class="col-md-6">
                <div class="profile-images-card">
                    <div class="profile-images">
                       
                        @if (empty($information_data[0]['profile_avatar']) || $information_data[0]['profile_avatar'] == '')
                                <img src="{{asset('portal_assets/form/img/profile.png')}}"  id="upload-img">
                            @else

                                @if(file_exists('portal_assets/profile_pictures/'.$information_data[0]['profile_avatar']))
                                    <img src="{{asset('portal_assets/profile_pictures'). '/' . $information_data[0]['profile_avatar'] }}"
                                    id="upload-img">
                                @else
                                    <img src="{{asset('portal_assets/form/img/profile.png')}}"  id="upload-img">
                                @endif
                               
                            @endif
                    </div>
                    <div class="custom-file">
                        @if (empty($information_data[0]['profile_avatar']) || $information_data[0]['profile_avatar'] == '')
                        <label for="fileupload">Upload Picture</label>
                    @else
                        <label for="fileupload">Change Picture</label>
                    @endif
                    <input type="file" id="fileupload" accept="image/*" name="profile_picture">
                    </div>
                </div>
            </div>

        </div>
    </div>


    <section>
        <div class="container">
            <div class="row mt-3 tt">


                <div class="table-responsive-sm table-responsive-md table-responsive-lg ">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" class="pi">PERSONAL INFORMATION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Father's Name <span class="text-danger">*</span></th>
                                <td> <span><input type="text" class="form-control f_error" name="fathersname"
                                    value="{{ $information_data[0]['fathername'] }}"><input
                                            type="hidden" class="form-control"
                                            value="{{ $employee_data[0]['employee_id'] }}"
                                            name="employeeid"><input type="hidden" class="form-control"
                                            value="{{ $information_data[0]['information_id'] }}"
                                            name="information_id"></span></td>
                                <th scope="row">Date of Birth <span class="text-danger">*</span></th>
                                <td><input type="date" class="form-control f_error" name="birthdate"
                                    value="{{ $information_data[0]['dob'] }}"></td>
                            </tr>
                            <tr>
                                <th scope="row">Gender <span class="text-danger">*</span></th>
                                <td>

                                    <select class="form-select" name="gender">
                                    <option value="">Select Gender</option>
                                        <option value="Male" @if ( $employee_data[0]['employee_gender'] == 'Male') selected @endif>
                                            Male</option>
                                        <option value="Female" @if ( $employee_data[0]['employee_gender'] == 'Female') selected @endif>
                                            Female</option>
                                    </select>

                                </td>
                                <th scope="row">Marital status <span class="text-danger">*</span></th>
                                <td>
                                    
                                    <select class="form-select" name='martial_status'>
                                        <option value="">Marital status</option>
                                        <option value="Single" @if (empty($information_data) || ($information_data[0]['maritalstatus'] == 'Single')) selected @endif>
                                            Single</option>
                                        <option value="Married" @if (empty($information_data) ||  ($information_data[0]['maritalstatus'] == 'Married')) selected @endif>
                                            Married</option>
                                        <option value="Widowed" @if (empty($information_data) ||  ($information_data[0]['maritalstatus'] == 'Widowed')) selected @endif>
                                            Widowed</option>
                                        <option value="Separated"
                                            @if (empty($information_data) ||  ($information_data[0]['maritalstatus'] == 'Separated')) selected @endif>Separated</option>
                                        <option value="Divorced" @if (empty($information_data) ||  ($information_data[0]['maritalstatus'] == 'Divorced')) selected @endif>
                                            Divorced</option>
                                    </select>
                                    
                                </td>

                            </tr>
                            <tr>
                                <th scope="row">CNIC# <span class="text-danger">*</span></th>
                                <td> <input type="text" class="form-control" name="cnic"
                                    value="{{ $employee_data[0]['employee_cnic'] }}" readonly>
                                    
                                    </td>

                                <th scope="row">Date of expiry</th>
                                <td>

                                    <input type="date" class="form-control" name="cnic_expiry"
                                    value="{{ $information_data[0]['dateofexpiry'] }}">

                                </td>
                            </tr>


                            <tr>
                                <th scope="row">Birth place</th>
                                <td> <input type="text" class="form-control" name="birth_place"
                                    value="{{ $information_data[0]['birthplace'] }}"></td>
                                <th scope="row">Citizenship</th>
                                <td> <input type="text" class="form-control" name="citizenship"
                                    value="{{ $information_data[0]['citizenship'] }}"></td>
                            </tr>


                            <tr>
                                <th scope="row">Passport#</th>
                                <td> <input type="text" class="form-control" name="passport"
                                        value="{{ $information_data[0]['passport'] }}"></td>
                                <th scope="row">Religion <span class="text-danger">*</span></th>
                                <td>
                                    <select class="form-select" name="religion"
                                        aria-label="Default select example">
                                        <option value="">Select Religion</option>
                                        <option value="">Select Religion</option>
                                        <option value="Atheism/Agnosticism">Atheism/Agnosticism</option>
                                        <option value="Bahai" @if ($information_data[0]['religion'] == 'Divorced') selected @endif>
                                            Bahai</option>
                                        <option value="Buddhism" @if ($information_data[0]['religion'] == 'Divorced') selected @endif>
                                            Buddhism</option>
                                        <option value="Christianity"
                                            @if ($information_data[0]['religion'] == 'Divorced') selected @endif>Christianity</option>
                                        <option value="Confucianism"
                                            @if ($information_data[0]['religion'] == 'Divorced') selected @endif>Confucianism
                                        </option>
                                        <option value="Druze" @if ($information_data[0]['religion'] == 'Druze') selected @endif>
                                            Druze</option>
                                        <option value="Gnosticism"
                                            @if ($information_data[0]['religion'] == 'Gnosticism') selected @endif>Gnosticism</option>
                                        <option value="Hinduism"
                                            @if ($information_data[0]['religion'] == 'Hinduism') selected @endif>Hinduism</option>
                                        <option value="Islam" @if ($information_data[0]['religion'] == 'Islam') selected @endif>
                                            Islam</option>
                                        <option value="Jainism"
                                            @if ($information_data[0]['religion'] == 'Jainism') selected @endif>Jainism</option>
                                        <option value="Rastafarianism"
                                            @if ($information_data[0]['religion'] == 'Rastafarianism') selected @endif>Rastafarianism
                                        </option>
                                        <option value="Shinto" @if ($information_data[0]['religion'] == 'Shinto') selected @endif>
                                            Shinto</option>
                                        <option value="Sikhism"
                                            @if ($information_data[0]['religion'] == 'Sikhism') selected @endif>Sikhism</option>
                                        <option value="Zoroastrianism"
                                            @if ($information_data[0]['religion'] == 'Zoroastrianism') selected @endif>Zoroastrianism
                                        </option>
                                        <option value="Traditional African Religions"
                                            @if ($information_data[0]['religion'] == 'Traditional African Religions') selected @endif>Traditional African
                                            Religions
                                        </option>
                                        <option value="African Diaspora Religions"
                                            @if ($information_data[0]['religion'] == 'African Diaspora Religions') selected @endif>African Diaspora
                                            Religions</option>
                                        <option value="Indigenous American Religions"
                                            @if ($information_data[0]['religion'] == 'Indigenous American Religions') selected @endif>Indigenous American
                                            Religions
                                        </option>
                                    </select>
                                </td>
                            </tr>

                            <tr>


                            </tr>

                            <tr>
                                <th scope="row">Permanent Address <span class="text-danger">*</span></th>
                                <td> <input type="text" class="form-control" name="permanentaddress"
                                    value="{{ $information_data[0]['permanentaddress'] }}"></td>
                                <th scope="row">Current Address <span class="text-danger">*</span></th>
                                <td> <input type="text" class="form-control" name="currentaddress"
                                    value="{{ $information_data[0]['currentaddress'] }}"></td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- Emergency Contact Information-->
                <div class="table-responsive-sm table-responsive-md table-responsive-lg ">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" colspan="6" class="pi">Emergency Contact Information </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Name <span class="text-danger">*</span></th>
                                <td>
                                    <input type="text" class="form-control" name="emergency_name"
                                    value="{{$information_data[0]['emergency_name']}}">
                                </td>
                                <th scope="row">Contact No <span class="text-danger">*</span></th>
                                <td> <input type="text" class="form-control" name="emergency_contact_no"
                                    value="{{$information_data[0]['emergency_contact_no']}}"></td>
                                <th scope="row">Relation <span class="text-danger">*</span></th>
                                <td>  <select class="form-control" name="employee_relation" style="width: 100%;">
                                    <option value="" >-Select Relation </option>
                                    @if (!empty($relations))
                                        @foreach($relations as $relation)
                                            <option value="{{$relation}}" @if($relation == $information_data[0]['employee_relation'] )  selected @endif>{{$relation}}</option>
                                        @endforeach   
                                    @endif
                                </select></td>
                            </tr>
                           
                        </tbody>
                    </table>
                </div>

                <!-- BANK DETAILS-->
                <div class="table-responsive-sm table-responsive-md table-responsive-lg ">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" class="pi">BANK DETAILS </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Bank Name <span class="text-danger">*</span></th>
                                <td>
                                    <select class="form-control" name="bank_names" style="width: 100%;">
                                        <option value="" >-Select Bank </option>
                                        @if (!empty($all_banks))
                                            @foreach ($all_banks as $all_bank)
                                                <option value="{{ $all_bank['bank_name'] }}" @if($information_data[0]['bankname'] == $all_bank['bank_name']) selected @endif>{{ $all_bank['bank_name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </td>
                                <th scope="row">Bank code <span class="text-danger">*</span></th>
                                <td> <input type="number" class="form-control" name="bankcode"
                                    value="{{ $information_data[0]['bankcode'] }}"></td>
                            </tr>
                            <tr>
                                <th scope="row">Account# <span class="text-danger">*</span></th>
                                <td> <input type="number" class="form-control" name="accounnumb"
                                    value="{{ $information_data[0]['accountno'] }}"></td>
                                <th scope="row">Account title <span class="text-danger">*</span></th>
                                <td> <input type="text" class="form-control" name="accountitle"
                                    value="{{ $information_data[0]['accounttitle'] }}"></td>
                            </tr>

                            <tr>
                                <th scope="row">IBAN number <span class="text-danger">*</span></th>
                                <td> <input type="text" class="form-control" name="ibannumb"
                                    value="{{ $information_data[0]['ibannumber'] }}"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>



                <!-- History employee-->
                <div class="table-responsive-sm table-responsive-md table-responsive-lg">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" colspan="8" class="pi">EMPLOYEMENT HISTORY
                                    <small>(Starting with your current organization)</small><span
                                        class="text-danger">*</span></th>
                            </tr>
                            <tr>
                                <th scope="col">Employers's Name</th>
                                <th scope="col">Designation</th>
                                <th scope="col">Salary & Benefits</th>
                                <th scope="col">Duration(Start/End date)</th>
                                <th scope="col">Reason for leaving<span class="text-danger">*</span></th>
                            </tr>
                        </thead>
                        @php
                            $emphistory = $information_data[0]['employementhistory'] ? json_decode($information_data[0]['employementhistory']) : '';
                            $empcount = $emphistory ? count($emphistory) : 1;
                        @endphp
                        <tbody id="employee_history_container">
                            <input type="hidden" name="employement_history_length" value="{{$empcount }}">

                            @for ($i = 1; $i <= $empcount; $i++)
                            @php $temp =  ($emphistory) ? $emphistory[$i-1] : '' @endphp
                    
                                <tr id="employee_row{{ $i }}">
                                    <td scope="row"><input type="text" class="form-control"
                                        name="employername{{ $i }}"
                                        value="{{ $emphistory ? $temp->employername : '' }}"></td>
                                    <td><input type="text" class="form-control"
                                        name="designation{{ $i }}"
                                        value="{{ $emphistory ? $temp->designation : '' }}"></td>
                                    <td><input type="text" class="form-control"
                                        name="salaryandbenefits{{ $i }}"
                                        value="{{ $emphistory ? $temp->salaryandbenefits : '' }}"></td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td>

                                                    <input type="date" class="form-control"
                                                    name="durationstartdate{{ $i }}"
                                                    value="{{ $emphistory ? $temp->durationstartdate : '' }}">

                                                </td>
                                                <td>

                                                    <input type="date" class="form-control"
                                                    name="durationenddate{{ $i }}"
                                                    value="{{ $emphistory ? $temp->durationenddate : '' }}">

                                                </td>
                                            </tr>
                                        </table>

                                    </td>
                                    <td><input type="text" class="form-control"
                                        name="reasonforleaving{{ $i }}"
                                        value="{{ $emphistory ? $temp->reasonforleaving : '' }}"></td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                    <div class="text-center">
                        <div class="btn-group mb-3">
                            <button type="button" class="btn btn-primary" onclick="addEmployeeRow()">Add
                                More</button>
                            <button type="button" class="btn btn-dark mx-3"
                                onclick="deleteEmployeeRow()">Less One</button>
                        </div>
                    </div>
                </div>




                <div class="table-responsive-sm table-responsive-md table-responsive-lg ">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" colspan="8" class="pi">ACADEMIC CREDENTIALS<span
                                        class="text-danger">*</span></th>
                            </tr>
                            <tr>
                                <th scope="col">Degree</th>
                                <th scope="col">Institution</th>
                                <th scope="col">Majors</th>
                                <th scope="col">Passing year</th>
                                <th scope="col">Grade/CGPA</th>
                            </tr>
                        </thead>
                            @php
                                $acadcred = $information_data[0]['academiccredential'] ? json_decode($information_data[0]['academiccredential']) : '';
                                $acadcount = $acadcred ? count($acadcred) : 1;
                            @endphp
                            <tbody id="academic_credential_container">
                                <input type="hidden" name="academic_credential_length" value="{{ $acadcount }}">

                                @for ($i = 1; $i <= $acadcount; $i++)
                                    @php $temp =  ($acadcred) ? $acadcred[$i-1] : '' @endphp
                                    <tr id="academic_row{{ $i }}">
                                        <th scope="row"><input type="text" class="form-control"
                                                name="degree{{ $i }}"
                                                value="{{ $acadcred ? $temp->degree : '' }}"></th>
                                        <td><input type="text" class="form-control"
                                                name="intitution{{ $i }}"
                                                value="{{ $acadcred ? $temp->intitution : '' }}"></td>
                                        <td><input type="text" class="form-control"
                                                name="majors{{ $i }}"
                                                value="{{ $acadcred ? $temp->majors : '' }}">
                                        </td>
                                        <td><input type="text" class="form-control"
                                                name="passingyear{{ $i }}"
                                                value="{{ $acadcred ? $temp->passingyear : '' }}">
                                        </td>
                                        <td><input type="text" class="form-control"
                                                name="grade{{ $i }}"
                                                value="{{ $acadcred ? $temp->grade : '' }}"></td>
                                    </tr>
                                @endfor

                            </tbody>
                    </table>
                    <div class="text-center">
                        <div class="btn-group mb-3">
                            <button type="button" class="btn btn-primary" onclick="addAcademicRow()">Add
                                More</button>
                            <button type="button" class="btn btn-dark mx-3"
                                onclick="deleteAcademicRow()">Less One</button>
                        </div>
                    </div>
                </div>




                <div class="table-responsive-sm table-responsive-md table-responsive-lg ">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" colspan="8" class="pi">PROFESSIONAL QUALIFICATION /
                                    CERTIFICATIONS / TRAININGS (If any)</th>
                            </tr>
                            <tr>
                                <th scope="col">Type</th>
                                <th scope="col">Details</th>
                                <th scope="col">Institute</th>
                                <th scope="col">From</th>
                                <th scope="col">Till</th>
                            </tr>
                        </thead>
                        @php
                        $profqua = $information_data[0]['professionalqualification'] ? json_decode($information_data[0]['professionalqualification']) : '';
                        $profcount = $profqua ? count($profqua) : 1;
                    @endphp
                    <tbody id="professional_qualification_container">
                        <input type="hidden" name="professional_qualification_length"
                            value="{{ $profcount }}">
                        @for ($i = 1; $i <= $profcount; $i++)
                            @php $temp =  ($profqua) ? $profqua[$i-1] : '' @endphp

                            <tr id="qualification_row{{ $i }}">
                                <th scope="row"><input type="text" class="form-control"
                                        name="type{{ $i }}"
                                        value="{{ $profqua ? $temp->type : '' }}"></th>
                                <td><input type="text" class="form-control"
                                        name="details{{ $i }}"
                                        value="{{ $profqua ? $temp->details : '' }}"></td>
                                <td><input type="text" class="form-control"
                                        name="institute{{ $i }}"
                                        value="{{ $profqua ? $temp->institute : '' }}"></td>
                                <td><input type="text" class="form-control"
                                        name="from{{ $i }}"
                                        value="{{ $profqua ? $temp->from : '' }}"></td>
                                <td><input type="text" class="form-control"
                                        name="till{{ $i }}"
                                        value="{{ $profqua ? $temp->till : '' }}"></td>
                            </tr>
                        @endfor

                    </tbody>
                    </table>
                    <div class="text-center">
                        <div class="btn-group mb-3">
                            <button type="button" class="btn btn-primary" onclick="addQualificationRow()">Add
                                More</button>
                            <button type="button" class="btn btn-dark mx-3"
                                onclick="deleteQualificationRow()">Less One</button>
                        </div>
                    </div>
                </div>


                <!-- relatives -->
                <div class="table-responsive-sm table-responsive-md table-responsive-lg ">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" class="pi">DO YOU HAVE ANY RELATIVES AT
                                    ISMAIL INUDSTRIES LIMITED?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th colspan="4" class="text-center">Relative 1</th>
                            </tr>
                            <tr>
                                <th scope="row">Name</th>
                                <td> <input type="text" class="form-control" name="relativename1"
                                        value="{{ $information_data[0]['relativename1'] }}"></td>
                                <th scope="row">Designation</th>
                                <td> <input type="text" class="form-control" name="relativedesignation1"
                                        value="{{ $information_data[0]['relativedesignation1'] }}"></td>
                            </tr>

                            <tr>
                                <th scope="row">Department</th>
                                @csrf
                                <td> <input type="text" class="form-control" name="relativedepartment1"
                                        value="{{ $information_data[0]['relativedepartment1'] }}"></td>
                                <th scope="row">Relation type</th>
                                <td> <input type="text" class="form-control" name="relativerelationtype1"
                                        value="{{ $information_data[0]['relativerelationtype1'] }}"></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-center">Relative 2</th>
                            </tr>
                            <tr>
                                <th scope="row">Name</th>
                                <td> <input type="text" class="form-control" name="relativename2"
                                        value="{{ $information_data[0]['relativename2'] }}"></td>
                                <th scope="row">Designation</th>
                                <td> <input type="text" class="form-control" name="relativedesignation2"
                                        value="{{ $information_data[0]['relativedesignation2'] }}"></td>
                            </tr>

                            <tr>
                                <th scope="row">Department</th>
                                <td> <input type="text" class="form-control" name="relativedepartment2"
                                        value="{{ $information_data[0]['relativedepartment2'] }}"></td>
                                <th scope="row">Relation type</th>
                                <td> <input type="text" class="form-control" name="relativerelationtype2"
                                        value="{{ $information_data[0]['relativerelationtype2'] }}"></td>
                            </tr>


                        </tbody>
                    </table>
                </div>



                <!-- relatives -->
                <div class="table-responsive-sm table-responsive-md table-responsive-lg ">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col" colspan="4" class="pi">PREVIOUS APPLICATION IN
                                    SOURCECODE?
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">Have you applied in Sourcecode before?</th>
                                <td>
                                    <select class="form-select" name="appliedbefore" aria-label="Default select example" onchange="appliedBefore(this.value)">
                                        <option selected>Select Option</option>
                                        <option value="Yes"  @if ($information_data[0]['appliedbefore'] == 'Yes') selected @endif>Yes, I've Applied</option>
                                        <option value="No"  @if ($information_data[0]['appliedbefore'] == 'No') selected @endif>No, I haven't Applied</option>
                                    </select>
                                </td>
                            </tr>
                            <tr class=" @if ($information_data[0]['appliedbefore'] == 'No') d-none @endif appliedBeforeDiv">
                                <th scope="row">What Position</th>
                                <td> <input type="text" class="form-control" name="whatposition"
                                        value="{{ $information_data[0]['whatposition'] }}"></td>
                            </tr>
                            <tr class=" @if ($information_data[0]['appliedbefore'] == 'No') d-none @endif appliedBeforeDiv">
                                        <th scope="row">Result of Application</th>
                                <td> <input type="text" class="form-control" name="resultofapplication"
                                        value="{{ $information_data[0]['resultofapplication'] }}"></td>
                            </tr>



                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <div class="row text-center">
            <div class="col-md-12">
                <button class="btn submit-btn-form" style="background: #1076bc; color:#fff;padding: 15px 25px 15px 25px;">SAVE
                    INFORMATION</button>
            </div>
        </div>
    </section>

</form>

    <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.8.2/js/lightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>

    <script src="{{ url('portal_assets/form/js/main.js') }}"></script>
    <script src="{{ url('portal_assets/form/js/form-validation.js') }}"></script>
    <script src="{{ url('portal_assets/form/js/jquery-latest.min.js') }}"></script>

    <script>
        var disabled_option = "{{ $disabled }}";
       var base_url = "{{url('/')}}";  
    </script>

    <script src="{{url('assets/js/personal-information/form.js')}}"></script>


