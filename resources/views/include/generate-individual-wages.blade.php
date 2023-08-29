<form id="addDailyWagesForm" class="form">
    @csrf
    <div class="modal fade" id="addDailyWages" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable mw-650px">
            <div class="modal-content rounded">
                <div class="modal-header">
                <h4 class="modal-title pl-4">Generate Individual Daily Wages Payroll</h4>
                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                    <span class="svg-icon svg-icon-1">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                        </svg>
                    </span>
                    
                </button>
                </div>
                <div class="m-0">
                </div>
                <div class="modal-body scroll-y pt-0 pb-15" >

                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Daily Wages Day(s)</label>
                            <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Attendance Days" name="days_assign" id="days_assign" required>
                                <option value="single">Single Day </option>
                                <option value="multiple">Multiple Days </option>
                                <option value="till">Till {{date('d-M-Y',strtotime('-2 days'))}}</option>
                            </select>
                        </div>
                    </div>

                    <div class="from_to_date_input row mt-5 d-none" >
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2 required">From Date </label>
                            <input type="date" class="date-control form-control" name="from_date"
                                placeholder=""  max="{{date('Y-m-d',strtotime('-2 days'))}}" >
                        </div>
                        <div class="col-md-6 fv-row">
                            <label class="fs-6 fw-semibold mb-2 required">To Date</label>
                            <input type="date" class="date-control form-control" name="to_date"
                                placeholder=""  max="{{date('Y-m-d',strtotime('-2 days'))}}" >
                        </div>
                    </div>

                    <div class="only_date_input row mt-5" >
                        <div class="col-md-12 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Select Wage Date</label>
                            <div class="position-relative d-flex align-items-center">
                                <input type="date" class="form-control" name="wage_date" id="wage_date" max="{{ date('Y-m-d') }}" required/>
                            </div>
                        </div>
                    </div>

                    <div class="till_note row mt-5 d-none" >
                        <div class="col-md-12 fv-row">
                            <p><b>Note* </b> Dues remaining from DOJ till {{date('d-M-Y',strtotime('-2 days'))}}</p>
                        </div>
                    </div>

                    <div class="row mt-5" >
                        <div class="col-md-12 fv-row" id="selectDepartmentCalculate" >
                            <label class="fs-6 fw-semibold mb-2 required">Employee</label>
                            <select class="form-select" id="add_daily_wages_employee" data-kt-select2="true" data-placeholder="Select Employee" name="employee_id" data-close-on-select="true" required >
                                @if(!empty($daily_wages_employee ))
                                    @foreach($daily_wages_employee as $employee )
                                        <option value="{{$employee['employee_id']}}" >{{$employee['employee_code']}}-{{$employee['employee_name']}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
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