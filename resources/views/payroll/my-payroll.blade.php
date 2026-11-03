@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My Payslips</h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#calculatePayrollAmount">
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3" d="M3 3V17H7V21H15V9H20V3H3Z" fill="currentColor"/>
                                <path d="M20 22H3C2.4 22 2 21.6 2 21V3C2 2.4 2.4 2 3 2H20C20.6 2 21 2.4 21 3V21C21 21.6 20.6 22 20 22ZM19 4H4V8H19V4ZM6 18H4V20H6V18ZM6 14H4V16H6V14ZM6 10H4V12H6V10ZM10 18H8V20H10V18ZM10 14H8V16H10V14ZM10 10H8V12H10V10ZM14 18H12V20H14V18ZM14 14H12V16H14V14ZM14 10H12V12H14V10ZM19 14H17V20H19V14ZM19 10H17V12H19V10Z" fill="currentColor"/>
                            </svg>
                        </span>
                        Calculate Payroll </a>
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
                        
                        <div id="tablePayroll_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="table-responsive">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                				 				
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="text-center" >Ref ID</th>
                                        <th>Pay Period</th>
                                        <th>Gross Salary</th>
                                        <th>Extra Earning</th>
                                        <th>Total Deduction</th>
                                        <th>Net Salary</th>
                                        <th>Generation Date</th>
                                        <th>Salary Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($fetch_employee_payroll))
                                        @foreach ($fetch_employee_payroll as $employee_payroll)
                                            <tr @if(request()->get('slip-id') != '' && $employee_payroll['pc_id'] == request()->get('slip-id')) style="background-color:bisque;" @endif >
                                                <td class="ps-8">{{ $employee_payroll['pc_id'] }}</td>
                                                <td>{{ date('M-Y',strtotime($employee_payroll['pc_year'].'-'.$employee_payroll['pc_month'].'-'.'01')) }} </td>
                                                <td>{{ number_format($employee_payroll['pc_employee_gross_salary'],2) }}</td>
                                                <td>{{ number_format($employee_payroll['pc_employee_extra_salary'],2) }}</td>
                                                <td>{{ number_format($employee_payroll['pc_employee_deduction'],2) }}</td>
                                                <td>{{ number_format($employee_payroll['pc_employee_net_salary'],2) }}</td>
                                                <td>{{ date('d-M-Y',strtotime($employee_payroll['pc_created_at'])) }}</td>
                                                <td><span class="badge badge-light-success font-weight-bold">Monthly</span></td>
                                                
                                                <td>
                                                    <a  href="{{ url('individual-payroll-pdf/'.$employee_payroll['pc_id'])}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Payslip" target="_blank" > 
                                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    <a  href="{{ url('individual-payroll-pdf/'.$employee_payroll['pc_id'])}}?download=TRUE" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Download Payslip" target="_blank" > 
                                                        <i class="fa fa-download" aria-hidden="true"></i>
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
    </div>
</div>
<form id="calculateMyPayrollAmountForm" class="form">
    @csrf
    <div class="modal fade" id="calculatePayrollAmount" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-650px">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Calculate Payroll</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="modal-body scroll-y pt-0 pb-10" >
                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row">
                            <label class="fs-6 fw-semibold mb-2 required">Select Month</label>
                            <select class="form-select" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Month" name="month" data-close-on-select="true" required >
                                <option value="" >Select</option>
                                @if(empty($current_calculate_option))
                                    <option value="1" >Current Month ({{date('F, Y')}})</option>
                                @endif
                                @if(empty($pervious_calculate_option))
                                    <option value="2" >Pervious Month ({{date('F, Y',strtotime('-1 month'))}})</option>
                                @endif
                            </select>
    
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer justify-content-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit_btn">
                        <span class="indicator-label">Calculate Payroll</span>
                        <span class="indicator-progress">Please wait
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    {{--end::Modal - New Target--}}
</form>
@endsection

@push('js-link')
<script>


    $('#calculateMyPayrollAmountForm').submit(function(e) {

        e.preventDefault();

        var url = "{{url('/calculate-my-payroll')}}";

        $('.submit_btn').prop('disabled', true);
        $('.submit_btn').attr('data-kt-indicator', 'on');
        $('.submit_btn').css('cursor', 'not-allowed');

        let form_data = new FormData(this);

        body_data = {

            // Adding method type
            method: 'POST',

            // Adding body or contents to send
            // body: JSON.stringify({form_data}),
            body : form_data,

            // Adding headers to the request
            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

            Swal.fire({
                title: json.title,
                html: json.text,
                icon: json.icon,
                buttonsStyling: false,
                confirmButtonText: "Thanks",
                customClass: {
                    confirmButton: "btn btn-primary"
                }

            }).then(function(result) {
                if (result.value) {
                    // location.reload();
                }
            });


        } else {


            Swal.fire({
            title: json.title,
            text: json.text,
            icon: json.icon,
            buttonsStyling: false,
            confirmButtonText: "Thanks",
            customClass: {
                confirmButton: "btn btn-primary"
            }
            });

            if (json.reload == 'TRUE') {
                setTimeout(() => {
                    location.reload();
                }, 3000);
            }

            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

        }

        }).catch(response => {

            $('.submit_btn').prop('disabled', false);
            $('.submit_btn').removeAttr('data-kt-indicator');
            $('.submit_btn').css('cursor', 'pointer');

            Swal.fire({
                icon: 'warning',
                title: response.statusText,
                buttonsStyling: false,
                confirmButtonText: "Thanks",
                customClass: {
                confirmButton: "btn btn-primary"
                }  
            })
        });
    })
</script>


    
@endpush