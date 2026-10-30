@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                   Manage Letters
                </h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">

                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addletter">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Add New Letter</a>
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
                     <div class="card-toolbar" >
                        @php $filter = 'btn-color-primary'; @endphp

                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('employee_location')) ? $filter : '' }} {{!empty(Request::get('emp_status')) ? $filter : '' }} {{!empty(Request::get('emp_name')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">

                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>

                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('letters') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                    </div>

                                    <div class="separator border-gray-200"></div>
                                    <div class="px-7 py-5">

                                        <div class="mb-3">
                                            
                                            <label class="form-label fw-semibold">
                                                <span>Office Location</span>
                                            </label>
                                            
                                            <div>
                                                <select class="form-select" data-control="select2" data-hide-search="false" name="employee_location" id="employee_location">
                                                    <option value="">Select Office Location</option>
                                                    @if(!empty($employee_locations))
                                                        @foreach ($employee_locations as $location)
                                                            <option value="{{ $location['location_id'] }}" @if (Request::get('employee_location') == $location['location_id']) selected @endif>{{ $location['location_title'] }}</option>
                                                        @endforeach
                                                    @endif                                                             
                                                </select>
                                            </div>
                                            
                                        </div>

                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                                <span class="required">Status</span>
                                            </label>

                                            <div>
                                                <select class="form-select " data-kt-select2="true" data-placeholder="Select Status" name="emp_status" id="employee_data" required>
                                                    <option value="1" @if ( Request::get('emp_status') == '1') selected @endif>Active Employees</option>
                                                    <option value="2" @if( Request::get('emp_status') == '2') selected @endif>De-active Employees</option>

                                                </select>
                                            </div>

                                        </div>
                                        <div class="mb-3">

                                            <label class="form-label fw-semibold">
                                             <span>By Name</span>
                                            </label>

                                            <div>
                                                <select class="form-select " data-kt-select2="true"  name="emp_name"
                                                id="company_emp_name" data-employee-select data-scope="active">


                                                    @if(!empty($employees))
                                                        <option value="">All Employees</option>
                                                        @include('include.employee-selected-options', ['selected' => Request::get('emp_name')])
                                                    @endif

                                                </select>
                                            </div>

                                        </div>

                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('employee_location')) || !empty(Request::get('emp_name')) || !empty(Request::get('emp_status')))

                                                <a href="{{ url('letters') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                                    >Reset Filter</a>
                                             @endif
                                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                        </div>

                                    </div>
                                 </form>
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
                                        <th>Employee</th>
                                        <th>Title</th>
                                        <th>Document Attachment</th>
                                        <th>Upload By</th>
                                        <th>Upload Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     @if(!empty($letters))
                                        @foreach ($letters as $letter)
                                            <tr>
                                                <td class="ps-8">{{ $letter['letter_id'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($letter['letter_employee_id']) !!}</td>
                                                <td>{{ $letter['letter_title'] }}</td>
                                                <td class="ps-15">
                                                   <a href="{{ url('/letters/download-employee-letter?path=letter/'.$letter['document_name']) }}&title=employee letter" target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Download File"><i class="fa fa-download" aria-hidden="true"></i></a>
                                                </td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($letter['uploader']) !!}</td>
                                                <td>{{ $letter['upload_date'] }}</td>
                                                <td>
                                                    <a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#updateletter" onclick="letterEditModel( '{{ $letter['letter_id'] }}','{{ $letter['letter_employee_id'] }}','{{ $letter['letter_title'] }}','{{ $letter['document_name'] }}')">
                                                        <span class="svg-icon svg-icon-3" data-bs-toggle="tooltip" title="Edit letter Detail">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
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

            
            <form id="addletterForm" class="form">
                @csrf
                <div class="modal fade" id="addletter" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Add Letter</h4>
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
                                    <label class="fs-6 fw-semibold mb-2 required">Employees</label>
                                    <select class="form-select  select" data-control="select2" data-hide-search="true" data-placeholder="Select Employee" name="employee" required="" data-employee-select data-scope="active">
                                        <option value="">Select Employee</option>
                                        @include('include.employee-selected-options', ['selected' => null])
                                    </select>
                                </div>
                                <div id="file_div">
                                    <div class="row mt-5" >
                                    <input type="hidden" name="file_length" value="1">
                                        <div class="col-md-6 fv-row">
                                            <label class="fs-6 fw-semibold mb-2 required">Title </label>
                                            <input type="text" class="form-control " name="title[]" placeholder="title" required="">
                                        </div>

                                        <div class="col-md-6 fv-row">
                                            <label class="required fs-6 fw-semibold mb-2">Document </label>
                                            <input type="file" class="form-control" accept=".docx,.txt,application/pdf,image/*" name="document_name[]"  placeholder="Document" required="">
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">

                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit-btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                                <button class="btn btn-primary" type="button" onclick="addfileRow()"><i class="fa fa-plus"></i></button>
                                <button class="btn btn-danger" type="button" onclick="deletefileRow()"><i class="fa fa-minus"></i></button>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                    {{--end::Modal - New Target--}}
            </form>
            

              

            
            <form id="updateletterForm" class="form">
                @csrf
                <div class="modal fade" id="updateletter" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                                <div class="modal-header">
                                    <h4 class="modal-title pl-4">Update Letter</h4>
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
                                        <label class="fs-6 fw-semibold mb-2 required">Employees</label>
                                        <input type="hidden" name="letter_id">
                                        <select class="form-select  select-update" data-control="select2" data-hide-search="true"  name="employee" id="letterupdate" required="" disabled data-employee-select data-scope="active">
                                            <option value="">Select Employee</option>
                                            @include('include.employee-selected-options', ['selected' => null])
                                        </select>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-6 fv-row">
                                            <label class="fs-6 fw-semibold mb-2 required">Title </label>
                                            <input type="text" class="form-control " name="title" id="title2" placeholder="title" required="">
                                        </div>

                                        <div class="col-md-6 fv-row">
                                            <label class="fs-6 fw-semibold mb-2">Document </label>
                                            <input type="file" class="form-control" accept=".docx,.txt,application/pdf,image/*" name="document_name" value="" placeholder="Attach Document" >
                                        </div>
                                    </div>
                                </div>
                            
                            
                            <div class="modal-footer justify-content-center">

                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="update-btn" class="btn btn-primary">
                                    <span class="indicator-label">Update</span>
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

<script src="{{url('assets/js/company-letters/all-letters.js')}}"></script>

@endpush
