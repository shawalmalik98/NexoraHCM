@extends('layout.main')






@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
	{{--begin::Toolbar--}}
	<div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
		{{--begin::Toolbar container--}}
		<div id="kt_app_toolbar_container" class="app-container col-12  d-flex flex-stack">
			{{--begin::Page title--}}
			<div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
				{{--begin::Title--}}
				<h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Gadget Detail</h1>
				{{--end::Title--}}
				
			</div>
			<div class="d-flex align-items-center gap-2 gap-lg-3">
                
				<a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
			   
			</div>
		
			
		</div>
		{{--end::Toolbar container--}}
	</div>
	{{--end::Toolbar--}}
	{{--begin::Content--}}
	<div class="app-content flex-column-fluid">
		{{--begin::Content container--}}
		<div class="app-container">
			{{--begin::Social - Activity --}}
			<div class="d-flex flex-row">
				{{--begin::Start sidebar--}}
				<div class="d-lg-flex flex-column flex-lg-row-auto w-lg-400px" data-kt-drawer="true" data-kt-drawer-name="start-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '250px': '300px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_social_start_sidebar_toggle">
					
					{{--end::User menu--}}
					{{--begin::List widget 10--}}
					<div class="card card-flush">
						
						{{--begin::Body--}}
						<div class="card-body">
							{{--begin::Nav--}}
							<ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9">

								<li class="nav-item col-6 mx-0 p-0">
									<a class="nav-link active d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#gadget_detail_tab">
										<span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Gadget Details</span>
										<span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
									</a>
								</li>

								<li class="nav-item col-6 mx-0 px-0">
									<a class="nav-link d-flex justify-content-center w-100 border-0 h-100" data-bs-toggle="pill" href="#gadget_allotment_detail_tab">
										<span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Assignation Details</span>
										<span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
									</a>
								</li>
								
								<span class="position-absolute z-index-1 bottom-0 w-100 h-4px bg-light rounded"></span>
							</ul>

							<div class="tab-content">

								<div class="tab-pane fade show active" id="gadget_detail_tab">

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">ID</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_id'] ?? 'N/A' }}</span>
										</div>
									</div>

									<div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Type</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_type'] ?? 'N/A' }}</span>
										</div>
									</div>

									<div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Brand</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_brand'] ?? 'N/A' }}</span>
										</div>
									</div>

									<div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Model No</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_model_no'] ?? 'N/A' }}</span>
										</div>
									</div>

									<div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Serial No</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_serial_no'] ?? 'N/A' }}</span>
										</div>
									</div>

									<div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Configuration</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_configuration'] ?? 'N/A' }}</span>
										</div>
									</div>

									<div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Mouse</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_mouse'] ?? 'N/A' }}</span>
										</div>
									</div>

									<div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Keyboard</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_keyboard'] ?? 'N/A' }}</span>
										</div>
									</div>

									<div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Lcd</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_lcd'] ?? 'N/A' }}</span>
										</div>
									</div>
                                    <div class="separator separator-dashed my-2"></div>

                                    <div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Headphone</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_headphone'] ?? 'N/A' }}</span>
										</div>
									</div>
                                    <div class="separator separator-dashed my-2"></div>

                                    <div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Mac Wifi</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_mac_wifi'] ?? 'N/A' }}</span>
										</div>
									</div>

                                    <div class="separator separator-dashed my-2"></div>

                                    <div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Mac Lan</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_mac_lan'] ?? 'N/A' }}</span>
										</div>
									</div>

                                    <div class="separator separator-dashed my-2"></div>

									<div class="d-flex align-items-sm-center">
										<div class="d-flex align-items-center flex-row-fluid flex-wrap">
											<div class="flex-grow-1 me-2">
												<span class="text-gray-800 fw-bold d-block fs-5">Last Updated</span>
											</div>
											<span class="text-gray-500 fw-bold my-2">{{$gadget_detail[0]['gadget_create_date'] ?? 'N/A' }}</span>
										</div>
									</div>
									
								</div>
								

								<div class="tab-pane fade" id="gadget_allotment_detail_tab">
									
									@if(!empty($gadget_allotment))
										@foreach($gadget_allotment as $gadget_history)
											<div class="d-flex align-items-sm-center">
												<div class="d-flex align-items-center flex-row-fluid flex-wrap">
													<div class="flex-grow-1 me-2">
														<span class="text-gray-800 fw-bold d-block fs-5">{!! CustomHelper::getEmpProfileDiv($gadget_history['allotment_employee_id']) !!}</span>
													</div>
													<span class="text-gray-500 fw-bold my-2">{{$gadget_history['allotment_created_date'] }}</span>
												</div>
											</div>
											<div class="separator separator-dashed my-2"></div>
										@endforeach
									@else
										<p class="text-center">This device doesn't assigned to anyone yet.</p>
									@endif	

								</div>
								
							</div>
							{{--end::Tab Content--}}
						</div>
						{{--end: Card Body--}}
					</div>
					{{--end::List widget 10--}}
				</div>
				{{--end::Start sidebar--}}
				{{--begin::Content--}}
				<div class="w-100 flex-lg-row-fluid mx-lg-13">
					{{--begin::Mobile toolbar--}}
					<div class="d-flex d-lg-none align-items-center justify-content-end mb-10">
						<div class="d-flex align-items-center gap-2">
							<div class="btn btn-icon btn-active-color-primary w-30px h-30px" id="kt_social_start_sidebar_toggle">
								{{--begin::Svg Icon | path: icons/duotune/communication/com006.svg--}}
								<span class="svg-icon svg-icon-1">
									<svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path opacity="0.3" d="M16.5 9C16.5 13.125 13.125 16.5 9 16.5C4.875 16.5 1.5 13.125 1.5 9C1.5 4.875 4.875 1.5 9 1.5C13.125 1.5 16.5 4.875 16.5 9Z" fill="currentColor" />
										<path d="M9 16.5C10.95 16.5 12.75 15.75 14.025 14.55C13.425 12.675 11.4 11.25 9 11.25C6.6 11.25 4.57499 12.675 3.97499 14.55C5.24999 15.75 7.05 16.5 9 16.5Z" fill="currentColor" />
										<rect x="7" y="6" width="4" height="4" rx="2" fill="currentColor" />
									</svg>
								</span>
								{{--end::Svg Icon--}}
							</div>
							<div class="btn btn-icon btn-active-color-primary w-30px h-30px" id="kt_social_end_sidebar_toggle">
								{{--begin::Svg Icon | path: icons/duotune/coding/cod002.svg--}}
								<span class="svg-icon svg-icon-1">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path opacity="0.3" d="M18 22C19.7 22 21 20.7 21 19C21 18.5 20.9 18.1 20.7 17.7L15.3 6.30005C15.1 5.90005 15 5.5 15 5C15 3.3 16.3 2 18 2H6C4.3 2 3 3.3 3 5C3 5.5 3.1 5.90005 3.3 6.30005L8.7 17.7C8.9 18.1 9 18.5 9 19C9 20.7 7.7 22 6 22H18Z" fill="currentColor" />
										<path d="M18 2C19.7 2 21 3.3 21 5H9C9 3.3 7.7 2 6 2H18Z" fill="currentColor" />
										<path d="M9 19C9 20.7 7.7 22 6 22C4.3 22 3 20.7 3 19H9Z" fill="currentColor" />
									</svg>
								</span>
								{{--end::Svg Icon--}}
							</div>
						</div>
					</div>
					{{--end::Mobile toolbar--}}
					{{--begin::Timeline--}}
					<div class="card">
						{{--begin::Card head--}}
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
										<a id="edit_gadget_detail_tab" class="nav-link justify-content-center text-active-gray-800 active" data-bs-toggle="tab" role="tab" href="#edit_gadget_details">Edit Gadget Details</a>
									</li>
									<li class="nav-item" role="presentation">
										<a id="edit_gadget_allotment_tab" class="nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#edit_gadget_allotment">Assign Gadget</a>
									</li>
								</ul>
								{{--end::Tab nav--}}
							</div>
							{{--end::Toolbar--}}
						</div>
						{{--end::Card head--}}
						{{--begin::Card body--}}
						<div class="card-body">
							{{--begin::Tab Content--}}
							<div class="tab-content">
								{{--begin::Tab panel--}}
								<div id="edit_gadget_details" class="card-body p-0 tab-pane fade show active" role="tabpanel" aria-labelledby="edit_gadget_detail_tab">
									{{--begin::Timeline--}}
									<div class="timeline">
										<form id="update_gadget_detail">	
											@csrf 
											
											<input type="hidden" value="{{$gadget_detail[0]['gadget_id']}}" name="gadget_id" />
											<div class="row g-9 mb-5">
					
												<div class="col-md-6 fv-row">
													<label class="required fs-6 fw-semibold mb-2">Gadget Type</label>
													<select class="form-select " data-control="select2" data-hide-search="true" required  name="gadget_type">
														@foreach($gadget_detail as $all_gadget)
															<option value="Computer" {{ ($all_gadget['gadget_type']	== "Computer") ? 'selected' : '' }}>Computer</option>
															<option value="Laptop" {{ ($all_gadget['gadget_type']	== "Laptop") ? 'selected' : '' }}>Laptop</option>
															<option value="Mobile" {{ ($all_gadget['gadget_type']	== "Mobile") ? 'selected' : '' }}>Mobile</option> 
															
														@endforeach
															                                       
													</select>
												</div>
					
												<div class="col-md-6 fv-row">
													<label class="required fs-6 fw-semibold mb-2">Brand Name</label>
													<input type="text" class="form-control " name="gadget_brand" placeholder="Enter Brand Name" value="{{$gadget_detail[0]['gadget_brand'] }}" required="required"/>
												</div>
											   
											</div>
					
											<div class="row g-9 mb-8">
												 
												<div class="col-md-6 fv-row">
													<label class="required fs-6 fw-semibold mb-2">Model No</label>
													<input type="text" class="form-control "  name="gadget_model_no" placeholder="Enter Model Number" value="{{$gadget_detail[0]['gadget_model_no'] }}" required="required"/>
												</div>
					
												<div class="col-md-6 fv-row">
												   <label class="required fs-6 fw-semibold mb-2">Serial No</label>
												   <input type="text" class="form-control "name="gadget_serial_no" placeholder="Enter Serial Number" value="{{$gadget_detail[0]['gadget_serial_no']}}" required="required"/>
											   </div>
											  
											   
											</div>
					
											<div class="row g-9 mb-8">
					
												<div class="col-md-6 fv-row">
													<label class="required fs-6 fw-semibold mb-2">Configuration</label>
													<input type="text" class="form-control " name="gadget_configuration" value="{{$gadget_detail[0]['gadget_configuration']}}"placeholder="Enter Configuration" required="required">
												</div>
					
												<div class="col-md-6 fv-row">
													<label class="fs-6 fw-semibold mb-2">Mouse</label>
													<input type="text" class="form-control " value="{{$gadget_detail[0]['gadget_mouse']}}" name="gadget_mouse" placeholder="Enter Mouse" />
												</div>
											  
											</div>
					
											<div class="row g-9 mb-8">
												
												<div class="col-md-6 fv-row">
													<label class="fs-6 fw-semibold mb-2">Keyboard</label>
													<input type="text" class="form-control " value="{{$gadget_detail[0]['gadget_keyboard']}}"  name="gadget_keyboard" placeholder="Enter KeyBoard" >
												</div>
					
												<div class="col-md-6 fv-row">
													<label class="fs-6 fw-semibold mb-2">LCD</label>
													<input type="text" class="form-control " value="{{$gadget_detail[0]['gadget_lcd']}}" name="gadget_lcd" placeholder="Enter LCD" />
												</div>
											</div>
					
											<div class="row g-9 mb-8">
												
												<div class="col-md-6 fv-row">
													<label class="fs-6 fw-semibold mb-2">HeadPhone</label>
													<input type="text" class="form-control " value="{{$gadget_detail[0]['gadget_headphone']}}" name="gadget_headphone" placeholder="Enter HeadPhone" />
												</div>
					
												<div class="col-md-6 fv-row">
													<label class="fs-6 fw-semibold mb-2 required">MAC ADDRESS WIFI</label>
													<input type="text" class="form-control " value="{{$gadget_detail[0]['gadget_mac_wifi']}}" name="gadget_mac_wifi" placeholder="Enter MAC ADDRESS WIFI" required/>
												</div>
											</div>
					
											<div class="row g-9 mb-8">
											
												<div class="col-md-6 fv-row">
													<label class="fs-6 fw-semibold mb-2 required">MAC ADDRESS LAN</label>
													<input type="text" class="form-control " value="{{$gadget_detail[0]['gadget_mac_lan']}}" name="gadget_mac_lan" placeholder="Enter MAC ADDRESS LAN" required/>
												</div>
											</div>

											<div class="text-end">
												<button type="submit" id="update_gadget_detail_submit_btn" class="btn btn-success">
													<span class="indicator-label">Save Changes</span>
													<span class="indicator-progress">Please wait... 
													<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
												</button>
											</div>
										</form>
									</div>
								</div>

								<div id="edit_gadget_allotment" class="card-body p-0 tab-pane fade show" role="tabpanel" aria-labelledby="edit_gadget_allotment_tab">
									<div class="timeline">
										<form id="assign_new_gadget">	
											@csrf 
											<div class="mb-10 mt-n1">
												<div class="row g-9 mb-8">
													<div class="col-md-6 fv-row">
														<label class="d-flex align-items-center fs-6 fw-semibold mb-2">
														<span class="required">Employee</span>
														</label>
														
														<input type="hidden" name="gadget_id" value="{{ $gadget_detail[0]['gadget_id'] }}">
														
														<select class="form-select  select-employee"  data-control="select2" name="employee_code" required="required">
															<option value="">Select Employee</option>
															@if (!empty($all_employees))
																@foreach ($all_employees as $all_employee)
																	<option value="{{ $all_employee['employee_id'] }}" 
																		@if(!empty($gadget_allotment))
																			@if($gadget_detail[0]['gadget_status'] == 1)
																				@if ($all_employee['employee_id'] == $gadget_allotment[0]['allotment_employee_id']) 		
																					selected 
																				@endif
																			@endif
																		@endif>
																		{{ $all_employee['employee_name'] }}
																		({{ $all_employee['department_name'] }})
																	</option>
																@endforeach
															@endif                                                   
														</select>
													</div>
													@if($gadget_detail[0]['gadget_status'] == 1)
													<div class="col-md-5 fv-row mt-10 gadget-free">
														<div class="form-check form-check-sm form-check-custom mt-10">
															<label class="form-check form-check-custom ">
																<input class="form-check-input h-20px w-20px check" type="checkbox" id="" name="status" value="2" />
																<span class="form-check-label fw-semibold">Mark this gadget as free</span>
															</label>
														</div>
													</div>
													@endif
													{{-- <div class="col-md-1 fv-row"></div> --}}
													{{--end::Col--}}
												</div>
												<div class="text-end">
													<button type="submit" id="assign_new_gadget_submit_btn" class="btn btn-success">
														<span class="indicator-label">Save Changes</span>
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
			</div>
		</div>
	</div>
</div>

@endsection


@push('js-link')

<script src="{{url('assets/js/gadget/view-gadget-detail.js')}}"></script>


@endpush