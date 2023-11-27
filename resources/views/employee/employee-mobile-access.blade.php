@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Employees Mobile Access
                </h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                 
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add_button_modal">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Add buttons</a>   
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
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{ !empty(Request::get('department_id')) ? $filter : '' }} {{ !empty(Request::get('business_unit')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                       
                            <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                <form action="{{ url('employee-mobile-access') }}" method="GET">
                                    <div class="px-7 py-5">
                                        <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                    </div>

                                    <div class="separator border-gray-200"></div>
                                    <div class="px-7 py-5">

                                        <div class="mb-3">
                                    
                                            <label class="form-label fw-semibold">
                                                <span>Department</span>
                                            </label>
                                            <div>
                                                <select class="form-select" data-kt-select2="true" name="department_id" data-placeholder="Select Department">
                                                    <option value="">Select Department</option>
                                                    <option value="All" @if (Request::get('department_id') == 'All') selected @endif>All Departments</option>
                                                    @if(!empty($departments))
                                                        @foreach ($departments as $department)
                                                            <option value="{{ $department['department_id'] }}" @if (Request::get('department_id') == $department['department_id']) selected @endif>{{ $department['department_name'] }}</option>
                                                        @endforeach
                                                    @endif                                                             
                                                </select>
                                            </div>
                                        </div>
                                        <div class="mb-3 row" >
                                            <div class="mb-3">
                                            
                                                <label class="form-label fw-semibold">Business Unit</label>
                                                
                                                <div>
                                                    <select class="form-select " data-kt-select2="true" data-placeholder="Select Business Unit" name="business_unit">
                                                    <option value="">Select Business Unit</option>
                                                    <option value="All" @if (Request::get('business_unit') == 'All') selected @endif>All Business Units</option>
                                                    <option value="Candyland" @if (Request::get('business_unit') == 'Candyland') selected @endif>CandyLand</option>
                                                    <option value="Bisconni" @if (Request::get('business_unit') == 'Bisconni') selected @endif>Bisconni</option>
                                                    <option value="Snackcity" @if (Request::get('business_unit') == 'Snackcity') selected @endif>Snackcity</option>
                                                    <option value="Hudson Pharma" @if (Request::get('business_unit') == 'Hudson Pharma') selected @endif>Hudson Pharma</option>
                                                    <option value="Astro" @if (Request::get('business_unit') == 'Astro') selected @endif>Astro</option>  
                                                    <option value="Act Group" @if (Request::get('business_unit') == 'Act Group') selected @endif>Act Group</option>  
                                                    <option value="Ismail Nutrition" @if (Request::get('business_unit') == 'Ismail Nutrition') selected @endif>Ismail Nutrition</option>
                                                    <option value="Flour" @if (Request::get('business_unit') == 'Flour') selected @endif>Flour</option>
                                                </select>
                                                </div>
                                                
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                        
                                            <label class="form-label fw-semibold">Employee Group </label>
                                            
                                            <div>
                                                <select class="form-select " data-kt-select2="true" data-placeholder="Select Employee group" name="employee_group">
                                                    <option value="">Select Employee group</option>
                                                    <option value="All" @if (Request::get('employee_group') == 'All') selected @endif>All Employee Group</option>
                                                    @if(!empty($employee_groups))
                                                        @foreach ($employee_groups as $employee_group)
                                                            <option value="{{ $employee_group['employee_group_id'] }}"
                                                            @if(!empty(Request::get('employee_group')) && Request::get('employee_group') == $employee_group['employee_group_id']) selected @endif>
                                                                {{ $employee_group['group_name'] }}</option>
                                                        @endforeach 
                                                    @endif    
                                                </select>
                                            </div> 
                                        </div>
                                        
                                        <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('business_unit')) || !empty(Request::get('department_id')) || !empty(Request::get('employee_group')))
                                                <a href="{{ url('employee-mobile-access') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 ">Reset Filter</a>
                                            @endif
                                            <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                            <button type="submit" class="btn btn-sm btn-primary" >Apply</button>
                                        </div>
                                        
                                    </div>
                                 </form>
                            </div>
                       
                    </div>
                </div>

                <div class="card-body py-3">

                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">

                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Employee Name</th>
                                        <th>Depart &amp; Desig</th>
                                        <th>Button Name</th>
                                        <th>Button Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($all_mobile_access))
                                        @foreach($all_mobile_access as $all_device)
                                            <tr>
                                                <td class="ps-4">{{$all_device['employee_code']}} - {{$all_device['employee_name']}}</td>
                                                <td>
                                                    <p class="text-dark fw-bold mb-1 fs-6"> {{ $all_device['department_name'] }}</p>
                                                    <p class="text-muted fw-semibold text-muted fs-7">{{$all_device['designation_name'] }}</p>
                                                </td>
                                                <td> 
                                                    @if( $all_device['access_button_id'] == 1)
                                                        <span>Attendance</span>
                                                    @elseif($all_device['access_button_id'] == 2)
                                                        <span>Leave</span>
                                                    @elseif($all_device['access_button_id'] == 3)
                                                        <span>Resignation</span>
                                                     @elseif($all_device['access_button_id'] == 4)
                                                         <span>Salary</span>
                                                     @elseif($all_device['access_button_id'] == 5)
                                                        <span>Loan</span>
                                                    @else
                                                        <span>News</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if( $all_device['access_buttons'] == 1)
                                                        <span class="badge badge-light-success font-weight-bold">Active</span>
                                                    @else
                                                        <span class="badge badge-light-danger font-weight-bold">De-active</span>

                                                    @endif
                                                </td>

                                                <td>

                                                    <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Edit mobile button" onclick="editbutton('{{$all_device['mobile_access_id']}}','{{$all_device['employee_name']}}','{{$all_device['access_button_id']}}','{{$all_device['access_buttons']}}')"  >
                                                        <span class="svg-icon svg-icon-3">
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

        </div>

        <form id="add_mobile_button_form" class="form" autocomplete="off">
            @csrf	
            <div class="modal fade" id="add_button_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content rounded">
                        
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Add Mobille Access button</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        
                        <div class="modal-body scroll-y pt-3 pb-5">
                            
                            <div class="col-md-12 mb-8 mt-3 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Employee Name</label>
                                <div class="position-relative d-flex align-items-center">
                                    <select class="form-select addemployeeaccess" data-control="select2" data-placeholder="Select Employee" name="employee_name" required>
                                        <option value="">Select Employee </option>
                                        @if(!empty($all_employees))
                                            @foreach ($all_employees as $employees)
                                                <option value="{{ $employees['employee_id'] }}">
                                                    {{ $employees['employee_name'] }}</option>
                                            @endforeach 
                                        @endif    
                                    </select>
                                   
                                </div>
                            </div>
        
                            <div class="col-md-12 mb-8 mt-3 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Button Name</label>
                                <div class="position-relative d-flex align-items-center">
                                    <select class="form-select addemployeeaccess" data-control="select2" data-hide-search="false" data-placeholder="Select button" name="button_name" required>
                                        <option value="">Select button</option>
                                            <option value="1">Attendance</option>
                                            <option value="2" >Leave</option>
                                            <option value="3" >Resignation</option>
                                            <option value="4">Salary</option>
                                            <option value="5">Loan</option>
                                            <option value="6" >News</option>
                                    </select>
                                </div>
                            </div>    
                            
                            <div class="col-md-12 mb-8 mt-3 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Button Status</span>
                                </label>
                                <select class="form-select addemployeeaccess" data-control="select2" data-hide-search="false" data-placeholder="Select button Status" name="button_status" required> 
                                    <option value="">Button Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
        
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="submit-btn" class="btn btn-primary">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        <form id="edit_mobile_button_form" class="form" autocomplete="off">
            @csrf
            <div class="modal fade" id="edit_button_modal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable">
                    <div class="modal-content rounded">
                        
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Edit Mobille Access button</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                    
                        <div class="modal-body scroll-y pt-3 pb-5">

                            <input type="hidden" class="form-control "  name="mobile_access_id" required=""/>
                            
                            <div class="col-md-12 mb-8 mt-3 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Employee Name</label>
                                <div class="position-relative d-flex align-items-center">
                                    {{-- <select class="form-select updateemployeeaccess" data-control="select2" data-placeholder="Select Employee" id="employee_id" name="employee_name" required>
                                        <option value="">Select Employee </option>
                                        @if(!empty($all_employees))
                                            @foreach ($all_employees as $employees)
                                                <option value="{{ $employees['employee_id'] }}">
                                                    {{ $employees['employee_name'] }}</option>
                                            @endforeach 
                                        @endif    
                                    </select> --}}
                                    
                                    <input type="text" class="form-control" name="employee_name"  id="employee_id"  required="" readonly=""/>
                                                                       
                                </div>
                            </div>
        
                            <div class="col-md-12 mb-8 mt-3 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Button Name</label>
                                <div class="position-relative d-flex align-items-center">
                                    <select class="form-select updateemployeeaccess" data-control="select2" data-hide-search="false" data-placeholder="Select button" id="button_id" name="button_name" required>
                                        <option value="">Select button</option>
                                        <option value="1">Attendance</option>
                                            <option value="2" >Leave</option>
                                            <option value="3" >Resignation</option>
                                            <option value="4">Salary</option>
                                            <option value="5">Loan</option>
                                            <option value="6" >News</option>
                                    </select>
                                </div>
                            </div>    
                            
                            <div class="col-md-12 mb-8 mt-3 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Button Status</span>
                                </label>
                                <select class="form-select updateemployeeaccess" data-control="select2" data-hide-search="false" data-placeholder="Select button Status" id="status_id" name="button_status" required> 
                                    <option value="">Button Status</option>
                                    <option value="1">Active</option>
                                    <option value="0">Deactive</option>
                                </select>
                            </div>
        
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="update-btn" class="btn btn-primary">
                                <span class="indicator-label">Submit</span>
                                <span class="indicator-progress">Please wait 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>



@endsection

@push('js-link')

<script src="{{url('assets/js/employees/employee-mobile-access.js')}}"></script>

@endpush