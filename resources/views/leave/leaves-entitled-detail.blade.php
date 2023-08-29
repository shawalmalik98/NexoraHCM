@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        {{--begin::Toolbar container--}}
        <div class="app-container col-12 d-flex flex-stack">
            <div class="page-title  justify-content-center flex-wrap me-3">
                <h1 class="page-heading  text-dark fw-bold fs-3 flex-column justify-content-center my-0">Leave Allotment Form ({!! CustomHelper::getEmpProfileDiv($employee_id)!!})</h1>
            </div>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary"  >Go Back</a>
            </div>
        </div>
    </div>


    <div class="app-content flex-column-fluid">
        <div class="app-container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body py-3 pt-5">
                    <form  id="update_employee_leave_ent" class="form" autocomplete="off">
                        @csrf
                        <div class="row g-9 mb-8">
                            <input type="hidden" value="{{$employee_id}}" name="employee_id" />
                            
                            @foreach ($leaves_entitled as $leaves_entitled_employee)

                            <input type="hidden" value="{{$leaves_entitled_employee['leave_manage_id']}}" name="leave_type_id[]" />
                            <input type="hidden" value="{{$leaves_entitled_employee['leave_ent_created_at']}}" name="leave_ent_created_at[]" />
                                <div class="col-md-4 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Leave Name</span>
                                    </label>
                                    
                                    <input type="text" class="form-control bg-light" placeholder="Leave Name"  value="{{$leaves_entitled_employee['leave_name']}}"  readonly/>
                                </div>
                                
                                
                                <div class="col-md-4 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Leave Alloted</span>
                                    </label>
                                    
                                    <input type="number" class="form-control " 
                                    placeholder="Leave Alloted Employee" value="{{!empty($leaves_entitled_employee['leave_allotement'])?$leaves_entitled_employee['leave_allotement']:0}}" name="leave_alloted[]" required min="0" max="100" />
                                </div>

                                <div class="col-md-4 fv-row">
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Leave Availed</span>
                                    </label>
                                    
                                    <input type="text" class="form-control bg-light" 
                                    placeholder="Leave Availed Employee" value="{{$leaves_entitled_employee['employee_availed_leaves']}}" readonly/>
                                </div>
                            
                            @endforeach
                            
                        </div>
    
                        <div class="text-center">
                            <button type="submit"class="btn btn-lg btn-success" id="submit_btn">
                                <span class="indicator-label">Update Employee Leave</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                               </span>
                            </button>
                        </div>
                    </form>
                    {{--end::Table container--}}
                </div>
                {{--begin::Body--}}
            </div>
            {{--end::Tables Widget 11--}}
        </div>
        {{--end::Content container--}}

    </div>

</div>

@endsection



@push('js-link')

<script src="{{url('assets/js/leave/leaves-entitled-detail.js')}}"></script>

@endpush
