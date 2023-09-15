@extends('layout.main')

@section('main-section')
    @php
        $permission = CustomHelper::access_permission();
        $attendance_summary = CustomHelper::attendance_summary_employee();
    @endphp

    <style>
        .custom-div-size-250{
            height:250px !important;
        }
        .imagenone{
            pointer-events: none;

        }
    </style>

    <link href="{{ url('assets/css/polling-style.css') }}" rel="stylesheet" type="text/css" />

    <div class="app-main flex-column flex-row-fluid" id="kt_app_main">

        <div class="d-flex flex-column flex-column-fluid">
            <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
                <div class="app-container col-12 d-flex flex-stack">
                    <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                        <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0"></h1>
                    </div>
                </div>
            </div>

            <div class="app-content flex-column-fluid">
                {{--begin::Content container--}}
                <div class="app-container">
                    <div class="row justify-content-center">
                        @if(empty($pollings))
                            @php $pollings = $voted_pollings; @endphp
                        @endif
                        @php $j=0; @endphp
                        @if(!empty($pollings))
                            @foreach($pollings as $polling)

                                <div class="col-md-12 mb-xl-10" id="polling_div_{{$polling['polling_id']}}">
                                    <div class="card wrapper">
                                        <div class="row">
                                            <input type="hidden" name="poll" value="{{ $polling['polling_id'] }}">

                                            <div class="col-md-12">
                                                <header class="mb-4 ms-4">{{ $polling['polling_title'] }} <br></header>
                                            </div>

                                            @php $i=0; @endphp
                                            @foreach( $polling['polling_options'] as $polling_option)
                                                @php $i++; $j++; @endphp

                                                @if($i == 1)

                                                    <div class="col-xl-6 col-md-12" id="{{$j}}">
                                                        @if(!in_array($polling['polling_id'],$employee_voting))
                                                            <span onclick="pollingshow('{{$polling['polling_id']}}','{{$polling_option['polling_option_id']}}')">
                                                        @else
                                                            <span>
                                                        @endif

                                                        <div class="row align-items-center mb-4">
                                                            @if(!in_array($polling['polling_id'],$employee_voting))
                                                                <div class="col-12">
                                                                    <div class="card-body p-0">
                                                                        <div class="mt-4">
                                                                        <a href="javascript:void(0);" class="btn btn-outline btn-block w-100 skill-name rounded-pill p-3">{{$polling_option['polling_options']}}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                            @if(in_array($polling['polling_id'],$employee_voting))
                                                                <div class="row align-items-center mb-4">
                                                                    <div class="col-10">
                                                                        <div class="progress">
                                                                            <div class="progress-bar light-grey" role="progressbar" aria-valuenow="{{$polling_option['percentage']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$polling_option['percentage']}}%">
                                                                            <span class="skill-name">{{$polling_option['polling_options']}}</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-2 text-left"><span>{{$polling_option['percentage']}}%</span></div>
                                                                </div>
                                                            @endif
                                                        </span>
                                                    </div>

                                                @elseif($i == 2)

                                                    <div class="col-xl-6 col-md-12" id="{{$j}}">
                                                        @if(!in_array($polling['polling_id'],$employee_voting))
                                                            <span onclick="pollingshow('{{$polling['polling_id']}}','{{$polling_option['polling_option_id']}}' ) ">
                                                        @else
                                                            <span>
                                                        @endif

                                                        <div class="row align-items-center mb-4">
                                                            @if(!in_array($polling['polling_id'],$employee_voting))
                                                                <div class="col-12">
                                                                    <div class="card-body p-0">
                                                                        <div class="mt-4">
                                                                        <a href="javascript:void(0);" class="btn btn-outline btn-block w-100 skill-name rounded-pill p-3">{{$polling_option['polling_options']}}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if(in_array($polling['polling_id'],$employee_voting))
                                                            <div class="row align-items-center mb-4">
                                                                <div class="col-10">
                                                                    <div class="progress">
                                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="{{$polling_option['percentage']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$polling_option['percentage']}}%">
                                                                        <span class="skill-name">{{$polling_option['polling_options']}}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 text-left"><span>{{$polling_option['percentage']}}%</span></div>
                                                            </div>
                                                        @endif
                                                        </span>
                                                    </div>

                                                @elseif($i == 3)

                                                    <div class="col-xl-6 col-md-12" id="{{$j}}">
                                                        @if(!in_array($polling['polling_id'],$employee_voting))
                                                            <span onclick="pollingshow('{{$polling['polling_id']}}','{{$polling_option['polling_option_id']}}' ) ">
                                                        @else
                                                            <span>
                                                        @endif

                                                        <div class="row align-items-center mb-4">
                                                            @if(!in_array($polling['polling_id'],$employee_voting))
                                                                <div class="col-12">
                                                                    <div class="card-body p-0">
                                                                        <div class="mt-4">
                                                                        <a href="javascript:void(0);" class="btn btn-outline btn-block w-100 skill-name rounded-pill p-3">{{$polling_option['polling_options']}}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if(in_array($polling['polling_id'],$employee_voting))
                                                            <div class="row align-items-center mb-4">
                                                                <div class="col-10">
                                                                    <div class="progress">
                                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="{{$polling_option['percentage']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$polling_option['percentage']}}%">
                                                                        <span class="skill-name">{{$polling_option['polling_options']}}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 text-left"><span>{{$polling_option['percentage']}}%</span></div>
                                                            </div>
                                                        @endif
                                                        </span>
                                                    </div>

                                                @elseif($i == 4)

                                                    <div class="col-xl-6 col-md-12" id="{{$j}}">
                                                        @if(!in_array($polling['polling_id'],$employee_voting))
                                                            <span onclick="pollingshow('{{$polling['polling_id']}}','{{$polling_option['polling_option_id']}}' ) ">
                                                        @else
                                                            <span>
                                                        @endif
                                                        <div class="row align-items-center mb-4">
                                                            @if(!in_array($polling['polling_id'],$employee_voting))
                                                                <div class="col-12">
                                                                    <div class="card-body p-0">
                                                                        <div class="mt-4">
                                                                        <a href="javascript:void(0);" class="btn btn-outline btn-block w-100 skill-name rounded-pill p-3">{{$polling_option['polling_options']}}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        @if(in_array($polling['polling_id'],$employee_voting))
                                                            <div class="row align-items-center mb-4">
                                                                <div class="col-10">
                                                                    <div class="progress">
                                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="{{$polling_option['percentage']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$polling_option['percentage']}}%">
                                                                        <span class="skill-name">{{$polling_option['polling_options']}}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 text-left"><span>{{$polling_option['percentage']}}%</span></div>
                                                            </div>
                                                        @endif
                                                        <span>
                                                    </div>

                                                @else

                                                    <div class="col-xl-6 col-md-12" id="{{$j}}">
                                                        @if(!in_array($polling['polling_id'],$employee_voting))
                                                            <span onclick="pollingshow('{{$polling['polling_id']}}','{{$polling_option['polling_option_id']}}' ) ">
                                                        @else
                                                            <span>
                                                        @endif
                                                        <div class="row align-items-center mb-4">
                                                            @if(!in_array($polling['polling_id'],$employee_voting))
                                                                <div class="col-12">
                                                                    <div class="card-body p-0">
                                                                        <div class="mt-4">
                                                                        <a href="javascript:void(0);" class="btn btn-outline btn-block w-100 skill-name rounded-pill p-3">{{$polling_option['polling_options']}}</a>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        </div>

                                                        @if(in_array($polling['polling_id'],$employee_voting))
                                                            <div class="row align-items-center mb-4">
                                                                <div class="col-10">
                                                                    <div class="progress">
                                                                        <div class="progress-bar light-grey" role="progressbar" aria-valuenow="{{$polling_option['percentage']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$polling_option['percentage']}}%">
                                                                        <span class="skill-name">{{$polling_option['polling_options']}}</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-2 text-left"><span>{{$polling_option['percentage']}}%</span></div>
                                                            </div>
                                                        @endif
                                                        </span>
                                                    </div>

                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        @endif
                    </div>

                    @if(!empty($alerts))
                        @foreach($alerts as $alert)
                            <div class="alert alert-primary d-flex align-items-center p-5 mb-10" id="alert_div{{ $alert['alert_id'] }}">
                                <span class="svg-icon svg-icon-2hx svg-icon-primary me-4">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path opacity="0.3" d="M20.5543 4.37824L12.1798 2.02473C12.0626 1.99176 11.9376 1.99176 11.8203 2.02473L3.44572 4.37824C3.18118 4.45258 3 4.6807 3 4.93945V13.569C3 14.6914 3.48509 15.8404 4.4417 16.984C5.17231 17.8575 6.18314 18.7345 7.446 19.5909C9.56752 21.0295 11.6566 21.912 11.7445 21.9488C11.8258 21.9829 11.9129 22 12.0001 22C12.0872 22 12.1744 21.983 12.2557 21.9488C12.3435 21.912 14.4326 21.0295 16.5541 19.5909C17.8169 18.7345 18.8277 17.8575 19.5584 16.984C20.515 15.8404 21 14.6914 21 13.569V4.93945C21 4.6807 20.8189 4.45258 20.5543 4.37824Z" fill="currentColor" />
                                        <path d="M10.5606 11.3042L9.57283 10.3018C9.28174 10.0065 8.80522 10.0065 8.51412 10.3018C8.22897 10.5912 8.22897 11.0559 8.51412 11.3452L10.4182 13.2773C10.8099 13.6747 11.451 13.6747 11.8427 13.2773L15.4859 9.58051C15.771 9.29117 15.771 8.82648 15.4859 8.53714C15.1948 8.24176 14.7183 8.24176 14.4272 8.53714L11.7002 11.3042C11.3869 11.6221 10.874 11.6221 10.5606 11.3042Z" fill="currentColor" />
                                    </svg>
                                </span>
                                <div class="d-flex flex-column">
                                    <h4 class="mb-1 text-primary">{{ $alert['title'] }}</h4>
                                    <p>{{ $alert['message'] }}</p>
                                </div>
                                <button type="button" onclick="alert_remove({{$alert['alert_id']}})" class="position-absolute position-sm-relative m-2 m-sm-0 top-0 end-0 btn btn-sm btn-icon btn-active-color-primary ms-sm-auto" aria-label="Close">
                                    <span class="svg-icon svg-icon-1">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                            <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                                        </svg>
                                    </span>
                                </button>
                            </div>
                        @endforeach
                    @endif

                    @if(!empty($total_count))

                    <div class="alert alert-primary d-flex align-items-center p-5">

                        <div class="d-flex flex-column">

                            <h4 class="mb-1">Meetings Acknowledgement</h4>
                            <span>You have ({{$total_count}}) meetings scheduled for tomorrow / today. <a href="{{url('my-meetings')}}">Click here to see details.</a></span>
                        </div>
                    </div>
                    @endif
                    @if(!empty($login_user) && date('d-m', strtotime($login_user[0]['dob'])) == date('d-m'))
                        <div class="card border-transparent mb-5" data-theme="light" style="background-color: #1C325E;">
                            <div class="card-body d-flex ps-xl-15">
                                <div class="m-0">
                                    <div class="position-relative fs-2x z-index-2 fw-bold text-white mb-7">
                                        <span class="me-2">Happy Birthday
                                            <span class="position-relative d-inline-block text-danger">
                                                <p class="text-danger">Champ !</p>
                                            </span>
                                        </span>
                                        <br>Best of luck for the upcoming years.
                                    </div>
                                </div>
                                <img src="{{ url('portal_assets/images/17-dark.png') }}" class="position-absolute me-3 bottom-0 end-0 h-200px" alt="">
                            </div>
                        </div>
                    @endif
                    @if($login_user[0]['employee_can_ra'] == 1 && !empty($my_team_mates))
                        <div class="row g-5 g-xl-10 mb-5">

                            <div class="col-xl-4 mb-xl-10 ">
                                <div class="card card-flush" style="height : 500px;">
                                    <div class="card-header rounded bgi-no-repeat bgi-size-cover bgi-position-y-top bgi-position-x-center align-items-start h-150px" style="background-image:url('{{ url('/') }}/assets/media/svg/top-green.png" data-theme="light">
                                        <h3 class="card-title align-items-start flex-column text-white pt-5">
                                            <span class="fw-bold fs-2x mb-3">{{ $login_user[0]['employee_name'] }}</span>
                                            <div class="fs-4 text-white ">
                                                <span class="opacity-75">You have following remaining leaves</span>
                                            </div>
                                        </h3>
                                    </div>

                                    <div class="card-body">
                                        <div class="">
                                            <div class="d-flex flex-stack">
                                                <div class="text-gray-700 fw-semibold fs-6 me-2">Casual Leave(s)</div>                   
                                                <div class="d-flex align-items-senter">
                                                    <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>  
                                                    <span class="text-gray-900 fw-bolder fs-6">{{ $casual_used }} &nbsp;</span> 
                                                    <span class="text-gray-400 fw-bold fs-6">/&nbsp; {{ $leaves_entitiled[0]['leave_allotement'] }}</span> 
                                                </div>  
                                            </div>
                                            <div class="separator separator-dashed my-3"></div>
                                            <div class="d-flex flex-stack">
                                                <div class="text-gray-700 fw-semibold fs-6 me-2">Sick Leave(s)</div>                   
                                                <div class="d-flex align-items-senter">
                                                    <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>  
                                                    <span class="text-gray-900 fw-bolder fs-6">{{ $sick_used }} &nbsp;</span> 
                                                    <span class="text-gray-400 fw-bold fs-6">/&nbsp; {{ $leaves_entitiled[1]['leave_allotement'] }}</span> 
                                                </div>  
                                            </div>
                                            <div class="separator separator-dashed my-3"></div>
                                            <div class="d-flex flex-stack">
                                                <div class="text-gray-700 fw-semibold fs-6 me-2">Annual Leave(s)</div>                   
                                                <div class="d-flex align-items-senter">
                                                    <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>  
                                                    <span class="text-gray-900 fw-bolder fs-6">{{ $annual_used }} &nbsp;</span> 
                                                    <span class="text-gray-400 fw-bold fs-6">/&nbsp; {{ $leaves_entitiled[2]['leave_allotement'] }}</span> 
                                                </div>  
                                            </div>
                                            <div class="separator separator-dashed my-3"></div>
                                        </div>
                                        <div class="text-center pt-10">
                                            <p>Today's Working Hour(s)</p>
                                            <span class="fs-4x fw-bold text-gray-800 txt" id="clock"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="card card-flush" style="height : 500px;">
                                    <div class="card-header pt-7">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">Reporting Authorities</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">You are supposed to report your activities to the following authorities</span>
                                        </h3>
                                    </div>
                                    <div class="card-body pt-7">
                                        <div class="row g-5 g-xl-9 mb-xl-3 mt-3">
                                            <div class="col-md-6 col-6 mb-3 mb-sm-0">
                                                <div class="m-0">
                                                    <div class="card-rounded position-relative mb-5">
                                                        <div class="mx-auto mb-6 bgi-no-repeat bgi-size-contain bgi-position-center rounded-circle w-125px h-125px"
                                                            @if(empty($senior_manager))
                                                                style="background-image:url('{{ asset('/portal_assets/images/default-img.png') }}')">
                                                            @elseif(empty($senior_manager[0]['profile_avatar'] ))
                                                                style="background-image:url('{{ asset('/portal_assets/images/default-img.png') }}')">
                                                            @else
                                                                @if(file_exists('portal_assets/profile_pictures/'.$senior_manager[0]['profile_avatar']))
                                                                    style="background-image:url('{{asset('portal_assets/profile_pictures').'/'.$senior_manager[0]['profile_avatar'] }} ')">
                                                                 @else
                                                                   style="background-image:url('{{ asset('/portal_assets/images/default-img.png') }}')">
                                                                @endif

                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="m-0">
                                                        <a  class="text-gray-800 fs-3 fw-bold d-block mb-2 text-center">HOD</a>
                                                        <span class="fw-bold fs-6 text-gray-400 d-block lh-1 text-center">
                                                            @if(empty($senior_manager))
                                                                Not Assigned
                                                            @else
                                                                {{ $senior_manager[0]['employee_name']}}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-6 mb-3 mb-sm-0">
                                                <div class="m-0">
                                                    <div class="card-rounded position-relative mb-5">
                                                        <div class="mx-auto mb-6 bgi-no-repeat bgi-size-contain bgi-position-center rounded-circle w-125px h-125px"
                                                            @if(empty($junior_manager))
                                                                style="background-image:url('{{ asset('portal_assets/images/default-img.png') }}')">
                                                            @elseif(empty($junior_manager[0]['profile_avatar'] ))
                                                                style="background-image:url('{{ asset('portal_assets/images/default-img.png') }}')">
                                                            @else
                                                                @if(file_exists('portal_assets/profile_pictures/'.$junior_manager[0]['profile_avatar']))
                                                                    style="background-image:url('{{asset('portal_assets/profile_pictures').'/'.$junior_manager[0]['profile_avatar'] }} ')">
                                                                 @else
                                                                   style="background-image:url('{{ asset('/portal_assets/images/default-img.png') }}')">
                                                                @endif

                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="m-0">
                                                        <a  class="text-gray-800 fs-3 fw-bold d-block mb-2 text-center">Line Manager</a>
                                                        <span class="fw-bold fs-6 text-gray-400 d-block lh-1 text-center">
                                                            @if(empty($junior_manager))
                                                                Not Assigned
                                                            @else
                                                                {{ $junior_manager[0]['employee_name'] }}
                                                            @endif
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{--<div class="col-xl-4">
                                <div class="card card-flush" style="height: 500px;">
                                    <div class="card-header pt-7" >
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">MegaHCM Mobile App</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Launching our newly built HCM Mobile app. Click here to download.</span>
                                        </h3>
                                    </div>

                                    <div class="card-body">
                                        
                                            <img src="{{ asset('portal_assets/images/mobile-app2.webp') }}" style="width:49%;height:245px;object-fit: contain;border-radius:10px;" draggable="false">
                                            <img src="{{ asset('portal_assets/images/mobile-app.webp') }}" style="width:49%;height:245px;object-fit: contain;border-radius:10px;" draggable="false">
                                            
                                            <a target="_blank" href="https://play.google.com/store/apps/details?id=com.sourcecodeemployees" style="display:block; text-align:center;"><img src="{{ asset('portal_assets/images/play-store.png') }}" style="margin-top:15px;width:100%;max-width: 250px;" draggable="false"></a>
                                    </div>
                                </div>
                            </div>--}}
                            @if(!empty($my_team_mates))
                                <div class="col-xl-4">
                                    <div class="card card-flush" style="height: 500px;" >
                                        <div class="card-header pt-7" >
                                            <h3 class="card-title align-items-start flex-column">
                                                <span class="card-label fw-bold text-dark">My Team</span>
                                                <span class="text-gray-400 mt-1 fw-semibold fs-6">Approx. {{ count($my_team_mates) }} employees under your reporting</span>
                                            </h3>
                                        </div>

                                        <div class="card-body">
                                            <div class="hover-scroll-overlay-y pe-6 me-n6" style="height: 342px ">

                                                @foreach($my_team_mates as $employee)
                                                    <div class="border border-dashed border-gray-300 rounded px-7 py-3 mb-6">
                                                        <div class="mb-3">
                                                            <div class="me-3">
                                                                <div class ="row">
                                                                    <div class = "col-md-3">
                                                                        @if(empty($employee['profile_avatar']))
                                                                            <img draggable="false" src="{{ asset('portal_assets/images/default-img.png') }}" class="mt-2 w-50px h-50px ms-n1 me-1 rounded-circle imagenone" alt="">
                                                                        @else
                                                                            @if(file_exists('portal_assets/profile_pictures/'.$employee['profile_avatar']))
                                                                                <img draggable="false" src="{{asset('portal_assets/profile_pictures').'/'.$employee['profile_avatar'] }}" class="mt-2 w-50px h-50px ms-n1 me-1 rounded-circle imagenone" alt="">
                                                                            @else
                                                                                <img draggable="false" alt="Logo" src="{{ asset('/portal_assets/images/default-img.png') }}" class="mt-2 w-50px h-50px ms-n1 me-1 rounded-circle imagenone"/>
                                                                            @endif
                                                                        @endif


                                                                    </div>

                                                                    <div class = "col-md-9 pt-2">
                                                                        <span class="text-gray-800 fw-bold">{{$employee['employee_name']}}</span><br>

                                                                        <span class="text-muted d-block fw-semibold">{{ $employee['employee_department'] }} - {{$employee['employee_designation']}}</span>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(!empty($setting[0]['linked_post1']) && !empty($setting[0]['linked_post2']))
                            @php  $linked_post1 =  substr($setting[0]['linked_post1'],0,7);
                                $linked_post2 =  substr($setting[0]['linked_post2'],0,7);
                            @endphp

                            @if($linked_post1 == '<iframe' && $linked_post2 == '<iframe' )
                                <div class="row g-5 g-xl-10 mb-5">
                                    <div class="col-md-6">
                                        <div class="card card-flush" style="height:443px;">
                                            <div class="card-body">
                                                <div class="hover-scroll-overlay-y pe-6 me-n6" style="height:370px">

                                                    {!!$setting[0]['linked_post1']!!}


                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-flush" style="height:443px;">
                                            <div class="card-body">
                                                <div class="hover-scroll-overlay-y pe-6 me-n6" style="height:370px ">
                                                    {!!$setting[0]['linked_post2']!!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endif

                        @endif
                    @else
                        <div class="row g-5 g-xl-10 mb-5">

                            <div class="col-xl-4 mb-xl-10 ">
                                <div class="card card-flush" style="height : 500px;">
                                    <div class="card-header rounded bgi-no-repeat bgi-size-cover bgi-position-y-top bgi-position-x-center align-items-start h-150px" style="background-image:url('{{ url('/') }}/assets/media/svg/top-green.png" data-theme="light">
                                        <h3 class="card-title align-items-start flex-column text-white pt-5">
                                            <span class="fw-bold fs-2x mb-3">{{ $login_user[0]['employee_name'] }}</span>
                                            <div class="fs-4 text-white ">
                                                <span class="opacity-75">You have following remaining leaves</span>
                                            </div>
                                        </h3>
                                    </div>

                                    <div class="card-body">
                                        <div class="">
                                            <div class="d-flex flex-stack">
                                                <div class="text-gray-700 fw-semibold fs-6 me-2">Casual Leave(s)</div>                   
                                                <div class="d-flex align-items-senter">
                                                    <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>  
                                                    <span class="text-gray-900 fw-bolder fs-6">{{ $casual_used }} &nbsp;</span> 
                                                    <span class="text-gray-400 fw-bold fs-6">/&nbsp; {{ $leaves_entitiled[0]['leave_allotement'] }}</span> 
                                                </div>  
                                            </div>
                                            <div class="separator separator-dashed my-3"></div>
                                            <div class="d-flex flex-stack">
                                                <div class="text-gray-700 fw-semibold fs-6 me-2">Sick Leave(s)</div>                   
                                                <div class="d-flex align-items-senter">
                                                    <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>  
                                                    <span class="text-gray-900 fw-bolder fs-6">{{ $sick_used }} &nbsp;</span> 
                                                    <span class="text-gray-400 fw-bold fs-6">/&nbsp; {{ $leaves_entitiled[1]['leave_allotement'] }}</span> 
                                                </div>  
                                            </div>
                                            <div class="separator separator-dashed my-3"></div>
                                            <div class="d-flex flex-stack">
                                                <div class="text-gray-700 fw-semibold fs-6 me-2">Annual Leave(s)</div>                   
                                                <div class="d-flex align-items-senter">
                                                    <i class="ki-duotone ki-arrow-up-right fs-2 text-success me-2"><span class="path1"></span><span class="path2"></span></i>  
                                                    <span class="text-gray-900 fw-bolder fs-6">{{ $annual_used }} &nbsp;</span> 
                                                    <span class="text-gray-400 fw-bold fs-6">/&nbsp; {{ $leaves_entitiled[2]['leave_allotement'] }}</span> 
                                                </div>  
                                            </div>
                                            <div class="separator separator-dashed my-3"></div>
                                        </div>
                                        <div class="text-center pt-10">
                                            <p>Today's Working Hour(s)</p>
                                            <span class="fs-4x fw-bold text-gray-800 txt" id="clock"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="card card-flush" style="height: 500px;">
                                    <div class="card-header pt-7" >
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">MegaHCM Mobile App</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Launching our newly built HCM Mobile app. Click here to download.</span>
                                        </h3>
                                    </div>

                                    <div class="card-body">
                                        
                                            <img src="{{ asset('portal_assets/images/mobile-app2.webp') }}" style="width:49%;height:245px;object-fit: contain;border-radius:10px;" draggable="false">
                                            <img src="{{ asset('portal_assets/images/mobile-app.webp') }}" style="width:49%;height:245px;object-fit: contain;border-radius:10px;" draggable="false">
                                            
                                            <a target="_blank" href="https://play.google.com/store/apps/details?id=com.sourcecodeemployees" style="display:block; text-align:center;"><img src="{{ asset('portal_assets/images/play-store.png') }}" style="margin-top:15px;width:100%;max-width: 250px;" draggable="false"></a>
                                        {{-- </div> --}}
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4">
                                <div class="card card-flush" style="height : 500px;">
                                    <div class="card-header pt-7">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">Reporting Authorities</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">You are supposed to report your activities to the following authorities</span>
                                        </h3>
                                    </div>
                                    <div class="card-body pt-7">
                                        <div class="row g-5 g-xl-9 mb-xl-3 mt-3">
                                            <div class="col-md-6 col-6 mb-3 mb-sm-0">
                                                <div class="m-0">
                                                    <div class="card-rounded position-relative mb-5">
                                                        <div class="mx-auto mb-6 bgi-no-repeat bgi-size-contain bgi-position-center rounded-circle w-125px h-125px"
                                                            @if(empty($senior_manager))
                                                                style="background-image:url('{{ asset('/portal_assets/images/default-img.png') }}')">
                                                            @elseif(empty($senior_manager[0]['profile_avatar'] ))
                                                                style="background-image:url('{{ asset('/portal_assets/images/default-img.png') }}')">
                                                            @else
                                                                @if(file_exists('portal_assets/profile_pictures/'.$senior_manager[0]['profile_avatar']))
                                                                    style="background-image:url('{{asset('portal_assets/profile_pictures').'/'.$senior_manager[0]['profile_avatar'] }} ')">
                                                                @else
                                                                    style="background-image:url('{{ asset('/portal_assets/images/default-img.png') }}')">
                                                                @endif

                                                            @endif

                                                        </div>
                                                    </div>

                                                    <div class="m-0">
                                                        <a  class="text-gray-800 fs-3 fw-bold d-block mb-2 text-center">HOD</a>
                                                        <span class="fw-bold fs-6 text-gray-400 d-block lh-1 text-center">
                                                            @if(empty($senior_manager))
                                                                Not Assigned
                                                            @else
                                                                {{ $senior_manager[0]['employee_name']}}
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-6 mb-3 mb-sm-0">
                                                <div class="m-0">
                                                    <div class="card-rounded position-relative mb-5">
                                                        <div class="mx-auto mb-6 bgi-no-repeat bgi-size-contain bgi-position-center rounded-circle w-125px h-125px"
                                                            @if(empty($junior_manager))
                                                                style="background-image:url('{{ asset('portal_assets/images/default-img.png') }}')">
                                                            @elseif(empty($junior_manager[0]['profile_avatar'] ))
                                                                style="background-image:url('{{ asset('portal_assets/images/default-img.png') }}')">
                                                            @else
                                                                @if(file_exists('portal_assets/profile_pictures/'.$junior_manager[0]['profile_avatar']))
                                                                    style="background-image:url('{{asset('portal_assets/profile_pictures').'/'.$junior_manager[0]['profile_avatar'] }} ')">
                                                                @else
                                                                    style="background-image:url('{{ asset('/portal_assets/images/default-img.png') }}')">
                                                                @endif

                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="m-0">
                                                        <a  class="text-gray-800 fs-3 fw-bold d-block mb-2 text-center">Line Manager</a>
                                                        <span class="fw-bold fs-6 text-gray-400 d-block lh-1 text-center">
                                                            @if(empty($junior_manager))
                                                                Not Assigned
                                                            @else
                                                                {{ $junior_manager[0]['employee_name'] }}
                                                            @endif
                                                        </span>
                                                    </div>

                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        @if(!empty($setting[0]['linked_post1']) && !empty($setting[0]['linked_post2']))
                            @php  $linked_post1 =  substr($setting[0]['linked_post1'],0,7);
                                $linked_post2 =  substr($setting[0]['linked_post2'],0,7);
                            @endphp

                            @if($linked_post1 == '<iframe' && $linked_post2 == '<iframe' )
                                <div class="row g-5 g-xl-10 mb-5">
                                    <div class="col-md-6">
                                        <div class="card card-flush" style="height:443px;">
                                            <div class="card-body">
                                                <div class="hover-scroll-overlay-y pe-6 me-n6" style="height:370px">

                                                    {!!$setting[0]['linked_post1']!!}


                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card card-flush" style="height:443px;">
                                            <div class="card-body">
                                                <div class="hover-scroll-overlay-y pe-6 me-n6" style="height:370px ">

                                                    {!!$setting[0]['linked_post2']!!}


                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            @endif

                        @endif
                    @endif

                    @if(!empty($permission) && $permission[12]['module_accessibility'] == 1 && $permission[12]['permission_status'] == 1)
                        <div class="row  gy-5 g-xl-10">
                            <div class="col-md-3 col-6 mb-10">

                                <div class="card card-flush">
                                    <a href="{{url('employees')}}" >
                                        <div class="card-header pt-5">

                                            <div class="card-title d-flex flex-column">

                                                <div class="m-0 mb-5">
                                                    <span class="svg-icon svg-icon-2hx svg-icon-gray-600">
                                                        <img  src="{{ asset('portal_assets/dashboard_icons/ActiveEmployees.png') }}" />
                                                    </span>
                                                </div>

                                                <div class="d-flex align-items-center">

                                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $active_employees }}</span>

                                                    <span class="badge badge-light-success fs-base">

                                                    <span class="svg-icon svg-icon-5 svg-icon-success ms-n1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    @php $active_employee_ratio =   ($active_employees /$total_employees)*100; @endphp
                                                        {{substr($active_employee_ratio,0,5).'%'}}</span>

                                                </div>

                                                <span class="text-gray-400 pt-1 fw-semibold fs-6">Active Employees</span>

                                            </div>

                                        </div>

                                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">

                                            <div class="d-flex flex-center me-5 pt-2">
                                                @php $active_gender = $active_male_employees + $active_female_employees;  @endphp
                                                <div id="active_male_female" style="min-width: 70px; min-height: 70px" data-kt-size="70" data-kt-line="11"></div>
                                            </div>

                                            <div class="d-flex flex-column content-justify-center flex-row-fluid">

                                                <div class="d-flex fw-semibold align-items-center">

                                                    <div class="bullet w-8px h-3px rounded-2  bg-primary me-3"></div>

                                                    <div class="text-gray-500 flex-grow-1 me-4">Male</div>

                                                    <div class="fw-bolder text-gray-700 text-xxl-end">{{$active_male_employees}}</div>

                                                </div>

                                                <div class="d-flex fw-semibold align-items-center my-3">

                                                    <div class="bullet w-8px h-3px rounded-2  bg-success me-3"></div>

                                                    <div class="text-gray-500 flex-grow-1 me-4">Female</div>

                                                    <div class="fw-bolder text-gray-700 text-xxl-end">{{$active_female_employees}}</div>

                                                </div>

                                            </div>

                                        </div>
                                    </a>

                                </div>


                            </div>

                            <div class="col-md-3 col-6 mb-10 ">

                                <div class="card card-flush">
                                    <a href="{{url('employees?employee_isactive=2')}}">
                                        <div class="card-header pt-5">

                                            <div class="card-title d-flex flex-column">

                                                <div class="m-0 mb-5">
                                                    <span class="svg-icon svg-icon-2hx svg-icon-gray-600">
                                                        <img src="{{ asset('portal_assets/dashboard_icons/DeactiveEmployees.png') }}" />
                                                    </span>
                                                </div>

                                                <div class="d-flex align-items-center">

                                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $deactive_employees }}</span>

                                                    <span class="badge badge-light-success fs-base">

                                                    <span class="svg-icon svg-icon-5 svg-icon-success ms-n1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    @php $deactive_employee_ratio = ($deactive_employees /$total_employees)*100; @endphp
                                                    {{substr($deactive_employee_ratio,0,5).'%'}}

                                                </div>

                                                <span class="text-gray-400 pt-1 fw-semibold fs-6">Deactive Employees</span>

                                            </div>

                                        </div>

                                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">

                                            <div class="d-flex flex-center me-5 pt-2">
                                                <div id="deactive_male_female" style="min-width: 70px; min-height: 70px" data-kt-size="70" data-kt-line="11"></div>
                                            </div>

                                            <div class="d-flex flex-column content-justify-center flex-row-fluid">

                                                <div class="d-flex fw-semibold align-items-center">

                                                    <div class="bullet w-8px h-3px rounded-2  bg-primary me-3"></div>

                                                    <div class="text-gray-500 flex-grow-1 me-4">Male</div>

                                                    <div class="fw-bolder text-gray-700 text-xxl-end">{{$deactive_male_employees}}</div>

                                                </div>

                                                <div class="d-flex fw-semibold align-items-center my-3">

                                                    <div class="bullet w-8px h-3px rounded-2  bg-success me-3"></div>

                                                    <div class="text-gray-500 flex-grow-1 me-4">Female</div>

                                                    <div class="fw-bolder text-gray-700 text-xxl-end">{{$deactive_female_employees}}</div>

                                                </div>

                                            </div>

                                        </div>
                                    </a>

                                </div>


                            </div>

                            <div class="col-md-3 col-6 mb-10">

                                <div class="card card-flush">
                                    <a href="{{url('employees?employee_isactive=1&employee_status=3')}}" >
                                        <div class="card-header pt-5">

                                            <div class="card-title d-flex flex-column">

                                                <div class="m-0 mb-5">
                                                    <span class="svg-icon svg-icon-2hx svg-icon-gray-600">
                                                        <img  src="{{ asset('portal_assets/dashboard_icons/PermanentEmployees.png') }}" />
                                                    </span>
                                                </div>

                                                <div class="d-flex align-items-center">

                                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $permanent_employees }}</span>

                                                    <span class="badge badge-light-success fs-base">

                                                    <span class="svg-icon svg-icon-5 svg-icon-success ms-n1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    @php $permanent_employee_ratio =   ($permanent_employees /$active_employees)*100; @endphp
                                                    {{substr($permanent_employee_ratio,0,5).'%'}}

                                                </div>

                                                <span class="text-gray-400 pt-1 fw-semibold fs-6">Permanent Employees (Active)</span>

                                            </div>

                                        </div>

                                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">

                                            <div class="d-flex flex-center me-5 pt-2">
                                                <div id="permanent_male_female" style="min-width: 70px; min-height: 70px" data-kt-size="70" data-kt-line="11"></div>
                                            </div>

                                            <div class="d-flex flex-column content-justify-center flex-row-fluid">

                                                <div class="d-flex fw-semibold align-items-center">

                                                    <div class="bullet w-8px h-3px rounded-2  bg-primary me-3"></div>

                                                    <div class="text-gray-500 flex-grow-1 me-4">Male</div>

                                                    <div class="fw-bolder text-gray-700 text-xxl-end">{{$permanent_male_employees}}</div>

                                                </div>

                                                <div class="d-flex fw-semibold align-items-center my-3">

                                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>

                                                    <div class="text-gray-500 flex-grow-1 me-4">Female</div>

                                                    <div class="fw-bolder text-gray-700 text-xxl-end">{{$permanent_female_employees}}</div>

                                                </div>

                                            </div>

                                        </div>
                                    </a>

                                </div>


                            </div>

                            <div class="col-md-3 col-6 mb-10">

                                <div class="card card-flush">
                                    <a href="{{url('employees?employee_isactive=1&employee_status=2')}}">
                                        <div class="card-header pt-5">

                                            <div class="card-title d-flex flex-column">

                                                <div class="m-0 mb-5">
                                                    <span class="svg-icon svg-icon-2hx svg-icon-gray-600">
                                                        <img  src="{{ asset('portal_assets/dashboard_icons/ProbationEmployees.png') }}" />
                                                    </span>
                                                </div>

                                                <div class="d-flex align-items-center">

                                                    <span class="fs-2hx fw-bold text-dark me-2 lh-1 ls-n2">{{ $probation_employees }}</span>

                                                    <span class="badge badge-light-success fs-base">

                                                    <span class="svg-icon svg-icon-5 svg-icon-success ms-n1">
                                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="currentColor" />
                                                            <path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                    @php $probation_employee_ratio =   ($probation_employees /$active_employees)*100; @endphp
                                                    {{substr($probation_employee_ratio,0,5).'%'}}

                                                </div>

                                                <span class="text-gray-400 pt-1 fw-semibold fs-6">Probation Employees (Active)</span>

                                            </div>

                                        </div>

                                        <div class="card-body pt-2 pb-4 d-flex flex-wrap align-items-center">

                                            <div class="d-flex flex-center me-5 pt-2">
                                                <div id="probation_male_female" style="min-width: 70px; min-height: 70px" data-kt-size="70" data-kt-line="11"></div>
                                            </div>

                                            <div class="d-flex flex-column content-justify-center flex-row-fluid">

                                                <div class="d-flex fw-semibold align-items-center">

                                                    <div class="bullet w-8px h-3px rounded-2  bg-primary me-3"></div>

                                                    <div class="text-gray-500 flex-grow-1 me-4">Male</div>

                                                    <div class="fw-bolder text-gray-700 text-xxl-end">{{$probation_male_employees}}</div>

                                                </div>

                                                <div class="d-flex fw-semibold align-items-center my-3">

                                                    <div class="bullet w-8px h-3px rounded-2 bg-success me-3"></div>

                                                    <div class="text-gray-500 flex-grow-1 me-4">Female</div>

                                                    <div class="fw-bolder text-gray-700 text-xxl-end">{{$probation_female_employees}}</div>

                                                </div>

                                            </div>

                                        </div>
                                    </a>

                                </div>


                            </div>
                        </div>

                    @endif

                    <div class="row">
                        @if(!empty($permission) && $permission[29]['module_accessibility'] == 1 && $permission[29]['permission_status'] == 1 )
                            <div class="col-xl-12">
                                <div class="card card-flush overflow-hidden h-md-100">
                                    <div class="card-header py-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">Expense of Month</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Expense of Month</span>
                                        </h3>
                                        <div class="card-toolbar" >

                                            <a href="#" class="btn btn-sm btn-flex btn-light btn-active-color-primary fw-bold" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" id="expense_filter">

                                            <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            Filter</a>

                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                                    <form action="{{ url('attendance') }}" method="GET">
                                                        <div class="px-7 py-5">
                                                            <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                                        </div>

                                                        <div class="separator border-gray-200"></div>
                                                        <div class="px-7 py-5">

                                                            <div class="mb-10 row" >
                                                                <div class="col-12">

                                                                    <label class="form-label fw-semibold">Select Month</label>

                                                                    <div>
                                                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Month" name="m"
                                                                        required="required" id="expense_month" >
                                                                        <option value="">Select Month</option>
                                                                        <option value="01"  @if(date('m') == '01') selected @endif>January
                                                                        </option>
                                                                        <option value="02"  @if(date('m') == '02') selected @endif>February
                                                                        </option>
                                                                        <option value="03"  @if(date('m') == '03') selected @endif>March
                                                                        </option>
                                                                        <option value="04"  @if(date('m') == '04') selected   @endif>April
                                                                        </option>
                                                                        <option value="05"  @if(date('m') == '05') selected   @endif>May
                                                                        </option>
                                                                        <option value="06"  @if(date('m') == '06') selected   @endif>June
                                                                        </option>
                                                                        <option value="07"  @if(date('m') == '07') selected   @endif>July
                                                                        </option>
                                                                        <option value="08"  @if(date('m') == '08') selected   @endif>August
                                                                        </option>
                                                                        <option value="09"  @if(date('m') == '09') selected   @endif>September
                                                                        </option>
                                                                        <option value="10"  @if(date('m') == '10') selected @endif>October
                                                                        </option>
                                                                        <option value="11"  @if(date('m') == '11') selected @endif>November
                                                                        </option>
                                                                        <option value="12"  @if(date('m') == '12') selected @endif>December
                                                                        </option>
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 pt-5">

                                                                    <label class="form-label fw-semibold">Select Year</label>

                                                                    <div>
                                                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Year"  name="y" required="required" id="expense_year" >
                                                                            <option value="">Select Year</option>
                                                                            @if(!empty($dynamic_years))
                                                                                @foreach ($dynamic_years as $year_list)
                                                                                    <option value="{{$year_list}}" @if (!empty(Request::get('y')) && Request::get('y') == $year_list) selected @elseif(empty(Request::get('y')) && date('Y') == $year_list) selected  @endif>{{$year_list}}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                        </div>
                                    </div>
                                    @php  $currency = CustomHelper::hcm_system_currency(); @endphp
                                    <div class="card-body d-flex justify-content-between flex-column pb-1 px-0">
                                        <div class="px-9 mb-5">
                                            <div class="d-flex mb-2">
                                                <span class="fs-2hx fw-bold text-gray-800 me-2 lh-1 ls-n2" id="total_expense">0</span>
                                                <span class="fs-4 fw-semibold text-gray-400 me-1">{{$currency[0]['system_currency']}} </span>
                                            </div>
                                        </div>

                                        <div id="expense_graph" class="min-h-auto ps-4 pe-6" style="height: 300px"></div>
                                    </div>
                                </div>
                            </div>

                        @endif

                        @if(!empty($permission) && $permission[12]['module_accessibility'] == 1  && $permission[12]['permission_status'] == 1)
                            <div class="col-xl-12 mt-4">
                                <div class="card card-xl-stretch mb-5 mb-xl-8">
                                    <div class="card-header border-0 pt-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold fs-3 mb-1">Department Employee</span>
                                            <span class="text-muted fw-semibold fs-7">Department Employee</span>
                                        </h3>
                                    </div>

                                    <div class="card-body">
                                        <div id="department_employee_graph" style="height: 350px"></div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!empty($permission) && ($permission[23]['module_accessibility'] == 1  && $permission[23]['permission_status'] == 1) && ($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2))
                            <div class="col-xl-12 mb-5">
                                <div class="card card-flush overflow-hidden h-md-100">
                                    <div class="card-header py-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">Employee Leave Request</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Employee Leave Request</span>
                                        </h3>
                                        <div class="card-toolbar" >

                                            <a href="#" class="btn btn-sm btn-flex btn-light btn-active-color-primary fw-bold " data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" id="leave_filter">

                                            <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            Filter</a>

                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                                    <form action="" method="GET">
                                                        <div class="px-7 py-5">
                                                            <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                                        </div>

                                                        <div class="separator border-gray-200"></div>
                                                        <div class="px-7 py-5">

                                                            <div class="mb-10 row" >
                                                                <div class="col-12">
                                                                    <label class="fs-6 fw-semibold mb-2 required">Departments</label>

                                                                    <div>
                                                                        <select class="form-select" data-control="select2"  data-hide-search="true"  name="department_id[]" required id="select-department">
                                                                            <option value="">Select All Department</option>
                                                                            @if(!empty($departments))
                                                                                @foreach ($departments as $department)
                                                                                    <option value="{{$department['department_id']}}">{{ $department['department_name'] }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                    </select>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 mt-4">
                                                                    <label class="fs-6 fw-semibold mb-2 required">Employee</label>

                                                                    <div>
                                                                        <select class="form-select" data-control="select2"  data-hide-search="true" data-placeholder="Select Employee" name="employee_id" id="employees">

                                                                        </select>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 pt-5">

                                                                    <label class="form-label fw-semibold">Select Year</label>

                                                                    <div>
                                                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Year"  name="y" required="required" id="leave_request_year" >
                                                                            <option value="">Select Year</option>
                                                                            @if(!empty($dynamic_years))
                                                                                @foreach ($dynamic_years as $year_list)
                                                                                <option value="{{$year_list}}" @if (!empty(Request::get('y')) && Request::get('y') == $year_list) selected @elseif(empty(Request::get('y')) && date('Y') == $year_list) selected  @endif>{{$year_list}}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                        </div>
                                    </div>

                                    <div class="card-body" id="leave-request-chart">
                                        <canvas id="leave-chart" style="height:250px"></canvas>
                                    </div>
                                </div>
                            </div>

                        @endif

                        @if(!empty($permission) && ($permission[22]['module_accessibility'] == 1  && $permission[22]['permission_status'] == 1) && ($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2))
                            <div class="col-xl-12 mb-5">
                                <div class="card card-flush overflow-hidden h-md-100">
                                    <div class="card-header py-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">Employee Remote Request</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Employee Remote Request</span>
                                        </h3>
                                        <div class="card-toolbar" >

                                            <a href="#" class="btn btn-sm btn-flex btn-light btn-active-color-primary fw-bold " data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" id="wfh_filter">

                                            <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            Filter</a>

                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                                    <form action="" method="GET">
                                                        <div class="px-7 py-5">
                                                            <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                                        </div>

                                                        <div class="separator border-gray-200"></div>
                                                        <div class="px-7 py-5">

                                                            <div class="mb-10 row" >
                                                                <div class="col-12">
                                                                    <label class="fs-6 fw-semibold mb-2 required">Departments</label>

                                                                    <div>
                                                                        <select class="form-select" data-control="select2"  data-hide-search="true" name="department_id[]" required id="select-department-wfh">
                                                                            <option value="">Select All Department</option>
                                                                            @if(!empty($departments))
                                                                                @foreach ($departments as $department)
                                                                                    <option value="{{$department['department_id']}}">{{ $department['department_name'] }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                    </select>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 mt-4">
                                                                    <label class="fs-6 fw-semibold mb-2 required">Employee</label>

                                                                    <div>
                                                                        <select class="form-select" data-control="select2"  data-hide-search="true" data-placeholder="Select Employee" name="employee_id" id="employees-wfh">

                                                                        </select>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 pt-5">

                                                                    <label class="form-label fw-semibold">Select Year</label>

                                                                    <div>
                                                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Year"  name="y" required="required" id="wfh_request_year" >
                                                                            <option value="">Select Year</option>
                                                                            @if(!empty($dynamic_years))
                                                                                @foreach ($dynamic_years as $year_list)
                                                                                    <option value="{{$year_list}}" @if (!empty(Request::get('y')) && Request::get('y') == $year_list) selected @elseif(empty(Request::get('y')) && date('Y') == $year_list) selected  @endif>{{$year_list}}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                        </div>
                                    </div>

                                    <div class="card-body" id="wfh-request-chart">
                                        <canvas id="wfh_chart_js" style="height:300px"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!empty($permission) && ($permission[12]['module_accessibility'] == 1 && $permission[12]['permission_status'] == 1) && ($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2))
                            <div class="col-xl-12 mb-5">
                                <div class="card card-flush overflow-hidden h-md-100">
                                    <div class="card-header py-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">Employee Monthly Strength</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Employee Monthly Strength</span>
                                        </h3>
                                        <div class="card-toolbar" >

                                            <a href="#" class="btn btn-sm btn-flex btn-light btn-active-color-primary fw-bold " data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" id="employee_strength_filter">

                                            <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            Filter</a>

                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                                    <form action="" method="GET">
                                                        <div class="px-7 py-5">
                                                            <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                                        </div>

                                                        <div class="separator border-gray-200"></div>
                                                        <div class="px-7 py-5">

                                                            <div class="mb-10 row" >
                                                                <div class="col-12">
                                                                    <label class="fs-6 fw-semibold mb-2 required">Departments</label>

                                                                    <div>
                                                                        <select class="form-select" data-control="select2"  data-hide-search="true"  name="department_id[]" required id="select-department-employee">
                                                                            <option value="">Select All Department</option>
                                                                            @if(!empty($departments))
                                                                                @foreach ($departments as $department)
                                                                                    <option value="{{$department['department_id']}}">{{ $department['department_name'] }}</option>
                                                                                @endforeach
                                                                            @endif
                                                                    </select>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 mt-4">
                                                                    <label class="fs-6 fw-semibold mb-2 required">Employee</label>

                                                                    <div>
                                                                        <select class="form-select" data-control="select2"  data-hide-search="true" data-placeholder="Select Employee" name="employee_id" id="employee_list">

                                                                        </select>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 pt-5">

                                                                    <label class="form-label fw-semibold">Select Year</label>

                                                                    <div>
                                                                        <select class="form-select " data-kt-select2="true" data-placeholder="Select Year"  name="y" required="required" id="employee_strength_year" >
                                                                            <option value="">Select Year</option>
                                                                            @if(!empty($dynamic_years))
                                                                                @foreach ($dynamic_years as $year_list)
                                                                                    <option value="{{$year_list}}" @if (!empty(Request::get('y')) && Request::get('y') == $year_list) selected @elseif(empty(Request::get('y')) && date('Y') == $year_list) selected  @endif>{{$year_list}}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                        </div>
                                    </div>

                                    <div class="card-body" id="employee-monthly-strength-chart">
                                        <canvas id="monthly-strength-chart" style="height:250px"></canvas>
                                    </div>
                                </div>
                            </div>

                        @endif
                        @if(!empty($permission) && ($permission[25]['module_accessibility'] == 1 && $permission[25]['permission_status'] == 1) && ($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2))
                            <div class="col-xl-6 mb-5">
                                <div class="card card-flush overflow-hidden h-md-100">
                                    <div class="card-header py-5">
                                        <h3 class="card-title align-items-start flex-column">
                                            <span class="card-label fw-bold text-dark">Candidate Interviews</span>
                                            <span class="text-gray-400 mt-1 fw-semibold fs-6">Candidate Interviews</span>
                                        </h3>
                                        <div class="card-toolbar" >

                                            <a href="#" class="btn btn-sm btn-flex btn-light btn-active-color-primary fw-bold " data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" id="interview_filter">

                                            <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                                                </svg>
                                            </span>
                                            Filter</a>

                                                <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1" >
                                                    <form action="" method="GET">
                                                        <div class="px-7 py-5">
                                                            <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                                        </div>

                                                        <div class="separator border-gray-200"></div>
                                                        <div class="px-7 py-5">

                                                            <div class="mb-10 row" >
                                                                <div class="col-12 pt-2">

                                                                    <label class="form-label fw-semibold">Select Month</label>

                                                                    <div>
                                                                        <select class="form-select " data-kt-select2="true"   name="m" required="required" id="interview_month" >
                                                                            <option value="">Select All Month</option>
                                                                            <option value="01" @if(!empty(Request::get('m')) && Request::get('m') == '01') selected @endif>January</option>
                                                                            <option value="02" @if(!empty(Request::get('m')) && Request::get('m') == '02') selected @endif>February</option>
                                                                            <option value="03" @if(!empty(Request::get('m')) && Request::get('m') == '03') selected @endif>March</option>
                                                                            <option value="04" @if(!empty(Request::get('m')) && Request::get('m') == '04') selected @endif>April</option>
                                                                            <option value="05" @if(!empty(Request::get('m')) && Request::get('m') == '05') selected @endif>May</option>
                                                                            <option value="06" @if(!empty(Request::get('m')) && Request::get('m') == '06') selected @endif>June</option>
                                                                            <option value="07" @if(!empty(Request::get('m')) && Request::get('m') == '07') selected @endif>July</option>
                                                                            <option value="08" @if(!empty(Request::get('m')) && Request::get('m') == '08') selected @endif>August</option>
                                                                            <option value="09" @if(!empty(Request::get('m')) && Request::get('m') == '09') selected @endif>September</option>
                                                                            <option value="10" @if(!empty(Request::get('m')) && Request::get('m') == '10') selected @endif>October</option>
                                                                            <option value="11" @if(!empty(Request::get('m')) && Request::get('m') == '11') selected @endif>November</option>
                                                                            <option value="12" @if(!empty(Request::get('m')) && Request::get('m') == '12') selected @endif>December</option>
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                                <div class="col-12 pt-5">

                                                                    <label class="form-label fw-semibold">Select Year</label>

                                                                    <div>
                                                                        <select class="form-select" data-kt-select2="true" data-placeholder="Select Year" name="y" required="required" id="interview_year" >
                                                                            <option value="">Select Year</option>
                                                                            @if(!empty($dynamic_years))
                                                                                @foreach ($dynamic_years as $year_list)
                                                                                    <option value="{{$year_list}}" @if (!empty(Request::get('y')) && Request::get('y') == $year_list) selected @elseif(empty(Request::get('y')) && date('Y') == $year_list) selected  @endif>{{$year_list}}</option>
                                                                                @endforeach
                                                                            @endif
                                                                        </select>
                                                                    </div>

                                                                </div>
                                                            </div>

                                                        </div>
                                                    </form>
                                                </div>

                                        </div>
                                    </div>

                                    <div class="card-body" id="interview-chart">
                                        <canvas id="kt_chartjs_3" class="mh-300px"></canvas>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-xl-6">
                            <div class="card mb-5 mb-xl-8">
                                <div class="card-header border-0 pt-5">
                                    <div class="card-title">
                                        <div class="d-flex align-items-center position-relative my-1">
                                            My Attendance Summary ({{date('F') . '-' . date('Y') }})
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body py-3">
                                    <table class="table align-middle gs-0 gy-4 table-row-bordered">
                                        <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th class="ps-4 min-w-100px sorting">Description</th>
                                                <th class="min-w-10px sorting">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="ps-4">Absent</td>
                                                <td class="ps-8 fs-6">{{$attendance_summary['absent']}}</td>
                                            <tr>
                                                <td class="ps-4">Present</td>
                                                <td class="ps-8 fs-6">{{$attendance_summary['present']}}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4">OffDays</td>
                                                <td class="ps-8 fs-6">{{$attendance_summary['offdays']}}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-4">Leaves</td>
                                                <td class="ps-8 fs-6">{{$attendance_summary['leave']}}</td>
                                            </tr>
                                                <tr>
                                                <td class="ps-4 fs-6">Total</td>
                                                @php $total = $attendance_summary['absent'] + $attendance_summary['present'] + $attendance_summary['leave'] + $attendance_summary['offdays'] @endphp
                                                <td class="ps-8 text-dark fw-bold mb-1 fs-6">{{$total}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        @if(!empty($permission) && $permission[14]['module_accessibility'] == 1 && $permission[14]['permission_status'] == 1)

                            <div class="col-xl-6">
                                <div class="card mb-5 mb-xl-8">
                                    <div class="card-header border-0 pt-5">
                                        <div class="card-title">
                                            Employees Birthday ({{ date('F - Y') }})
                                        </div>
                                    </div>

                                    <div class="card-body py-3">
                                        <div class="table-responsive" style="height:297px">
                                            @if(!empty($birthdays))
                                                <table class="table align-middle gs-0 gy-4 table-row-bordered" >
                                                    <thead>
                                                        <tr class="fw-bold text-muted bg-light">
                                                            <th class="ps-4">Employee Name</th>
                                                            <th>Employee Department</th>
                                                            <th>Birthday</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>

                                                        @foreach($birthdays as $birthday)
                                                            <tr>
                                                                <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($birthday['employee_id']) !!}</td>
                                                                <td>
                                                                    <span class="text-dark fw-bold mb-1 fs-6"> {{ $birthday['employee_department'] }}</span> - <span class="text-muted fw-semibold text-muted fs-7">{{$birthday['employee_designation'] }}</span>
                                                                </td>
                                                                <td>{{ date('d, F' , strtotime($birthday['dob'])) }}</td>
                                                            </tr>
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="alert alert-primary" role="alert">
                                                    No Record Found!
                                                </div>
                                            @endif


                                        </div>
                                    </div>
                                </div>

                            </div>

                        @endif

                        <div class="col-xl-6">
                            <div class="card mb-5 mb-xl-8" style="height:390px">
                                <div class="card-header border-0 pt-5">
                                    <div class="card-title">
                                        Todays Interviews ({{ date('d M, Y') }})
                                    </div>
                                </div>

                                <div class="card-body py-3">
                                    @if(!empty($interviews))
                                        <div class="table-responsive " style="height:297px">
                                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="tableInterview">
                                                <thead>
                                                    <tr class="fw-bold text-muted bg-light">
                                                        <th class="ps-6">ID</th>
                                                        <th>Candidate Name</th>
                                                        <th>Interview Timing</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($interviews as $interview)
                                                        <tr @if(request()->get('interview_id') != '' && $interview['interview_id'] == request()->get('interview_id')) style="background-color:bisque;" @endif>
                                                            <td class="ps-5">{{ $interview['interview_id'] }}</td>
                                                            <td>{{ $interview['candidate_name'] }}</td>
                                                            <td>{{ $interview['interview_date'] }} - {{ date('h:i A', strtotime($interview['interview_time'])) }}</td>
                                                            <td>
                                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)

                                                                @if($interview['interview_interviewer1'] == $login_user[0]['employee_id'] || $interview['interview_interviewer2'] == $login_user[0]['employee_id'])
                                                                    <a href="{{url('my-interviews')}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-title="View Interview">
                                                                        <i class="fa fa-eye"></i>
                                                                    </a>
                                                                @else
                                                                    <a href="{{ url('interviews')}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" title="View Interview">
                                                                     <i class="fa fa-eye" aria-hidden="true"></i>
                                                                    </a>
                                                                @endif
                                                               
                                                            @else
                                                                <a href="{{url('my-interviews')}}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1" data-bs-toggle="tooltip" data-bs-title="View Interview">
                                                                   <i class="fa fa-eye"></i>
                                                                </a>
                                                            @endif    
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-primary" role="alert">
                                            No Interview's For Today!
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(!empty($permission) &&  $permission[12]['module_accessibility'] == 1 &&  $permission[12]['permission_status'] == 1)

                            <div class="col-xl-8">
                                <div class="card mb-5 mb-xl-8">
                                    <div class="card-header border-0 pt-5">
                                        <div class="card-title">Probation Period Employee(s)</div>
                                    </div>

                                    <div class="card-body py-3">
                                        <div class="table-responsive " style="height:297px">
                                            @if(!empty($probation_employees_detail))
                                                <table class="table align-middle gs-0 gy-4 table-row-bordered">
                                                    <thead>
                                                        <tr class="fw-bold text-muted bg-light">
                                                            <th class="ps-4">Employee Name</th>
                                                            <th>Probation Status</th>
                                                            <th>Employment Days</th>
                                                            <th>Date of Joining</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($probation_employees_detail as $probation_employees_detail)
                                                            <tr>
                                                                <td class="ps-4">{!! CustomHelper::getEmpProfileDiv($probation_employees_detail['employee_id']) !!}</td>
                                                                <td>
                                                                    @php
                                                                        $now = time(); // or your date as well
                                                                        $your_date = strtotime($probation_employees_detail['employee_doj']);
                                                                        $datediff = $now - $your_date;

                                                                        $days = round($datediff / (60 * 60 * 24));
                                                                    @endphp
                                                                    @if($days >= 90)
                                                                        <span class="badge badge-light-success font-weight-bold">Probation Completed</span>
                                                                    @else
                                                                        <span class="badge badge-light-danger font-weight-bold">Probation Incomplete</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $days }} Days</td>
                                                                <td>{{ date('d F, Y' , strtotime($probation_employees_detail['employee_doj'])) }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <div class="alert alert-primary" role="alert">
                                                    No Record Found!
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                            </div>
                        @endif

                    </div>

                </div>
                {{--end::Content container--}}
            </div>
            {{--end::Content--}}

        </div>
        {{--end::Content wrapper--}}

@endsection

@push('js-link')

<script src="{{url('assets/js/dashboard/dashboard.js')}}"></script>

<script src="{{url('assets/js/widgets.bundle.js')}}"></script>
<script src="{{url('assets/js/custom/widgets.js')}}"></script>

@if(!empty($permission) && $permission[29]['module_accessibility'] == 1 && $permission[29]['permission_status'] == 1)
<script src="{{url('assets/js/dashboard-expense-charts.js')}}?js_v=2"></script>
@endif


@if(!empty($permission) && ($permission[22]['module_accessibility'] == 1 && $permission[22]['permission_status'] == 1) && ($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2))
    <script src="{{url('assets/js/dashboard-wfh-request-chart.js')}}?js_v=2"></script>
@endif

@if(!empty($permission) && ($permission[23]['module_accessibility'] == 1 && $permission[23]['permission_status'] == 1) && ($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2))
    <script src="{{url('assets/js/dashboard-leave-request-chart.js')}}?js_v=2"></script>
@endif

@if(!empty($permission) && ($permission[12]['module_accessibility'] == 1 && $permission[12]['permission_status'] == 1) && ($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2))
    <script src="{{url('assets/js/dashboard-employee-monthly-strength-charts.js')}}?js_v=2"></script>
@endif

@if(!empty($permission) && ($permission[12]['module_accessibility'] == 1 && $permission[25]['permission_status'] == 1) && ($login_user[0]['employee_department'] == 1 ||  $login_user[0]['employee_department'] == 2))
    <script src="{{url('assets/js/dashboard-interview-chart.js')}}?js_v=2"></script>
@endif


@if(!empty($permission) && $permission[12]['module_accessibility'] == 1 && $permission[12]['permission_status'] == 1)
    <script src="{{url('assets/js/dashboard-department-charts.js')}}?js_v=2"></script>
    <script src="{{url('assets/js/dashboard-employee-card-charts.js')}}?js_v=2"></script>

<script>
    var active_employees = {{$active_employees}}
    var active_male = {{$active_male_employees}};
    var active_female = {{$active_female_employees}};

    ActiveMaleFemaleChart.init(active_employees,active_male,active_female);

    var deactive_employees = {{$deactive_employees}}
    var deactive_male = {{$deactive_male_employees}};
    var deactive_female = {{$deactive_female_employees}};


    DeactiveMaleFemaleChart.init(deactive_employees,deactive_male, deactive_female);


    var permanent_employees = {{$permanent_employees}}
    var per_male = {{$permanent_male_employees}};
    var per_female = {{$permanent_female_employees}};

    PermanentMaleFemaleChart.init(permanent_employees,per_male, per_female);


    var probation_employees = {{$probation_employees}};
    var pro_male = {{$probation_male_employees}};
    var pro_female = {{$probation_female_employees}};

    ProbationMaleFemaleChart.init(probation_employees,pro_male, pro_female);

    const attendance_record = {!! json_encode($get_check_in_details) !!};

    if(attendance_record.attendance_record != '00:00:00'){
        if(attendance_record.attendance_record.att_check_out_status != '1'){
            const [hours , minutes , seconds] = attendance_record.attendance_record.att_check_in_time.split(':');

            const check_in_time = new Date();
            check_in_time.setHours(hours);
            check_in_time.setMinutes(minutes);
            check_in_time.setSeconds(seconds);

            function working_hours_timer() {
                const now = new Date();
            
                if (now < check_in_time) {
                    check_in_time.setDate(check_in_time.getDate() - 1);
                }

                const elapsed_milliseconds = now - check_in_time;

                const hours = Math.floor(elapsed_milliseconds / 3600000);
                const minutes = Math.floor((elapsed_milliseconds % 3600000) / 60000);
                const seconds = Math.floor((elapsed_milliseconds % 60000) / 1000);

                const timeString = `${format_number(hours)}:${format_number(minutes)}:${format_number(seconds)}`;
                document.getElementById("clock").textContent = timeString;
            }

            function format_number(number) {
                return number < 10 ? "0" + number : number;
            }

            setInterval(working_hours_timer, 1000);

            working_hours_timer();
        }else{
            calculateShiftDuration(attendance_record.attendance_record.att_check_in_time , attendance_record.attendance_record.att_check_out_time);
        }
    }else{
        document.getElementById("clock").textContent = '00:00:00';
    }

    function calculateShiftDuration(start_time , end_time) {
        var time_start = new Date();
        var time_end = new Date();
        var value_start = start_time.split(':');
        var value_end = end_time.split(':');
        time_start.setHours(value_start[0], value_start[1], 0, 0);
        time_end.setHours(value_end[0], value_end[1], 0, 0);
        var duration_in_ms = time_end - time_start;
        var duration_period = moment.utc(duration_in_ms).format('HH:mm:ss');

        if (duration_period != 'Invalid date') {
            document.getElementById("clock").textContent = duration_period;
        }
        if (duration_period == '00:00:00') {
            document.getElementById("clock").textContent = '24:00:00';
        }
    }

</script>

@endif

@endpush
