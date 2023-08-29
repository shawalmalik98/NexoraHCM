@extends('layout.main')






@section('main-section')

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

            <div class="app-container col-12 d-flex flex-stack">
    
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
    
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">
                        Assets of {{ $employee_data[0]['employee_name']?? '' }}
                    </h1>
                </div>
                {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
    
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addDocument">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Add New Document</a>
                    </div>
                </div>
     --}}
            </div>
    
        </div>
                            
                              
        <div class="app-content flex-column-fluid">
            
            <div class="app-container">

                <div class="card mb-5 mb-xl-8">

                    <div class="card-header border-0 pt-5">
                       <h3 class="pt-2">Gadget Allotment</h3>
                        <div class="card-title">
                           
                            <div class="d-flex align-items-center position-relative my-1">
                                
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                                <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterAllotment">
                            </div>
                        </div>
                    
                    </div> 
                    
                    
                    <div class="card-body py-3">
                        
                        <div class="table-responsive popup-visible ">
                            
                            <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="table-responsive popup-visible ">
                                <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tableAllotment" aria-describedby="tableEmployee_info">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-4">Allotment Name</th>
                                            <th class="ps-4">Gadget Type</th>
                                            <th>Brand</th>
                                            <th>Model No</th>
                                            <th>Serial No</th>
                                            <th>MAC Address WIFI</th>
                                            <th>MAC Address LAN</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($gadget_allotments))
                                            @foreach ($gadget_allotments as $gadget_allotment)
                                                <tr>
                                                    <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($gadget_allotment['employee_id']) !!}</td>
                                                    <td>{{ $gadget_allotment['gadget_type'] }}</td>
                                                    <td>{{ $gadget_allotment['gadget_brand'] }}</td>
                                                    <td>{{ $gadget_allotment['gadget_model_no'] }}</td>
                                                    <td>{{ $gadget_allotment['gadget_serial_no'] }}</td>
                                                    <td>{{ $gadget_allotment['gadget_mac_wifi'] }}</td>
                                                    <td>{{ $gadget_allotment['gadget_mac_lan'] }}</td>
                                                    <td>
                                                        @if(in_array($gadget_allotment['allotment_id'],$gadget_allotments_active))
                                                        <span class="badge badge-light-success font-weight-bold">Active</span>
                                                        @else
                                                            <span class="badge badge-light-danger font-weight-bold">Deactive</span>
                                                        @endif
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
      

                               
        <div class="app-content flex-column-fluid">
            
            <div class="app-container">

                <div class="card mb-5 mb-xl-8">

                    <div class="card-header border-0 pt-5">
                        <h3 class="pt-2">Vehicle Allotment</h3>
                        <div class="card-title">
                           
                            <div class="d-flex align-items-center position-relative my-1">
                                <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                        <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                    </svg>
                                </span>
                                <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..." id="searchFilterVehicle">
                            </div>
                        </div>
                    
                    </div> 
                    
                    
                    <div class="card-body py-3">
                          
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                            <div class="table-responsive popup-visible ">
                                <table class="table align-middle gs-0 gy-4 dataTable no-footer" id="tableVehicle" aria-describedby="tableEmployee_info">
                                    <thead>
                                        <tr class="fw-bold text-muted bg-light">
                                            <th class="ps-4">Ref ID</th>
                                            <th>Vehicle Company Name</th>
                                            <th>Vehicle Name</th>
                                            <th>Vehicle Model</th>
                                            <th>Vehicle Reg.</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($get_all_vehicles))
                                            @foreach ($get_all_vehicles as $vehicle)
                                                <tr>
                                                    <td class="ps-8">{{ $vehicle['vehicle_id'] }}</td>
                                                    <td>{{ $vehicle['vehicle_company_name'] }}</td>
                                                    <td>{{ $vehicle['vehicle_name'] }}</td>
                                                    <td>{{ $vehicle['vehicle_model'] }}</td>
                                                    <td>{{ $vehicle['vehicle_number'] }}</td>
                                                    <td>
                                                        @if (!empty($vehicle['allotment_cancellation_date']) && $vehicle['allotment_cancellation_date'] != '')
                                                        
                                                        <span class="badge badge-light-danger font-weight-bold">Deactive</span>
                                                        @else
                                                        <span class="badge badge-light-success font-weight-bold">Active</span>
                                                        @endif
                                                    </td>
                                                            
                                                    <td>
                                                        <a href="{{ url('vehicles/vehicle-detail/'.$vehicle['vehicle_id']) }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="Vehicle detail"> 
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
            
            </div>
        
        </div>
       



    </div>

@endsection



@push('js-link')
<script>
    $(function(){
		dataTable = $('#tableVehicle').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterVehicle').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
        dataTable = $('#tableAllotment').DataTable({
            order: false,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            iDisplayLength: 50
        });
		$('#searchFilterAllotment').keyup(function(){
			dataTable.search($(this).val()).draw();
		})
	});    
</script>


    
@endpush