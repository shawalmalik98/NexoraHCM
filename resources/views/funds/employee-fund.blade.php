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
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Employee Funds</h1>
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
                                <div class="col-md-6 fv-row" id="" >
                                    <label class="fs-6 fw-semibold mb-2">Fund Type</label>
                                    <select class="form-select" id="fund_policy" data-hide-search="true" data-kt-select2="true" data-placeholder="Select fund Type" name="fund_name">
                                    <option value="">Select Fund Status</option>
                                        @if(!empty($employee_funds))
                                            @foreach ($employee_funds as $fund)
                                                <option value="{{$fund['fund_id']}}">{{$fund['fund_name']}}</option>
                                            @endforeach
                                        @endif  
                                    </select>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
            <div class="card mb-5 mb-xl-8" id="tableForm" >

                <div class="card-body py-3">
                     
                    <table class="table table-bordered  table-row-bordered">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-6">Employee ID</th>
                                <th>Employee Name</th>
                                <th>Fund Name</th>
                                <th>Fund Amount</th>
                                <th>Start Date</th>

                            </tr>
                        </thead>
                        <tbody id = "responsefundname">
                       
                        </tbody>
                    </table>
                    
                </div>
            
            </div>
        
        </div>
    
    </div>
</div>


@endsection



@push('js-link')

<script src="{{url('assets/js/funds/employee-fund.js')}}"></script>

@endpush