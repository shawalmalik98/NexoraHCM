<div class="modal fade" id="modalDisburse" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-650px">
        <form id="modalDisburseForm">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Disbursement of {{ (!empty($month)) ? "(".date("F", mktime(0, 0, 0, $month, 10))."-".$year.")" : '' }}</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="modal-body pt-0">
                     
                    <div class="row mt-5" >
                        <div class="col-md-9 fv-row" id="selectEmployee" >
                            <label class="fs-6 fw-semibold mb-2 required">Employee</label>
                            <select class="form-select" multiple="multiple" id="select_employee"  data-kt-select2="true" data-placeholder="Select Employee" name="employee_ids[]" data-close-on-select="true" required >
                                @if(!empty($fetch_employee_disburse))
                                    @foreach ($fetch_employee_disburse as $employee_disburse)
                                        <option value="{{ $employee_disburse['dw_id'] }}" >{{ $employee_disburse['employee_name'] }} - {{ $employee_disburse['employee_code'] }} ({{date('d-M-Y',strtotime($employee_disburse['dw_of_date']))}})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-8 mt-3 fv-row">
                            
                            <label class="fs-6 fw-semibold mb-2">All Wage(s)</label>
                                <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2">
                                <input class="form-check-input h-20px w-20px" type="checkbox" id="select_employee_check" name="select_all" value="Select All" /> 
                        
                            </label>
                        </div>
                    </div>

                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="included" id="included"  />
                                <label class="form-check-label" for="included">
                                    Disburse wages exclduing these selected employees 
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-5" >

                        <div class="col-md-12 fv-row">

                            <p onselectstart="javascript:return false;" >This action can't be undone. If you want to disburse the wages,
                                Please type <b>TODP-ID</b> (case sensitive) to confirm.</p>
                            <input type="text" class="form-control " name="confirmation_code" placeholder="Enter Confirmation Code" required="" autocomplete="off" onpaste="return false">

                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="modalDisburseFormButton" class="btn btn-primary">
                        <span class="indicator-label">Yes, Disburse</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>