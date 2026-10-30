@extends('layout.main')

@push('title') {{ $title ?? 'Nexora HCM' }} @endpush

@section('main-section')

    <div class="d-flex flex-column flex-column-fluid">

        {{-- begin::Toolbar --}}
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            {{-- begin::Toolbar container --}}
            <div class="app-container col-12 d-flex flex-stack">
                {{-- begin::Page title --}}
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    {{-- begin::Title --}}
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Vehicle Detail</h1>
                    {{-- end::Title --}}
                    {{-- begin::Breadcrumb --}}

                    {{-- end::Breadcrumb --}}
                </div>
                {{-- end::Page title --}}
                {{-- begin::Actions --}}
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    <a href="{{ url()->previous() }}" class="btn btn-sm fw-bold btn-primary">Go Back</a>
                </div>
                {{-- end::Actions --}}
            </div>
            {{-- end::Toolbar container --}}
        </div>
        {{-- end::Toolbar --}}
        {{-- begin::Content --}}
        <div class="app-content flex-column-fluid">
            {{-- begin::Content container --}}
            <div class="app-container">
                {{-- begin::Social - Activity --}}
                <div class="row">
                    <div class="col-md-5 card mb-5">
                        <div class="card mb-5 mb-xl-8">
                            <div class="card card-flush">
                                <div class="card-body p-5">
                                    {{-- begin::Nav --}}
                                    <ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9"
                                        id="custom-tabs-four-tab" role="tablist">

                                        <li class="nav-item col mx-0 p-0">
                                            <a class="nav-link active d-flex justify-content-center w-100 border-0 h-100"
                                                id="custom-tabs-vehicle-detail-tab" data-bs-toggle="pill"
                                                href="#custom-tabs-vehicle-detail" role="tab"
                                                aria-controls="custom-tabs-vehicle-detail" aria-selected="true">
                                                <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Vehicle</span>
                                                <span
                                                    class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                            </a>
                                        </li>

                                        <li class="nav-item col mx-0 px-0">
                                            <a class="nav-link d-flex justify-content-center w-100 border-0 h-100"
                                                id="custom-tabs-vehicle-maintenance-tab" data-bs-toggle="pill"
                                                href="#custom-tabs-vehicle-maintenance" role="tab"
                                                aria-controls="custom-tabs-vehicle-maintenance" aria-selected="false">
                                                <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Maintenance</span>
                                                <span
                                                    class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                            </a>
                                        </li>

                                        <li class="nav-item col mx-0 px-0">
                                            <a class="nav-link d-flex justify-content-center w-100 border-0 h-100"
                                                id="custom-tabs-vehicle-allotment-tab" data-bs-toggle="pill"
                                                href="#custom-tabs-vehicle-allotment" role="tab"
                                                aria-controls="custom-tabs-vehicle-allotment" aria-selected="false"
                                                role="tab" aria-controls="custom-tabs-vehicle-images3"
                                                aria-selected="false">
                                                <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Allotment</span>
                                                <span
                                                    class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                            </a>
                                        </li>
                                        <li class="nav-item col mx-0 px-0">
                                            <a class="nav-link d-flex justify-content-center w-100 border-0 h-100"
                                                id="custom-tabs-vehicle-images1" data-bs-toggle="pill"
                                                href="#custom-tabs-vehicle-images3" role="tab"
                                                aria-controls="custom-tabs-vehicle-images3" aria-selected="false">
                                                <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Uploads</span>
                                                <span class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                            </a>
                                        </li>

                                        <span class="position-absolute z-index-1 bottom-0 w-100 h-5px bg-light rounded"></span>
                                    </ul>

                                    <div class="tab-content" id="custom-tabs-four-tabContent">

                                        <div class="tab-pane fade show active" id="custom-tabs-vehicle-detail" role="tabpanel"
                                            aria-labelledby="custom-tabs-vehicle-detail-tab">

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Vehicle ID</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_id']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Type</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_type']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Company</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_company_name']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Name</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_name']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Number</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_number']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Model</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_model']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Color</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_color']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Engine Number</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_engine_number']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Chassis Number</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_chassis_number']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Tax Paid Till</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_tax_paid_till']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Amount</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_amount']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Ownership</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_ownership']) ?? 'N/A' }}</span>
                                                </div>
                                            </div>

                                            <div class="separator separator-dashed my-2"></div>

                                            {{-- <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Installment</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_installment']) ?? 'N/A' }}</span>
                                                </div>
                                            </div> --}}

                                            {{-- <div class="separator separator-dashed my-2"></div>

                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Installment
                                                            Date</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_installment_date']) ?? 'N/A' }}</span>
                                                </div>
                                            </div> --}}

                                            {{-- <div class="separator separator-dashed my-2"></div> --}}

                                            {{-- <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <div class="flex-grow-1 me-2">
                                                        <span class="text-gray-800 fw-bold d-block fs-5">Status</span>
                                                    </div>
                                                    <span class="text-gray-500 fw-bold my-2">{{ ($get_vehicle_data['vehicle_status']) ?? 'N/A' }}</span>
                                                </div>
                                            </div> --}}

                                        </div>

                                        <div class="tab-pane fade" id="custom-tabs-vehicle-maintenance" role="tabpanel"
                                            aria-labelledby="custom-tabs-vehicle-maintenance-tab">
                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9">
                                                        @php $maintenance_date = array(); @endphp
                                                        @if (!empty($get_maintenance_data))
                                                            @foreach ($get_maintenance_data as $get_maintenance_data)
                                                                @php
                                                                    array_push($maintenance_date, $get_maintenance_data['maintenance_date']);
                                                                @endphp
                                                                <li class="list-group-item text-center">    
                                                                    <div class="row">
                                                                        <a class="nav-link active d-flex justify-content-center w-100 border-0 h-100"
                                                                            <span class="text-gray-800 fw-bold d-block fs-5">Maintenance On Date:</span>
                                                                        </a>
                                                                        <div class="col-md-5">Maintenance On Date:</div>
                                                                        <div class="col-md-3"><b>{{ $get_maintenance_data['maintenance_date'] }}</b></div>
                                                                            <input type="hidden" name="m_id"
                                                                                value="{{ $get_maintenance_data['maintenance_id'] }}">
                                                                            <span
                                                                                class="d-none">{{ $get_maintenance_data['maintenance_item'] }}</span>
                                                                                <div class="col-md-3">
                                                                                    <a href="javascript:;"
                                                                                        class="maintenance_edit_icon btn btn-icon btn-bg-light btn-active-color-primary btn-sm" >
                                                                                        <span class="svg-icon svg-icon-3">
                                                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                                        </svg>
                                                                                        </span>
                                                                                    </a>
                                                                            </div>
                                                                    </div>       
                                                                </li>
                                                                
                                                            @endforeach
                                                            @php $maintenance_date = implode(",", $maintenance_date); @endphp
                                                        @else
                                                            @php $maintenance_date = implode(",", $maintenance_date); @endphp
                                                            <p class="text-center">No Maintenance Record Found</p>
                                                        @endif

                                                            <span
                                                                class="position-absolute z-index-1 bottom-0 w-100 h-5px bg-light rounded">
                                                            </span>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-vehicle-allotment" role="tabpanel"
                                            aria-labelledby="custom-tabs-vehicle-allotment-tab">
                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9">
                                                        @if (!empty($all_vehicles))
                                                            <li class="nav-item col-3 mx-0 p-0">
                                                                <a
                                                                    class="nav-link active d-flex justify-content-center w-100 border-0 h-100">
                                                                    <div
                                                                        class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                        <span
                                                                            class="nav-text fw-bold fs-6 mb-3">Employee</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item col-3 mx-0 p-0">
                                                                <a
                                                                    class="nav-link active d-flex justify-content-center w-100 border-0 h-100">
                                                                    <div
                                                                        class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                        <span class="nav-text fw-bold fs-6 mb-3">Allotment
                                                                            Date</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item col-3 mx-0 p-0">
                                                                <a
                                                                    class="nav-link active d-flex justify-content-center w-100 border-0 h-100">
                                                                    <div
                                                                        class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                        <span class="nav-text fw-bold fs-6 mb-3">Cancle
                                                                            Date</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item col-3 mx-0 p-0">
                                                                <a
                                                                    class="nav-link active d-flex justify-content-center w-100 border-0 h-100">
                                                                    <div
                                                                        class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                        <span class="nav-text fw-bold fs-6 mb-3">Action</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                           
                                                        @else
                                                            <p class="text-center">No Allottee Found</p>
                                                        @endif

                                                        @if (!empty($all_vehicles))
                                                            @foreach ($all_vehicles as $key => $all_vehicles)
                                                                @php
                                                                    if (count($all_vehicles) == ++$key) {
                                                                        if ($all_vehicles['allotment_cancellation_date'] == '') {
                                                                            $disabled = 'disabled';
                                                                        } else {
                                                                            $disabled = '';
                                                                        }
                                                                    }
                                                                @endphp
                                                                <li class="list-group-item text-center">

                                                                    <div class="row mt-3 {{ $all_vehicles['allotment_cancellation_date'] == '' ? 'text-bold text-gray-800' : '' }}">
                                                                        <input type="hidden" name="a_id"
                                                                        value="{{ $all_vehicles['allotment_id'] }}">
                                                                        <input type="hidden" name="e_id"
                                                                            value="{{ $all_vehicles['employee_id'] }}">
                                                                       
                                                                        <div class="col-md-3 mt-2">
                                                                            {!! CustomHelper::getEmpProfileDiv($all_vehicles['employee_id']) !!}
                                                                        </div>
                                                                        <div class="col-md-3 mt-2" id="allotment_date1">{{ $all_vehicles['allotment_vehicle_date'] }}</div>
                                                                        
                                                                        <div class="col-md-3 mt-2" id="allotment_cancellation_date1"><a href="javascript:;">{{ $all_vehicles['allotment_cancellation_date'] }}</a></div>
                                                                        <div class="col-md-3 mt-2">
                                                                            @if($all_vehicles['employee_isactive'] == 1)
                                                                                <a href="javascript:;" class="allotment_edit_icon btn btn-icon btn-bg-light btn-active-color-primary btn-sm" onclick="allotment_edit_icon('{{ $all_vehicles['allotment_id'] }}','{{ $all_vehicles['employee_id'] }}','{{ $all_vehicles['allotment_vehicle_date'] }}','{{ $all_vehicles['allotment_cancellation_date'] }}')">
                                                                                    <span class="svg-icon svg-icon-3">
                                                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                                            <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                                                        </svg>
                                                                                    </span>
                                                                                </a>
                                                                            @endif
                                                                            
                                                                            <a href="javascript:;"
                                                                            class="allotment_trash_icon d-none">
                                                                            <i class="fas fa-trash text-danger"></i>
                                                                            
                                                                            </a>
                                                                            
                                                                        </div>

                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        @else
                                                            @php $disabled = ''; @endphp
                                                        @endif

                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="custom-tabs-vehicle-images3" role="tabpanel"
                                            aria-labelledby="custom-tabs-vehicle-images1">
                                            <div class="d-flex align-items-sm-center">
                                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                    <ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9">
                                                        @if (!empty($vehicle_images))
                                                            <li class="nav-item col-4 mx-0 p-0">
                                                                <a
                                                                    class="nav-link active d-flex justify-content-center w-100 border-0 h-100">
                                                                    <div
                                                                        class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                        <span class="nav-text fw-bold fs-6 mb-3">Upload
                                                                            ID</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item col-4 mx-0 p-0">
                                                                <a
                                                                    class="nav-link d-flex justify-content-center w-100 border-0 h-100">
                                                                    <div
                                                                        class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                        <span class="nav-text fw-bold fs-6 mb-3">Upload
                                                                            Date</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                            <li class="nav-item col-4 mx-0 p-0">
                                                                <a
                                                                    class="nav-link d-flex justify-content-center w-100 border-0 h-100">
                                                                    <div
                                                                        class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                        <span class="nav-text fw-bold fs-6 mb-3">Action</span>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @else
                                                            <p class="text-center">No Images Found</p>
                                                        @endif

                                                        @if (!empty($vehicle_images))
                                                            @foreach ($vehicle_images as $vehicle_image)
                                                                <li class="nav-item col-4 mx-0 p-0"">
                                                                    <a
                                                                        class="nav-link active d-flex justify-content-center w-100 border-0 h-100">
                                                                        <div
                                                                            class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                            <span
                                                                                class="nav-text text-gray-800 fw-bold fs-6 mb-3">{{ $vehicle_image['vehicle_img_id'] }}</span>
                                                                        </div>
                                                                    </a>

                                                                </li>
                                                                <li class="nav-item col-4 mx-0 p-0">
                                                                    <a
                                                                        class="nav-link d-flex justify-content-center w-100 border-0 h-100">
                                                                        <div
                                                                            class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                            <span
                                                                                class="nav-text text-gray-800 fw-bold fs-6 mb-3">{{ $vehicle_image['vehicle_img_upload_date'] }}</span>
                                                                        </div>
                                                                    </a>
                                                                </li>
                                                                <li class="nav-item col-4 mx-0 p-0">
                                                                    <div
                                                                        class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                                        <a
                                                                            class="nav-link d-flex justify-content-center w-100 border-0 h-100">
                                                                            <a href="{{ url('vehicles/download-vehicle-document?path=vehicles/'.$vehicle_image['vehicle_img_name'])}}&title=vehicle image"
                                                                                target="_blank" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Download File"><i class="fa fa-download" aria-hidden="true"></i>
                                                                            </a>
                                                                        </a>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                        @endif
                                                    </ul>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="card card-flush">
                            {{-- begin::Card head --}}
                            <div class="card-header card-header-stretch d-block">
                                {{-- begin::Title --}}
                                <div class="card-title d-flex align-items-center">

                                    {{-- <h3 class="fw-bold m-0 text-gray-800">Jan 23, 2022</h3> --}}
                                </div>
                                {{-- end::Title --}}
                                {{-- begin::Toolbar --}}
                                <div class="card-toolbar m-0">
                                    {{-- begin::Tab nav --}}
                                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0 fw-bold"
                                        role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <a id="custom-tabs-vehicle-detail-tab2" class="tab-nav nav-link justify-content-center text-active-gray-800 active" data-bs-toggle="tab" role="tab" href="#custom-tabs-vehicle-detail2">Vehicle Details</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a id="custom-tabs-vehicle-maintenance-tab2" class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#custom-tabs-vehicle-maintenance2">Maintenance Detail</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a id="custom-tabs-vehicle-allotment-tab2" class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#custom-tabs-vehicle-allotment2">Allotment Detail</a>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <a id="custom-tabs-vehicle-images" class="tab-nav nav-link justify-content-center text-active-gray-800" data-bs-toggle="tab" role="tab" href="#custom-tabs-vehicle-images2">Images</a>
                                        </li>
                                    </ul>
                                    {{-- end::Tab nav --}}
                                </div>
                                {{-- end::Toolbar --}}
                            </div>
                            {{-- end::Card head --}}
                            {{-- begin::Card body --}}
                            <div class="card-body pt-5">
                                {{-- begin::Tab Content --}}
                                <div class="tab-content" id="custom-tabs-four-tabContent2">
                                    {{-- begin::Tab panel --}}
                                    <div class="tab-panel card-body p-0 tab-pane fade show active" id="custom-tabs-vehicle-detail2"
                                        role="tabpanel" aria-labelledby="custom-tabs-vehicle-detail-tab2">
                                        {{-- begin::Timeline --}}
                                        <div class="timeline">
                                            <form id="update_vehicle_detail">

                                                <div class="mb-10 mt-n1">
                                                    {{-- begin::Timeline heading --}}
                                                    <div class="row g-9 mb-8">
                                                        
                                                        <input type="hidden" name="vehicle_id" value="{{ $get_vehicle_data['vehicle_id'] }}">
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Type</span>
                                                            </label>
                                                            
                                                            <select class="form-select vehicleselect" data-control="select2" data-hide-search="false" data-placeholder="Select a vehicle Type" name="vehicle_type">
                                                                <option value="">Select Vehicle Type</option>
                                                                <option value="Two Wheels" @if($get_vehicle_data['vehicle_type'] == 'Two Wheels') selected @endif>Two Wheels</option>
                                                                <option value="Three Wheels" @if($get_vehicle_data['vehicle_type'] == 'Three Wheels') selected @endif>Three Wheels</option>
                                                                <option value="Four Wheels" @if($get_vehicle_data['vehicle_type'] == 'Four Wheels') selected @endif>Four Wheels</option>
                                                            </select>
                                                        </div>
                                                        
                                                        
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Company Name</span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control" placeholder="Enter Vehcile Manufacturer Name for e.g (Honda, Toyota, Suzuki etc)" name="vehicle_company_name" value="{{ $get_vehicle_data['vehicle_company_name'] }}" required="required">
                                                        </div>
                                                        
                                                    </div>

                                                    <div class="row g-9 mb-8">
                                                        
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Name</span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control" placeholder="Enter Vehicle Name for e.g (Mira, Alto, Yaris etc)" name="vehicle_name" value="{{ $get_vehicle_data['vehicle_name'] }}" required="required">

                                                        </div>
                                                        
                                                        
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Registration Number </span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control "name="vehicle_number" placeholder="Enter Registration Number for e.g (ALU-551)" value="{{ $get_vehicle_data['vehicle_number'] }}" required="required">
                                                        </div>
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Model </span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control "name="vehicle_model" placeholder="Enter Model for e.g (2022)" value="{{ $get_vehicle_data['vehicle_model'] }}" required="required">
                                                        </div>
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Color </span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control "name="vehicle_color" placeholder="Enter Vehicle Color" value="{{ $get_vehicle_data['vehicle_color'] }}" required="required">
                                                        </div>
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Engine Number </span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control "name="vehicle_engine_number" placeholder="Enter Engine Number" value="{{ $get_vehicle_data['vehicle_engine_number'] }}" required="required">
                                                        </div>
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Chassis Number </span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control "name="vehicle_chassis_number" placeholder="Enter Chassis Number" value="{{ $get_vehicle_data['vehicle_chassis_number'] }}" required="required">
                                                        </div>
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span>Tax Paid Till </span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control "name="vehicle_tax_paid_till" placeholder="Enter Tax Paid Time Period" value="{{ $get_vehicle_data['vehicle_tax_paid_till'] }}">
                                                        </div>
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span>Vehicle Amount </span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control "name="vehicle_amount" placeholder="Enter Vehicle Amount" value="{{ $get_vehicle_data['vehicle_amount'] }}">
                                                                
                                                        </div>
                                                        {{-- <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span class="required">Vehicle Status </span>
                                                            </label>
                                                            <select class="form-select vehicleselect"
                                                                data-control="select2" data-hide-search="false"
                                                                name=vehicle_status>
                                                                <option value="">Select Vehicle Status</option>
                                                                <option value="Probation"
                                                                    @if ($get_vehicle_data['vehicle_status'] == 'Probation') Selected @endif>
                                                                    Probation</option>
                                                                <option value="Temporary"
                                                                    @if ($get_vehicle_data['vehicle_status'] == 'Temporary') Selected @endif>
                                                                    Temporary</option>
                                                                <option value="Contract"
                                                                    @if ($get_vehicle_data['vehicle_status'] == 'Contract') Selected @endif>
                                                                    Contract</option>
                                                                <option value="Permanent"
                                                                    @if ($get_vehicle_data['vehicle_status'] == 'Permanent') Selected @endif>
                                                                    Permanent</option>
                                                            </select>
                                                        </div> --}}
                                                        <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span>Vehicle Ownership </span>
                                                            </label>
                                                            
                                                            <input type="text" class="form-control "name="vehicle_ownership" placeholder="Enter Vehicle Ownership" value="{{ $get_vehicle_data['vehicle_ownership'] }}">
                                                        </div>
                                                        {{-- <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span>Vehicle Installment </span>
                                                            </label>
                                                            <input type="text"
                                                                class="form-control "name="vehicle_installment"
                                                                placeholder="Enter Vehicle Installment Amount"
                                                                value="{{ $get_vehicle_data['vehicle_installment'] }}">
                                                        </div> --}}
                                                        {{-- <div class="col-md-6 fv-row">
                                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                <span>Vehicle Installment Date </span>
                                                            </label>
                                                            <div class="position-relative d-flex align-items-center">
                                                                <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                                                        <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                                                        <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                                                    </svg>
                                                                </span>
                                                                <input type="date" class="form-control ps-12" name="vehicle_installment_date"
                                                                    autocomplete="off" value="{{ $get_vehicle_data['vehicle_installment_date'] }}">
                                                            </div>       
                                                        </div> --}}
                                                    </div>

                                                    <div class="text-end">
                                                        <button type="submit" id="update_btn_vehicle" class="btn btn-primary">
                                                            <span class="indicator-label">Update Details</span>
                                                            <span class="indicator-progress">Please wait...
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                    </div>
                                                </div>   
                                            </form>
                                        </div>

                                    </div>

                                    <div class="tab-panel card-body p-0 tab-pane fade show" id="custom-tabs-vehicle-maintenance2"
                                        role="tabpanel" aria-labelledby="custom-tabs-vehicle-maintenance-tab2">
                                        <div class="timeline">
                                                <form id="maintenance_detail">
                                                    <div class="mb-10 mt-n1">
                                                        <div class="row g-9 mb-8">
                                                            <div class="col-md-12 fv-row">
                                                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                    <span class="required">Maintenance Date</span>
                                                                </label>
                                                                <div class="position-relative d-flex align-items-center"> 
                                                                    <input type="hidden" name="maintenance_length" value="1">
                                                                    <input type="hidden" name="maintenance_id" value="">
                                                                    <input type="hidden" name="vehicle_id"
                                                                        value="{{ $get_vehicle_data['vehicle_id'] }}">
                                                                    <input type="hidden" name="maintenance_sub" id="maintenance_sub"
                                                                        value="">
                                                                    <input type="date" autocomplete="off" class="form-control" name="maintenance_date" value="" required="" onchange="check_maintenance_date(this)">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12 fv-row">
                                                                <div class="table-responsive popup-visible ">

                                                                    <div id="tableEmployee_wrapper"
                                                                        class="dataTables_wrapper dt-bootstrap4 no-footer">
                                                                        <div class="table-responsive popup-visible ">
                                                                            <table
                                                                                class="table align-middle gs-0 gy-4 dataTable no-footer">

                                                                                <thead>
                                                                                    <tr class="fw-bold">
                                                                                        <th>Item</th>
                                                                                        <th>Value</th>
                                                                                        <th>Action</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="maintenance_tbody">
                                                                                    <tr id="maintenance_1">
                                                                                        <td><input type="text" name="maintenance_item1" class="form-control" placeholder="Item" required></td>
                                                                                        <td><input type="number" name="maintenance_value1" class="form-control" placeholder="Value" required></td>
                                                                                        <td><button type="button" class="btn btn-primary" onclick="add_maintenance()">+</button>
                                                                                        </td>
                                                                                        <td><button type="button" class="btn btn-danger" onclick="sub_maintenance_modal(1)">-</button>
                                                                                        </td>
                                                                                    </tr>

                                                                                </tbody>

                                                                            </table>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                                
                                                            </div>
                                                            
                                                        </div>
                                                    </div>   
                                                    <div class="text-end">

                                                        {{-- <button type="reset" id="kt_modal_new_target_cancel" class="btn btn-light me-3">Cancel</button> --}}
                                                        <button type="submit" id="maintenance_btn" class="btn btn-info">
                                                            <span class="indicator-label">Submit</span>
                                                            <span class="indicator-progress">Please wait...
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                        </button>
                                                    </div>        
                                                </form>
                                        </div>
                                    </div>

                                    <div class="tab-panel card-body p-0 tab-pane fade show" id="custom-tabs-vehicle-allotment2" role="tabpanel"
                                        aria-labelledby="custom-tabs-vehicle-allotment-tab2">
                                        <div class="timeline">
                                            <form id="allotment_detail">

                                                <div class="mb-10 mt-n1">
                                                    <div class="row g-9 mb-8">
                                                        <div class="col-md-6 fv-row">
                                                            <div class="form-group">
                                                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                    <span class="required">Employees</span>
                                                                </label>
                                                                <input type="text" class="d-none form-control " id="allotment_employee_id_text" value="" readonly>
                                                                <div id="allotment_employee_container">
                                                                    <select class="form-select" name="allotment_employee_id" id="allotment_employee_id_show"
                                                                            required="required"  data-control="select2" data-hide-search="false" data-employee-select data-scope="active">
                                                                        <option value="">Select Employee</option>
                                                                        @include('include.employee-selected-options', ['selected' => null])
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6 fv-row">
                                                            <label class="fs-6 fw-semibold mb-2">Allotment Date</label>
                                                            <div class="position-relative d-flex align-items-center">
                                                                <input type="hidden" name="allotment_id" value="">
                                                                <input type="hidden" name="allotment_vehicle_id" value="{{ $get_vehicle_data['vehicle_id'] }}">
                                                                <input type="date" autocomplete="off" class="form-control" name="allotment_vehicle_date" value="" required="">                                                            
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 fv-row">
                                                            <div class="form-group">
                                                                <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                                    <span class="required">Allotment Cancellation Date</span>
                                                                </label>
                                                                <div class="position-relative d-flex align-items-center">
                                                                    <input type="date" autocomplete="off" class="form-control" name="allotment_cancellation_date" value="" disabled />
                                                                </div>        
                                                            </div>
                                                        </div>
                                                        
                                                    </div>
                                                    
                                                </div>
                                                <div class="text-end">
                                                    {{-- <button type="reset" id="kt_modal_new_target_cancel" class="btn btn-light me-3">Cancel</button> --}}
                                                    <button type="submit" id="allotment_btn" class="btn btn-info" {{$disabled ?? ''}}>

                                                        <span class="indicator-label">Submit</span>
                                                        <span class="indicator-progress">Please wait...
                                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                                        </span>
                                                            
                                                    </button>
                                                </div>

                                            </form>
                                        </div>

                                    </div>

                                    <div class="tab-panel card-body p-0 tab-pane fade show" id="custom-tabs-vehicle-images2" role="tabpanel"
                                        aria-labelledby="custom-tabs-vehicle-images2">
                                        <div class="timeline">
                                            <div class="mb-10 mt-n1">
                                                <div class="row g-9 mb-8">
                                                    <div class="col-md-12 fv-row">
                                                        <div class="container">
                                                            <form method="post" action="{{ url('vehicles/upload_vehicles_images') }}"
                                                                enctype="multipart/form-data" class="dropzone" id="upload">
                                                                @csrf
                                                                <input type="hidden" value="{{ Request::segment(3) }}" name="vehicle_id">
                                                            </form>
                                                            <p class="text-center text-danger mt-2">Maximum 10 files can be upload at a time.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                {{-- end::Card body --}}
                            </div>
                           {{-- end::Timeline --}}
                        </div>
                    </div>
                </div>
              
            </div>
            {{-- end::Content --}}

            <div class="modal fade" id="modal-image" tabindex="-1" aria-hidden="true">
                {{-- begin::Modal dialog --}}
                <div class="modal-dialog modal-dialog-top">
                    {{-- begin::Modal content --}}
                    <div class="modal-content bg-rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Image</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal"
                                aria-label="Close">
                                <span class="svg-icon svg-icon-1 bg-white">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                            rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                            transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <img src="" alt="Car Image" id="car-image" class="w-100">
                        </div>
                            {{-- <div class="modal-footer justify-content-between">
                                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                                <a href="javascript:void(0);" class="btn btn-sm btn-primary" id="deactivate_button">Delete</a>
                            </div>  --}}
                    </div>

                    {{-- end::Modal content --}}
                </div>
                {{-- end::Modal dialog --}}
            </div>
            <div class="modal fade" id="modal-danger" tabindex="-1" aria-hidden="true">
                {{-- begin::Modal dialog --}}
                <div class="modal-dialog modal-dialog-top">
                    {{-- begin::Modal content --}}
                    <div class="modal-content bg-rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Confirmation Alert</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal"
                                aria-label="Close">
                                <span class="svg-icon svg-icon-1 bg-white">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                            rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                            transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure to delete this <span id="modal-text"></span> ?</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                            <a href="javascript:void(0);" class="btn btn-sm btn-primary" id="deactivate_button">Delete</a>
                        </div>
                    </div>

                    {{-- end::Modal content --}}
                </div>
                {{-- end::Modal dialog --}}
            </div>
            <div class="modal fade" id="modal-danger-maintenance" tabindex="-1" aria-hidden="true">
                {{-- begin::Modal dialog --}}
                <div class="modal-dialog modal-dialog-top">
                    {{-- begin::Modal content --}}
                    <div class="modal-content bg-rounded">
                        <div class="modal-header">
                            <h4 class="modal-title pl-4">Confirmation Alert</h4>
                            <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal"
                                aria-label="Close">
                                <span class="svg-icon svg-icon-1 bg-white">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2"
                                            rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1"
                                            transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure to delete this maintenance?</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                            <a href="javascript:void(0);" onclick="sub_maintenance()" class="btn btn-sm btn-primary"
                                id="deactivate_button_maintenance">Delete</a>
                        </div>
                    </div>

                    {{-- end::Modal content --}}
                </div>
                {{-- end::Modal dialog --}}
            </div>
        </div>

    </div>



@endsection


@push('js-link')
<script>
var maintenance_date_value = "{{ $maintenance_date }}";
var url_vehicle_id = "{{ url('vehicles/vehicle-detail') }}/{{ $get_vehicle_data['vehicle_id'] }}";
</script>
<script src="{{url('assets/js/vehicle/vehicle-detail.js')}}"></script>

@endpush
