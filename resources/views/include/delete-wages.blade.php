<div class="modal fade" id="modalDeleteDepartment" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable mw-650px">
        <form id="modalDeleteDepartmentForm">
            <div class="modal-content rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Delete Wages of {{ (!empty($month)) ? "(".date("F", mktime(0, 0, 0, $month, 10))."-".$year.")" : '' }}</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-white" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="" />
                            </svg>
                        </span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row mt-5" >
                        <div class="col-md-9 fv-row" id="selectDeleteDepartment" >
                            <label class="fs-6 fw-semibold mb-2 required">Department</label>
                            <select class="form-select" id="departments_all_delete" multiple="multiple" data-hide-search="true" data-kt-select2="true" data-placeholder="Select Department" name="department[]" data-close-on-select="false" required >
                                @if(!empty($all_department))
                                    @foreach ($all_department as $department)
                                        <option value="{{ $department['department_id'] }}" @if (!empty(Request::get('dept')) && Request::get('dept') == $department['department_id']) selected @endif>{{ $department['department_name'] }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3 mb-8 mt-3 fv-row">
                            
                            <label class="fs-6 fw-semibold mb-2">All Departments</label>
                                <label class="form-check form-check-custom me-10 fs-6 fw-semibold mb-2">
                                <input class="form-check-input h-20px w-20px" type="checkbox" id="department_all_delete" name="select_all" value="Select All" /> 
                        
                            </label>
                        </div> 
                    </div>
                </div>

                <div class="modal-footer">
                    <input type = "hidden" name="month" value="{{$month ?? ''}}" >
                    <input type = "hidden" name="year"  value="{{$year ?? ''}}" >
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="modalDeleteDepartmentFormButton" class="btn btn-primary">
                        <span class="indicator-label">Yes, Delete</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>