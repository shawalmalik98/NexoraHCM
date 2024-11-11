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
                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm m-1 view_details_btn" data-bs-toggle="tooltip" aria-label="View Incentive Details" data-kt-initialized="1" data-period="{{ $inc['incentive_period'] }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 download_details_btn" data-bs-toggle="tooltip" aria-label="Download Incentive Details" data-period="{{ $inc['incentive_period'] }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>

                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm m-1 edit_details_btn" data-bs-toggle="tooltip" title="Edit Incentive" data-period="{{ $inc['incentive_period'] }}">
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
                            {{-- @foreach($incentive as $key => $inc)
                                @php
                                    $incentiveMonth = date('n', strtotime($inc['incentive_period'])); 
                                @endphp

                                @if($incentiveMonth == $currentMonth)  
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
                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm m-1 view_details_btn" data-bs-toggle="tooltip" aria-label="View Incentive Details" data-kt-initialized="1" data-period="{{ $inc['incentive_period'] }}">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>
                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 download_details_btn" data-bs-toggle="tooltip" aria-label="Download Incentive Details" data-period="{{ $inc['incentive_period'] }}">
                                                <i class="fa fa-download" aria-hidden="true"></i>
                                            </a>

                                            <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm m-1 edit_details_btn" data-bs-toggle="tooltip" title="Edit Incentive" data-period="{{ $inc['incentive_period'] }}">
                                                <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                    </svg>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach --}}
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
                            <select class="form-select" name="incentive_period" required> 
                                <option value="">Select Month</option>

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
                                <option value="DSR + TSM">DSR + TSM</option>
                                <option value="TSM + ASM 2-3">TSM + ASM 2-3</option>
                                <option value="TSM - IMT">TSM - IMT</option>
                                <option value="ASM - 1 ZSM KAM">ASM - 1 ZSM KAM</option>
                                {{-- <option value="ASM + TSM">ASM + TSM</option> --}}
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
                                <option value="Drop Size">Drop Size</option>
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
                            <button class="btn btn-danger remove-row-btn" type="button" style="display: none;">-</button>

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

<form id="updatedesignation_form" class="form">
    @csrf
    <div class="modal fade" id="update-new-designation" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Update Incentive</h4>
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
                            <select class="form-select" name="incentive_period" required> 
                                <option value="">Select Month</option>

                                @for ($monthz = $currentMonth; $monthz <= 12; $monthz++)
                                    <option value="{{ $months[$monthz] }} - {{ date('Y') }}">{{ $months[$monthz] }} - {{ date('Y') }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div id="designation-rows-container"></div> 
                    
                    {{-- <div class="row mt-3">
                        <div class="col-md-3">
                            <button class="btn btn-primary" type="button" id="add-row-btn">+</button>
                        </div>
                    </div> --}}
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
    // $(document).ready(function() {
    //     $('#add-row-btn').on('click', function() {
    //         var newRow = $('.designation-row:first').clone();
    //         newRow.find('input').val('');
    //         newRow.find('select').val('');
    //         $('#designation-rows-container').append(newRow);
    //     });
    // });

    $(document).ready(function() {
    $('#add-row-btn').on('click', function() {
        // Clone the first row and clear input values
        var newRow = $('.designation-row:first').clone();
        newRow.find('input').val('');
        newRow.find('select').val('');
        $('#designation-rows-container').append(newRow);

        // Show the remove button if row count is greater than 1
        toggleRemoveButton();
    });

    // Remove the last row when the remove button is clicked
    $(document).on('click', '.remove-row-btn', function() {
        if ($('.designation-row').length > 1) {
            $('.designation-row:last').remove();
            toggleRemoveButton();
        }
    });

    // Function to show/hide remove button based on row count
    function toggleRemoveButton() {
        if ($('.designation-row').length > 1) {
            $('.remove-row-btn').show();
        } else {
            $('.remove-row-btn').hide();
        }
    }

    // Initialize the remove button state
    toggleRemoveButton();
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

    $('#updatedesignation_form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '/update-incentive',
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#update-btn .indicator-label').hide();
                $('#update-btn .indicator-progress').show();
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
                $('#update-btn .indicator-label').show();
                $('#update-btn .indicator-progress').hide();
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

    $('.edit_details_btn').on('click', function() {
        var incentivePeriod = $(this).data('period'); 

        $.ajax({
            url: '/get-incentive-details', 
            method: 'GET',
            data: {
                incentive_period: incentivePeriod 
            },
            success: function(response) {
                var modalBody = $('#update-new-designation .modal-body');
                modalBody.find('#designation-rows-container').empty(); 

                $.each(response.data, function(index, incentive) {
                    var htmlContent = `
                        <div class="row designation-row">
                            <input type="hidden" name="incentive_id[]" value="${incentive.incentive_id}">
                            <div class="col-md-2 mt-3">
                                <label class="fs-6 fw-semibold mb-2 required">Designation</label>
                                <select class="form-select" name="designation_name[]" required>
                                    <option value="DSR" ${incentive.employee_type === 'DSR' ? 'selected' : ''}>DSR</option>
                                    <option value="TSM" ${incentive.employee_type === 'TSM' ? 'selected' : ''}>TSM</option>
                                    <option value="ASM" ${incentive.employee_type === 'ASM' ? 'selected' : ''}>ASM</option>
                                    <option value="RSM" ${incentive.employee_type === 'RSM' ? 'selected' : ''}>RSM</option>
                                    <option value="ZSM" ${incentive.employee_type === 'ZSM' ? 'selected' : ''}>ZSM</option>
                                    <option value="NSM" ${incentive.employee_type === 'NSM' ? 'selected' : ''}>NSM</option>
                                    <option value="DSR + TSM" ${incentive.employee_type === 'DSR + TSM' ? 'selected' : ''}>DSR + TSM</option>
                                    <option value="TSM + ASM 2-3" ${incentive.employee_type === 'TSM + ASM 2-3' ? 'selected' : ''}>TSM + ASM 2-3</option>
                                    <option value="TSM - IMT"  ${incentive.employee_type === 'TSM - IMT' ? 'selected' : ''}>TSM - IMT</option>
                                    <option value="ASM - 1 ZSM KAM" ${incentive.employee_type === 'ASM - 1 ZSM KAM' ? 'selected' : ''}>ASM - 1 ZSM KAM</option>

                                </select>
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="fs-6 fw-semibold mb-2 required">Brand</label>
                                <input type="text" class="form-control" name="brand_name[]" value="${incentive.brand}" required />
                            </div>
                            <div class="col-md-2 mt-3">
                                <label class="fs-6 fw-semibold mb-2 required">Target Type</label>
                                <select class="form-select" name="target_type[]" required>
                                    <option value="Primary Target" ${incentive.productive_criteria === 'Primary Target' ? 'selected' : ''}>Primary Target</option>
                                    <option value="Secondary Target" ${incentive.productive_criteria === 'Secondary Target' ? 'selected' : ''}>Secondary Target</option>
                                    <option value="Productive Criteria" ${incentive.productive_criteria === 'Productive Criteria' ? 'selected' : ''}>Productive Criteria</option>
                                    <option value="Drop Size" ${incentive.productive_criteria === 'Drop Size' ? 'selected' : ''}>Drop Size</option>
                                </select>
                            </div>
                            <div class="col-md-3 mt-3">
                                <label class="fs-6 fw-semibold mb-2 required">Target Value</label>
                                <input type="text" class="form-control" name="target_value[]" value="${incentive.target_value}" required />
                            </div>
                            <div class="col-md-3 mt-3">
                                <label class="fs-6 fw-semibold mb-2 required">Incentive</label>
                                <input type="text" class="form-control" name="incentive[]" value="${incentive.incentive}" required />
                            </div>
                        </div>
                    `;

                    modalBody.find('#designation-rows-container').append(htmlContent);
                });

                $('#update-new-designation').modal('show'); 
            },
            error: function(error) {
                console.log('Error fetching incentive details:', error);
            }
        });
    });





</script>
@endpush