@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">

	<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
		<div class="app-container col-12 d-flex flex-stack">
			<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
				<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Loan Detail</h1>
			</div>
		</div>
	</div>
	{{--end::Toolbar--}}
	{{--begin::Content--}}
	<div class="app-content flex-column-fluid">

		<div class="app-container">

			<div class="row">

				<div class="col-md-4 card mb-5">
					<div class="card-body pt-6 px-0">
						<ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9">

							{{-- <li class="nav-item col-6 mx-0 p-0">
								<a class="nav-link active d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#loan_amount">
									<span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Loan Amount</span>
									<span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
								</a>
							</li> --}}

							<li class="nav-item col-6 mx-0 p-0">
								<a class="nav-link active d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#kt_list_widget_10_tab_1">
									<span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Loan Details</span>
									<span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
								</a>
							</li>

							<li class="nav-item col-6 mx-0 px-0">
								<a class="nav-link d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#kt_list_widget_10_tab_2">
									<span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Installment Details</span>
									<span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
								</a>
							</li>

							<span class="position-absolute z-index-1 bottom-0 w-100 h-4px bg-light rounded"></span>
						</ul>

						<div class="tab-content">
							<div class="tab-pane fade " id="loan_amount">
									
								<div class="d-flex flex-column text-center mb-9 px-9">
									<div class="symbol symbol-80px symbol-lg-150px mb-4">
										@if(!empty($image[0]['profile_avatar']) && $image[0]['profile_avatar'] != null)

											@if(file_exists(asset('portal_assets/profile_pictures').'/'.$image[0]['profile_avatar']))
												<img src="{{asset('portal_assets/profile_pictures').'/'.$image[0]['profile_avatar'] }}" class="" alt="">
											@else
												<img alt="Logo" src="{{ asset('/portal_assets/images/hcm-img.png') }}" class=""/>
											@endif
										@else
				
											<img alt="Logo" src="{{ asset('/portal_assets/images/hcm-img.png') }}" class=""/>
										@endif
				
									</div>
									<div class="text-center">
										<a href="../user-profile/overview.html"> <span class="text-gray-800 fw-bold text-hover-primary fs-4"></span></a>
										<span class="text-muted d-block fw-semibold">
											@if (!empty($employees[0]['department_name']))
												{{ $employees[0]['department_name'] }}
											@else
												N/A
											@endif
								
										</span>
										<span class="text-muted d-block fw-semibold">
											@if (!empty($employees[0]['designation_name']))
												{{ $employees[0]['designation_name'] }}
											@else
												N/A
											@endif
											
										</span>
									</div>
								</div>
																		
								<div class="row px-7 mb-4">
									@if(!empty($employee_total_loan))
										<div class="col-md-4 text-center">
											<div class="text-gray-800 fw-bold fs-3">
												<div class="m-0" data-kt-countup="true" data-kt-countup-value="{{$employee_total_loan}}"></div>
											</div>
											<span class="text-gray-500 fs-8 d-block fw-bold">Total Loan</span>
										</div>
									@endif	
									
									@if(!empty($employee_submitted_loan))
										<div class="col-md-4 text-center">
											<div class="text-gray-800 fw-bold fs-3">
												<div class="m-0" data-kt-countup="true" data-kt-countup-value="{{$employee_submitted_loan}}"></div>
											</div>
											<span class="text-gray-500 fs-8 d-block fw-bold">Loan Paid</span>
										</div>
									@endif

									@if(!empty($employee_remaining_loan))
										<div class="col-md-4 text-center">
											<div class="text-gray-800 fw-bold fs-3">
												<div class="m-0" data-kt-countup="true" data-kt-countup-value="{{$employee_remaining_loan}}"></div>
											</div>
											<span class="text-gray-500 fs-8 d-block fw-bold">Loan Remaining</span>
										</div>
									@endif
								</div>
									
								
							</div>

							<div class="tab-pane fade show active" id="kt_list_widget_10_tab_1">

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Loan ID</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['loan_id']))
												{{ $loan_detail[0]['loan_id'] }}
											@else
												N/A
											@endif
										
										</span>
									</div>
								</div>

								<div class="separator separator-dashed my-2"></div>

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Issuance Date</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['loan_issuance_date']))
												{{ $loan_detail[0]['loan_issuance_date'] }}
											@else
												N/A
											@endif
											
										</span>
									</div>
								</div>

								<div class="separator separator-dashed my-2"></div>

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Issued To</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['employee_code']))
												{!! CustomHelper::getEmpProfileDiv($loan_detail[0]['employee_code']) !!}
											@else
												N/A
											@endif
										
										</span>
									</div>
								</div>

								<div class="separator separator-dashed my-2"></div>

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Total Amount</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['loan_given_amount']))
												{{ $loan_detail[0]['loan_given_amount'] }}
											@else
												N/A
											@endif
											
										</span>
									</div>
								</div>

								<div class="separator separator-dashed my-2"></div>

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Returned Amount</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['loan_submitted_amount']))
												{{ $loan_detail[0]['loan_submitted_amount'] }}
											@else
												N/A
											@endif
										
										</span>
									</div>
								</div>

								<div class="separator separator-dashed my-2"></div>

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Remaining Amount</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['loan_remaining_amount']))
												{{ $loan_detail[0]['loan_remaining_amount'] }}
											@else
												N/A
											@endif
										
										</span>
									</div>
								</div>

								<div class="separator separator-dashed my-2"></div>

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Type</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['loan_type'] ))
												{{ $loan_detail[0]['loan_type'] }}
											@else
												N/A
											@endif
										
										</span>
									</div>
								</div>

								<div class="separator separator-dashed my-2"></div>

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Reason</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['loan_other_reason'] ))
												{{ $loan_detail[0]['loan_other_reason'] }}
											@else
												N/A
											@endif
										
										</span>
									</div>
								</div>

								<div class="separator separator-dashed my-2"></div>

								<div class="d-flex align-items-sm-center">
									<div class="d-flex align-items-center flex-row-fluid flex-wrap">
										<div class="flex-grow-1 me-2">
											<span class="text-gray-800 fw-bold d-block fs-5">Last Updated</span>
										</div>
										<span class="text-gray-500 fw-bold my-2">
											@if (!empty($loan_detail[0]['last_updated'] ))
												{{ $loan_detail[0]['last_updated'] }}
											@else
												N/A
											@endif
											
										</span>
									</div>
								</div>
							</div>

							<div class="tab-pane fade" id="kt_list_widget_10_tab_2">

								@if(!empty($installment_details))
									@php  $currency = CustomHelper::hcm_system_currency(); @endphp
									@foreach($installment_details as $installment_detail)
										<div class="d-flex align-items-sm-center">
											<div class="d-flex align-items-center flex-row-fluid flex-wrap">
												<div class="flex-grow-1 me-2">
													<span class="text-gray-800 fw-bold d-block fs-5">{{ date('Y-m-d', strtotime($installment_detail['loan_installment_date'])) }}</span>
												</div>
												
												@if(empty($installment_detail['loan_installment_payroll_id']))
													<span class="badge badge-light-danger font-weight-bold">Manually</span>
												@else
													<a href="{{ url('individual-payroll-pdf/'.$installment_detail['loan_installment_payroll_id'])}}" target="_blank" > <span class="badge badge-light-success font-weight-bold">From Payroll</span> </a>
												@endif
												<span class="text-gray-500 fw-bold my-2">{{ number_format((float) $installment_detail['loan_installment_amount'], 2) }} {{ $currency[0]['system_currency']}}</span>
													@if(!empty($installment_detail['loan_installment_carried']) && $installment_detail['loan_installment_carried'] > 0)
														<span class="badge badge-light-warning font-weight-bold" title="Salary was not enough; added to the next payroll">{{ number_format((float) $installment_detail['loan_installment_carried'], 2) }} carried to next month</span>
													@endif
												
												@if(!empty($installment_detail['loan_installment_source']) || !empty($installment_detail['loan_installment_source_remarks']))
													<div class="" style="margin-left:10px">
														<a href="javascript:void(0)" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" onclick="installementSource('{{$installment_detail['loan_installment_id']}}')">	
															<i class="fas fa-eye"  data-bs-toggle="tooltip" title="View Installement Source"></i>															
														</a>
													</div>
											    @endif
											</div>
										</div>
										<div class="separator separator-dashed my-2"></div>
									@endforeach
								@else
									<div class="alert alert-primary" role="alert">
										No Installment is paid yet!
									</div>
								@endif

							</div>

						</div>
					</div>
				</div>

				@if(!empty($loan_detail))
					<div class="col-md-8">
						<div class="card mb-5">
							<div class="card-header card-header-stretch">
								{{--begin::Title--}}
								<div class="card-title d-flex align-items-center">

									{{-- <h3 class="fw-bold m-0 text-gray-800">Jan 23, 2022</h3> --}}
								</div>
								{{--end::Title--}}
								{{--begin::Toolbar--}}
								<div class="card-toolbar m-0">
									{{--begin::Tab nav--}}
									<ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bold" role="tablist">
										<li class="nav-item" role="presentation">
											<a id="edit_loan_details_tab" class="nav-link justify-content-center text-active-gray-800 active" data-bs-toggle="tab" role="tab" href="#edit_loan_details">Edit Loan Details</a>
										</li>
										<li class="nav-item" role="presentation">
											<a id="kt_activity_week_tab" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#kt_activity_week">Pay Installment</a>
										</li>
									</ul>
									{{--end::Tab nav--}}
								</div>
								{{--end::Toolbar--}}
							</div>
							
							<div class="card-body">
								<div class="tab-content">
									<div id="edit_loan_details" class="card-body p-0 tab-pane fade show active" role="tabpanel" aria-labelledby="edit_loan_details_tab">
										{{--begin::Timeline--}}
										<div class="timeline">
											<form id="update_loan_detail">
												@php  $currency = CustomHelper::hcm_system_currency(); @endphp 
												<div class="mb-10 mt-n1">
													<div class="row g-9 mb-8">
														<div class="col-md-6 fv-row">
															<label class="d-flex align-items-center fs-6 fw-semibold mb-2">
															<span class="required">Employee</span>
															</label>

															<input type="hidden" name="loan_id" value="{{ $loan_detail[0]['loan_id'] }}">
															
															@if (!empty($all_employees))
																@foreach ($all_employees as $all_employee)
																	@if ($all_employee['employee_id'] == $loan_detail[0]['employee_code'])
																		<input type="text" class="form-control" name="" value="{{ $all_employee['employee_name'] }}"  readonly >
																		<input type="hidden" name="employee_code" value="{{$loan_detail[0]['employee_code']}}" />	
																		<input type="hidden" name="loan_issuance_date" value="{{$loan_detail[0]['loan_issuance_date']}}" />	
																	@endif
																@endforeach
															@endif

														</div>

														<div class="col-md-6 fv-row">
															<label class="d-flex align-items-center fs-6 fw-semibold mb-2">
															<span class="required">Loan Amount</span>
															</label>
															<input type="number" class="form-control " placeholder="Enter Loan Amount" id="loan_edit_amount" name="loan_amount" value="{{ $loan_detail[0]['loan_given_amount'] }}" readonly="readonly"  required="required"/>
														</div>
													</div>

													<div class="row g-9 mb-8">
														
														<div class="col-md-6 fv-row">
															<label class="d-flex align-items-center fs-6 fw-semibold mb-2">
															<span class="required">Loan Type</span>
															</label>
															{{--end::Label--}}
															<select class="form-select" data-control="select2" data-hide-search="true" name="loan_type" id="reason" required="required">
																<option value="">Select Reason</option>
																<option value="Bike Loan" @if ($loan_detail[0]['loan_type'] == 'Bike Loan') selected @endif>Bike Loan</option>
																<option value="Car Loan" @if ($loan_detail[0]['loan_type'] == 'Car Loan') selected @endif>Car Loan</option>
																<option value="Property Loan" @if ($loan_detail[0]['loan_type'] == 'Property Loan') selected @endif>Property Loan</option>
																<option value="Advance Salary" @if ($loan_detail[0]['loan_type'] == 'Advance Salary') selected @endif>Advance Salary</option>
																<option value="Other" @if ($loan_detail[0]['loan_type'] == 'Other') selected @endif>Other</option>
															</select>
														</div>
														
														
														<div class="col-md-6 fv-row reason_div @if($loan_detail[0]['loan_type'] != 'Other') d-none @endif">
															<label class="d-flex align-items-center fs-6 fw-semibold mb-2">
															<span class="required">Reason </span>
															</label>
															{{--end::Label--}}
															<input type="text" class="form-control " placeholder="Enter Loan Reason" name="loan_other_reason" value="{{ $loan_detail[0]['loan_other_reason'] }}" id="reason_input"/>
														</div>

														<div class="col-md-6 fv-row">
															<label class="d-flex align-items-center fs-6 fw-semibold mb-2">
																<span class="required">Loan Deduction</span>
															</label>
															<select class="form-select" data-control="select2" data-hide-search="false" data-placeholder="Select deduction Type" name="deduction_type" id="update_deduct_type" required> 
																<option value="">Loan Deduction</option>
																<option value="1"  @if ($loan_detail[0]['loan_deduction_type'] == 1) selected @endif>Fixed Amount</option>
																<option value="2" @if ($loan_detail[0]['loan_deduction_type'] == 2) selected @endif>Percentage</option>
															</select>
														</div>
							
														<div class="col-md-6 fv-row d-none" id="deduction_edit_div" >
														
															<label class="fs-6 fw-semibold mb-2 required" id="label_amount">Loan Deduction Amount</label> 
															<div class="input-group deduct-amout-edit">
																<input type="number" class="form-control" rows="5" name="deduction_amount" @if ($loan_detail[0]['loan_deduction_type'] == 1)  value="{{ $loan_detail[0]['loan_deduction_amount'] }}" @else value = 0 @endif max="{{ $loan_detail[0]['loan_given_amount'] }}"  placeholder="Loan Deduction Amount">
																<span class="input-group-text"> {{$currency[0]['system_currency']}}</span>
															</div>    
														
															<label class="fs-6 fw-semibold mb-2 required" id="label_percentage">Loan Deduction Percentage</label>
															<div class="input-group deduct-percentage-edit">
																<input type="number" class="form-control" rows="5" name="deduction_percentage"  @if ($loan_detail[0]['loan_deduction_type'] == 2) value="{{ $loan_detail[0]['loan_deduction_amount'] }}" @else value = 0 @endif max="100" placeholder="Loan Deduction Percentage">
																<span class="input-group-text">%</span>
															</div>    
														</div>

														<div class="col-md-6 fv-row">
															<label class="required fs-6 fw-semibold mb-2">Loan Deduction Start</label>
															<input type="month" class="form-control" name="deduction_start"  value="{{ $loan_detail[0]['loan_deduction_start'] }}" min="{{ date('Y-m') }}" max="{{ date('Y-m',strtotime('+1 month')) }}" placeholder="Enter Amount" required>

														</div>

														
													</div>

													@if($loan_detail[0]['loan_submitted_amount'] == '0')
														<div class="text-end">
															<button type="submit" id="update_loan_detail_submit_btn" class="btn btn-primary">
																<span class="indicator-label">Update Details</span>
																<span class="indicator-progress">Please wait...
																<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
															</button>
														</div>
													@else
														<div class="text-end">
															<button type="submit" class="btn btn-primary" disabled>
																<span class="indicator-label">Update Details</span>
																<span class="indicator-progress">Please wait...
																<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
															</button>
														</div>

													@endif
												</div>
											</form>
										</div>

									</div>

									<div id="kt_activity_week" class="card-body p-0 tab-pane fade show" role="tabpanel" aria-labelledby="kt_activity_week_tab">
										<div class="timeline">
											<form id="add_installment">
												<div class="mb-10 mt-n1">
													{{--begin::Timeline heading--}}
													<div class="row g-9 mb-8">
														
														<div class="col-md-6 fv-row">
															<label class="d-flex align-items-center fs-6 fw-semibold mb-2">
															<span class="required">Total Remaining Amount</span>
															</label>
															{{--end::Label--}}
															<input type="number" class="form-control " placeholder="Enter Employee Name" name="loan_remaining_amount" value="{{ $loan_detail[0]['loan_remaining_amount'] }}" readonly="readonly" id="loan_remaining"/>
															<input type="hidden" class="form-control" name="loan_id" required="" value="{{ $loan_detail[0]['loan_id'] }}" readonly="" id="loan_remain">
														</div>
														
														
														<div class="col-md-6 fv-row">
															<label class="d-flex align-items-center fs-6 fw-semibold mb-2">
															<span class="required">Deposit Amount (Installment Amount)</span>
															</label>
															{{--end::Label--}}
															<input type="number" class="form-control " placeholder="Enter Deposit Amount" step="0.0001" name="installment_amount" id="installment_amount" required/>
														</div>
														
													</div>
													<div class="row">
														<div class="col-md-6 mb-8 mt-1 fv-row">
															{{--begin::Label--}}
															<label class="required fs-6 fw-semibold mb-2">Installement Source</label>
																
															<div class="position-relative d-flex align-items-center">
																<select class="form-select " data-control="select2" data-hide-search="false" data-placeholder="Select Installement Source" name="loan_installement_source" id="installement_source"> 
																	<option value="">Select Installement Source</option>
																	<option value="Cash">Cash</option>
																	<option value="Bank Transfer">Bank Transfer</option>
																	<option value="Cheque">Cheque</option>
																
																</select>
															</div>
								
														</div>

														<div class="resoucrce-use col-md-6 fv-row d-none">
															<label class="required d-flex align-items-center fs-6 fw-semibold mb-2" id="installment_resource_label">

															</label>
															{{--end::Label--}}
															<input type="text" class="form-control " placeholder="Enter Installement Resource" name="loan_installment_source_remarks" id="installment_resource" required/>
														</div>
													</div>
													<div class="text-end">
														<button type="submit" id="installment_btn" @if($loan_detail[0]['loan_remaining_amount'] == '0') disabled @endif class="btn btn-primary">
															<span class="indicator-label">Pay Installment</span>
															<span class="indicator-progress">Please wait...
															<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
														</button>
													</div>

												</div>
											</form>

										</div>
									</div>

								</div>
							</div>
								
						</div>
					</div>
				@endif	
				{{--end::End sidebar--}}
			</div>
			{{--end::Social - Activity--}}
		</div>
		{{--end::Content container--}}
	</div>
	{{--end::Content--}}
	<div class="modal fade" id="view-installement-source" tabindex="-1" aria-hidden="true">
       
        <div class="modal-dialog modal-dialog-top mw-650px">
          
            <div class="modal-content rounded">
                
                <div class="modal-content rounded">
                    <div class="modal-header">
                        <h4 class="modal-title pl-4">Installement Source Record</h4>
                        <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                            <span class="svg-icon svg-icon-1">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                    <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
              
                <div class="modal-body" style="overflow-y:scroll;overflow-x:hidden;max-height: 500px;">
                    <table class="table table-row-bordered me-5" id="installement-source-response">
                        
                    </table>
                </div>
              
            </div>
         
        </div>
       
    </div>
</div>

@endsection


@push('js-link')

<script src="{{url('assets/js/loan/loan-detail.js')}}"></script>

@endpush
