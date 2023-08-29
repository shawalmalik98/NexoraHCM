@extends('layout.main')






@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">All Gadgets</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                
                <div class="m-0">
                    <a href="{{url('gadgets-inventory/add-new-gadget')}}" class="btn btn-sm btn-primary">											
                        <span class="svg-icon svg-icon-2">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                            </svg>
                        </span>
                       Add New Gadget</a>   
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
                    <div class="card-toolbar">
                        @php $filter = 'btn-color-primary'; @endphp
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('gadget_type')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1">
                            
                            <div class="px-7 py-5">
                                <div class="fs-5 text-dark fw-bold">Filter Options</div>
                            </div>

                            <div class="separator border-gray-200"></div>
                            <form action="{{ url('gadgets-inventory') }}" method="GET">
                                <div class="px-7 py-5">
                                    
                                    <div class="mb-10">
                                        
                                        <label class="form-label fw-semibold">Gadget Type :</label>
                                        
                                        <div>
                                            <select class="form-select " data-kt-select2="true" data-placeholder="Select Name" name="gadget_type"
                                            required="">
                                            <option value="Computer" @if (!empty(Request::get('gadget_type')) && Request::get('gadget_type') == 'Computer') selected @endif>Computer</option>
                                            <option value="Laptop" @if (!empty(Request::get('gadget_type')) && Request::get('gadget_type') == 'Laptop') selected @endif>Laptop</option>
                                            <option value="Mobile" @if (!empty(Request::get('gadget_type')) && Request::get('gadget_type') == 'Mobile') selected @endif>Mobile</option>
                                        </select>
                                        </div>
                                        
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('gadget_type')))
                                           
                                        <a href="{{ url('gadgets-inventory') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2 "
                                            >Reset Filter</a>
                                        @endif
                                        <button type="reset" class="btn btn-sm btn-light btn-active-light-primary me-2" data-kt-menu-dismiss="true">Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>

                </div> 
   
                <div class="card-body py-3">
                    
                    <div class="table-responsive popup-visible ">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer "><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                            
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4 min-w-50px sorting">Gadget Type</th>
                                        <th>Brand</th>
                                        <th>Model No</th>
                                        <th>Serial No</th>
                                        <th>MAC Address WIFI</th>
                                        <th>MAC Address LAN</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                    @if (!empty($all_gadgets))
                                        @foreach ($all_gadgets as $all_gadget)
                                            <tr>
                                                <td class="ps-4">{{ $all_gadget['gadget_type'] }}</td>
                                                <td>{{ $all_gadget['gadget_brand'] }}</td>
                                                <td>{{ $all_gadget['gadget_model_no'] }}</td>
                                                <td>{{ $all_gadget['gadget_serial_no'] }}</td>
                                                <td>{{ $all_gadget['gadget_mac_wifi'] }}</td>
                                                <td>{{ $all_gadget['gadget_mac_lan'] }}</td>
                                                <td>
                                                    @if( $all_gadget['gadget_status'] == 1)
                                                        <span class="badge badge-light-success font-weight-bold">Alloted</span>
                                                    @else
                                                        <span class="badge badge-light-danger font-weight-bold">Free Gadget</span>
                                                    
                                                    @endif

                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ url('gadgets-inventory/gadget-detail') . '/' . $all_gadget['gadget_id']  }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Gadget Detail">
                                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                    
                                                    <a href="#" onclick="deactivate_confirm('{{ url('gadgets-inventory/discard_gadget') . '/' . $all_gadget['gadget_id'] }}')" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" data-bs-toggle="tooltip" title="Delete Gadget">
                                                        <span class="svg-icon svg-icon-3">
                                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
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
            
        </div>
        
    </div>

    <div class="modal fade" id="modal-danger" tabindex="-1" aria-hidden="true">
        
        <div class="modal-dialog modal-dialog-top">
             
            <div class="modal-content bg-rounded">
                <div class="modal-header">
                    <h4 class="modal-title pl-4">Confirmation Alert</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1 bg-white">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure to you want to delete this gadget ?</p>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Cancel</button>
                    <a href="javascript:void(0);" class="btn btn-sm btn-primary" id="delete_button">Delete</a>
                </div>
            </div>

            
        </div>
        
    </div>
    
</div>

@endsection



@push('js-link')
<script>
   
    function deactivate_confirm(delete_url) {
        $('#modal-danger').modal('show');
        document.getElementById('delete_button').setAttribute("href", delete_url);
    }
    
</script>


    
@endpush