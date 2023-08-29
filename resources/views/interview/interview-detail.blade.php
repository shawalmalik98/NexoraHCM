@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Interview Detail</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
                @if(empty($employee_onboarding[0]['onboarding_candidate_id']))
                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#hired_interviewer">Confirm This Candidate</a>
                    </div>

                    <div class="m-0">
                        <a href="javascript:void(0)" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#schedule_interview">											
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2" rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1" fill="currentColor"></rect>
                                </svg>
                            </span>
                            Re-Schedule Interview</a>   
                    </div>
                @endif
               
                <div class="m-0">
                    <a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#interviewer_status">Change Interview Status</a>
                </div>

                <div class="m-0">
                     <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
                </div>
         
            </div>
           
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
      
        <div class="app-container">
            <div class="row mb-5">
                <div class="col-md-5 card scroll scroll-pull" data-wheel-propagation="true" >
                    <div class="card card-flush">
                        <div class="card-body pt-5" >
                            {{-- begin::Nav --}}
                            <ul class="nav nav-pills nav-pills-custom row position-relative mx-0 mb-9"
                                id="custom-tabs-four-tab" role="tablist">

                                <li class="nav-item col mx-0 p-0">
                                    <a class="nav-link active d-flex justify-content-center w-100 border-0 h-100"
                                        id="custom-tabs-interview-detail-tab" data-bs-toggle="pill"
                                        href="#custom-tabs-interview-detail" role="tab"
                                        aria-controls="custom-tabs-interview-detail" aria-selected="true">
                                        <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Interview Detail</span>
                                        <span
                                            class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                    </a>
                                </li>

                                <li class="nav-item col mx-0 px-0">
                                    <a class="nav-link d-flex justify-content-center w-100 border-0 h-100"
                                        id="custom-tabs-interview-status-tab" data-bs-toggle="pill"
                                        href="#custom-tabs-interview-status" role="tab"
                                        aria-controls="custom-tabs-interview-status" aria-selected="false">
                                        <span class="nav-text text-gray-800 fw-bold fs-6 mb-3">Status Log(s)</span>
                                        <span
                                            class="bullet-custom position-absolute z-index-2 bottom-0 w-100 h-4px bg-primary rounded"></span>
                                    </a>
                                </li>
                                <span class="position-absolute z-index-1 bottom-0 w-100 h-5px bg-light rounded"></span>
                            </ul>

                            <div class="tab-content"  id="custom-tabs-four-tabContent">

                                <div class="tab-pane fade show active" id="custom-tabs-interview-detail" role="tabpanel"
                                    aria-labelledby="custom-tabs-interview-detail-tab">

                                    <div class="card-body p-0">
                            
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview ID</span>
                                                </div>
                                            <span>{{ $interview[0]['interview_id'] }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>

                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Requisition Reference</span>
                                                </div>
                                                    <span> {{ $interview[0]['requisition_reference'] }} - {{$interview[0]['requisition_job_title']}}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                         
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Date</span>
                                                </div>
                                            <span>{{ $interview[0]['interview_date'] }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Time</span>
                                                </div>
                                            <span>{{ date('h:i A', strtotime($interview[0]['interview_time'] )) }}</span>
                                            </div>
                                        </div>
                                        
                                        
                                         <div class="separator separator-dashed my-4"></div>
                                        
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Duration Time</span>
                                                </div>
                                            <span>{{ ($interview[0]['interview_duration'] ) }} Minutes</span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Department</span>
                                                </div>
                                            <span>{{ $interview[0]['department_name'] }}</span>
                                            </div>
                                        </div>
                                    
                                        <div class="separator separator-dashed my-4"></div>
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Job Title</span>
                                                </div>
                                            <span>{{ $interview[0]['interview_jobtitle'] }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">1st Interviewer</span>
                                                </div>
                                            <span>{!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer1']) !!}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">2nd Interviewer</span>
                                                </div>
                                            <span>
                                                @if(!empty($interview) && $interview[0]['interview_interviewer2'])
                                                    {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer2']); !!}
                                                @else
                                                    N\A
                                                @endif
                                               
                                            </span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Round</span>
                                                </div>
                                            <span>{{ $interview[0]['interview_round'] }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                             
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                 <div class="flex-grow-1 me-2">
                                                     <span class="text-gray-800  fs-6 fw-bold">Room Name</span>
                                                 </div>
                                                <span>
                                                    @if (!empty($interview[0]['room_name']))
                                                        {{ $interview[0]['room_name'] }}                                   
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Candidate Name</span>
                                                </div>
                                            <span>{{ $interview[0]['candidate_name'] }}</span>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Candidate Email</span>
                                                </div>
                                            <span>{{ $interview[0]['candidate_email']??'N\A' }}</span>
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Candidate CV</span>
                                                </div>
                                            <a href="{{ url('download-candidate-cv?path=interviews/'.$interview[0]['interview_candidate_cv']) }}&title=Download Candidate CV" target="_blank" class="btn btn-success">Download File</a>
                                            </div>
                                        </div>
                                        
                                        
                                         <div class="separator separator-dashed my-4"></div>

                                         <div class="d-flex flex-stack">
                                             <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                 <div class="flex-grow-1 me-2">
                                                     <span class="text-gray-800  fs-6 fw-bold">Candidate Contact No</span>
                                                 </div>
                                                
                                                
                                                <span>{{ $interview[0]['candidate_contact_no'] }}</span>
                                               
                                                
                                             </div>
                                            
                                         </div>
                                        
                                        
                                         <div class="separator separator-dashed my-4"></div>
                                        
                                         
                                         <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                 <div class="flex-grow-1 me-2">
                                                     <span class="text-gray-800  fs-6 fw-bold">Current Salary</span>
                                                 </div>
                                                
                                                
                                                <span>
                                                    @if (!empty($interview[0]['interview_current_salary']))
                                                        {{ $interview[0]['interview_current_salary'] }}                                   
                                                    @else
                                                        N/A
                                                    @endif

                                                </span>
                                               
                                                
                                            </div>
                                            
                                         </div>
                                        
                                        
                                         <div class="separator separator-dashed my-4"></div>
                                        
                                         
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Expected Salary</span>
                                                </div>
                                                
                                                <span>
                                                    @if (!empty($interview[0]['interview_expected_salary']))
                                                        {{ $interview[0]['interview_expected_salary'] }}                                   
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                                
                                            </div>
                                        
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Notice Period</span>
                                                </div>
                                                
                                                <span>
                                                    @if (!empty($interview[0]['interview_noticepriod']))
                                                        {{ $interview[0]['interview_noticepriod'] }}                                   
                                                    @else
                                                        N/A
                                                    @endif
                                                </span>
                                                
                                            </div>
                                            
                                        </div>
                                        
                                        <div class="separator separator-dashed my-4"></div>
                                        
                                        <div class="d-flex flex-stack">
                                             <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                             
                                                 <div class="flex-grow-1 me-2">
                                                     <span class="text-gray-800  fs-6 fw-bold">Created At</span>
                                                 </div>
                                             
                                             
                                             <span>{{ $interview[0]['interview_scheduled_time'] }}</span>
                                             
                                             
                                             </div>
                                         
                                        </div>
            
                                        
                                        <div class="separator separator-dashed my-4"></div>
            
                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                            
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Status</span>
                                                </div>
                                            
                                                 <span>{{ $interview[0]['interview_status_level'] }}</span>
                                            
                                            </div>
                                        
                                        </div>

                                       
                                       <div class="separator separator-dashed my-4"></div>

                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                            
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Type</span>
                                                </div>
                                            
                                            
                                                <span>{{ $interview[0]['interview_type'] }}</span>
                                            
                                            
                                            </div>
                                    
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>

                                        <div class="d-flex flex-stack">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                            
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Interview Link</span>
                                                </div>

                                                <div class="ms-2">
                                                    <button type="button" class="btn btn-sm btn-icon btn-light btn-active-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                        {{--begin::Svg Icon | path: icons/duotune/coding/cod007.svg--}}
                                                        <span class="svg-icon svg-icon-5 m-0"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path opacity="0.3" d="M18.4 5.59998C18.7766 5.9772 18.9881 6.48846 18.9881 7.02148C18.9881 7.55451 18.7766 8.06577 18.4 8.44299L14.843 12C14.466 12.377 13.9547 12.5887 13.4215 12.5887C12.8883 12.5887 12.377 12.377 12 12C11.623 11.623 11.4112 11.1117 11.4112 10.5785C11.4112 10.0453 11.623 9.53399 12 9.15698L15.553 5.604C15.9302 5.22741 16.4415 5.01587 16.9745 5.01587C17.5075 5.01587 18.0188 5.22741 18.396 5.604L18.4 5.59998ZM20.528 3.47205C20.0614 3.00535 19.5074 2.63503 18.8977 2.38245C18.288 2.12987 17.6344 1.99988 16.9745 1.99988C16.3145 1.99988 15.661 2.12987 15.0513 2.38245C14.4416 2.63503 13.8876 3.00535 13.421 3.47205L9.86801 7.02502C9.40136 7.49168 9.03118 8.04568 8.77863 8.6554C8.52608 9.26511 8.39609 9.91855 8.39609 10.5785C8.39609 11.2384 8.52608 11.8919 8.77863 12.5016C9.03118 13.1113 9.40136 13.6653 9.86801 14.132C10.3347 14.5986 10.8886 14.9688 11.4984 15.2213C12.1081 15.4739 12.7616 15.6039 13.4215 15.6039C14.0815 15.6039 14.7349 15.4739 15.3446 15.2213C15.9543 14.9688 16.5084 14.5986 16.975 14.132L20.528 10.579C20.9947 10.1124 21.3649 9.55844 21.6175 8.94873C21.8701 8.33902 22.0001 7.68547 22.0001 7.02551C22.0001 6.36555 21.8701 5.71201 21.6175 5.10229C21.3649 4.49258 20.9947 3.93867 20.528 3.47205Z" fill="currentColor"></path>
                                                            <path d="M14.132 9.86804C13.6421 9.37931 13.0561 8.99749 12.411 8.74695L12 9.15698C11.6234 9.53421 11.4119 10.0455 11.4119 10.5785C11.4119 11.1115 11.6234 11.6228 12 12C12.3766 12.3772 12.5881 12.8885 12.5881 13.4215C12.5881 13.9545 12.3766 14.4658 12 14.843L8.44699 18.396C8.06999 18.773 7.55868 18.9849 7.02551 18.9849C6.49235 18.9849 5.98101 18.773 5.604 18.396C5.227 18.019 5.0152 17.5077 5.0152 16.9745C5.0152 16.4413 5.227 15.93 5.604 15.553L8.74701 12.411C8.28705 11.233 8.28705 9.92498 8.74701 8.74695C8.10159 8.99737 7.5152 9.37919 7.02499 9.86804L3.47198 13.421C2.52954 14.3635 2.00009 15.6417 2.00009 16.9745C2.00009 18.3073 2.52957 19.5855 3.47202 20.528C4.41446 21.4704 5.69269 21.9999 7.02551 21.9999C8.35833 21.9999 9.63656 21.4704 10.579 20.528L14.132 16.975C14.5987 16.5084 14.9689 15.9544 15.2215 15.3447C15.4741 14.735 15.6041 14.0815 15.6041 13.4215C15.6041 12.7615 15.4741 12.108 15.2215 11.4983C14.9689 10.8886 14.5987 10.3347 14.132 9.86804Z" fill="currentColor"></path>
                                                            </svg>
                    
                                                        </span>
                                                    </button>
                                                    {{--end::Svg Icon--}}            
                                                    {{--begin::Menu--}}
                                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-300px" data-kt-menu="true" style="">
                                                        {{--begin::Card--}}
                                                        <div class="card card-flush">
                                                            <div class="card-body p-5">
                                                                {{--begin::Link--}}
                                                                    <div class="d-flex flex-column text-start">
                                                                        <div class="d-flex mb-3">
                                                                                {{--begin::Svg Icon | path: icons/duotune/arrows/arr085.svg--}}
                                                                            <span class="svg-icon svg-icon-2 svg-icon-success me-3"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                <path d="M9.89557 13.4982L7.79487 11.2651C7.26967 10.7068 6.38251 10.7068 5.85731 11.2651C5.37559 11.7772 5.37559 12.5757 5.85731 13.0878L9.74989 17.2257C10.1448 17.6455 10.8118 17.6455 11.2066 17.2257L18.1427 9.85252C18.6244 9.34044 18.6244 8.54191 18.1427 8.02984C17.6175 7.47154 16.7303 7.47154 16.2051 8.02984L11.061 13.4982C10.7451 13.834 10.2115 13.834 9.89557 13.4982Z" fill="currentColor"></path>
                                                                                </svg>
                                                                            </span>
                                                                            {{--end::Svg Icon--}}        
                                                                            <div class="fs-6 text-dark">Share Link Generated</div>           
                                                                        </div>  

                                                                        <div class="row">
                                                                        
                                                                            <div class="col-md-10">
                                                                                <input type="text" id="url"  class="form-control form-control-sm" value="{{$interview[0]['interview_link'] ?? 'N\A' }}" />
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <button  class="btn btn-active-color-primary btn-icon btn-sm btn-outline-light" onclick="Copy();">
                                                                                    {{--begin::Svg Icon | path: icons/duotune/general/gen054.svg--}}
                                                                                    <span class="svg-icon svg-icon-2"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                        <path opacity="0.5" d="M18 2H9C7.34315 2 6 3.34315 6 5H8C8 4.44772 8.44772 4 9 4H18C18.5523 4 19 4.44772 19 5V16C19 16.5523 18.5523 17 18 17V19C19.6569 19 21 17.6569 21 16V5C21 3.34315 19.6569 2 18 2Z" fill="currentColor"></path>
                                                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7857 7.125H6.21429C5.62255 7.125 5.14286 7.6007 5.14286 8.1875V18.8125C5.14286 19.3993 5.62255 19.875 6.21429 19.875H14.7857C15.3774 19.875 15.8571 19.3993 15.8571 18.8125V8.1875C15.8571 7.6007 15.3774 7.125 14.7857 7.125ZM6.21429 5C4.43908 5 3 6.42709 3 8.1875V18.8125C3 20.5729 4.43909 22 6.21429 22H14.7857C16.5609 22 18 20.5729 18 18.8125V8.1875C18 6.42709 16.5609 5 14.7857 5H6.21429Z" fill="currentColor"></path>
                                                                                        </svg>
                                                                                    </span>
                                                                                    {{--end::Svg Icon--}}
                                                                                </button>
                                                                            </div> 
                                                                       </div> 
                                                                        
                                                                        <div class="text-muted fw-normal mt-2 fs-8 px-2">Copy Link.</div> 
                                                                        
                                                                    </div>
                                                                    {{--end::Link--}}
                                                                </div>
                                                            </div>
                                                            {{--end::Card--}}
                                                    </div>
                                                </div>                                            
                                            
                                            </div>
                                    
                                        </div>

                                        <div class="separator separator-dashed my-4"></div>
        
                                        <div class="d-flex flex-stack  mt-10">
                                            <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                             
                                                <div class="flex-grow-1 me-2">
                                                    <span class="text-gray-800  fs-6 fw-bold">Scheduled By</span>
                                                </div>

                                                <span>
                                                {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_created_by']); !!}
                                                </span>
                                             
                                            </div>
                                         
                                        </div>
                                        
                                    </div>

                                </div>

                                <div class="tab-pane fade" id="custom-tabs-interview-status" role="tabpanel"
                                    aria-labelledby="custom-tabs-vehicle-maintenance-tab">
                                    <div class="card-body p-0">

                                        @if(!empty($interview_status))
                                            @foreach ($interview_status as $interview_status )
                                                <div class="d-flex flex-stack">
                                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                                        {{--begin:Author--}}
                                                        <div class="flex-grow-1 me-2">
                                                            <span class="text-gray-800  fs-6 fw-bold">
                                                                {!! CustomHelper::getEmpProfileDiv($interview_status['interview_status_created_by']) !!}
                                                            </span>
                                                        </div>
                                                        @if($interview_status['interview_status_level'] == 'Interview Scheduled')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Interview Scheduled</span>
                                                        @elseif($interview_status['interview_status_level'] == 'Hold for more interviews')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Hold for more Interviews</span>
                                                        @elseif($interview_status['interview_status_level'] == 'Candidate Shortlisted')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Candidate Shortlisted</span>
                                                        @elseif($interview_status['interview_status_level'] == 'Candidate Rejected')
                                                            <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Candidate Rejected</span>
                                                        @elseif($interview_status['interview_status_level'] == 'Offer Placed')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Offer Placed</span>
                                                        @elseif($interview_status['interview_status_level'] == 'Offer Rejected')
                                                            <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Offer Rejected</span>
                                                        @elseif($interview_status['interview_status_level'] == "Candidate didn't Appeared")
                                                            <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Candidate didn't Appeared</span>
                                                        @elseif($interview_status['interview_status_level'] == 'Candidate Hired')
                                                            <span class="badge badge-light-success font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Candidate Hired</span>
                                                        @elseif($interview_status['interview_status_level'] == 'Interview Cancelled')
                                                            <span class="badge badge-light-danger font-weight-bold" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="{{ $interview_status['interview_status_created_at'] }}">Interview Cancelled</span>
                                                        @endif
                                                      

                                                    </div>
                                                </div>
                                                <div class="separator separator-dashed my-4"></div>
                                            @endforeach
                                       
                                        @endif
                                            
                                    </div>
                                </div>
                              
                            </div>
                        </div>
                    </div>
                </div>
               

                <div class="col-md-7 scroll scroll-pull"  data-wheel-propagation="true" >
                    @if (!empty($interview[0]['interview_interviewer_remarks']) && $interview[0]['interview_interviewer_remarks'] != '')
                    <div class="card card-flush mb-10">
                        <div class="card-body">
                            <div class="row g-9 mb-2">
                                <div class="col-md-12">
                                    <label class="fw-bold">Remarks by 1st interviewer ( {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer1']) !!} )</label>
                                    <p class="pt-5">
                                        @if (!empty($interview[0]['interview_interviewer_remarks']) && $interview[0]['interview_interviewer_remarks'] != '')
                                            {{ $interview[0]['interview_interviewer_remarks'] }}
                                        @else
                                            No Remarks By Interviewer.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    @endif

                    @if (!empty($interview[0]['interview_interviewer_remarks2']) && $interview[0]['interview_interviewer_remarks2'] != '')
                        <div class="card card-flush mb-10">
                            <div class="card-body">
                                <div class="row g-9 mb-2">
                                    <div class="col-md-12">
                                        <label class="fw-bold">Remarks by 2nd interviewer ( {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer2']) !!} )</label>
                                        <p class="pt-5">
                                            @if (!empty($interview[0]['interview_interviewer_remarks2']) && $interview[0]['interview_interviewer_remarks2'] != '')
                                                {{ $interview[0]['interview_interviewer_remarks2'] }}
                                            @else
                                                No Remarks By Interviewer.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endif

                    <form id="update_interview">
                        <div class="card card-flush mb-10 ">
                        
                            <div class="card-body">
                            
                                <div class="row g-9 mb-2" >
                                    
                                    <div class="col-md-12 fv-row ">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                        <span class="required">Requisition Reference</span>
                                        </label>

                                        <div class=" d-flex align-items-center">
                                            <select class="form-select mySelect3" data-control="select2" data-hide-search="false" data-placeholder="Select Requisition"  name="requisition_reference" required=""> 
                                                <option value="">Select Requisition</option>
                                                    @if(!empty($requisitions))
                                                        @foreach($requisitions as $requisition)
                                                            <option value="{{ $requisition['requisition_id'] }}"
                                                                @if (!empty($interview[0]['requisition_reference']) && $interview[0]['requisition_reference'] == $requisition['requisition_id']) selected @endif>
                                                             {{ $requisition['requisition_id'] }} - {{ $requisition['requisition_job_title'] }} - {{ $requisition['department_name'] }} - {!! CustomHelper::getEmpProfileDiv($requisition['requisition_added_by']) !!}
                                                            </option>  
                                                        @endforeach
                                                @endif
                                                        
                                            </select>
                                        </div>
                                    </div>    

                                    <div class="col-md-12 ">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                        <span class="required">Interview Type</span>
                                        </label>

                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select myselect3" data-control="select2" data-hide-search="false" data-placeholder="Select Interview Type" id="interview_type" name="interview_type" required=""> 
                                                <option value="">Select Interview Type</option>
                                                <option value="Online" @if ($interview[0]['interview_type'] == 'Online') selected @endif>Online Interview</option>
                                                <option value="Physical" @if ($interview[0]['interview_type'] == 'Physical') selected @endif>Physical Interview</option>
                                                
                                                    
                                            </select>
                                        </div>
                                    
                                    </div>
                            
                                  
                                    <div class="col-md-6 child-div-interview-type d-none">
                                        {{--begin::Label--}}
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required" id="label-interview-room">Interviewer Room</span>
                                        </label>
                                        {{--end::Label--}}
                                    
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select mySelect3" data-control="select2" data-hide-search="true"  id="interview_room" name="room_no" required="required" value="{{$interview[0]['interview_room_nbr'] ?? ''}}">
                                                <option value="">Select Room</option>

                                                @if(!empty($office_rooms))

                                                    @foreach ($office_rooms as $office_room)
                                                        <option value="{{$office_room['room_id']}}" @if ($interview[0]['interview_room_nbr'] == $office_room['room_id']) selected @endif>{{$office_room['room_name']}}</option>
                                                    @endforeach

                                                @endif
                                          
                                                
                                                
                                            </select>
                                        </div>
                                        {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6 child-div-interview-type ">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                            <span class="required" id="label-interview-link">Interview Link</span>
                                        </label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="url" class="form-control" id="interview_link" name="interview_link" required="required" placeholder="Interview Link"  value="{{$interview[0]['interview_link'] ?? ''}}">
                                        </div>
                                        
                                        
                                    </div>
                                
                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Interview Date</label>
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            
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
                                                    <input type="hidden" class="form-control  ps-12" name="interview_id"
                                                        required="" value="{{ $interview[0]['interview_id'] }}">
                                                        <input type="hidden" class="form-control "  name="candidate_id" value="{{$interview[0]['candidate_id'] }}"  />
                                                    <input class="form-control  ps-12" autocomplete="off"  id="interview_update"  name="interview_date"
                                                        required="" value="{{ $interview[0]['interview_date'] }}"
                                                        min="{{ date('Y-m-d') }}">
                                            {{--end::Datepicker--}}
                                        </div>
                                        {{--end::Input--}}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Interview Time</label>
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            
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
                                            <input type="time" class="form-control  ps-12" name="interview_time" value="{{ $interview[0]['interview_time'] }}" required=""/>
                                            {{--end::Datepicker--}}
                                        </div>
                                        {{--end::Input--}}
                                    </div>
                                    
                                    <div class="col-md-6">
                                        {{--begin::Label--}}
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Expected Duration Time</span>
                                        </label>
                                        {{--end::Label--}}
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select mySelect3" data-control="select2" data-hide-search="true"  name="duration" required="required">
                                                <option value="">Select Duration</option>
                                                
                                                    <option value="10" {{($interview[0]['interview_duration']	== "10")? 'selected' : ''}}>10 minutes</option>
                                                    <option value="20" {{($interview[0]['interview_duration']	== "20")? 'selected' : ''}}>20 minutes</option> 
                                                    <option value="30" {{($interview[0]['interview_duration']	== "30")? 'selected' : ''}}>30 minutes</option>
                                                    <option value="40" {{($interview[0]['interview_duration']	== "40")? 'selected' : ''}}>40 minutes</option>
                                                    <option value="50" {{($interview[0]['interview_duration']	== "50")? 'selected' : ''}}>50 minutes</option>
                                                    <option value="60" {{($interview[0]['interview_duration']	== "60")? 'selected' : ''}}>60 minutes</option>
                                                    
                                                
                                                
                                            </select>
                                        </div>
                                        {{--end::Input--}}
                                    </div>
                                    <div class="col-md-6">
                                        {{--begin::Label--}}
                                    <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Interview Department</label>
                                    {{--end::Label--}}
                                    
                                    {{--begin::Input--}}
                                    <div class="position-relative d-flex align-items-center">
                                        <select class="form-select mySelect3" data-control="select2" data-hide-search="true" name="interview_department" required="required">
                                                @if (!empty($departments))
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department['department_id'] }}"
                                                            @if (!empty($interview[0]['interview_department']) && $interview[0]['interview_department'] == $department['department_id']) selected @endif>
                                                            {{ $department['department_name'] }}</option>
                                                    @endforeach
                                                @endif
                                        </select>
                                    </div>
                                    {{--end::Input--}}
                                    </div>
                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Job Title</label>
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="text" class="form-control " placeholder="Enter Job Title" name="interview_jobtitle" value="{{ $interview[0]['interview_jobtitle'] }}" required="">
                                            {{--end::Datepicker--}}
                                        </div>
                                        {{--end::Input--}}
                                    </div>
                                    <div class="col-md-6">
                                        {{--begin::Label--}}
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span class="required">Interviewer 1st </span>
                                    </label>
                                    {{--end::Label--}}
                                    
                                    {{--begin::Input--}}
                                    <div class="position-relative d-flex align-items-center">
                                        <select class="form-select mySelect3" data-control="select2" data-hide-search="true" name="interviewer_1"  required="required">
                                            <option value="">Select Interviwer</option>
                                                    @if (!empty($employees))
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee['employee_id'] }}"
                                                                @if (!empty($interview[0]['interview_interviewer1']) && $interview[0]['interview_interviewer1'] == $employee['employee_id']) selected @endif>
                                                                {{ $employee['employee_code'] . ' - ' . $employee['employee_name'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                        </select>
                                    </div>
                                    {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6">
                                        {{--begin::Label--}}
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span> Interviewer 2nd </span>
                                    </label>
                                    {{--end::Label--}}
                                    
                                    {{--begin::Input--}}
                                    <div class="position-relative d-flex align-items-center">
                                        <select class="form-select mySelect3" data-control="select2" data-hide-search="true" name="interviewer_2">
                                                <option value="">No 2nd Interviewer</option>
                                                        @if (!empty($employees))
                                                            @foreach ($employees as $employee)
                                                                <option value="{{ $employee['employee_id'] }}"
                                                                    @if (!empty($interview[0]['interview_interviewer2']) && $interview[0]['interview_interviewer2'] == $employee['employee_id']) selected @endif>
                                                                    {{ $employee['employee_code'] . ' - ' . $employee['employee_name'] }}
                                                                </option>
                                                            @endforeach
                                                    @endif
                                        </select>
                                    </div>
                                    {{--end::Input--}}
                                    </div>
                                    
                                    <div class="col-md-6">
                                        {{--begin::Label--}}
                                    <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Interview Round</label>
                                    {{--end::Label--}}
                                    
                                    {{--begin::Input--}}
                                    <div class="position-relative d-flex align-items-center">
                                        <select class="form-select mySelect3" data-control="select2" data-hide-search="true" name="interview_round"  required="required">
                                            <option value="">Select Interview Round</option>
                                            <option value="1st"
                                                    @if (!empty($interview[0]['interview_round']) && $interview[0]['interview_round'] == '1st') selected @endif>1st
                                                </option>
                                                <option value="2nd"
                                                    @if (!empty($interview[0]['interview_round']) && $interview[0]['interview_round'] == '2nd') selected @endif>2nd
                                                </option>
                                                <option value="3rd"
                                                    @if (!empty($interview[0]['interview_round']) && $interview[0]['interview_round'] == '3rd') selected @endif>3rd
                                                </option>
                                                <option value="4th"
                                                    @if (!empty($interview[0]['interview_round']) && $interview[0]['interview_round'] == '4th') selected @endif>4th
                                                </option>
                                                <option value="5th"
                                                    @if (!empty($interview[0]['interview_round']) && $interview[0]['interview_round'] == '5th') selected @endif>5th
                                                </option>
                                                <option value="6th"
                                                    @if (!empty($interview[0]['interview_round']) && $interview[0]['interview_round'] == '6th') selected @endif>6th
                                                </option>
                                                <option value="7th"
                                                    @if (!empty($interview[0]['interview_round']) && $interview[0]['interview_round'] == '7th') selected @endif>7th
                                                </option>
                                                <option value="8th"
                                                    @if (!empty($interview[0]['interview_round']) && $interview[0]['interview_round'] == '8th') selected @endif>8th
                                                </option>
                                        </select>
                                    </div>
                                    {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6">
                                        {{--begin::Label--}}
                                    <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                        <span> Notice Period </span>
                                    </label>
                                    {{--end::Label--}}
                                    
                                    {{--begin::Input--}}
                                    <div class="position-relative d-flex align-items-center">
                                        <select class="form-select mySelect3" data-control="select2" data-hide-search="true" name="notice_period" >
                                            <option value="">Select Notice Period</option>
                                            @foreach($notice_period as $notice_value)
                                                <option  {{ $interview[0]['interview_noticepriod'] == $notice_value ? "selected" : "" }}  value="{{ $notice_value }}">{{$notice_value}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                    {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Name</label>
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="text" class="form-control " placeholder="Candidate Name" name="candidate_name" value="{{ $interview[0]['candidate_name'] }}" required="" />
                                        
                                        </div>
                                        {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6">
                                        <label class="fs-6 fw-semibold mb-2">Candidate Email</label>
                                        <input type="email" class="form-control " placeholder="Enter Candidate Email" name="candidate_email" value="{{ $interview[0]['candidate_email'] }}" />
                                    </div>

                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Cv</label>
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="file" class="form-control" accept=".docx,.txt,application/pdf,image/*"  name="candidate_cv" value="{{ $interview[0]['interview_candidate_cv'] }}">
                                        
                                        </div>
                                        {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6 fv-row">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Candidate Contact No</span>
                                        </label>
                                    
                                        {{--begin::Input--}}
                                        <div class=" d-flex align-items-center">
                                            
                                            <input type="number" class="form-control " placeholder="Enter Candidate contact number" name="contact_number" value="{{$interview[0]['candidate_contact_no']}}" required/>
                                        
                                        </div>
                                        {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Current Salary</label>
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="text" class="form-control " placeholder="Enter Current Salary(optional)" name="current_salary" value="{{ $interview[0]['interview_current_salary'] }}">
                                        
                                        </div>
                                        {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Expected Salary</label>
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="text" class="form-control " placeholder="Enter Expected Salary(optional)" name="expected_salary" value="{{ $interview[0]['interview_expected_salary'] }}">
                                        
                                        </div>
                                        {{--end::Input--}}
                                    </div>

                                    <div class="col-md-12">
                                        <label class="fs-6 fw-semibold mb-2">Additional Remarks</label>
                                        <textarea class="form-control " rows="3" name="additional_remarks" placeholder="Enter Additional Remarks(optional)">{{ $interview[0]['interview_additional_remarks'] }}</textarea>
                                    </div>
                                </div>
                                
                                @if(empty($interview[0]['interview_interviewer_remarks']) && $interview[0]['interview_interviewer_remarks'] == '')
                                    <button type="submit" class="btn btn-lg btn-info mt-2" id="update-btn">
                                        <span class="indicator-label">Update Details</span>
                                        <span class="indicator-progress">Please wait
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                    </button>
                                @endif
                            </div>
                        
                        
                        </div>
                    
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 card mb-5">
                    <div class="flex-lg-row-fluid card card-flush mb-10">
                        {{--begin::Messenger--}}
                        <div class="card direct-chat direct-chat-primary">
                                        {{--begin::Card header--}}
                            <div class="card-header" id="kt_chat_messenger_header">
                                {{--begin::Title--}}
                                <div class="card-title">
                                    {{--begin::User--}}
                                    <div class="d-flex justify-content-center flex-column me-3">
                                        <p class="fs-4 fw-bold text-gray-900 me-1 mb-2 lh-1">Interview Chat</p>
                                        
                                    </div>
                                    {{--end::User--}}
                                </div>
                                {{--end::Title--}}
                                
                            </div>
                            {{--end::Card header--}}
                            {{--begin::Card body--}}
                            <div class="card-body" id="kt_chat_messenger_body">
                                {{--begin::Messages--}}
                                <div class="scroll-y me-n5 pe-5 h-300px h-lg-auto " data-kt-element="messages" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-max-height="auto" data-kt-scroll-dependencies="#kt_header, #kt_app_header, #kt_app_toolbar, #kt_toolbar, #kt_footer, #kt_app_footer, #kt_chat_messenger_header, #kt_chat_messenger_footer" data-kt-scroll-wrappers="#kt_content, #kt_app_content, #kt_chat_messenger_body" data-kt-scroll-offset="5px" style="max-height: 151px;" id="interview-chat-message">
                                    <!--begin::Message(in)-->
                                    <div class="direct-chat-messages">

                                    </div>
                        
                                </div>  
                            </div>
                            {{--end::Card body--}}
                                    {{--begin::Card footer--}}
                            <div class="card-footer pt-4" >
                                <form id="sendChatMessage">
                                    {{--begin::Input--}}

                                    {{-- <input type="hidden" name="interview_index_id" id="index_input"> --}}
                                    <input type="hidden" name="interview_id" value="{{Request::segment(3)}}">
                                    <input type="hidden" name="interviewer1" value="{{$interview[0]['interview_interviewer1']}}" />
                                    <input type="hidden" name="interviewer2" value="{{$interview[0]['interview_interviewer2']}}" />
                                    <div class="input-group">
                                        {{-- <input type="hidden" id="fileHolder" name="chat_file" > --}}
                                        <textarea class="form-control form-control-flush mb-3" id="messageInputBox"  name="message" rows="1" data-kt-element="input" placeholder="Type a message"></textarea>
                                        {{-- end::Input --}}
                                        {{--begin:Toolbar--}}
                                        <div class="d-flex flex-stack">
                                            <button type="submit" id="sendMessageBtn"class="btn btn-primary">
                                                <span class="indicator-label">Submit</span>
                                                <span class="indicator-progress">Please wait... 
                                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                            </button>
                                            
                                            
                                        </div>
                                    </div>  
                                    
                                </form>    {{-- end::Toolbar--}}
                            </div>
                                    {{-- end::Card footer --}}
                        </div>
                        {{-- end::Messenger --}}
                    </div>
                </div>
            </div>
            
            
            <form id="add_interview" class="form">
             @csrf
                <div class="modal fade" id="schedule_interview" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-750px">
                        
                        <div class="modal-content rounded">
                            
                                <div class="modal-header">
                                    <h4 class="modal-title pl-4">Re-Schedule Interview</h4>
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
                                
                                <input type="hidden" name="reSchedule" value="1" /> 
                                <input type="hidden" class="form-control "  name="candidate_id" value="{{$interview[0]['candidate_id'] }}"  />
                                
                                <div class="row g-5 mt-1" >

                                    <div class="col-md-12 fv-row ">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                        <span class="required">Requisition Reference</span>
                                        </label>

                                        <div class=" d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false" data-placeholder="Select Requisition"  name="requisition_reference" required=""> 
                                                <option value="">Select Requisition</option>
                                                    @if(!empty($requisitions))
                                                        @foreach($requisitions as $requisition)
                                                            <option value="{{ $requisition['requisition_id'] }}"
                                                                @if (!empty($interview[0]['requisition_reference']) && $interview[0]['requisition_reference'] == $requisition['requisition_id']) selected @endif>
                                                              {{ $requisition['requisition_id'] }} - {{ $requisition['requisition_job_title'] }} - {{ $requisition['department_name'] }} - {!! CustomHelper::getEmpProfileDiv($requisition['requisition_added_by']) !!}
                                                            </option>  
                                                        @endforeach
                                                @endif
                                                    
                                            </select>
                                        </div>
                                    </div>    

                                    <div class="col-md-12 ">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                        <span class="required">Interview Type</span>
                                        </label>
                                    
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false" data-placeholder="Select Interview Type" id="interview_type1" name="interview_type" required=""> 
                                                <option value="">Select Interview Type</option>
                                                <option value="Online" @if ($interview[0]['interview_type'] == 'Online') selected @endif>Online Interview</option>
                                                <option value="Physical" @if ($interview[0]['interview_type'] == 'Physical') selected @endif>Physical Interview</option>
                                                
                                                    
                                            </select>
                                        </div>
                                    
                                    </div>
                                    
                                    
                                    
                                    <div class="col-md-6 child-div-interview-type ">
                                        {{--begin::Label--}}
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required" id="label-interview-room1">Interviewer Room</span>
                                        </label>
                                        {{--end::Label--}}
                                    
                                    
                                        {{--begin::Input--}}
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false"  id="interview_room1" name="room_no" required="required" value="{{$interview[0]['interview_room_nbr'] ?? ''}}">
                                                <option value="">Select Room</option>
                                    
                                                @if(!empty($office_rooms))
                                    
                                                    @foreach ($office_rooms as $office_room)
                                                        <option value="{{$office_room['room_id']}}" @if ($interview[0]['interview_room_nbr'] == $office_room['room_id']) selected @endif>{{$office_room['room_name']}}</option>
                                                    @endforeach
                                    
                                                @endif
                                          
                                                
                                                
                                            </select>
                                        </div>
                                        {{--end::Input--}}
                                    </div>
                                    
                                    <div class="col-md-6 child-div-interview-type ">
                                        <label class="d-flex align-items-cente fs-6 fw-semibold mb-2">
                                            <span class="required" id="label-interview-link1">Interview Link</span>
                                        </label>
                                        <div class="position-relative d-flex align-items-center">
                                            <input type="url" class="form-control" id="interview_link1" name="interview_link" required="required" placeholder="Interview Link"  value="{{$interview[0]['interview_link'] ?? ''}}">
                                        </div>
                                        
                                        
                                    </div>
                                
                                
                                    <div class="col-md-6">
                                            <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Interview Date</label>
                                        
                                    
                                        <div class="position-relative d-flex align-items-center">
                                        
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                                    <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                                    <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                        
                                                @php 
                                                    $date = date('Y-m-d',strtotime($interview[0]['interview_date']));
                                                    $tomorrow = date('Y-m-d',strtotime($date . "+1 days")); 
                                                    
                                                @endphp
                                                
                                                <input type="hidden" class="form-control  ps-12" name="interview_id"
                                                    required="" value="{{ $interview[0]['interview_id'] }}">
                                                <input class="form-control ps-12" autocomplete="off"  id="interview_reschedule"  name="interview_date"
                                                    required="" value="{{$tomorrow}}"
                                                    min="{{ date('Y-m-d') }}">
                                        
                                        </div>
                                    
                                    </div>
                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Interview Time</label>
                                        <div class="position-relative d-flex align-items-center">
                                        
                                            <span class="svg-icon svg-icon-2 position-absolute mx-4">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path opacity="0.3" d="M21 22H3C2.4 22 2 21.6 2 21V5C2 4.4 2.4 4 3 4H21C21.6 4 22 4.4 22 5V21C22 21.6 21.6 22 21 22Z" fill="currentColor" />
                                                    <path d="M6 6C5.4 6 5 5.6 5 5V3C5 2.4 5.4 2 6 2C6.6 2 7 2.4 7 3V5C7 5.6 6.6 6 6 6ZM11 5V3C11 2.4 10.6 2 10 2C9.4 2 9 2.4 9 3V5C9 5.6 9.4 6 10 6C10.6 6 11 5.6 11 5ZM15 5V3C15 2.4 14.6 2 14 2C13.4 2 13 2.4 13 3V5C13 5.6 13.4 6 14 6C14.6 6 15 5.6 15 5ZM19 5V3C19 2.4 18.6 2 18 2C17.4 2 17 2.4 17 3V5C17 5.6 17.4 6 18 6C18.6 6 19 5.6 19 5Z" fill="currentColor" />
                                                    <path d="M8.8 13.1C9.2 13.1 9.5 13 9.7 12.8C9.9 12.6 10.1 12.3 10.1 11.9C10.1 11.6 10 11.3 9.8 11.1C9.6 10.9 9.3 10.8 9 10.8C8.8 10.8 8.59999 10.8 8.39999 10.9C8.19999 11 8.1 11.1 8 11.2C7.9 11.3 7.8 11.4 7.7 11.6C7.6 11.8 7.5 11.9 7.5 12.1C7.5 12.2 7.4 12.2 7.3 12.3C7.2 12.4 7.09999 12.4 6.89999 12.4C6.69999 12.4 6.6 12.3 6.5 12.2C6.4 12.1 6.3 11.9 6.3 11.7C6.3 11.5 6.4 11.3 6.5 11.1C6.6 10.9 6.8 10.7 7 10.5C7.2 10.3 7.49999 10.1 7.89999 10C8.29999 9.90003 8.60001 9.80003 9.10001 9.80003C9.50001 9.80003 9.80001 9.90003 10.1 10C10.4 10.1 10.7 10.3 10.9 10.4C11.1 10.5 11.3 10.8 11.4 11.1C11.5 11.4 11.6 11.6 11.6 11.9C11.6 12.3 11.5 12.6 11.3 12.9C11.1 13.2 10.9 13.5 10.6 13.7C10.9 13.9 11.2 14.1 11.4 14.3C11.6 14.5 11.8 14.7 11.9 15C12 15.3 12.1 15.5 12.1 15.8C12.1 16.2 12 16.5 11.9 16.8C11.8 17.1 11.5 17.4 11.3 17.7C11.1 18 10.7 18.2 10.3 18.3C9.9 18.4 9.5 18.5 9 18.5C8.5 18.5 8.1 18.4 7.7 18.2C7.3 18 7 17.8 6.8 17.6C6.6 17.4 6.4 17.1 6.3 16.8C6.2 16.5 6.10001 16.3 6.10001 16.1C6.10001 15.9 6.2 15.7 6.3 15.6C6.4 15.5 6.6 15.4 6.8 15.4C6.9 15.4 7.00001 15.4 7.10001 15.5C7.20001 15.6 7.3 15.6 7.3 15.7C7.5 16.2 7.7 16.6 8 16.9C8.3 17.2 8.6 17.3 9 17.3C9.2 17.3 9.5 17.2 9.7 17.1C9.9 17 10.1 16.8 10.3 16.6C10.5 16.4 10.5 16.1 10.5 15.8C10.5 15.3 10.4 15 10.1 14.7C9.80001 14.4 9.50001 14.3 9.10001 14.3C9.00001 14.3 8.9 14.3 8.7 14.3C8.5 14.3 8.39999 14.3 8.39999 14.3C8.19999 14.3 7.99999 14.2 7.89999 14.1C7.79999 14 7.7 13.8 7.7 13.7C7.7 13.5 7.79999 13.4 7.89999 13.2C7.99999 13 8.2 13 8.5 13H8.8V13.1ZM15.3 17.5V12.2C14.3 13 13.6 13.3 13.3 13.3C13.1 13.3 13 13.2 12.9 13.1C12.8 13 12.7 12.8 12.7 12.6C12.7 12.4 12.8 12.3 12.9 12.2C13 12.1 13.2 12 13.6 11.8C14.1 11.6 14.5 11.3 14.7 11.1C14.9 10.9 15.2 10.6 15.5 10.3C15.8 10 15.9 9.80003 15.9 9.70003C15.9 9.60003 16.1 9.60004 16.3 9.60004C16.5 9.60004 16.7 9.70003 16.8 9.80003C16.9 9.90003 17 10.2 17 10.5V17.2C17 18 16.7 18.4 16.2 18.4C16 18.4 15.8 18.3 15.6 18.2C15.4 18.1 15.3 17.8 15.3 17.5Z" fill="currentColor" />
                                                </svg>
                                            </span>
                                            <input type="time" class="form-control  ps-12" name="interview_time" value="{{ $interview[0]['interview_time'] }}" required=""/>
                                        </div>
                                    </div>
                                    

                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Expected Duration Time</span>
                                        </label>
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false"  name="duration" required="required">
                                                <option value="">Select Duration</option>
                                                
                                                    <option value="10" {{($interview[0]['interview_duration']	== "10")? 'selected' : ''}}>10 minutes</option>
                                                    <option value="20" {{($interview[0]['interview_duration']	== "20")? 'selected' : ''}}>20 minutes</option> 
                                                    <option value="30" {{($interview[0]['interview_duration']	== "30")? 'selected' : ''}}>30 minutes</option>
                                                    <option value="40" {{($interview[0]['interview_duration']	== "40")? 'selected' : ''}}>40 minutes</option>
                                                    <option value="50" {{($interview[0]['interview_duration']	== "50")? 'selected' : ''}}>50 minutes</option>
                                                    <option value="60" {{($interview[0]['interview_duration']	== "60")? 'selected' : ''}}>60 minutes</option>
                                                    
                                                
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Interview Department</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false" name="interview_department" required="required">
                                                @if (!empty($departments))
                                                    @foreach ($departments as $department)
                                                        <option value="{{ $department['department_id'] }}"
                                                            @if (!empty($interview[0]['interview_department']) && $interview[0]['interview_department'] == $department['department_id']) selected @endif>
                                                            {{ $department['department_name'] }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Job Title</label>
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="text" class="form-control " placeholder="Enter Job Title" name="interview_jobtitle" value="{{ $interview[0]['interview_jobtitle'] }}" required="">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Interviewer 1st </span>
                                        </label>
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false" name="interviewer_1"  required="required">
                                                <option value="">Select Interviwer</option>
                                                    @if (!empty($employees))
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee['employee_id'] }}"
                                                                @if (!empty($interview[0]['interview_interviewer1']) && $interview[0]['interview_interviewer1'] == $employee['employee_id']) selected @endif>
                                                                {{ $employee['employee_code'] . ' - ' . $employee['employee_name'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span> Interviewer 2nd </span>
                                        </label>
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false" name="interviewer_2">
                                                <option value="">No 2nd Interviewer</option>
                                                        @if (!empty($employees))
                                                            @foreach ($employees as $employee)
                                                                <option value="{{ $employee['employee_id'] }}"
                                                                    @if (!empty($interview[0]['interview_interviewer2']) && $interview[0]['interview_interviewer2'] == $employee['employee_id']) selected @endif>
                                                                    {{ $employee['employee_code'] . ' - ' . $employee['employee_name'] }}
                                                                </option>
                                                            @endforeach
                                                        @endif
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Interview Round</label>
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false" name="interview_round"  id="interview_round" required="required" >
                                                <option value="">Select Interview Round</option>
                                                  
                                                @if(!empty($interview_round))
                                                    @foreach ( $interview_round as $key => $round)

                                                        @if ($round == '8th')
                                                            <option {{ $interview_round[$next_interview_round] == $round ? "selected" : "" }} value="{{ $round }}">{{ $round }}</option>
                                                        @else
                                                            @php
                                                                $nextRound = $interview_round[$next_interview_round + 1] ?? null;
                                                                $selectedRound = $round == $nextRound ? "selected" : "";
                                                            @endphp
                                                            <option {{ $selectedRound }} value="{{ $round }}">{{ $round }}</option>
                                                        @endif
                                                       
                                                    @endforeach
                                                @endif
                                                
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span> Notice Period </span>
                                        </label>
                                        <div class="position-relative d-flex align-items-center">
                                            <select class="form-select requitionselect" data-control="select2" data-hide-search="false" name="notice_period" >
                                                <option value="">Select Notice Period</option>
                                                @foreach($notice_period as $notice_value)
                                                <option  {{ $interview[0]['interview_noticepriod'] == $notice_value ? "selected" : "" }}  value="{{ $notice_value }}">{{$notice_value}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Name</label>
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="text" class="form-control " placeholder="Candidate Name" name="candidate_name" value="{{ $interview[0]['candidate_name'] }}" required="" readonly/>
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="fs-6 fw-semibold mb-2">Candidate Email</label>
                                        <input type="email" class="form-control " placeholder="Enter Candidate Email" name="candidate_email" value="{{ $interview[0]['candidate_email'] }}" />
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="required d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Cv</label>
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="file" class="form-control " accept=".docx,.txt,application/pdf,image/*"  name="candidate_cv" value="{{$interview[0]['interview_candidate_cv']}}">
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                            <span class="required">Candidate Contact No</span>
                                        </label>
                                    
                                        {{--begin::Input--}}
                                        <div class=" d-flex align-items-center">
                                            
                                            <input type="number" class="form-control " placeholder="Enter Candidate contact number" name="contact_number" value="{{$interview[0]['candidate_contact_no']}}" @if(!empty($interview[0]['candidate_contact_no'])) readonly @endif  required />
                                        
                                        </div>
                                        {{--end::Input--}}
                                    </div>

                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Current Salary</label>
                                        
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="text" class="form-control " placeholder="Enter Current Salary(optional)" name="current_salary" value="{{ $interview[0]['interview_current_salary'] }}">
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="d-flex align-items-center fs-6 fw-semibold mb-2">Candidate Expected Salary</label>
                                        <div class="position-relative d-flex align-items-center">
                                            
                                            <input type="text" class="form-control " placeholder="Enter Expected Salary(optional)" name="expected_salary" value="{{ $interview[0]['interview_expected_salary'] }}">
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="fs-6 fw-semibold mb-2">Additional Remarks</label>
                                        <textarea class="form-control " rows="3" name="additional_remarks" placeholder="Enter Additional Remarks(optional)">{{ $interview[0]['interview_additional_remarks'] }}</textarea>
                                    </div>
                                </div>
                                        
                            </div>
                            <div class="modal-footer justify-content-center">
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit_btn" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            {{--end::Modal - New Target--}}

            {{--begin::Modal -Hired--}}
            <form id="hired_interviewer_employee" class="form">
                @csrf
                <div class="modal fade" id="hired_interviewer" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Joining Confirmation of Candidate</h4>
                            
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                        </svg>
                                    </span>
                                </button>
                                
                            </div>
                            
                            
                    
                            {{--end::Modal header--}}
                            
                            <div class="modal-body scroll-y pt-0 pb-15">
                                
                                <h6 class="pt-5 pb-3 ">By marking this candidate as confirmed joining. It will be added in <a href="{{ url('/employee-onboarding') }}">On Boarding Employees</a>.</h6>
                            
                                
                                <div class="d-flex flex-column mb-8 mt-3">
                                    <label class="fs-6 fw-semibold mb-2 required">Candidate Name</label>
                                    <input type="hidden" class="form-control "  name="candidate_id" placeholder="Candidate Id" value="{{$interview[0]['candidate_id'] }}"  />
                                    <input type="hidden" class="form-control "  name="deprt_id"  value="{{$interview[0]['interview_department']  }}"  />
                                    <input type="text" class="form-control " name="candidate_name"  placeholder="Candidate Name" value="{{$interview[0]['candidate_name'] }}" required readonly />
                                </div>

                                <div class="d-flex flex-column mb-8 mt-3">
                                    <label class="fs-6 fw-semibold mb-2 required">Department Name</label>
                                    <input type="text" class="form-control" name="deprt_name"  placeholder="Department Name" value="{{$interview[0]['department_name']  }}" required readonly />
                                </div>
                                

                                <div class="col-md-12 mb-8 mt-3">
                                    <label class="required fs-6 fw-semibold mb-2">Expected Joining Date</label>
                                    {{--begin::Input--}}
                                    <div class="position-relative d-flex align-items-center">
                                        
                                        
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
                                        <input type="date" class="form-control  ps-12" placeholder="Select a date" name="joining_date" min="{{ date('Y-m-d') }}" required/>
                                        {{--end::Datepicker--}}
                                    </div>
                                    {{--end::Input--}}
                                </div>
                                
                                {{--end::Input group--}}
                                
                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">
                            
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit_btn_hired" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                {{--end::Modal - Hired--}}
            </form>
            


             {{--begin::Modal -Interview Status--}}
             <form id="interviewer_status_candidate" class="form">
                @csrf
                <div class="modal fade" id="interviewer_status" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-scrollable mw-650px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-header">
                                <h4 class="modal-title pl-4">Change Interview Status</h4>
                            
                                <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                        </svg>
                                    </span>
                                </button>
                                
                            </div>
                            
                            
                    
                            {{--end::Modal header--}}
                            
                            <div class="modal-body scroll-y pt-0 ">
                                
                                {{-- <h6 class="pt-5 pb-3 ">By marking this candidate as confirmed joining. It will be added in <a href="{{ url('/employee-onboarding') }}">On Boarding Employees</a>.</h6> --}}
                            
                                
                                <div class="d-flex flex-column  mt-3">
                                    <label class="fs-6 fw-semibold mb-2 required">Interview Status</label>

                                    <input type="hidden" class="form-control"  name="interview_id"value="{{$interview[0]['interview_id'] }}"  />
                                    <input type="hidden" class="form-control"  name="employee_id"  value="{{$login_user[0]['employee_id']  }}"  />
                                    <input type="hidden" class="form-control"  name="requisition_reference_id"  value="{{$interview[0]['requisition_reference']}}"  />

                                    <select class="form-select select" data-control="select2" data-hide-search="true"  name="interviewer_status" required="required">
                                        <option value="">Select Interview Status</option>
                                        @if(!empty($interview_status_levels))

                                            @foreach ($interview_status_levels as $interview_status_level)
                                                <option value="{{$interview_status_level['interview_status_level_id']}}" @if($interview_status['interview_status_level_id'] == $interview_status_level['interview_status_level_id'] )   selected @endif>{{$interview_status_level['interview_status_level']}}</option>
                                            @endforeach

                                        @endif
                                    </select>
 
                                </div>

                                {{--end::Input group--}}
                                
                            </div>
                            
                            
                            <div class="modal-footer justify-content-center">
                            
                                <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                <button type="submit" id="submit_btn_status" class="btn btn-primary">
                                    <span class="indicator-label">Submit</span>
                                    <span class="indicator-progress">Please wait... 
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                </button>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                {{--end::Modal - Interview Status--}}
            </form>
            
        </div>


    </div>
    
</div>

            

@endsection



@push('js-link')

<script>
    var interview_id = '{{ Request::segment(3) }}';

    function Copy(Url) {
        var Url = document.getElementById("url");
        Url.select();
        document.execCommand("copy");
    }

</script>

<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script>
<script src="{{url('/')}}/assets/js/firebase-interview-chat.js?js_v=2"></script>

<script src="{{url('assets/js/interview/interview-detail.js')}}"></script>

@endpush