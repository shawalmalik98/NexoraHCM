@extends('layout.main')

@section('main-section')
<style>
    .tox-promotion{
        display: none;
    }
</style>
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Letters Template
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#AddLetter">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Make New Letter</a>
                </div>

            </div>

        </div>

    </div>

    <div class="app-content flex-column-fluid">

        <div class="app-container">

            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilter">
                        </div>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="table-responsive popup-visible ">
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4 min-w-50px sorting">Ref ID</th>
                                        <th>Letter Name</th>
                                        <th>Create By</th>
                                        <th>Create At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @if(!empty($all_letter_template))
                                        @foreach ($all_letter_template as $letter)
                                            <tr>
                                                <td class="ps-8">{{ $letter['letter_template_id'] }}</td>
                                                <td>{{ $letter['letter_template_name'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($letter['letter_template_create_by']) !!}</td>
                                                <td>{{ $letter['letter_template_created_at'] }}</td>
                                                <td>
                                                    <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#employeeLetter" onclick="employeeLetter({{$letter['letter_template_id']}},{{$letter['letter_template_variables']}})">
                                                        <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Add Employee Letter">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                    </a>

                                                    <a  href="{{url('letter-template/letter-pdf/'.$letter['letter_template_id'])}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Template" target="_blank" > 
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            
            <form id="addLetterForm" class="form">
                @csrf
                <div class="modal fade" id="AddLetter" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable mw-950px">
                        <div class="modal-content rounded">
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Make New Letter</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                                </button>
                            </div>

                            <div class="modal-body scroll-y pt-0 pb-15" >
                                <div class="col-md-12 mb-8 mt-3 fv-row">
                                    <label class="required fs-6 fw-semibold mb-2">Letter Name</label>
                                    <div class="position-relative d-flex align-items-center">
                                        <input type="text" class="form-control " placeholder="Enter a Letter Name" name="letter_name" required=""/>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-8 mt-3 fv-row">
                                    <textarea id="tiny"  name="template" ></textarea>
                                </div>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="addLetterFormButton" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>                  
                    </div>
                </div>
                {{--end::Modal - New Target--}}
            </form>


            <form id="employeeLetterForm" class="form">
                @csrf
                <div class="modal fade" id="employeeLetter" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable mw-950px">
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Employee Letter</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                                </button>
                            </div>

                            <div class="modal-body scroll-y pt-0 pb-15"  >
                                <div class="col-md-12 mb-8 mt-3 fv-row">
                                    <label class="fs-6 fw-semibold mb-2">Employee</label>
                                    <select class="form-select selectemployee" data-control="select2" data-hide-search="false" data-placeholder="Select Employee" name="employee_id" > 
                                        <option value="">Select Employee</option>
                                        @if(!empty($employees))
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee['employee_id'] }}">{{ $employee['employee_name'] }} ({{ $employee['department_name'] }} - {{ $employee['designation_name'] }})</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div id="formFied" >
                                </div>
                            </div>

                            <div class="modal-footer justify-content-center">
                                <input type="hidden" name="letter_id" value="" >
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="employeeLetterFormButton" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                {{--end::Modal - New Target--}}
            </form>
        </div>
    </div>
</div>

@endsection

@push('js-link')

<script src="{{url('assets/js/tinymce/tinymce.min.js')}}" referrerpolicy="origin"></script><script>
    tinymce.init({
    selector: 'textarea#tiny',
        // plugins: [
        // 'a11ychecker','advlist','advcode','advtable','autolink','checklist','export',
        // 'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks',
        // 'powerpaste','fullscreen','formatpainter','insertdatetime','media','table','help','wordcount'
        // ],
        plugins: [
        'advlist','autolink',
        'lists','link','image','charmap','preview','anchor','searchreplace','visualblocks','fullscreen','insertdatetime','media','table','help','wordcount'
        ],
        toolbar: 'undo redo | a11ycheck casechange blocks | bold italic backcolor | alignleft aligncenter alignright alignjustify |' +
        'bullist numlist checklist outdent indent | removeformat | code table help'
    })
</script>
<script>

    $('#addLetterForm').submit(function(e){
        e.preventDefault()
        send_server_request_tiny( 'POST' ,'letter-template/add-new-letter-template' , this ,'addLetterFormButton', "Yes" , '' ,3000)
    })

    $('#employeeLetterForm').submit(function(e){
        e.preventDefault()
        send_server_request( 'POST' ,'letter-template/employee-letter-template' , this ,'employeeLetterFormButton', "No" , 'employee-letter' ,3000);
    })

    function employeeLetter(letter_id,letter_template_variables){

        $('input[name="letter_id"]').val(letter_id);
        $('#formFied').html('');
        var fields = '';
        $.each(letter_template_variables, function(key, variables) {
             
            fields += `
            <div class="col-md-12 mb-8 mt-3 fv-row">
                <label class="required fs-6 fw-semibold mb-2">${variables.replaceAll('_',' ')}</label>
                <div class="position-relative d-flex align-items-center">
                    <input type="text" class="form-control " placeholder="Enter a ${variables.replaceAll('_',' ')}" name="${variables}" required=""/>
                </div>
            </div>`;
        })      
        $('#formFied').html(fields);
    }
    


    function send_server_request_tiny( method_type ,action_url , form_data_this ,       button_id, screen_reload_same = "Yes" , target_url=null , reload_seconds = 3000){

        url = base_url + '/' + action_url;

        $('#'+button_id).prop('disabled', true);
        $('#'+button_id).attr('data-kt-indicator', 'on');
        $('#'+button_id).css('cursor', 'not-allowed');

        setTimeout(()=>{

            let form_data = new FormData(form_data_this);
            var body_data ;

            if(method_type == 'GET'){
                body_data = {
                    // Adding method type
                    method: method_type
                }
            }else{
                body_data = {
                    // Adding method type
                    method: method_type,
                    // Adding body or contents to send
                    // body: JSON.stringify({form_data}),
                    body : form_data,
                    // Adding headers to the request
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                }
            }
            fetch(url,body_data)
            // Converting to JSON
            .then(response =>{
                // response.json()
                if (response.ok) {
                    return response.json();
                }
                return Promise.reject(response);
            })
            // Displaying results to console
            .then(json =>{
                if (json.status == 'TRUE') {
                    Toast.fire({
                        icon: 'success',
                        title: json.msg,
                        timer: reload_seconds,
                    })
                    if(screen_reload_same == "Yes"){
                        setTimeout(() => {
                            location.reload();
                        }, reload_seconds);
                    }else{
                        setTimeout(() => {
                            window.location.href = base_url+"/"+target_url;
                        }, reload_seconds);
                    }
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: json.msg,
                        timer: reload_seconds,
                    })
                    $('#'+button_id).prop('disabled', false);
                    $('#'+button_id).removeAttr('data-kt-indicator');
                    $('#'+button_id).css('cursor', 'pointer');
                }
            }).catch(response => {
                $('#'+button_id).prop('disabled', false);
                $('#'+button_id).removeAttr('data-kt-indicator');
                $('#'+button_id).css('cursor', 'pointer');
                Toast.fire({
                    icon: 'warning',
                    title: response.statusText,
                    timer: reload_seconds,
                })
            });
        },1000)
    }   
</script>
@endpush
