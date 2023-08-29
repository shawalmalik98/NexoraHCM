@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                Employee Letters Template
                </h1>
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
                                        <th>Create By</th>
                                        <th>Employee</th>
                                        <th>Title</th>
                                        <th>Upload By</th>
                                        <th>Submitted </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @if(!empty($all_employee_letter))
                                        @foreach ($all_employee_letter as $letter)
                                            <tr>
                                                <td class="ps-8">{{ $letter['temp_emp_id'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($letter['temp_emp_create_by']) !!}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($letter['temp_emp_employee_id']) !!}</td>
                                                <td>{{ $letter['letter_template_name'] }}</td>
                                                <td>{{ $letter['temp_emp_created_at'] }}</td>
                                                <td>
                                                    @if( $letter['temp_emp_submit'] == 1)
                                                        <span class="badge badge-light-danger font-weight-bold">Unsubmitted</span>
                                                    @else
                                                        <span class="badge badge-light-success font-weight-bold">Submitted</span>
                                                    @endif
                                                </td>
                                                <td>    

                                                    <a  href="{{url('employee-letter/employee-letter-pdf/'.$letter['temp_emp_id'])}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Letter" target="_blank" > 
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                    </a>

                                                    @if(!empty($letter['temp_emp_employee_id']) && $letter['temp_emp_submit'] == 1)
                                                    <a  href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Send Letter To Employee"  onclick="employeeLetterTransferred({{$letter['temp_emp_id']}})" > 
                                                        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                                                    </a>
                                                    @else
                                                    <a  href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1"   style="cursor: not-allowed;" > 
                                                        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                                                    </a>
                                                    @endif


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

            <form id="employeeLetterTransferredForm" class="form">
                @csrf
                <div class="modal fade" id="employeeLetterTransferred" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollabl">
                        {{-- sasa --}}
                        <div class="modal-content rounded">
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Confirmation Alert</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
            
                            <div class="modal-body">
                                <p>Are you sure to you want to transfer letter to employee ?</p>
                            </div>
            
                            <div class="modal-footer">
                                <input type="hidden" name="letter_id" value="" >
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="employeeLetterTransferredFormButton" class="btn btn-primary">
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
<script>

    function employeeLetterTransferred (letter_id){

        $('input[name="letter_id"]').val(letter_id);
        $('#employeeLetterTransferred').modal('show');
    }
    $('#employeeLetterTransferredForm').submit(function(e){
        e.preventDefault()
        send_server_request( 'POST' ,'employee-letter/insert-template-in-document' , this ,'employeeLetterTransferredFormButton', "No" , 'letters' ,3000)
    })

</script>
@endpush
