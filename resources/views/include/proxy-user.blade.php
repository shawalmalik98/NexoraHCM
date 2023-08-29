<div class="modal fade" id="proxy_user_model" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top mw-650px">
        <div class="modal-content rounded">
            <div class="modal-header">
            <h4 class="modal-title pl-4">Proxy User Login</h4>
            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                <span class="svg-icon svg-icon-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                    </svg>
                </span>
            </button>
            </div>
            
            <div class="modal-body">
                <form id="proxy_user_form" class="form">
                @csrf
                    <div class="row mb-8">
                        <div class="col-md-12 fv-row">
                            <p class="text-center text-danger">Proxy login is a time saving feature that automatically login an account of selected employee whether he/she is active or not without asking for their password. This feature is just enabled for Directors / CEO's and company's HR employees.</p>
                        </div>
                        <div class="col-md-12 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Employees Status</label>
                            <select class="form-select  fw-bold" data-hide-search="true" data-control="select2" data-placeholder="Select" name="" required="" id="proxy_employee_data">
                                <option value="1">Active Employees</option>
                                <option value="2">De-active Employees</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-8">
                    <div class="col-md-12 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Employee</label>
                            <select class="form-select  search-employee" data-hide-search="true" data-control="select2" data-placeholder="Select Employee" name="proxy_by_name" required="" id="proxy_by_name">
                            </select>
                    </div>
                </div>
                    <div class="text-center">
                        <div id="gl_div">
                        </div>
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="proxy_form_btn" class="btn btn-primary">
                            <span class="indicator-label">Login</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@push('js-link')
<script>

    $(function(){
        $('#proxy_by_name').select2({
            dropdownParent: $('#proxy_user_model')
        });
    });
</script>


    
@endpush