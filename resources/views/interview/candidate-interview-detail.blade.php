@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Candidate Interviews</h1>   
            </div>
           
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
            
            <div class="card mb-5 mb-xl-8">
                <div class="card-header border-0 pt-5">

                    <div class="card-title">
                        <div class="d-flex align-items-center my-1">
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
                    
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer ">
                        <div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                                                                            
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Ref ID</th>
                                        <th>Candidate Name</th>
                                        <th>1st Interviewer</th>
                                        <th>2nd Interviewer</th>
                                        <th>Interview Timing</th>
                                        <th>Interview Dept.</th>
                                        <th>Interviewer Status</th>
                                        <th>Interviewer Remarks</th>
                                        <th>Action</th>
                                      
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($interview_candidates))
                                        @foreach($interview_candidates as $interview)
                                            <tr>
                                                <td class="ps-4">{{ $interview['interview_id'] }}</td>
                                                <td>{{ $interview['candidate_name'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer1']); !!}   
                                                </td>
                                                <td>@if(!empty($interview['interview_interviewer2']))
                                                    {!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer2']) !!}
                                                    @else
                                                        <span >N\A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $interview['interview_date'] }} - {{ date('h:i A', strtotime($interview['interview_time'])) }}</td>
                                                <td>{{ $interview['department_name'] }}</td>
                                                <td>
                                                    <span class="badge badge-light-success text-center d-inline-block font-weight-bold">{{$interview['interview_status_level']}}</span>
                                                </td>
                                                <td>
                                                    @if (empty($interview['interview_interviewer_remarks']) && $interview['interview_interviewer_remarks'] == '')
                                                        <span class="badge badge-light-danger text-center d-inline-block font-weight-bold">Not given</span>
                                                    @else
                                                            <span class="badge badge-light-success text-center d-inline-block font-weight-bold">Given</span>
                                                    @endif
                                                </td>
                                            
                                                <td>
                                                    <a href="{{ url('interviews/interviewdetail') . '/' . $interview['interview_id']}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Interview detail">
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
            
        </div>
        
    </div>
    
</div>

@endsection
