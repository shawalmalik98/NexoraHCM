<div class="modal fade" id="change_password_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top mw-650px">
        <div class="modal-content rounded">
            <div class="modal-header">
            <h4 class="modal-title pl-4">Change Account Password</h4>
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
                <form id="change_password_form" class="form">
                @csrf

                    <div class="row mb-8">
                        <div class="col-10 col-md-11">
                            <label class="required fs-6 fw-semibold mb-2">Current Password</label>
                            <div class="position-relative d-flex align-items-center">
                            <input type="password" class="form-control showpassword" placeholder="Enter current password" name="current_password" id="current_password" required/>
                            </div>
                        </div>
                        <div class="col-2 col-md-1 d-flex align-items-center justify-content-center justify-content-md-start mt-8 mt-md-8">
                            <label class="fs-6 fw-semibold mb-0"></label>

                            <a href="javascript:void(0)" onclick="show_password_function(this,'current_password')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" >
                                <i class="fa fa-eye" aria-hidden="true"></i>
                            </a>
                            
                        </div>
                    </div>

                    <div class="row mb-8" >
                    <div class="col-10 col-md-11">
                        <label class="required fs-6 fw-semibold mb-2">New Password</label> <span class="text-danger">(Max 10 characters)</span>
                        <div class="position-relative d-flex align-items-center">
                            <input type="password" class="form-control showpassword" placeholder="Enter new password"  name="new_password" id="new_password"  required/>
                        </div>
                    </div>
                    <div class="col-2 col-md-1 d-flex align-items-center justify-content-center justify-content-md-start mt-8 mt-md-8">
                        <label class="fs-6 fw-semibold mb-0"></label>
                        
                        <a href="javascript:void(0)" onclick="show_password_function(this,'new_password')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" >
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                        
                    </div>
                </div>
                <div class="row mb-8" >
                    <div class="col-10 col-md-11">
                        <label class="required fs-6 fw-semibold mb-2">Confirm New Password</label> <span class="text-danger">(Max 10 characters)</span>
                        <div class="position-relative d-flex align-items-center">
                            <input type="password" class="form-control showpassword" placeholder="Enter confirm new password" name="confirm_new_password" id="confirm_new_password" required/>
                        </div>
                    </div>
                    <div class="col-2 col-md-1 d-flex align-items-center justify-content-center justify-content-md-start mt-8 mt-md-8">
                        <label class="fs-6 fw-semibold mb-0"></label>
                        
                        <a href="javascript:void(0)"  onclick="show_password_function(this,'confirm_new_password')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" >
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </a>
                        
                    </div>
                </div>
                    <div class="text-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="password_form_btn" class="btn btn-primary">
                            <span class="indicator-label">Change Password</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>