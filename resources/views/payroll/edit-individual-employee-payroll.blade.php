@extends('layout.main')






@section('main-section')
<div class="d-flex flex-column flex-column-fluid"  >
    {{--begin::Toolbar--}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        {{--begin::Toolbar container--}}
        <div class="app-container col-12 d-flex flex-stack">
            {{--begin::Page title--}}
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                {{--begin::Title--}}
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Edit Payroll</h1>
                
            </div>
            <div class="gap-2 gap-lg-3">
                <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
            </div>
        </div>
    </div>
    {{--end::Toolbar--}}
    {{--begin::Content--}}
    <div class="app-content flex-column-fluid">
        {{--begin::Content container--}}
        <div class="app-container">
            {{--begin::Tables Widget 11--}}
            <div class="card mb-5 mb-xl-8">
                {{--begin::Body--}}
                <div class="card-body py-3 mb-12">
                    {{--begin::Table container--}}
                    <form  id="edit_payroll_form" class="form" autocomplete="off">
                        @csrf 

                        <input type="hidden" name="pc_id" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_id'] : 0 }}" />
                        
                        <div class="row g-9 mb-12 mt-3">
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Pay Period</label>
                                <input type="month" class="form-control " name="pay_period"
                                placeholder="" required="" 
                                @if(!empty($fetch_payroll_details))
                                    value="{{$fetch_payroll_details[0]['pc_year']."-".sprintf("%02d", $fetch_payroll_details[0]['pc_month'])}}"
                                    readonly=""
                                @endif
                                />
                            </div>
                            
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Employee</label>
                                <input type="hidden" class="form-control " name="emp_name" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['employee_id'] : 0 }}"  readonly />
                                <input type="text" class="form-control " name="" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['employee_code']. ' - ' .$fetch_payroll_details[0]['employee_name'] : 0 }}"  readonly/>
                            </div>

                        </div>

                        <div class="row g-9 mb-12 mt-3">
                            <div class="col-md-6 fv-row">
                                <input type="text" class="form-control" value="Total Present" readonly=""/>
                            </div>
                            <div class="col-md-6 fv-row">
                                <input type="number" name="pc_present" class="form-control" placeholder="" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_present'] : 0 }}"  readonly="" />
                            </div>

                            <div class="col-md-6 fv-row">
                                <input type="text" class="form-control" value="Total Off Day" readonly=""/>
                            </div>
                            <div class="col-md-6 fv-row">
                                <input type="number" name="pc_off_day" class="form-control" placeholder="" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_off_day'] : 0 }}"  readonly="" />
                            </div>


                            <div class="col-md-6 fv-row">
                                <input type="text"  class="form-control" value="Total Leave" readonly=""/>
                            </div>
                            <div class="col-md-6 fv-row">
                                <input type="number" name="pc_leave" class="form-control" placeholder="" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_leave'] : 0 }}"  readonly="" />
                            </div>

                            <div class="col-md-6 fv-row">
                                <input type="text" class="form-control" value="Total Holiday" readonly=""/>
                            </div>
                            <div class="col-md-6 fv-row">
                                <input type="number" name="pc_holiday" class="form-control" placeholder="" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_holiday'] : 0 }}"  readonly="" />
                            </div>



                            <div class="col-md-6 fv-row">
                                <input type="text" class="form-control" value="Total Absent" readonly=""/>
                            </div>
                            <div class="col-md-6 fv-row">
                                <input type="number" name="pc_absent" class="form-control" placeholder="" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_absent'] : 0 }}"  readonly="" />
                            </div>


                            <div class="col-md-6 fv-row">
                                <input type="text" class="form-control" value="Total Late Coming + Early Out" readonly=""  />
                            </div>
                            <div class="col-md-6 fv-row">
                                <input type="number" name="pc_late_early" class="form-control" placeholder="" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_late_early'] : 0 }}"  readonly="" />
                            </div>


                            <div class="col-md-6 fv-row">
                                <input type="text" class="form-control" value="Total Half Day" readonly=""/>
                            </div>
                            <div class="col-md-6 fv-row">
                                <input type="number" name="pc_half_day" class="form-control" placeholder="" value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_half_day'] : 0 }}"  readonly="" />
                            </div>

                        </div>

                        <div class="row" > 
                            <div class="col-md-6">
                                <div class="row mb-8">

                                    <div class="card-title d-flex align-items-center justify-content-between flex-wrap mt-2">
                                        <h2>Earning</h2>
                                        <span>
                                            <button class="btn btn-sm btn-flex btn-light btn-active-color-primary fw-bold" type="button" onclick="addEarningRow()"><i class="fa fa-plus"></i></button>
                                            {{-- <button class="btn btn-sm btn-flex btn-light btn-active-color-danger fw-bold" type="button" onclick="deleteEarningRow()"><i class="fa fa-minus"></i></button> --}}
                                        </span>
                                    </div>
                                    

                                    <div class="col-md-6">
                                        <input type="text" class="form-control " value="Gross Salary" readonly />
                                    </div>

                                    <div class="col-md-6">
                                        <input type="number"  class="form-control "name="pc_employee_gross_salary"
                                        value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_employee_gross_salary'] : 0 }}" readonly />
                                    </div>

                                    <div id="earning_div" >
                                    @php $i = 1 ; @endphp
                                
                                    @if(!empty($fetch_extra_earning))
                                    @foreach ($fetch_extra_earning as $earning) 
                                        @php $i++; @endphp

                                        @if(in_array(strtolower($earning['pc_earning_title']),$not_editable_title))

                                        <div class="row mt-5" id="deletefileEarning{{$i}}">
                                            <div class="col-md-6 fv-row">
                                                <input type="text" class="form-control" name="extra_earning_title[]" placeholder="Enter Earning Title" value="{{$earning['pc_earning_title']}}" required="" readonly >
                                            </div>

                                            <div class="col-md-6 fv-row">
                                                <input type="number" step="0.01" max="999999" class="form-control extra_earning_amount" name="extra_earning_amount[]" value="{{$earning['pc_earning_amount']}}" placeholder="Enter Earning Amount" required=""  onInput="calculate_net_amount()" readonly >
                                            </div>

                                        </div>
                                        @else
                                        <div class="row mt-5" id="deletefileEarning{{$i}}">
                                            <div class="col-md-6 fv-row">
                                                <input type="text" class="form-control" name="extra_earning_title[]" placeholder="Enter Earning Title" value="{{$earning['pc_earning_title']}}" required="">
                                            </div>

                                            <div class="col-md-5 fv-row">
                                                <input type="number" step="0.01" max="999999" class="form-control extra_earning_amount" name="extra_earning_amount[]" value="{{$earning['pc_earning_amount']}}" placeholder="Enter Earning Amount" required=""  onInput="calculate_net_amount()" >
                                            </div>

                                            <div class="col-md-1  mt-1" style=" padding-left: 0%;" >
                                                <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" onClick="removeDiv(this)" >
                                                    <span class="svg-icon svg-icon-3">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                            <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>

                                        </div>
                                        @endif
                                        <input name="earning_lenght" type="hidden" value="{{$i}}" >
                                    @endforeach
                                    @endif

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div class="row mb-8">

                                    <div class="card-title d-flex align-items-center justify-content-between flex-wrap mt-2">
                                        <h2>Deduction</h2>
                                        <span>
                                            <button class="btn btn-sm btn-flex btn-light btn-active-color-primary fw-bold" type="button" onclick="addDeductionRow()"><i class="fa fa-plus"></i></button>
                                            {{-- <button class="btn btn-sm btn-flex btn-light btn-active-color-danger fw-bold" type="button" onclick="deleteDeductionRow()"><i class="fa fa-minus"></i></button> --}}
                                        </span>
                                    </div>

                                    

                                    <div class="col-md-6">
                                        <input type="text" class="form-control " 
                                        value="Absent" required="required" readonly/>
                                    </div>

                                    <div class="col-md-6">
                                        <input type="text" class="form-control "name="pc_absent_deduction"
                                        value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_absent_deduction'] : 0 }}" required="required" readonly/>
                                    </div>


                                    <div class="col-md-6 mt-5">
                                        <input type="text" class="form-control " 
                                        value="Half Day" readonly/>
                                    </div>

                                    <div class="col-md-6 mt-5">
                                        <input type="text" class="form-control "name="pc_half_day_deduction"
                                        value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_half_day_deduction'] : 0 }}" readonly/>
                                    </div>


                                    <div class="col-md-6 mt-5">
                                        <input type="text" class="form-control " 
                                        value="Late Comming / Early Out" readonly/>
                                    </div>

                                    <div class="col-md-6 mt-5">
                                        <input type="text" class="form-control "name="pc_late_early_deduction"
                                        value="{{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_late_early_deduction'] : 0 }}" readonly/>
                                    </div>


                                    <div id="deduction_div" >
                                        @php $i = 1; @endphp

                                        @if(!empty($fetch_extra_deduction))
                                        @foreach($fetch_extra_deduction as $deduction)
                                            @php $i++; @endphp

                                            @if(in_array(strtolower($deduction['pc_deduction_title']),$not_editable_title))

                                            
                                                <div class="row mt-5" id="deletefileDeduction{{$i}}">
                                                        <div class="col-md-6 fv-row">
                                                            <input type="text" class="form-control" name="extra_deduction_title[]" placeholder="Enter Earning Title" value="{{$deduction['pc_deduction_title']}}" required="" readonly>
                                                        </div>

                                                        <div class="col-md-6 fv-row">
                                                            <input type="number" step="0.01" max="999999" class="form-control extra_deduction_amount" name="extra_deduction_amount[]" value="{{$deduction['pc_deduction_amount']}}" placeholder="Enter Earning Amount" required="" onInput="calculate_net_amount()" readonly  >
                                                        </div>
                                                    </div>

                                            @else

                                                <div class="row mt-5" id="deletefileDeduction{{$i}}">
                                                    <div class="col-md-6 fv-row">
                                                        <input type="text" class="form-control" name="extra_deduction_title[]" placeholder="Enter Earning Title" value="{{$deduction['pc_deduction_title']}}" required="">
                                                    </div>

                                                    <div class="col-md-5 fv-row">
                                                        <input type="number" step="0.01" max="999999" class="form-control extra_deduction_amount" name="extra_deduction_amount[]" value="{{$deduction['pc_deduction_amount']}}" placeholder="Enter Earning Amount" required="" onInput="calculate_net_amount()"  >
                                                    </div>

                                                    <div class="col-md-1  mt-1" style=" padding-left: 0%;" >
                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" onClick="removeDiv(this)" >
                                                            <span class="svg-icon svg-icon-3">
                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                    <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                    <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                                                                </svg>
                                                            </span>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                

                                        @endforeach
                                        @endif
                                    </div>
                                    <input name="deduction_lenght" type="hidden" value="{{$i}}" >

                                </div>
                            </div>
                        </div>

                        <div class="row g-9 mb-12 mt-3">

                            
                            <div class="col-md-6 fv-row">
                                <input type="text" class="form-control" value="Net Salary" readonly=""/>
                            </div>
                            <div class="col-md-6 fv-row">
                                <input type="number" class="form-control" placeholder="" name="pc_employee_net_salary" value=""  readonly="" />
                            </div>

                        </div>

                        
                        <div class="text-center">
                            <button type="submit" id="submit_btn" class="btn btn-primary">
                                <span class="indicator-label">Update Payroll</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection



