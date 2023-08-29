@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">All Meetings</h1>   
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
                </div> 
                <div class="card-body py-3">
                    
                    <div class="table-responsive popup-visible ">
                        
                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer "><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Ref ID</th>
                                        <th>Meeting Title</th>
                                        <th>Meeting Host</th>
                                        <th>Meeting Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @if (!empty($meetings))
                                            @foreach ($meetings as $meeting)
                                                <tr>
                                                    <td class="ps-4">
                                                        @if ($meeting['meeting_id'] != '')
                                                            {{ $meeting['meeting_id'] }}
                                                        @else
                                                            Not Available
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($meeting['meeting_title'] != '')
                                                            {{ $meeting['meeting_title'] }}
                                                        @else
                                                            Not Available
                                                        @endif
                                                    </td>
                                                    <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($meeting['employee_id']) !!}</td>
                                                    <td>
                                                        @if ($meeting['meeting_time'] != '')
                                                            {{ $meeting['meeting_time'] }}
                                                        @else
                                                            Not Available
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('meeting-detail') }}/{{ $meeting['meeting_id'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Meeting detail">
                                                            <i class="fa fa-eye" aria-hidden="true"></i>
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
            
            
		
            {{--end::Modal - New Target--}}
            
        </div>
        
    </div>
    
</div>


@endsection
