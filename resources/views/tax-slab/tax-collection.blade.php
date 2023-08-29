@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Tax Collection ({{date("F", mktime(0, 0, 0, $month, 10))}}-{{$year}}) </h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">

            <div class="row g-6 g-xl-9">
                    
                <div class="col-xl-4">
                                        
                    <div class="card bg-body hoverable card-xl-stretch mb-xl-8">
                        
                        <div class="card-body">
                            <div class="text-gray-900 fw-bold fs-2 mb-2 mt-5" data-kt-countup="true" data-kt-countup-value="{{(empty($total_tax_collection)) ? '0' : $total_tax_collection }}"></div>
                            <div class="fw-semibold text-gray-400">Total tax collection of {{date("F", mktime(0, 0, 0, $month, 10))}}-{{$year}}</div>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
           
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
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('m')) ? $filter : '' }} {{!empty(Request::get('y')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter
                        </a>
                       
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                            <form action="{{ url('/tax-collection') }}" method="GET">

                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                </div>

                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5">
                                        
                                    <div class="mb-2">
                                            
                                        <label class="form-label fw-semibold">Month:</label>
                                            
                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Month"name="m"
                                            required="required">
                                            <option value="">Select Month</option>
                                            <option value="01" @if (!empty(Request::get('m')) && Request::get('m') == '01') selected @elseif (empty(Request::get('m')) && date('m') == '01') selected @endif>January
                                            </option>
                                            <option value="02" @if (!empty(Request::get('m')) && Request::get('m') == '02') selected @elseif (empty(Request::get('m')) && date('m') == '02') selected @endif>February
                                            </option>
                                            <option value="03" @if (!empty(Request::get('m')) && Request::get('m') == '03') selected @elseif (empty(Request::get('m')) && date('m') == '03') selected @endif>March
                                            </option>
                                            <option value="04" @if (!empty(Request::get('m')) && Request::get('m') == '04') selected @elseif (empty(Request::get('m')) && date('m') == '04') selected @endif>April
                                            </option>
                                            <option value="05" @if (!empty(Request::get('m')) && Request::get('m') == '05') selected @elseif (empty(Request::get('m')) && date('m') == '05') selected @endif>May
                                            </option>
                                            <option value="06" @if (!empty(Request::get('m')) && Request::get('m') == '06') selected @elseif (empty(Request::get('m')) && date('m') == '06') selected @endif>June
                                            </option>
                                            <option value="07" @if (!empty(Request::get('m')) && Request::get('m') == '07') selected @elseif (empty(Request::get('m')) && date('m') == '07') selected @endif>July
                                            </option>
                                            <option value="08" @if (!empty(Request::get('m')) && Request::get('m') == '08') selected @elseif (empty(Request::get('m')) && date('m') == '08') selected @endif>August
                                            </option>
                                            <option value="09" @if (!empty(Request::get('m')) && Request::get('m') == '09') selected @elseif (empty(Request::get('m')) && date('m') == '09') selected @endif>September
                                            </option>
                                            <option value="10" @if (!empty(Request::get('m')) && Request::get('m') == '10') selected @elseif (empty(Request::get('m')) && date('m') == '10') selected @endif>October
                                            </option>
                                            <option value="11" @if (!empty(Request::get('m')) && Request::get('m') == '11') selected @elseif (empty(Request::get('m')) && date('m') == '11') selected @endif>November
                                            </option>
                                            <option value="12" @if (!empty(Request::get('m')) && Request::get('m') == '12') selected @elseif (empty(Request::get('m')) && date('m') == '12') selected @endif>December
                                            </option>
                                        </select>
                                    </div>

                                    <div class="mb-5">
                                        
                                        <label class="form-label fw-semibold">Select Year</label>
                                        
                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Year" name="y" required="required">
                                            <option value="">Select Year</option>
                                            @if(!empty($dynamic_years))
                                                @foreach ($dynamic_years as $year_list)
                                                    <option value="{{$year_list}}" @if (!empty(Request::get('y')) && Request::get('y') == $year_list) selected @elseif(empty(Request::get('y')) && date('Y') == $year_list) selected  @endif>{{$year_list}}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                       
                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('m')) && !empty(Request::get('y')))
                                        
                                        <a href="{{ url('tax-collection') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
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
                    
                    <div class="dt-buttons btn-group flex-wrap"><a href="{{url('tax-slab/download-tax-collection-pdf?month='.$month.'&year='.$year )}}" class="btn btn-secondary buttons-excel buttons-html5 mb-5 btn-sm btn-primary" tabindex="0" target="_blank" ><span>View Tax Collection Record</span></a> </div>

                    <div class="dt-buttons btn-group flex-wrap"><a href="{{url('tax-slab/download-tax-collection-pdf?month='.$month.'&year='.$year.'&download=TRUE')}}" class="btn btn-secondary buttons-excel buttons-html5 mb-5 btn-sm btn-primary" tabindex="0" target="_blank" ><span>View Tax Collection Record</span></a> </div>

                    <div class="table-responsive popup-visible ">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="system_datatable" aria-describedby="tableEmployee_info">
                                
                                
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="text-center" >Slip No.</th>
                                        <th>Employee Name</th>
                                        <th>Pay Period</th>
                                        <th>Net Salary</th>
                                        <th>Tax Amount</th>
                                        <th>Generation Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($fetch_employee_tax))
                                        @foreach ($fetch_employee_tax as $employee_tax)
                                            <tr>
                                                <td class="ps-8">{{ $employee_tax['pc_id'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($employee_tax['pc_employee_id']) !!}</td>
                                                <td>{{ date('M-Y',strtotime($employee_tax['pc_year'].'-'.$employee_tax['pc_month'].'-'.'01')) }} </td>
                                                
                                                <td>{{ $employee_tax['pc_employee_net_salary'] }}</td>
                                                <td>{{ $employee_tax['total_tax_collection'] }}</td>

                                                <td>{{ date('d-M-Y',strtotime($employee_tax['pc_created_at'])) }}</td>
                                                
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            
                            </table>
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
            
        </div>
        
    </div>
    
</div>

@endsection



@push('js-link')
<script>
    
</script>
@endpush