@push('js-link')
<script>

    function addEarningRow() {
        add_file_counter = $("input[name=earning_lenght]").val();

        add_file_counter++;
        $("input[name=earning_lenght]").val(add_file_counter);

        $("#earning_div").append(`
            <div class="row mt-5" id="deletefileEarning${add_file_counter}">
                <div class="col-md-6 fv-row">
                    <input type="text" class="form-control" name="extra_earning_title[]" placeholder="Enter Earning Title" required="">
                </div>

                <div class="col-md-5 fv-row">
                    <input type="number" step="0.01" max="999999" class="form-control extra_earning_amount" name="extra_earning_amount[]" placeholder="Enter Earning Amount" required=""  onInput="calculate_net_amount()" >
                </div>

                <div class="col-md-1  mt-1" style=" padding-left: 0%;" >
                    <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" onClick="removeDiv(this)" >
                        <span class="svg-icon svg-icon-3">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>

        `);
    }


    function deleteEarningRow() {
        add_file_counter = $("input[name=earning_lenght]").val()
            if (add_file_counter > 1) {
            swal.fire({
            title: 'Confirmation Alert !',
            text: 'Are you sure to delete this last earning record ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText:'Yes',
            cancelButtonText:'Cancel',
            }).then(function (value) {
                if(value.isConfirmed == true){
                    $("#deletefileEarning" + add_file_counter).remove();
                    add_file_counter--;
                    $("input[name=earning_lenght]").val(add_file_counter);
                    calculate_net_amount();
                }

            });
        }
    }


    function addDeductionRow() {
        add_file_counter = $("input[name=deduction_lenght]").val();

        add_file_counter++;
        $("input[name=deduction_lenght]").val(add_file_counter);

        $("#deduction_div").append(`
            <div class="row mt-5" id="deletefileDeduction${add_file_counter}">
                <div class="col-md-6 fv-row">
                    <input type="text" class="form-control " name="extra_deduction_title[]" placeholder="Enter Deduction Title" required="">
                </div>

                <div class="col-md-5 fv-row">
                    <input type="number" step="0.01" max="999999" class="form-control extra_deduction_amount" name="extra_deduction_amount[]" placeholder="Enter Deduction Amount" required="" onInput="calculate_net_amount()"  >
                </div>

                <div class="col-md-1  mt-1" style=" padding-left: 0%;" >
                    <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" onClick="removeDiv(this)" >
                        <span class="svg-icon svg-icon-3">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>

        `);
    }


    function deleteDeductionRow() {
        add_file_counter = $("input[name=deduction_lenght]").val()
            if (add_file_counter > 1) {
            swal.fire({
            title: 'Confirmation Alert !',
            text: 'Are you sure to delete this last deduction record ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText:'Yes',
            cancelButtonText:'Cancel',
            }).then(function (value) {
                if(value.isConfirmed == true){
                    $("#deletefileDeduction" + add_file_counter).remove();
                    add_file_counter--;
                    $("input[name=deduction_lenght]").val(add_file_counter);
                    calculate_net_amount();
                }

            });
        }
    }

    function removeDiv(elem){

        swal.fire({
        title: 'Confirmation Alert !',
        text: 'Are you sure to delete record ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText:'Yes',
        cancelButtonText:'Cancel',
        }).then(function (value) {
            if(value.isConfirmed == true){
                $(elem).parent('div').parent('div').remove();
                calculate_net_amount();
            }
        });
            
        }

    $('#edit_payroll_form').submit(function(e){

        e.preventDefault()
        send_server_request( 'POST' ,'payroll/edit-individaul-employee-payroll' , this ,'submit_btn', "No" , 'payroll?m={{$fetch_payroll_details[0]['pc_month']}}&y={{$fetch_payroll_details[0]['pc_year']}}' ,3000)
    
    })


    let calculate_net_amount = () => {

        var net_amount_other = 0;

        var net_amount = {{ (!empty($fetch_payroll_details)) ? $fetch_payroll_details[0]['pc_employee_net_salary'] : 0 }};
        
        $('.extra_earning_amount').each(function(){
            net_amount_other += parseFloat(this.value || 0);
        });

        $('.extra_deduction_amount').each(function(){
            net_amount_other -= parseFloat(this.value || 0);
        });
        var employee_net_salary = net_amount + net_amount_other;
        employee_net_salary = Math.round(employee_net_salary * 100) / 100;
        $("input[name='pc_employee_net_salary']").val(employee_net_salary);


    }
    calculate_net_amount()

</script>


    
@endpush