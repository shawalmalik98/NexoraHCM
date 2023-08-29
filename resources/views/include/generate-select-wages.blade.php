<form id="addMainForm" class="form">
    @csrf
    <div class="modal fade" id="addMain" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-650px">
            <div class="modal-content rounded">
                <div class="modal-header">
                <h4 class="modal-title pl-4">Generate Wage(s)</h4>
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                </button>
                </div>
                <div class="modal-body scroll-y pt-0 pb-15 pt-15" >
                    <div class="row mt-5" >
                        {{--begin::Radio group--}}
                        <div data-kt-buttons="true">
                            {{--begin::Radio button--}}
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5">
                                {{--end::Description--}}
                                <div class="d-flex align-items-center me-2">
                                    {{--begin::Radio--}}
                                    <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                        <input class="form-check-input" type="radio" name="payroll_type" value="bulk" required />
                                    </div>
                                    {{--end::Radio--}}

                                    {{--begin::Info--}}
                                    <div class="flex-grow-1">
                                        <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                            Generate Bulk Payroll
                                        </h2>
                                        <div class="fw-semibold opacity-50">
                                            Create department wise payroll or all departments
                                        </div>
                                    </div>
                                    {{--end::Info--}}
                                </div>
                                {{--end::Description--}}
                            </label>
                            {{--end::Radio button--}}

                            {{--begin::Radio button--}}
                            <label class="btn btn-outline btn-outline-dashed btn-active-light-primary d-flex flex-stack text-start p-6 mb-5">
                                {{--end::Description--}}
                                <div class="d-flex align-items-center me-2">
                                    {{--begin::Radio--}}
                                    <div class="form-check form-check-custom form-check-solid form-check-primary me-6">
                                        <input class="form-check-input" type="radio" name="payroll_type" value="individual" required />
                                    </div>
                                    {{--end::Radio--}}

                                    {{--begin::Info--}}
                                    <div class="flex-grow-1">
                                        <h2 class="d-flex align-items-center fs-3 fw-bold flex-wrap">
                                            Generate Individual Employee Payroll
                                        </h2>
                                        <div class="fw-semibold opacity-50">
                                            Create specific employee payroll
                                        </div>
                                    </div>
                                    {{--end::Info--}}
                                </div>
                                {{--end::Description--}}
                            </label>
                            {{--end::Radio button--}}
                        </div>
                        {{--end::Radio group--}}
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit_btn">
                        <span class="indicator-label">Submit</span>
                        <span class="indicator-progress">Please wait
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
        {{--end::Modal - New Target--}}
</form>