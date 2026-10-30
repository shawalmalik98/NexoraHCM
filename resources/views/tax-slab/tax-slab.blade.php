@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Manage Tax Slab {{(empty($fetch_tax_slab )) ? '' : '('.CustomHelper::dateformatDFY($fetch_tax_slab[0]['ts_fiscal_start']).'-'.CustomHelper::dateformatDFY($fetch_tax_slab[0]['ts_fiscal_end']).')'}} </h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTaxSlab">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                        Add New Tax Slab</a>   
                </div>
         
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
           
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">  

                    

                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilter">
                        </div>
                    </div>


                    <div class="card-toolbar" >
                        @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('fiscal')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter
                        </a>
                       
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                            <form action="{{ url('tax-slab') }}" method="GET">

                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                </div>

                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5">
                                        
                                    <div class="mb-10">
                                            
                                        <label class="form-label fw-semibold">Month:</label>
                                            
                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Fiscal Year" name="fiscal"
                                            required="required">
                                            <option value="">Select Month</option>

                                            @foreach($tax_slab_filter as $filter)
                                                <option value="{{$filter['ts_fiscal_start']}}"  @if (!empty(Request::get('fiscal')) && Request::get('fiscal') == $filter['ts_fiscal_start'] ) selected @endif >{{ CustomHelper::dateformatDFY($filter['ts_fiscal_start']) }} - {{ CustomHelper::dateformatDFY($filter['ts_fiscal_end']) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                       
                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('fiscal')))
                                        
                                        <a href="{{ url('tax-slab') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                            >Reset Filter</a>
                                        @endif
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary" >Apply</button>
                                    </div>
                                        
                                </div>
                            </form>
                        </div>
                    </div>

                </div> 

                
                <div class="card-body py-3">
                    
                    <div class="table-responsive popup-visible ">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="system_datatable" aria-describedby="tableEmployee_info">
                                
                                
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Ref ID</th>
                                        <th>Lowest Yearly Income</th>
                                        <th>Highest Yearly Income</th>
                                        <th>Deduction Percentage</th>
                                        <th>Deduction Amount</th>
                                        <td>Updated At</td>
                                        <th>Created By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                {{-- // SELECT `tax_slab_id`, `ts_lowest_yearly_income`, `ts_highest_yearly_income`, `ts_deduction_percentage`, `ts_deduction_amount`, `ts_fiscal_start`, `ts_fiscal_end`, `ts_created_by`, `ts_created_at`, `ts_updated_at` FROM `tbl_payroll_tax_slab` WHERE 1   --}}
                                <tbody>
                                     @if(!empty($fetch_tax_slab))
                                        @foreach ($fetch_tax_slab as $tax_slab)
                                            <tr>
                                                <td class="ps-8">{{ $tax_slab['tax_slab_id'] }}</td>
                                                <td>{{ number_format($tax_slab['ts_lowest_yearly_income']) }}</td>
                                                <td>{{ number_format($tax_slab['ts_highest_yearly_income']) }}</td>
                                                <td>{{ $tax_slab['ts_deduction_percentage'] }}%</td>
                                                <td>{{ number_format($tax_slab['ts_deduction_amount']) }}</td>
                                                <th>{{ (empty($tax_slab['ts_updated_at'])) ? '' : CustomHelper::dateformatDFY($tax_slab['ts_updated_at']) }}</th>
                                                <td>{!! CustomHelper::getEmpProfileDiv( $tax_slab['ts_created_by'] ) !!}</td>

                                                <td  class="ps-8" >
                                                    <a href="javascript:;" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="modal" data-bs-target="#editTaxSlab"> 
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                                <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                            </svg>
                                                        </span>
                                                    </a>

                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            
                            </table>
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
            
            
            <form id="addTaxSlabForm" class="form">
                @csrf
                <div class="modal fade" id="addTaxSlab" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-900px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Add New Tax Slab</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                                </button>
                            </div>
                            
                            
                            <div class="modal-body scroll-y pt-0 pb-15">

                                <div class="col-md-12 fv-row pt-10" >
                                    <label class="fs-6 fw-semibold mb-2 required">Fiscal Year</label>
                                    <select class="form-select"  data-kt-select2="true" data-placeholder="Select Fiscal Year" name="fiscal_start" required >
                                        @if(!empty($fiscal_dates_array))
                                            @foreach ($fiscal_dates_array as $fiscal_date)
                                                <option value="{{ $fiscal_date['start_fiscal'] }}">{{CustomHelper::dateformatDFY($fiscal_date['start_fiscal'])}} - {{CustomHelper::dateformatDFY($fiscal_date['end_fiscal'])}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div id="slab_div">

                                    <div class="row pt-10">



                                        <div class="col-3" >
                                            
                                            <div class="d-flex flex-column mb-4">
                                                <label class="fs-6 fw-semibold mb-2 required">Lowest Yearly Income </label>
                                                <input type="number" step="0.01" class="form-control ts_lowest_yearly_income" name="lowest_yearly_income[]" placeholder="Enter Lowest Yearly Inocme" required="" readonly value="0" >
                                            </div>
                                            {{--end::Input group--}}
                                        </div>

                                        <div class="col-3" >
                                            <div class="d-flex flex-column mb-4">
                                                <label class="fs-6 fw-semibold mb-2 required">Highest Yearly Income</label>
                                                <input type="number" step="0.01" class="form-control ts_highest_yearly_income1" name="highest_yearly_income[]" placeholder="Enter Highest Yearly Income" value="" required="" oninput="nextLowerIcome(1,2)" >
                                            </div>
                                        </div>

                                        <div class="col-3" >
                                            
                                            <div class=" d-flex flex-column mb-4">
                                                <label class="fs-6 fw-semibold mb-2 required">Deduction Percentage(%)</label>
                                                <div class="input-group" >
                                                    <input type="number" step="0.01" class="form-control" min="0" max="100" name="deduction_percentage[]" placeholder="Enter Deduction Percentage(%)" required="" value="0" >
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                            {{--end::Input group--}}
                                        </div>


                                        <div class="col-3" >
                                            
                                            <div class="d-flex flex-column mb-4">
                                                <label class="fs-6 fw-semibold mb-2 required">Tax Fix Amount </label>
                                                <input type="number" class="form-control"  name="deduction_amount[]" placeholder="Enter Tax Slab Fix Amount " required="">
                                            </div>
                                            {{--end::Input group--}}
                                        </div>

                                    </div>

                                </div>

                                <input type="hidden" name="slab_length" value="1">






                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">
                                
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="addTaxSlabButton" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>

                                <button class="btn btn-primary" type="button" onclick="addSlabRow()"><i class="fa fa-plus"></i></button>
                                <button class="btn btn-danger" type="button" onclick="deleteSlabRow()"><i class="fa fa-minus"></i></button>
                                <button class="btn btn-light-primary" type="button" onclick="fillPakistanSalariedSlabs()" title="Fills the Pakistan salaried individual slabs (Finance Act 2026, from 1 July 2026). Verify against the current Finance Act before saving.">Pakistan Salaried Slabs</button>

                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                {{--end::Modal - New Target--}}
            </form>
            


            
            @if(!empty($fetch_tax_slab))
            <form id="editTaxSlabForm" class="form">
                @csrf
                <div class="modal fade" id="editTaxSlab" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-900px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Edit Tax Slab</h4>
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                        <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                    </svg>
                                </span>
                                </button>
                            </div>
                            
                            
                            <div class="modal-body scroll-y pt-0 pb-15">

                                <div class="col-md-12 fv-row pt-10" >
                                    <label class="fs-6 fw-semibold mb-2 required">Fiscal Year</label>
                                    <input type="hidden" name="fiscal_start" value="{{$fetch_tax_slab[0]['ts_fiscal_start']}}" />
                                    <input type="text" class="form-control" value="{{CustomHelper::dateformatDFY($fetch_tax_slab[0]['ts_fiscal_start'])}} - {{CustomHelper::dateformatDFY($fetch_tax_slab[0]['ts_fiscal_end'])}}" >
                                </div>

                                <div id="edit_slab_div">

                                    @if(!empty($fetch_tax_slab))
                                        @php $i = 0; @endphp
                                        @foreach ($fetch_tax_slab as $tax_slab)
                                        @php $i ++; @endphp

                                        <div class="row {{($i != 1) ? '':'pt-10'}}" id="deleteEditSlab{{$i}}">

                                            <div class="col-3" >
                                                
                                                <div class="d-flex flex-column mb-4">
                                                    {!! ($i != 1) ? '' : '<label class="fs-6 fw-semibold mb-2 required">Lowest Yearly Income </label>' !!}
                                                    <input type="number" step="0.01" class="form-control lowest_yearly_income_edit{{$i}}" name="lowest_yearly_income[]" placeholder="Enter Lowest Yearly Inocme" required="" readonly value="{{$tax_slab['ts_lowest_yearly_income']}}" >
                                                </div>
                                                {{--end::Input group--}}
                                            </div>

                                            <div class="col-3" >
                                                <div class="d-flex flex-column mb-4">
                                                    {!! ($i != 1) ? '' : '<label class="fs-6 fw-semibold mb-2 required">Highest Yearly Income</label>' !!}
                                                    <input type="number" step="0.01" class="form-control ts_highest_yearly_income_edit{{$i}}" name="highest_yearly_income[]" placeholder="Enter Highest Yearly Income" required="" oninput="nextLowerIcomeEdit({{$i}},{{$i+1}})" value="{{$tax_slab['ts_highest_yearly_income']}}" >
                                                </div>
                                            </div>

                                            <div class="col-3" >
                                                
                                                <div class=" d-flex flex-column mb-4">
                                                    {!! ($i != 1) ? '' : '<label class="fs-6 fw-semibold mb-2 required">Deduction Percentage(%)</label>' !!}
                                                    <div class="input-group" >
                                                        <input type="number" step="0.01" class="form-control" min="0" max="100" name="deduction_percentage[]" placeholder="Enter Deduction Percentage(%)" required="" value="{{$tax_slab['ts_deduction_percentage']}}" >
                                                        <span class="input-group-text">%</span>
                                                    </div>
                                                </div>
                                                {{--end::Input group--}}
                                            </div>


                                            <div class="col-3" >
                                                
                                                <div class="d-flex flex-column mb-4">
                                                    {!! ($i != 1) ? '' : '<label class="fs-6 fw-semibold mb-2 required">Tax Fix Amount </label>'!!}
                                                    <input type="number" class="form-control"  name="deduction_amount[]" placeholder="Enter Tax Slab Fix Amount " required="" value="{{$tax_slab['ts_deduction_amount']}}" >
                                                </div>
                                                {{--end::Input group--}}
                                            </div>

                                        </div>

                                        @endforeach
                                    @endif


                                </div>

                                <input type="hidden" name="edit_slab_length" value="{{$i}}">






                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">
                                
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="editTaxSlabButton" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>

                                <button class="btn btn-primary" type="button" onclick="addSlabRowEdit()"><i class="fa fa-plus"></i></button>
                                <button class="btn btn-danger" type="button" onclick="deleteSlabRowEdit()"><i class="fa fa-minus"></i></button>

                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                {{--end::Modal - New Target--}}
            </form>
            @endif
            
            
        </div>
        
    </div>
    
</div>

@endsection

@push('js-link')

<script src="{{url('assets/js/tax-slab/tax-slab.js')}}?v=2"></script>

@endpush