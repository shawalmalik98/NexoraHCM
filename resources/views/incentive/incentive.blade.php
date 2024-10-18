@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Incentive Attachments</h1>
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
                <div class="card-body py-3">
                    <table class="table table-bordered  table-row-bordered" id="system_datatable22222">
                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4">Ref ID</th>
                                <th>Incentive Period</th>
                                <th>Incentive Attachment</th>
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
                            @endphp
                                @for ($month = 1; $month <= 12; $month++)
                                    <tr>
                                        <td class="ps-5">
                                            {{ $month }}
                                        </td>
                                        <td>
                                            {{ $months[$month] }} - {{ date('Y') }}
                                        </td>
                                        <td>
                                            <input type="file" class="form-control" name="attachments">
                                        </td>
                                        <td>
                                            <button class="btn btn-primary" style="width:100%;">Save</button>
                                        </td>
                                    </tr>
                            @endfor
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
                            <label class="fs-6 fw-semibold mb-2 required">Department (Month)</label>
                            <select class="form-select" name="department_ids[]">
                                @for ($monthz = 1; $monthz <= 12; $monthz++)
                                    <option value="{{ $months[$monthz] }}">{{ $months[$monthz] }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <!-- First Row with Month Dropdown and Fields to be cloned -->
                    <div class="row designation-row">
                        
                        <div class="col-md-3 mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Designation</label>
                            <select class="form-select" name="target_type[]" required>
                                <option value="DSR">DSR</option>
                                <option value="TSM">TSM</option>
                                <option value="ASM">ASM</option>
                                <option value="RSM">RSM</option>
                                <option value="ZSM">ZSM</option>
                                <option value="NSM">NSM</option>
                            </select>
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Brand</label>
                            <input type="text" class="form-control" name="brand_name[]" placeholder="Enter Brand" required />
                        </div>
                        <div class="col-md-3 mt-3">
                            <label class="fs-6 fw-semibold mb-2 required">Target Type</label>
                            <select class="form-select" name="target_type[]" required>
                                <option value="Productive Criteria">Productive Criteria</option>
                                <option value="Primary Target">Primary Target</option>
                                <option value="Secondary Target">Secondary Target</option>
                            </select>
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






@endsection
@push('js-link')
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
                    // alert('Designations added successfully!');
                    // $('#adddesignation_form')[0].reset();
                    // $('#designation-rows-container').empty();
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
</script>
@endpush