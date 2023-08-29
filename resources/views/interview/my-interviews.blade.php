@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My Interviews</h1>   
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
                        
                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('candidate_name')) ? $filter : '' }} {{!empty(Request::get('interview_date')) ? $filter : '' }} {{!empty(Request::get('interview_status')) ? $filter : '' }} {{!empty(Request::get('interview_type')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                    
                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                            <form action="{{ url('my-interviews') }}" method="GET">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                </div>

                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5">
                                    
                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Candidate Name</label>
                                        
                                        <div>
                                            <select class="form-select" data-kt-select2="true" name="candidate_name">
                                                <option value="">Select Name</option>
                                                @php $candidates_name = array(); @endphp
                                                @if(!empty($interviews_candidate_names))
                                                    @foreach($interviews_candidate_names as $data)
                                                        @if(!in_array($data['candidate_id'], $candidates_name))
                                                        
                                                            @php array_push($candidates_name, $data['candidate_id']); @endphp
                                                        
                                                            <option value="{{$data['candidate_id']}}"@if (Request::get('candidate_name') == $data['candidate_id']) selected @endif>{{$data['candidate_name']}}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        
                                    </div>
                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Interview Date</label>
                                        
                                        <div class=" d-flex align-items-center">
                                        
                                                
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                                    <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                                    <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            {{--end::Svg Icon--}}
                                            {{--end::Icon--}}
                                            {{--begin::Datepicker--}}
                                            <input type="date" class="form-control  ps-12 " placeholder="Select a date" name="interview_date" @if(!empty(Request::get('interview_date'))) value="{{ Request::get('interview_date') }}" @else value={{date('Y-m-d')}} @endif>
                                            {{--end::Datepicker--}}
                                        </div>
                                        
                                    </div>

                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Interviewer Status</label>
                                        
                                        <div>
                                            <select class="form-select" data-kt-select2="true" name="interview_status" >
                                                    <option value="">Select status</option>
                                                    @if(!empty($interview_status_level))
                                                        @foreach($interview_status_level as $data)
                                                        <option value="{{$data['interview_status_level']}}" @if (Request::get('interview_status') == $data['interview_status_level']) selected @endif>{{$data['interview_status_level']}}</option>
                                                        @endforeach
                                                    @endif
                                            </select>
                                        </div>
                                        
                                    </div>

                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Interviewer Type</label>
                                        
                                        <div>
                                            <select class="form-select" data-kt-select2="true" name="interview_type" >
                                                <option value="">Select Type</option>
                                                <option value="Physical" @if (Request::get('interview_type') == 'Physical') selected @endif>Physical</option>
                                                <option value="Online" @if (Request::get('interview_type') == 'Online') selected @endif>Online</option>
                                
                                            </select>
                                        </div>
                                        
                                    </div>
                                
                                    <div class="d-flex justify-content-end">
                                        @if (!empty(Request::get('candidate_name')) || !empty(Request::get('interview_date')) || !empty(Request::get('interview_status')) || !empty(Request::get('interview_type')))
                                            
                                        <a href="{{ url('my-interviews') }}" class="btn btn-sm btn-danger  me-2"
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
                    
                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer ">
                        <div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable">
                                                                                            
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-6">Ref ID</th>
                                        <th>Candidate Name</th>
                                        <th>1st Interviewer</th>
                                        <th>2nd Interviewer</th>
                                        <th>Timing</th>
                                        <th>Deptartment</th>
                                        <th>1st Interviewer Remarks</th>
                                        <th>2nd Interviewer Remarks</th>
                                        <th>Interviewer Status</th>
                                        <th>Interview Type</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (!empty($interviews))
                                        @foreach ($interviews as $interview)
                                            <tr @if(request()->get('interview_id') != '' && $interview['interview_id'] == request()->get('interview_id')) style="background-color:bisque;" @endif>
                                                <td class="ps-5">{{ $interview['interview_id'] }}</td>
                                                <td>{{ $interview['candidate_name'] }}</td>
                                                <td>{!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer1']) !!}</td>
                                                <td>
                                                    @if(!empty($interview['interview_interviewer2']))
                                                        {!! CustomHelper::getEmpProfileDiv($interview['interview_interviewer2']) !!}
                                                    @else
                                                        <span >N\A</span>
                                                    @endif
                                                </td>
                                                <td>{{ $interview['interview_date'] }} - {{ date('h:i A', strtotime($interview['interview_time'])) }}</td>
                                                <td>{{ $interview['department_name'] }}</td>
                                                <td class="text-center">
                                                    @if (empty($interview['interview_interviewer_remarks']) && $interview['interview_interviewer_remarks'] == '')
                                                        <span class="badge badge-light-danger text-center d-inline-block font-weight-bold">Not given</span>
                                                    @else
                                                        <span class="badge badge-light-success text-center d-inline-block font-weight-bold">Given</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (empty($interview['interview_interviewer_remarks2']) && $interview['interview_interviewer_remarks2'] == '')
                                                        <span class="badge badge-light-danger text-center d-inline-block font-weight-bold">Not given</span>
                                                    @else
                                                        <span class="badge badge-light-success text-center d-inline-block font-weight-bold">Given</span>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span class="badge badge-light-success text-center d-inline-block font-weight-bold">{{$interview['interview_status_level']}}</span>
                                                </td>

                                                <td>
                                                     
                                                    <span class="badge badge-light-success text-center d-inline-block font-weight-bold">{{$interview['interview_type']}}</span>
                                                   
                                                </td>
                                                <td>
                                                    <a href="{{ url('myinterviewdetail') . '/' . $interview['interview_id'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Interview detail">
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
