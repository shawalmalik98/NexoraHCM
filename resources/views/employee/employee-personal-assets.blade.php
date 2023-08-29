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
      

        @if(!empty($get_all_vehicles))                      
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
                                            
                                            </tr>
                                        </thead>
                                        <tbody>
                                        
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
                                                            
                                                </tr>
                                            @endforeach
                                        
                                        </tbody>
                                        
                                    </table>
                                    
                            
                                </div>
                        
                                </div>
                        
                        </div>  
                    
                        
                    </div>
                
                </div>
            
            </div>
        @endif
       



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