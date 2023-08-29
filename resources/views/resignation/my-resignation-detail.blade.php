@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
    {{--begin::Toolbar--}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        {{--begin::Toolbar container--}}
        <div class="app-container col-12 d-flex flex-stack">
            {{--begin::Page title--}}
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                {{--begin::Title--}}
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Employee Resignation Detail</h1>
                {{--end::Title--}}
                {{--begin::Breadcrumb--}}
                {{--end::Breadcrumb--}}
            </div>
            {{--end::Page title--}}
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                   <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
            </div>
           
        </div>
        {{--end::Toolbar container--}}
    </div>
    {{--end::Toolbar--}}
    {{--begin::Content--}}

    @if(!empty($resignations))
        <div class="app-content flex-column-fluid mt-5">
           
            <div class="app-container">
              
                <div class="row">
                    @foreach ($resignations as $resignation)
                        <div class="col-sm-12 ">
                            <div class="card mb-5 mb-xl-8">
                                <div class="d-flex flex-row-reverse ribbon ribbon-start ribbon-clip">
                                    <div class="ribbon-label fw-bolder">
                                        @if($resignation['resignation_status'] == 1)
                                            Resignation Approval Pending
                                        <span class="ribbon-inner bg-gray"></span>
                                        @elseif($resignation['resignation_status'] == 2)
                                            Resignation Approved
                                        <span class="ribbon-inner bg-success"></span>
                                        @else
                                            Resignation Rejected
                                        <span class="ribbon-inner bg-danger"></span>
                                        @endif
                                    </div>
                                </div>
                              
                                <div class="card-body py-8 pt-12 ">
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
                                                <span> Notice Period Start Date</span>
                                            </label>
                                            <input type="date" class="form-control " value="{{$resignation['resignation_from_date']}}" readonly id="fromDate" name="from_date"
                                            required="required" />
                                        </div>
                                        <div class="col-md-4 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                <span>Notice Period End Date </span>
                                            </label>
                                            <input type="date" class="form-control " value="{{$resignation['resignation_to_date']}}" id="toDate" readonly   name="to_date"
                                            required="required" />
                                        </div>
        
                                        <div class="col-md-4 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                <span> Notice Period Total Days</span>
                                            </label>
                                            @php
                                                $startDate = new DateTime($resignation['resignation_from_date']); 
                                                $endDate = new DateTime($resignation['resignation_to_date']);
                                                $total_days = $endDate->diff($startDate)->format("%a");
                                            @endphp
                                            <input type="text" class="form-control " value="{{$total_days+1}}" readonly  />
                                        </div>
                                    </div>
        
                                    <div class="row g-9 mb-8">
                                        <div class="col-md-12 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                <span>Resignation Description</span>
                                            </label>
                                            <textarea class="form-control" id="description" placeholder="Enter a Description" rows="4" name="resignation_message" readonly>{{ $resignation['resignation_description'] }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@push('js-link')

<script>

if (document.querySelector('#description') != null) {
    ClassicEditor

        .create(document.querySelector('#description'))

        .then(description => {
            description.enableReadOnlyMode("description");

        })
        .catch(error => {
            console.error(error);
        });
}

</script>

@endpush