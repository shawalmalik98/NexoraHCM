@extends('layout.main')



@push('css-link')
@endpush

@section('main-section')
<style>
    .has-time{
        border: 1px solid greenyellow;
    }
</style>
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Deduction Policy</h1>
            </div>            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        @if(Session::has('error'))
            <script>
                swal.fire("Error", "{{Session::get('error') }}", "error");
            </script>
        @endif
        <div class="app-container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body py-3">
                    <form class="form" autocomplete="off">

                        @if(!empty($login_user))
                            <div class="row mt-5 mb-5">
                                <div class="col-md-6 fv-row" id="deduction_source" >
                                    <label class="fs-6 fw-semibold mb-2">Deduction Source Type</label>
                                    <select class="form-select" id="deduction_policy" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Deduction Source Type" name="department"  >
                                        <option value=""></option>
                                        <option value="1">Late Check In / Early Check Out</option>
                                        <option value="2">Half Day</option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card mb-5 mb-xl-8" id="tableForm" >

                <div class="card-body py-3">
                    <form id="updateEmployeeDeductionPolicy">
                        
                        <table class="table table-bordered  table-row-bordered">
                            <thead>
                                <tr class="fw-bold text-muted bg-light">
                                    <th class="ps-6">No. of Days</th>
                                    <th>No. of Working days should be deduct</th>
                                </tr>
                            </thead>
                            <tbody id="responseAttendanceRule">
                            
                            </tbody>
                        </table>
                        
                    </div>

                    <div class="card-footer" id="submit_button_div">

                    </div>
                </form>
            
            </div>
        
        </div>
    
    </div>
</div>


@endsection



@push('js-link')

<script src="{{url('assets/js/payroll/deduction-policy.js')}}"></script>

@endpush