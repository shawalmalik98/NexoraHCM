@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                    Employees Salary
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
                    
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                        <div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                            
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Employee Name</th>
                                        <th>Depart &amp; Desig</th>
                                        <th>Basic Salary</th>
                                        <th>House Rent</th>
                                        <th>Utilities</th>
                                        <th>Gross Adjustment</th>
                                        <th>Earning Title</th>
                                        <th>Earning Amount</th>
                                        <th>Deduction Title</th>
                                        <th>Deduction Amount</th>
                                        <th>Created Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($employees_salary))
                                        @foreach($employees_salary as $salary)
                                            <tr>
                                                <td class="ps-4">{{ $salary['employee_code']}} - {{$salary['employee_name'] }}</td>
                                                <td>
                                                    <p class="text-dark fw-bold mb-1 fs-6"> {{ $salary['department_name'] }}</p>
                                                    <p class="text-muted fw-semibold text-muted fs-7">{{ $salary['designation_name'] }}</p>
                                                </td>
                                                <td> {{ Crypt::decryptString($salary['basic_salary']) ?? '-' }} </td>
                                                <td> {{ Crypt::decryptString($salary['house_rent']) ?? '-' }} </td>
                                                <td> {{ Crypt::decryptString($salary['utilities']) ?? '-' }} </td>
                                                <td> {{ Crypt::decryptString($salary['gross_adjustment']) ?? '-' }} </td>
                                                <td> {{ $salary['earning_title'] ?? '-' }} </td>
                                                <td> {{ Crypt::decryptString($salary['earning_amount']) ?? '-' }} </td>
                                                <td> {{ $salary['deduction_title'] ?? '-' }} </td>
                                                <td> {{ Crypt::decryptString($salary['deduction_amount']) ?? '-' }} </td>
                                                <td> {{ date('d F, Y H:i:s', strtotime($salary['created_at'])) ?? '-' }}</td>
                                                
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
    
    </div>
</div>

@endsection

@push('js-link')

{{-- <script src="{{url('assets/js/room-screen/rooms.js')}}"></script> --}}

@endpush       