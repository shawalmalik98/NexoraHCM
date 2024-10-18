@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Incentive Records</h1>
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-new-designation">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Add New Incentive</a>   
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
                    <table class="table table-bordered  table-row-bordered" id="system_datatable">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4">Ref ID</th>
                                <th>Incentive Period</th>
                                <th>Designation(s)</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $months = [
                                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 
                                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 
                                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                                ];
                                $currentMonth = date('n');
                            @endphp
                                @foreach($incentive as $key => $inc)
                                    <tr>
                                        <td class="ps-5">
                                            {{ ++$key }}
                                        </td>
                                        <td>
                                            {{ $inc['incentive_period'] }}
                                        </td>
                                        <td>
                                            {{ $inc['employee_types'] }}
                                        </td>
                                        <td>
                                            {{ $inc['max_created_at'] }}
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 view_details_btn" data-bs-toggle="tooltip" aria-label="View Incentive Details" data-kt-initialized="1" data-period="{{ $inc['incentive_period'] }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 download_details_btn" data-bs-toggle="tooltip" aria-label="Download Incentive Details" data-period="{{ $inc['incentive_period'] }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




<form id="adddesignation_form" class="form">
    @csrf
    <div class="modal fade" id="add-new-designation" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Add New Designation</h4>
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

                    <div class="row">
                        <div class="col-md-12">
                            <label class="fs-6 fw-semibold mb-2 required">Incentive Period</label>
                            {{-- <select class="form-select" name="incentive_period">
                                @for ($monthz = 1; $monthz <= 12; $monthz++)
                                    <option value="{{ $months[$monthz] }} - {{ date('Y') }}">{{ $months[$monthz] }}  - {{ date('Y') }}</option>
                                @endfor
                            </select> --}}
                            <select class="form-select" name="incentive_period"> 
                                @for ($monthz = $currentMonth; $monthz <= 12; $monthz++)
                                    <option value="{{ $months[$monthz] }} - {{ date('Y') }}">{{ $months[$monthz] }} - {{ date('Y') }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <!-- First Row with Month Dropdown and Fields to be cloned -->
                    <div class="row designation-row">
                        
                        <div class="col-md-2 mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Designation</label>
                            <select class="form-select" name="designation_name[]" required>
                                <option value="DSR">DSR</option>
                                <option value="TSM">TSM</option>
                                <option value="ASM">ASM</option>
                                <option value="RSM">RSM</option>
                                <option value="ZSM">ZSM</option>
                                <option value="NSM">NSM</option>
                            </select>
                        </div>
                        <div class="col-md-2 mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Brand</label>
                            <input type="text" class="form-control" name="brand_name[]" placeholder="Enter Brand" required />
                        </div>
                        <div class="col-md-2 mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Target Type</label>
                            <select class="form-select" name="target_type[]" required>
                                <option value="Primary Target">Primary Target</option>
                                <option value="Secondary Target">Secondary Target</option>
                                <option value="Productive Criteria">Productive Criteria</option>
                            </select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Target Value</label>
                            <input type="text" class="form-control" name="target_value[]" placeholder="Enter Target Value" required />
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Incentive</label>
                            <input type="text" class="form-control" name="incentive[]" placeholder="Enter Incentive" required />
                        </div>
                    </div>

                    <!-- Append New Rows Here -->
                    <div id="designation-rows-container"></div>
                    
                    <!-- Button to add a new row -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <button class="btn btn-primary" type="button" id="add-row-btn">+</button>
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
                </div>
                
            </div>
        </div>
    </div>
</form>

<div class="modal fade" id="incentiveDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Incentive Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>







@endsection
@push('js-link')
<script src="{{url('assets/js/department/department.js')}}"></script>
<script>
    $(document).ready(function() {
        $('#add-row-btn').on('click', function() {
            var newRow = $('.designation-row:first').clone();
            newRow.find('input').val('');
            newRow.find('select').val('');
            $('#designation-rows-container').append(newRow);
        });
    });

    $('#adddesignation_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '/save-incentive',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#submit-btn .indicator-label').hide();
                $('#submit-btn .indicator-progress').show();
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Something went wrong. Please try again.');
                }
            },
            error: function(xhr, status, error) {
                alert('Error: ' + xhr.responseText);
            },
            complete: function() {
                $('#submit-btn .indicator-label').show();
                $('#submit-btn .indicator-progress').hide();
            }
        });
    });

    $('.view_details_btn').on('click', function() {
        var incentivePeriod = $(this).data('period');

        $.ajax({
            url: '/get-incentive-details',
            method: 'GET',
            data: {
                incentive_period: incentivePeriod
            },
            success: function(response) {
                var modalBody = $('#incentiveDetailsModal .modal-body');
                modalBody.empty();
                var htmlContent = '<table class="custom-table">';
                htmlContent += '<thead><tr><th class="fw-bold">Incentive Period</th><th class="fw-bold">Designation</th><th class="fw-bold">Brand</th><th class="fw-bold">Target Type</th><th class="fw-bold">Target Value</th><th class="fw-bold">Incentive</th></tr></thead>';
                htmlContent += '<tbody>';
                $.each(response.data, function(index, incentive) {
                    htmlContent += '<tr>';
                    htmlContent += '<td>' + incentive.incentive_period + '</td>';
                    htmlContent += '<td>' + incentive.employee_type + '</td>';
                    htmlContent += '<td>' + incentive.brand + '</td>';
                    htmlContent += '<td>' + incentive.productive_criteria + '</td>';
                    htmlContent += '<td>' + incentive.target_value + '</td>';
                    htmlContent += '<td>' + incentive.incentive + '</td>';
                    htmlContent += '</tr>';
                });

                htmlContent += '</tbody></table>';
                modalBody.append(htmlContent);
                $('#incentiveDetailsModal').modal('show');
            },
            error: function(error) {
                console.log('Error fetching incentive details:', error);
            }
        });
    });

    $('.download_details_btn').on('click', function() {
        var incentivePeriod = $(this).data('period');
        
        window.location.href = '/download-incentive-details?incentive_period=' + incentivePeriod;
    });



</script>
@endpush