@extends('layout.main')



@push('css-link')
table.dataTable
{
 overflow-x:hidden !important;
 overflow-y:auto !important;
}

@endpush



@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
    <form id="overtime-insert-form">	
        @csrf 				 
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
            <div class="app-container col-12 d-flex flex-stack">
                
                <div class="page-title  me-3">
                    
                    <h1 class="page-heading text-dark fw-bold fs-3  my-0">
                        @if (empty($month) && empty($year))
                            Overtime Detail - {{$employee_name[0]}} ({{ date('F') . '-' . date('Y') }})
                        @else
                            Overtime Detail - {{$employee_name[0]}} ({{date("F", mktime(0, 0, 0, $month, 10)) }}-{{ $year }})
                        @endif
                        
                    </h1>  
                   
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    @if(!empty($employee_id))
                        <div class="m-0">
                            <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#download_modal_pdf">											
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                        <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                    </svg>
                                </span>
                                Download Overtime Report
                            </a>  
                        </div>  
                    @endif
                    <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary"  >Go Back</a>     
                    {{-- @if(!empty($employee_id))
                        <a href="{{ url('overtime-management/overtime-detail/'.$employee_id.'?is_active='.Request::get('is_active').'&y='.Request::get('y').'&m='.Request::get('m')).'&download=TRUE'}}" target="_blank"  class="btn btn-sm fw-bold btn-primary" >Download Overtime Detail Report</a>
                    @endif
                    <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary"  >Go Back</a> --}}
                    
                </div>
            </div>
        </div>

        
        <div class="app-content flex-column-fluid">
            
            <div class="app-container">
                
                <div class="card mb-5 mb-xl-8">
                    <div class="card-header border-0 pt-5">

                        <div class="card-title">
                            <div class="d-flex align-items-center position-relative my-1 me-5">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                                <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilter">
                            </div>
                            <div class="d-flex align-items-center  on-check d-none">
                        
                                {{-- @if(CustomHelper::dept_has_role($login_user[0]['employee_department'], 'hr', $login_user[0]['employee_id'] ?? null) || CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null)) --}}
    
                                    <a href="javascript:void(0)" class="overtime_reibursment text-white  btn btn-sm fw-bold btn-success mx-2"  data-bs-toggle="tooltip" data-bs-placement="top" title="Approve Reimburesement" >Approve</a>
                                
                                    <a href="javascript:void(0)"  class="overtime_reibursment text-white  btn btn-sm fw-bold btn-danger mx-2" data-value="Multiple-Remove" data-bs-toggle="tooltip" data-bs-placement="top"  title="Remove Reimburesement">Remove</a>
                                    
                                    <input type="hidden" name="title" id="title" />
                                   
                                {{-- @endif --}}
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-body py-3">				 
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive">
                            
                                <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="tableEmployeeOverTime">
                                                                                                            
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-6"> 
                                                <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                    <input class="form-check-input"  type="checkbox" id="parent_checkbox" />
                                                </div>     
                                            </th>
                                            {{-- <th>Employee</th> --}}
                                            <th>Shift Duration</th>
                                            <th>Check In Time</th>
                                            <th>Check Out Time</th>
                                            <th>Extra Time Worked</th>
                                            <th>Overtime Date</th>
                                            <th>Overtime Amount</th>
                                            <th>Amount Payable</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        
                                        </tr>
                                    </thead>
                                    <tbody>
                                    
                                        @if(!empty($all_attendaces))
                                            @php $overtime = 0; 
                                                $overtime_salary = 0;
                                                $count = 0;
                                                $amount_payable = 0;
                                               $currency = CustomHelper::hcm_system_currency();
                                            @endphp
                                            @foreach ($all_attendaces as $all_attendance)
                                                @if(!empty($all_attendance['attendance']))
                                                    @foreach ($all_attendance['attendance'] as $key=>$atty)
                                                        
                                                        @php 
                                                            
                                                            $employee_shift_times =  CustomHelper::calculate_shift_timings_shift_management($atty['att_shift_start_time'],$atty['att_shift_end_time'],$atty['att_check_in_status'],$atty['att_check_out_status']);
                                                            $employee_working_hours =  CustomHelper::calculate_working_hour($atty['att_check_out_status'],$atty['att_check_in_time'],$atty['att_check_out_time'],$atty['att_created_at'],$atty['att_updated_at']);
                                                        
                                                        @endphp
                                                        @if($employee_working_hours > $employee_shift_times)
                                                          
                                                            @php
                                                               
                                                                $ovt_date = $atty['att_for_year'].'-'.$atty['att_for_month'].'-'.$atty['att_for_date'];
                                                                $extra_hours =  CustomHelper::extra_hours_working($employee_shift_times,$employee_working_hours);
                                                                $components = explode(":",$extra_hours);
                                                                $seconds = $components[0]*60*60 + $components[1]*60;
                                                                $overtime +=$seconds;
                                                                $salary_overtime = round(CustomHelper::calculate_total_overtime_salary($all_attendance['employee_id'],$ovt_date,$employee_shift_times, $extra_hours));
                                                                $overtime_salary+=$salary_overtime;
                                                                
                                                             
                                                            @endphp
                                                           
                                                            <tr>
                                                                <td class="ps-6">
                                                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                                        <input class="form-check-input checked"  type="checkbox" name="checkbox_{{$count}}"  />
                                                                    </div>     
                                                                </td>
                                                                {{-- <td>{!! CustomHelper::getEmpProfileDiv($all_attendance['employee_id']) !!}</td> --}}
                                                                <td class="ps-6">  {{  $employee_shift_times }}</td>
                                                                <td class="ps-6">  {{$atty['att_check_in_time']}}</td>
                                                                <td class="ps-6">  {{$atty['att_check_out_time']}}</td>
                                                                <td class="text-center">  {{ $extra_hours}}</td>
                                                                <td class="ps-6">  {{date('d-F-Y', strtotime($atty['att_for_year'].'-'.$atty['att_for_month'].'-'.$atty['att_for_date'] ))}}</td>
                                                                <td class="ps-6">  {{ $salary_overtime.' '.$currency[0]['system_currency']}} </td>
                                                                <td class="ps-6"> 
                                                                    @if(!empty($overtime_record[$ovt_date]['amount_payable']))
                                                                        @if(in_array($ovt_date,$checking_exits_record)) 
                                                                       
                                                                            @php  $amount_payable += $overtime_record[$ovt_date]['amount_payable'];@endphp
                                                                                {{$overtime_record[$ovt_date]['amount_payable'].' '.$currency[0]['system_currency']}}

                                                                        @endif
                                                                    @else
                                                                        @php  $amount_payable += $salary_overtime;
                                                                        @endphp
                                                                         {{ $salary_overtime.' '.$currency[0]['system_currency']}} 
                                                                    
                                                                    @endif
                                                                </td>
                                                                <td class="text-center"> 
                                                                    @if(in_array($ovt_date,$checking_exits_record)) 
                                                                    
                                                                        @if($overtime_record[$ovt_date]['over_time_reibursement_status'] == 1)
                                                                            <span class="badge badge-light-success text-center font-weight-bold" >Paid</span>
                                                                      
                                                                        @else
                                                                            <span class="badge badge-light-danger text-center font-weight-bold" >Not Paid</span>
                                                                        @endif
                                                                    @else
                                                                        <span class="badge badge-light-danger text-center font-weight-bold" >Not Paid</span>
                                                                  
                                                                    @endif
                                                               </td>
                                                                <td >  
                                                                    <input type="hidden" name="employee_id[]" value="{{$all_attendance['employee_id']}}" required/>
                                                                    <input type="hidden" name="extra_hours[]" value="{{ $extra_hours}}" required/>
                                                                    <input type="hidden" name="over_time_date[]" value="{{ $ovt_date }}" required/>
                                                                    <input type="hidden" name="salary_amount[]" value="{{ $salary_overtime}}" required />
                                                                   
                                                                    @if(in_array($ovt_date,$checking_exits_record) && $overtime_record[$ovt_date]['over_time_reibursement_status'] == 1)
                                                                        
                                                                        <i class="fa-regular fa-circle-check   text-success mx-2"  data-bs-toggle="tooltip" data-bs-placement="top" title="Reimburesement Overtime"  disabled style="font-size: 20px;cursor: not-allowed;"></i>
                                                                        
                                                                        @if(!empty($overtime_record[$ovt_date]['ovt_payroll_id']))
                                                                            <i class="fa-regular fa-circle-xmark  text-danger mx-2" disabled style="font-size: 20px;cursor:not-allowed" ></i>
                                                                        @else
                                                                            <a href="javascript:void(0);" onclick="remove_overtime_salary('{{$all_attendance['employee_id']}}','{{$ovt_date}}')"   data-bs-toggle="tooltip" data-bs-placement="top" title="Remove Reimburesement">
                                                                                <i class="fa-regular fa-circle-xmark  text-danger mx-2"  style="font-size: 20px;cursor: pointer;"></i>
                                                                            </a>
                                                                        @endif
                                                                       
                                                                       

                                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" disabled style="font-size: 20px;cursor:not-allowed" >
                                                                            <span class="svg-icon svg-icon-3">
                                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                                </svg>
                                                                            </span>
                                                                        </a>
                                                                       
                                                                    @else 
                                                                      
                                                                        <i class="fa-regular fa-circle-check  text-success mx-2 "  data-bs-toggle="tooltip" data-bs-placement="top" title="Reimburesement Overtime" style="font-size: 20px;cursor: pointer;" onclick="overtime({{$all_attendance['employee_id']}},'{{ $extra_hours}}','{{  $ovt_date}}','{{ $salary_overtime}}')"></i>
                                                                        
                                                                        <i class="fa-regular fa-circle-xmark  text-danger mx-2" disabled style="font-size: 20px;cursor:not-allowed" ></i>
                                                                            
                                                                        <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit_btn" data-bs-toggle="tooltip" title="Edit Overtime Amount" data-salary="{{ $salary_overtime}}" data-id="{{$all_attendance['employee_id']}}" data-date="{{ $ovt_date}}" data-extra-hours="{{$extra_hours}}" data-amount-payable="{{$overtime_record[$ovt_date]['amount_payable']?? ''}}" >
                                                                            <span class="svg-icon svg-icon-3">
                                                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                    <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                                    <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                                </svg>
                                                                            </span>
                                                                        </a>
                                                                      
                                                                    @endif                                                                 
                                                                </td> 
                                                            </tr>
                                                            @php $count++; @endphp
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                            <tr class="bg-light">
                                                {{-- <td class="ps-6"></td> --}}
                                                <td class="ps-6">
                                                <td class="ps-6">
                                                <td class="ps-6">
                                                <td class="ps-6">
                                                <td class="text-center">   
                                                    @php $overtimes = CustomHelper::calculate_total_overtime($overtime); @endphp
                                                        {{$overtimes['overtime'] }}
                                                </td>
                                                <td class="text-center"></td>
                                                <td class="ps-6">   
                                                   {{$overtime_salary}} <b> {{$currency[0]['system_currency']}}</b>
                                                </td>
                                                <td class="ps-6"> {{$amount_payable}} <b>{{$currency[0]['system_currency']}}</b>
                                                    </td>  
                                                <td class="ps-6"> </td>
                                                <td class="ps-6">  </td>
                                                {{-- <td class="ps-6"> </td> --}}
                                
                                            </tr>
                                        
                                        @endif
                                    
                                    </tbody>
                                
                                </table>
                            </div>
                        </div>  
                    </div>
                </div>
            </div> 
        </div>
    </form>
    <div class="modal fade" tabindex="-1" id="modal-danger">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Confirmation Alert</h3>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1 bg-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <form id="removeReimburesement" class="form" >
                    @csrf
                    <div class="modal-body">
                    <input type="hidden" class="form-control" name="employee_id" id="emp_id" required=""/>
                    <input type="hidden" class="form-control" name="ovt_date" id="date" required=""/>
                        <p>Are you sure to delete this Reimburesement ?</p>
                    </div>
        
                    <div class="modal-footer">
                    <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                            <button type="submit" id="delete-btn" class="btn btn-primary">
                                <span class="indicator-label">Remove</span>
                                <span class="indicator-progress">Please wait 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                    </div>
                </form>    
            </div>
        </div>
    </div>

    
    <form id="edit_overtime_salary" class="form">
        @csrf
        <div class="modal fade" id="edit-overtime-salary" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content rounded">
                    
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Edit Overtime Amount</h4>
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
                        
                        <div class="col-md-12 mb-8 mt-3 fv-row">
                            <label class="required fs-6 fw-semibold mb-2">Amount Payable </label>
                            <div class="position-relative d-flex align-items-center"> 
                                <input type="number" class="form-control " placeholder="Enter a Amount Payable" name="amount_payable"  id="amount_payable" required="" min="0" max="99999999.99"/>
                                <input type="hidden" class="form-control" name="employee_id" id="employee_id" required=""/>
                                <input type="hidden" class="form-control" name="ovt_date" id="ovt_date" required=""/>
                                <input type="hidden" name="extra_hours" id="extra_hours" required />
                                <input type="hidden" name="salary_amount" id="salary_amount" required/>
                               
                               
                            </div>
                        </div>
                            
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="update-btn" class="btn btn-primary">
                            <span class="indicator-label">Submit</span>
                            <span class="indicator-progress">Please wait 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <form class="form" id="downloadbutton" action="{{ url('overtime-management/overtime-detail/overtime-detail-download') }}" method="POST">
        @csrf
        <div class="modal fade" id="download_modal_pdf"uploaddex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable mw-650px">
                <div class="modal-content rounded">
                
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Download Overtime Detail Report</h4>
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
                    
                        <div class="row g-8">
                            <input type="hidden" name="id" value="{{$employee_id}}">

                            <input name="is_active" type="hidden" value="{{Request::get('is_active')}}">

                            <input name="m" type="hidden" value="{{$month}}">

                            <input name="y" type="hidden" value="{{$year}}">

                            <div class="col-md-12 mt-3 fv-row">
                                <p onselectstart="javascript:return false;" >This action required a security check. If you want to download employee(s) overtime detail report,
                                Please type <b>TODP-ID</b> (case sensitive) to confirm.</p>
                                <input type="text" class="form-control " name="confirmation_code" placeholder="Enter Confirmation Code" required="required" autocomplete="off" onpaste="return false">
                            </div>

                            <div class="col-md-12 mt-3 fv-row text-center">
                                <span class="d-none text-danger" id="download_message">
                                    Please wait, PDF file generation is in process. It may take a while.
                                </span>
                            </div>
                        </div> 
                        
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="download_btn" class="btn btn-primary">
                            <span class="indicator-label">Download</span>
                            <span class="indicator-progress">Please wait... 
                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@endsection

@push('js-link')

<script src="{{url('assets/js/overtime-management/overtime-detail.js')}}"></script>

@endpush