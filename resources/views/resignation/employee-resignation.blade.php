@extends('layout.main')

@push('cs-link')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
@endpush

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
    {{--begin::Toolbar--}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        {{--begin::Toolbar container--}}
        <div class="app-container col-12 d-flex flex-stack">
            {{--begin::Page title--}}
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                {{--begin::Title--}}
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Employee Resignation Form</h1>
                {{--end::Title--}}
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                   <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
            </div>
           
        </div>
        {{--end::Toolbar container--}}
    </div>
    {{--end::Toolbar--}}
    {{--begin::Content--}}

    <div class="app-content flex-column-fluid">
        <div class="app-container">
            <div class="card mb-5 mb-xl-8">
                <div class="card-body py-8 pt-12">
                    <form id="add_employee_resignation_form" class="form" autocomplete="off">
                        @csrf
                        <div class="row g-9 mb-8">
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Employee Name</span>
                                </label>
                                <input type="text" class="form-control " value="{{$login_user[0]['employee_name'] ?? 'N/A'}}" readonly />
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Employee Department </span>
                                </label>
                                <input type="text" class="form-control " value="{{$department_name[0]['department_name'] ?? 'N/A'}}" readonly/>
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Employee Designation</span>
                                </label>
                                <input type="text" class="form-control " value="{{$designation_name[0]['designation_name'] ?? 'N/A'}}" readonly/>
                            </div>
                        </div>
                        <div class="row g-9 mb-8">
                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required"> Notice Period Start Date</span>
                                </label>
                                <input type="date" class="form-control " value="{{date('Y-m-d')}}" min="{{date('Y-m-d')}}" id="fromDate" name="from_date" required="required" />
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Notice Period End Date </span>
                                </label>
                                <input type="date" class="form-control " placeholder="mm/dd/yyyy" id="toDate"   min="{{date('Y-m-d')}}" name="to_date" required="required" />
                            </div>

                            <div class="col-md-4 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Notice Period Total Days</span>
                                </label>
                                <input type="text" class="form-control " placeholder="Total Notice Period Days" id="days"  readonly/>
                            </div>
                        </div>

                        <div class="row g-9 mb-8">
                            <div class="col-md-12 fv-row">
                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                    <span class="required">Resignation Message</span>
                                </label>
                                <textarea class="form-control" placeholder="Enter a Resignation Message" rows="4" name="resignation_message" id="reason" autocomplete="off"></textarea>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-lg btn-success" id="submit_btn">
                                <span class="indicator-label">Submit Resignation</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

@endsection



@push('js-link')

<script src="{{url('assets/js/resignation/employee-resignation.js')}}"></script>

@endpush
