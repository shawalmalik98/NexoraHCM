@extends('layout.main')

@section('main-section')

<link href="{{ url('assets/css/polling-style.css') }}" rel="stylesheet" type="text/css" />

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Polling Detail</h1>   
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
                <div class="col-md-5">
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-body pt-5">
                            
                            <div class="d-flex flex-stack">
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800  fs-6 fw-bold">Poll ID</span>
                                    </div>
                                    
                                    <span>
                                        @if (!empty($polling_details[0]['polling_id']))
                                            {{ $polling_details[0]['polling_id'] }}
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                    
                                </div>
                                
                            </div>
                            
                            <div class="separator separator-dashed my-4"></div>
                            
                            @if (!empty($polling_details[0]['polling_department']))
                                @php
                                    $department_name = explode("," ,$polling_details[0]['polling_department']);
                                    $count = 0
                                @endphp
                                @foreach ( $department_name as $department )
                                    @php $count ++ ; @endphp

                                    <div class="d-flex flex-stack">
                                        <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                            
                                            <div class="flex-grow-1 me-2">
                                                <span class="text-gray-800  fs-6 fw-bold">Department</span>
                                                
                                            </div>
                                                {!! CustomHelper::get_department_name($department) !!} 
                                        </div>
                                                
                                    </div>
                                    <div class="separator separator-dashed my-4"></div>
                                @endforeach
                            @else
                                <div class="d-flex flex-stack">
                                        <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                            
                                            <div class="flex-grow-1 me-2">
                                                <span class="text-gray-800  fs-6 fw-bold">Department</span>
                                                
                                            </div>
                                                N/A
                                        </div>
                                                
                                    </div>
                               
                                <div class="separator separator-dashed my-4"></div>
                            @endif   
                             
                            <div class="d-flex flex-stack">
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    
                                    <div class="flex-grow-1 me-2">
                                         <span class="text-gray-800  fs-6 fw-bold">Poll Title</span>
                                    </div>
                                        
                                    <span>
                                        @if (!empty($polling_details[0]['polling_title']))
                                            {{ $polling_details[0]['polling_title'] }}
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
                                         <span class="text-gray-800  fs-6 fw-bold">Winning Poll</span>
                                    </div>
                                        
                                    <span>
                                        @if (!empty($polling_option_check))
                                            @php 
                                                $winning_poll = $polling_option_check[0]['totalvote'];

                                            @endphp
                                        
                                            @foreach($polling_option_check as $key=> $polling_option)

                                                @if($winning_poll > $polling_option['totalvote'])

                                                @else
                                                    {{ $polling_option['polling_options'] }} 

                                                   @if($key != array_key_last($polling_option_check))
                                                        |
                                                   @endif
                                                @endif

                                            @endforeach
                                            
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                   
                                </div>
                                
                            </div>
                            
                            <div class="separator separator-dashed my-4"></div>
                            
                            @if (!empty($polling_options[0]['polling_options']))
                                @php $key = 0; @endphp
                                @foreach ($polling_options as $polling_option)
                                    <div class="d-flex flex-stack">
                                        <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                            
                                            <div class="flex-grow-1 me-2">
                                                <span class="text-gray-800  fs-6 fw-bold">Option {{++$key}} </span>
                                            </div>
                                                <a href="javascript:void(0);" onclick="pollingshow('{{$polling_details[0]['polling_id']}}','{{$polling_option['polling_option_id']}}' )">{{ $polling_option['polling_options'] }}</a>
                                        </div>
                    
                                    </div>
                                    <div class="separator separator-dashed my-4"></div>
                                @endforeach

                            @else
                                <div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                        
                                        <div class="flex-grow-1 me-2">
                                            <span class="text-gray-800  fs-6 fw-bold">Options</span>
                                        </div>

                                        N/A

                                    </div>
                
                                </div>

                                <div class="separator separator-dashed my-4"></div>
                            @endif

                            <div class="d-flex flex-stack">
                                <div class="d-flex align-items-center flex-row-fluid flex-wrap">
                                    
                                    <div class="flex-grow-1 me-2">
                                        <span class="text-gray-800  fs-6 fw-bold">Total Employees Voted</span>
                                    </div>
                                    
                                    <span>    
                                       @if (!empty($polling_status))
                                            {{($polling_status)}}
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
                                        <span class="text-gray-800  fs-6 fw-bold">Poll Created By</span>
                                    </div>
                                    
                                    <span>    
                                        @if(!empty($polling_details[0]['polling_created_by']))
                                            {!! CustomHelper::getEmpProfileDiv($polling_details[0]['polling_created_by']) !!}
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
                                        <span class="text-gray-800  fs-6 fw-bold">Poll Days</span>
                                    </div>
                                    
                                    <span>    
                                        @if (!empty($polling_details[0]['polling_days']))
                                            {{  $polling_details[0]['polling_days'] }}
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
                                        <span class="text-gray-800  fs-6 fw-bold">Poll Duration</span>
                                    </div>
                                    
                                    <span>
                                        @if (!empty($polling_details[0]['polling_duration']))
                                            {{  $polling_details[0]['polling_duration'] }}
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
                                        <span class="text-gray-800  fs-6 fw-bold">Poll Created At</span>
                                    </div>
                                    
                                    <span>
                                         @if (!empty($polling_details[0]['created_at']))
                                            {{  $polling_details[0]['created_at'] }}
                                        @else
                                            N/A
                                        @endif                                     
                                    </span>
                                    
                                </div>
                                
                            </div>

                            <div class="separator separator-dashed my-4"></div>
                            
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="card card-flush mb-10">
                        <div class="card-header border-0 pt-5">
                            <div class="card-title">
                                Employee Polling
                            </div>
                        </div> 
                        <div class="card-body">

                            <div class="row g-9 mb-2">
                                <div id="progresbar">
                                    
                                </div>
                                <div class="table-responsive">

                                    <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
                                        <div class="table-responsive">
                                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="pollingTable" aria-describedby="tableEmployee_info">
                                            
                                            </table>
                                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">
                                            
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    
    </div>

</div>

@endsection

@push('js-link')

<script src="{{url('assets/js/polling/polling-detail.js')}}"></script>

@endpush