@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Interview Detail</h1>   
            </div>
            <div class="d-flex align-items-center gap-2 gap-lg-3">
             
                <div class="m-0">
                     <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
                      
                </div>
         
            </div>
           
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
      
        <div class="app-container">
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-body pt-5">
                            
                            <div class="d-flex flex-stack">
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    
                                     <div class="flex-grow-1 me-2">
                                         <span class="text-gray-800  fs-6 fw-bold">Interview ID</span>
                                     </div>
                                    
                                    <span>
                                        @if (!empty($interview[0]['interview_id']))
                                            {{ $interview[0]['interview_id'] }}
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
                                        <span class="text-gray-800  fs-6 fw-bold">Requisition Reference</span>
                                    </div>
                                        <span> 
                                            @if (!empty($interview[0]['requisition_reference'] && !empty($interview[0]['requisition_job_title']) ))
                                                {{ $interview[0]['requisition_reference'] }} - {{$interview[0]['requisition_job_title']}}
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
                                        <span class="text-gray-800  fs-6 fw-bold">Interview Date</span>
                                    </div>
                                    
                                    <span>
                                        @if (!empty($interview[0]['interview_date']))
                                            {{ $interview[0]['interview_date'] }}
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
                                         <span class="text-gray-800  fs-6 fw-bold">Interview Time</span>
                                    </div>
                                        
                                    <span>
                                        @if (!empty($interview[0]['interview_time']))
                                            {{ date('h:i A',strtotime($interview[0]['interview_time'])) }}
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
                                        <span class="text-gray-800  fs-6 fw-bold">Interview Duration Time</span>
                                    </div>
                                    
                                    
                                    <span>
                                        @if (!empty($interview[0]['interview_duration']))
                                         {{ ($interview[0]['interview_duration'] ) }} Minutes
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
                                         <span class="text-gray-800  fs-6 fw-bold">Interview Department</span>
                                     </div>
                                    
                                    
                                        <span>
                                            @if (!empty($interview[0]['department_name']))
                                                {{ $interview[0]['department_name'] }}
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
                                        <span class="text-gray-800  fs-6 fw-bold">Interview Job Title</span>
                                    </div>
                                
                                    <span>
                                        @if (!empty($interview[0]['interview_jobtitle']))
                                            {{ $interview[0]['interview_jobtitle'] }}
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
                                        <span class="text-gray-800  fs-6 fw-bold">1st Interviewer</span>
                                    </div>
                                    
                                    <span>
                                    @if (!empty($interview[0]['interview_interviewer1']))
                                     {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer1']); !!}
                                    @else
                                        N/A
                                    @endif 
                                    </span>
                                    
                                 </div>
                                
                            </div>
                            
                            @if (!empty($interview[0]['interview_interviewer1']) && $interview[0]['interview_interviewer1'] == session('user_session')) 
                                <div class="separator separator-dashed my-4"></div>
                                
                                <div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                        
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800  fs-6 fw-bold">2nd Interviewer</span>
                                        </div>
                                        
                                        <span>
                                            @if (!empty($interview[0]['interview_interviewer2']))
                                                {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer2']); !!}
                                            @else
                                                N/A
                                            @endif
                                            @if(empty($interview[0]['interview_interviewer_remarks']) && empty($interview[0]['interview_interviewer_remarks2']))
                                                &nbsp;
                                                <i class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1 edit_second_interviewer" 
                                                    data-bs-toggle="modal" data-bs-target="#secondinterviewmodal"
                                                    >
                                                    <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                        </svg>
                                                    </span>
                                                </i>
                                                
                                            @endif
                                        </span>
                                        
                                    </div>
                                    
                                </div>
                            @else
                                <div class="separator separator-dashed my-4"></div>
                                
                                
                                <div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800  fs-6 fw-bold">2nd Interviewer</span>
                                        </div>
                                        <span>
                                            @if (!empty($interview[0]['interview_interviewer2']))
                                                {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer2']); !!}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            @endif
                            
                            
                            <div class="separator separator-dashed my-4"></div>
                            
                             
                            <div class="d-flex flex-stack">
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800  fs-6 fw-bold">Interview Round</span>
                                    </div>
                                <span>
                                    @if (!empty($interview[0]['interview_round']))
                                        {{ $interview[0]['interview_round'] }}                                     
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
                                    <span>
                                        @if (!empty($interview[0]['candidate_name']))
                                            {{ $interview[0]['candidate_name'] }}                                   
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
                                
                                    <span>
                                        @if (!empty($interview[0]['candidate_contact_no']))
                                            {{ $interview[0]['candidate_contact_no'] }}                                   
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
                                    <span>
                                        @if (!empty($interview[0]['interview_scheduled_time']))
                                            {{ $interview[0]['interview_scheduled_time'] }}                                   
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
                                        <span class="text-gray-800  fs-6 fw-bold">Interview Status</span>
                                    </div>

                                    <span>
                                        @if (!empty($interview[0]['interview_status_level']))
                                            {{ $interview[0]['interview_status_level'] }}                                   
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
                                        <span class="text-gray-800  fs-6 fw-bold">Interview Type</span>
                                    </div>
                                
                                    <span>
                                        @if (!empty($interview[0]['interview_type']))
                                            {{ $interview[0]['interview_type'] }}                                   
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
                                            @if (!empty($interview[0]['interview_created_by']))
                                                {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_created_by']); !!}
                                            @else
                                                N/A
                                            @endif
                                        </span>
                                    
                                    </div>
                                
                            </div>
                            
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-flush mb-10">
                        <div class="card-body">
                            <div class="row g-9 mb-2">
                                <div class="col-md-12 fv-row">
                                    <label class="fw-bold">Additional Remarks By HR</label>
                                    <p class="pt-5">
                                        @if (!empty($interview[0]['interview_additional_remarks']) && $interview[0]['interview_additional_remarks'] != '') {{ $interview[0]['interview_additional_remarks'] }}
                                        @else
                                            No Remarks By HR.
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                    @if ($interview[0]['interview_interviewer1'] == session('user_session') && $interview[0]['interview_interviewer_remarks'] == '')
                        <form id="submit_my_remarks">
                            <div class="card mb-5 mb-xxl-8">
                                {{--begin::Body--}}
                                <div class="card-body pb-0">
                                    {{--begin::Header--}}
                                    <div class="d-flex align-items-center mb-5">
                                        {{--begin::User--}}
                                        <div class="d-flex align-items-center flex-grow-1">
                                            {{--begin::Avatar--}}
                                            <div class="d-flex flex-column">
                                                <a href="#" class="text-gray-900 text-hover-primary fs-6 fw-bold">Your Remarks</a>
                                            </div>
                                            {{--end::Info--}}
                                        </div>
                                        {{--end::User--}}
                                    </div>
                                    {{--end::Header--}}
                                    {{--begin::Post--}}
                                    <div class="mb-5">
                                        {{--begin::Text--}}
                                        <div class="text-gray-800 fw-normal mb-5">
                                            <input type="hidden" class="form-control" name="interview_id" required="" value="{{ $interview[0]['interview_id'] }}">
                                            <textarea class="form-control bg-transparent border-0 px-0" name="my_remarks"  rows="1" placeholder="Enter your remarks ..." required></textarea>
                                        </div>
                                        {{--end::Text--}}
                                    </div>
                                        <div class="separator mb-5"></div>
                                    {{--end::Post--}}
                                        <div class="my-4 text-center">
                                            <button type="submit" class="btn btn-primary" id="update-remarks-btn">Submit Remarks</button>
                                    </div>
                                </div>
                                {{--end::Body--}}
                            </div>
                        </form>

                      
                    @endif

                    @if ($interview[0]['interview_interviewer2'] == session('user_session') && $interview[0]['interview_interviewer_remarks2'] == '')
                        <form id="submit_my_remarks2">
                            <div class="card mb-5 mb-xxl-8">
                                {{--begin::Body--}}
                                <div class="card-body pb-0">
                                    {{--begin::Header--}}
                                    <div class="d-flex align-items-center mb-5">
                                        {{--begin::User--}}
                                        <div class="d-flex align-items-center flex-grow-1">
                                            {{--begin::Avatar--}}
                                            <div class="d-flex flex-column">
                                                <a href="#" class="text-gray-900 text-hover-primary fs-6 fw-bold">Your Remarks as a 2nd Interviewer</a>
                                            </div>
                                            {{--end::Info--}}
                                        </div>
                                        {{--end::User--}}
                                    </div>
                                    {{--end::Header--}}
                                    {{--begin::Post--}}
                                    <div class="mb-5">
                                        {{--begin::Text--}}
                                        <div class="text-gray-800 fw-normal mb-5">
                                            <input type="hidden" class="form-control" name="interview_id" required="" value="{{ $interview[0]['interview_id'] }}">
                                            <textarea class="form-control bg-transparent border-0 px-0" name="my_remarks"  rows="1" placeholder="Type your message..." required></textarea>
                                        </div>
                                        {{--end::Text--}}
                                    </div>
                                        <div class="separator mb-5"></div>
                                    {{--end::Post--}}
                                        <div class="my-4 text-center">
                                            <button type="submit" class="btn btn-primary" id="update-remarks-btn">Submit Remarks</button>
                                    </div>
                                </div>   {{--end::Body--}}     
                            </div>  
                                
                        </form>    
                    @endif
                
                    @if (!empty($interview[0]['interview_interviewer_remarks']) && $interview[0]['interview_interviewer_remarks'] != '')
                        <div class="card card-flush mb-10">
                            <div class="card-body">
                                <div class="row g-9 mb-2">
                                    <div class="col-md-12 fv-row">
                                        <label class="fw-bold">Remarks by 1st interviewer ( {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer1']) !!} )</label>
                                        <p class="pt-5">
                                            @if (!empty($interview[0]['interview_interviewer_remarks']) && $interview[0]['interview_interviewer_remarks'] != '')
                                                {{ $interview[0]['interview_interviewer_remarks'] }}
                                            @else
                                                No Remarks By 1st Interviewer.
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
                                    <div class="col-md-12 fv-row">
                                        <label class="fw-bold">Remarks by 2nd interviewer ( {!! CustomHelper::getEmpProfileDiv($interview[0]['interview_interviewer2']) !!} )</label>
                                        <p class="pt-5">
                                            @if (!empty($interview[0]['interview_interviewer_remarks2']) && $interview[0]['interview_interviewer_remarks2'] != '')
                                                {{ $interview[0]['interview_interviewer_remarks2'] }}
                                            @else
                                                No Remarks By 2nd Interviewer.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                    <input type="hidden" name="interview_id" value="{{Request::segment(2)}}">
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
           
            <div class="modal fade" id="secondinterviewmodal" tabindex="-1" aria-hidden="true">
                    
                    <div class="modal-dialog modal-dialog-top mw-750px">
                        
                        <div class="modal-content rounded">
                            
                            <div class="modal-content rounded">
                                
                                <div class="modal-header">
                                <h4 class="modal-title pl-4">Edit 2nd Interviewer</h4>
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
                                
                                <form id="secondinterviewform">
                                    @CSRF
                                    
                                    <div class="row g-9 mt-1" >
                                        
                                        <div class="col-md-6 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                <span class="required">Selected Interviewer Name</span>
                                            </label>
                                        
                                            {{--begin::Input--}}
                                            <div class="d-flex align-items-center">
                                                {{--begin::--}}
                                                {{-- <input type="text" class="form-control  ps-12" id="interviewer_name" readonly=""> --}}
                                                   
                                                <input type="hidden" class="form-control  ps-12" name="interview_id"
                                                value="{{ $interview[0]['interview_id'] }}" readonly="">

                                                @php $count = 0  @endphp       
                                                @if (!empty($employees))
                                                    @foreach ($employees as $employee)
                                                        @if (!empty($interview[0]['interview_interviewer2']) && $interview[0]['interview_interviewer2'] == $employee['employee_id'])
                                                            @php $count ++; @endphp
                                                            <input type="text" class="form-control" value="{{ $employee['employee_code'] . ' - ' . $employee['employee_name'] }}" readonly>
                                                        @endif
                                                    @endforeach
                                                @endif
                                                @if($count == 0)
                                                    <input type="text" class="form-control" value="No Interviewer Selected" readonly>
                                                @endif
                                                
                                            </div>
                                            {{--end::Input--}}
                                        </div>

                                        <div class="col-md-6 fv-row">
                                            <label class="d-flex align-items-center fs-6 fw-semibold mb-2">
                                                <span class="required">New Interviewer </span>
                                            </label>
                                        
                                            {{--begin::Input--}}
                                            <div class="d-flex align-items-center">
                                               <select class="form-select mySelect3" data-control="select2" data-hide-search="true" name="interviewer_2" id="depart_name" required="">
                                                    <option value="">Select New Interviewer</option>
                                                    @if (!empty($employees))
                                                        @foreach ($employees as $employee)
                                                            <option value="{{ $employee['employee_id'] }}">
                                                                {{ $employee['employee_code'] . ' - ' . $employee['employee_name'] }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>        
                                            </div>
                                            {{--end::Input--}}
                                        </div>
                                    </div>

                                    <div class="text-center mt-5">
                                        <button type="reset" class="btn btn-light me-3" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" id="update-btn" class="btn btn-primary">
                                            <span class="indicator-label">Update</span>
                                            <span class="indicator-progress">Please wait 
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                        </button>
                                    </div>
                                    
                                </form>
                                
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
            </div>

            {{--end::Modal - New Target--}}

           
        </div>
    
    </div>

@endsection



@push('js-link')
<script>
    var interview_id = '{{ Request::segment(2) }}';
    
    function Copy(Url) {
        var Url = document.getElementById("url");
        Url.select();
        document.execCommand("copy");
    }
</script>
 <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
 <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script>
 <script src="{{url('/')}}/assets/js/firebase-interview-chat.js?js_v=2"></script>

@if(empty($interview[0]['interview_interviewer_remarks2']))
    <script src="{{url('assets/js/interview/my-interview-detail/edit-second-interviewer.js')}}"></script>
@endif

@if (empty($interview[0]['interview_interviewer_remarks']) && $interview[0]['interview_interviewer_remarks'] == '')
    <script src="{{url('assets/js/interview/my-interview-detail/interview-remarks1.js')}}"></script>
@endif

@if (empty($interview[0]['interview_interviewer_remarks2']) && $interview[0]['interview_interviewer_remarks2'] == '')
    <script src="{{url('assets/js/interview/my-interview-detail/interview-remarks2.js')}}"></script>
@endif


@endpush