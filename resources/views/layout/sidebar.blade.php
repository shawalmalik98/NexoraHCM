{{--begin::sidebar--}}

    @php
        $permission = CustomHelper::access_permission();
        $meetings  = CustomHelper::get_my_todays_meeting_status();
        $companySetting = CustomHelper::hcm_default_settings();
    @endphp

    <div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="225px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">

        <div class="app-sidebar-logo px-12" id="kt_app_sidebar_logo">
            <a href="{{url('/')}}">
                {{-- <img alt="Logo"  src=" {{$companySetting['company_favicon']}}" class="h-45px app-sidebar-logo-default" /> --}}

                {{-- <img alt="Logo" src="{{$companySetting['company_favicon']}}" class="h-20px app-sidebar-logo-minimize" /> --}}
                <span class="font-weight-bold text-white text-capitalize text-center">
                    @if(!empty($companySetting['company_name']))
                        {{$companySetting['company_name']}}
                    @else
                        Ismail Industries Limited
                    @endif
                </span>
            </a>


            <div id="kt_app_sidebar_toggle" class="app-sidebar-toggle btn btn-icon btn-shadow btn-sm btn-color-muted btn-active-color-primary body-bg h-30px w-30px position-absolute top-50 start-100 translate-middle rotate" data-kt-toggle="true" data-kt-toggle-state="active" data-kt-toggle-target="body" data-kt-toggle-name="app-sidebar-minimize">
                <span class="svg-icon svg-icon-2 rotate-180">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.5" d="M14.2657 11.4343L18.45 7.25C18.8642 6.83579 18.8642 6.16421 18.45 5.75C18.0358 5.33579 17.3642 5.33579 16.95 5.75L11.4071 11.2929C11.0166 11.6834 11.0166 12.3166 11.4071 12.7071L16.95 18.25C17.3642 18.6642 18.0358 18.6642 18.45 18.25C18.8642 17.8358 18.8642 17.1642 18.45 16.75L14.2657 12.5657C13.9533 12.2533 13.9533 11.7467 14.2657 11.4343Z" fill="currentColor" />
                        <path d="M8.2657 11.4343L12.45 7.25C12.8642 6.83579 12.8642 6.16421 12.45 5.75C12.0358 5.33579 11.3642 5.33579 10.95 5.75L5.40712 11.2929C5.01659 11.6834 5.01659 12.3166 5.40712 12.7071L10.95 18.25C11.3642 18.6642 12.0358 18.6642 12.45 18.25C12.8642 17.8358 12.8642 17.1642 12.45 16.75L8.2657 12.5657C7.95328 12.2533 7.95328 11.7467 8.2657 11.4343Z" fill="currentColor" />
                    </svg>
                </span>
            </div>
        </div>


        <div class="app-sidebar-menu overflow-hidden flex-column-fluid">

            <div id="kt_app_sidebar_menu_wrapper" class="app-sidebar-wrapper hover-scroll-overlay-y my-5" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_sidebar_logo, #kt_app_sidebar_footer" data-kt-scroll-wrappers="#kt_app_sidebar_menu" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true">

                <div class="menu menu-column menu-rounded menu-sub-indention px-3" id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false">

                    <div class="mb-2">
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-3">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control ps-15" placeholder="Search Menu ..." id="serachMenu" style="background: none">
                        </div>

                    </div>

                    <div class="menu-item search-item">
                        <a class="menu-link nav-link  @if($title == 'index') active @endif" href="{{ url('/') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>
                   
                    @if(!empty($permission) && ($permission[12]['permission_status'] == 1 || $permission[13]['permission_status'] == 1 || $permission[14]['permission_status'] == 1  ||  $permission[15]['permission_status'] == 1 ||  $permission[52]['permission_status'] == 1 || $permission[66]['permission_status'] == 1 || $permission[73]['permission_status'] == 1) && ($permission[12]['module_accessibility'] == 1 || $permission[13]['module_accessibility'] == 1 || $permission[14]['module_accessibility'] == 1  ||  $permission[15]['module_accessibility'] == 1 ||  $permission[52]['module_accessibility'] == 1 || $permission[66]['module_accessibility'] == 1 || $permission[73]['module_accessibility'] == 1))

                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'employee' || $title == 'employee-managers' || $title == 'birthdays' || $title == 'work-anniversay' || $title == 'employee-devices' || $title == 'employee-bank' || $title == 'employment-group') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Employees MDM.png') }}" />
                                </span>
                                <span class="menu-title">Employee MDM</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion menu-active-bg">

                                @if(!empty($permission) && $permission[12]['module_accessibility'] == 1 && $permission[12]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'employee') active @endif" href="{{ url('/employees') }}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title ">Manage Employees</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[13]['module_accessibility'] == 1 && $permission[13]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link @if($title == 'employee-managers') active @endif" href="{{ url('/employees-managers') }}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>

                                            <span class="menu-title ">Employees Managers</span>
                                        </a>
                                    </div>

                                @endif



                                @if(!empty($permission) && $permission[14]['module_accessibility'] == 1 && $permission[14]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'birthdays') active @endif" href="{{ url('/employee-birthdays') }}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title">Employees Birthday</span>
                                        </a>
                                    </div>

                                @endif


                                @if(!empty($permission) && $permission[15]['module_accessibility'] == 1 && $permission[15]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'work-anniversay') active @endif" href="{{ url('/employee-work-anniversay') }}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title">Work Anniversery</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[52]['module_accessibility'] == 1 && $permission[52]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'employee-devices') active @endif" href="{{ url('/employee-devices') }}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title">Employees Mobile Devices</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[66]['module_accessibility'] == 1 && $permission[66]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'employee-bank') active @endif" href="{{ url('/employee-bank') }}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title">Employees Bank Details</span>
                                        </a>
                                    </div>

                                @endif
                                
                                @if(!empty($permission) && $permission[73]['module_accessibility'] == 1 && $permission[73]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'employment-group') active @endif" href="{{ url('/employee-group') }}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title">Employee Groups</span>
                                        </a>
                                    </div>
                                @endif

                                
                            </div>


                        </div>

                    @endif
                    
                    @if(!empty($permission) && ($permission[19]['permission_status'] == 1 || $permission[20]['permission_status'] == 1 || $permission[21]['permission_status'] == 1 || $permission[1]['permission_status'] == 1 || $permission[2]['permission_status'] == 1 || $permission[54]['permission_status'] == 1 || $permission[22]['permission_status'] == 1 || $permission[22]['permission_status'] == 1 ||  $permission[59]['permission_status'] == 1 || $permission[65]['permission_status'] == 1 || $permission[67]['permission_status'] == 1 || $permission[70]['permission_status'] == 1 || $permission[72]['permission_status'] == 1) && ($permission[19]['module_accessibility'] == 1 || $permission[20]['module_accessibility'] == 1 || $permission[21]['module_accessibility'] == 1 || $permission[1]['module_accessibility'] == 1 || $permission[2]['module_accessibility'] == 1 || $permission[54]['module_accessibility'] == 1 || $permission[22]['module_accessibility'] == 1 || $permission[22]['module_accessibility'] == 1 ||  $permission[59]['module_accessibility'] == 1 || $permission[65]['module_accessibility'] == 1 ||  $permission[67]['module_accessibility'] == 1 ||  $permission[70]['module_accessibility'] == 1 ||  $permission[72]['module_accessibility'] == 1)) 
                    
                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'manage-attendance' || $title == 'daily-attendance' || $title == 'overtime-management' || $title == 'my-attendance' || $title == 'attendance-request' || $title == 'my-attendance-request' || $title == 'wfh-request' || $title == 'my-wfh-requests' || $title == 'shift-management' || $title == 'my-shift-time' || $title == 'shift-time-request' || $title == 'monthy-attendance' || $title == 'manage-attendance-devices' || $title == 'monthly-attendance' || $title == 'add-shift' || $title == 'attendance-locker') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Mark Attendance.png') }}" />
                                </span>
                                <span class="menu-title">Attendance</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion menu-active-bg">

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'mark-attendance') active @endif" href="{{ url('mark-attendance') }}" target="blank">
                                        <span class="menu-icon">
                                            <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Mark Attendance.png') }}" />
                                        </span>
                                        <span class="menu-title">Mark Attendance</span>
                                    </a>
                                </div>
                            
                                @if(!empty($permission) && $permission[67]['module_accessibility'] == 1 && $permission[67]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'monthly-attendance') active @endif" href="{{url('monthly-attendance')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Attendance.png') }}" />
                                            </span>
                                            <span class="menu-title">Monthly Attendance</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[19]['module_accessibility'] == 1 && $permission[19]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-attendance') active @endif" href="{{url('attendance')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Attendance.png') }}" />
                                            </span>
                                            <span class="menu-title">Detailed Attendance</span>
                                        </a>
                                    </div>

                                @endif

                                 

                                @if(!empty($permission) && $permission[21]['module_accessibility'] == 1 && $permission[21]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'daily-attendance') active @endif" href="{{url('daily-attendance-report')}}">
                                            <span class="menu-icon">

                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Daily Attendance Report.png') }}" />
                                            </span>
                                            <span class="menu-title">Daily Attendance Report</span>
                                        </a>
                                    </div>

                                @endif
                                @if(!empty($permission) && $permission[72]['module_accessibility'] == 1 && $permission[72]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'attendance-locker') active @endif" href="{{url('attendance-locker')}}">
                                            <span class="menu-icon">

                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Daily Attendance Report.png') }}" />
                                            </span>
                                            <span class="menu-title">Attendance Locker</span>
                                        </a>
                                    </div>
                                @endif
                                @if(!empty($permission) && $permission[1]['module_accessibility'] == 1 && $permission[1]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-attendance') active @endif" href="{{url('my-attendance')}}">
                                            <span class="menu-icon">
                                                <span class="menu-icon">
                                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Attendance.png') }}" />
                                                </span>
                                            </span>
                                            <span class="menu-title">My Attendance</span>
                                        </a>
                                    </div>
                                    
                                @endif

                                @if(!empty($permission) && $permission[20]['module_accessibility'] == 1 && $permission[20]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'attendance-request') active @endif" href="{{url('attendance-requests')}}" >
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Attendance Requests.png') }}" />
                                            </span>
                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                                <span class="menu-title">All Attendance Requests</span>
                                            @else
                                                <span class="menu-title">Team Attendance Requests</span>
                                            @endif
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[2]['module_accessibility'] == 1 && $permission[2]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-attendance-request') active @endif" href="{{url('my-attendance-request')}}" >
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Attendance Requests.png') }}" />
                                            </span>
                                            <span class="menu-title">My Attendance Requests</span>
                                        </a>
                                    </div>

                                @endif
                                @if(!empty($permission) && $permission[70]['module_accessibility'] == 1 && $permission[70]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-attendance-devices') active @endif " href="{{url('attendance-devices')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All WFH Requests.png') }}" />
                                            </span>
                                            <span class="menu-title">Attendance Devices</span>
                                        </a>
                                    </div>
                                @endif
                                @if(!empty($permission) && $permission[65]['module_accessibility'] == 1 && $permission[65]['permission_status'] == 1)
                        
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'shift-time-request') active @endif" href="{{ url('shift-time-request') }}" >
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Mark Attendance.png') }}" />
                                            </span>
                                            <span class="menu-title">Shift Time Request</span>
                                        </a>
                                    </div>
    
                                @endif
                                @if(!empty($permission) && $permission[54]['module_accessibility'] == 1 && $permission[54]['permission_status'] == 1)
                        
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'shift-management' || $title == 'add-shift') active @endif" href="{{ url('shift-management') }}" >
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Mark Attendance.png') }}" />
                                            </span>
                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                               <span class="menu-title">Shift Management</span>                                         
                                            @else
                                                <span class="menu-title">Team Shift Management</span>
                                            @endif
                                        </a>
                                    </div>
    
                                @endif
                                @if(!empty($permission) && $permission[59]['module_accessibility'] == 1 && $permission[59]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-shift-time') active @endif" href="{{ url('my-shift-time') }}" >
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Mark Attendance.png') }}" />
                                            </span>
                                            <span class="menu-title">My Shift Time</span>
                                        </a>
                                    </div>

                                @endif
                                
                                @if(!empty($permission) && $permission[56]['module_accessibility'] == 1 && $permission[56]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'overtime-management') active @endif" href="{{url('overtime-management')}}">
                                            <span class="menu-icon">

                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Daily Attendance Report.png') }}" />
                                            </span>
                                            <span class="menu-title">Overtime Sheet</span>
                                        </a>
                                    </div>

                                @endif
                                @if(!empty($permission) && $permission[22]['module_accessibility'] == 1 && $permission[22]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'wfh-request') active @endif" href="{{url('wfh-requests')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All WFH Requests.png') }}" />
                                            </span>
                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                                <span class="menu-title">All Remote Work Requests</span>
                                            @else
                                                <span class="menu-title">Team Remote Work Requests</span>
                                            @endif    
                                        </a>
                                    </div>
    
                                @endif

                                @if(!empty($permission) && $permission[7]['module_accessibility'] == 1 && $permission[7]['permission_status'] == 1)
    
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-wfh-requests') active @endif " href="{{url('my-wfh-requests')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My WFH Requests.png') }}" />
                                            </span>
                                            <span class="menu-title">My Remote Work Request</span>
                                        </a>
                                    </div>
    
                                @endif

                            </div>
                        </div>
                    @endif

                    @if(!empty($permission) && ($permission[3]['permission_status'] == 1 || $permission[23]['permission_status'] == 1 || $permission[24]['permission_status'] == 1 || $permission[31]['permission_status'] == 1 || $permission[63]['permission_status'] == 1 ||  $permission[74]['permission_status'] == 1) && ($permission[3]['module_accessibility'] == 1 || $permission[23]['module_accessibility'] == 1 || $permission[24]['module_accessibility'] == 1 || $permission[31]['module_accessibility'] == 1 || $permission[63]['module_accessibility'] == 1 || $permission[74]['module_accessibility'] == 1))
                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'employees-leaves' || $title == 'my-leaves' || $title == 'manage-leaves' || $title == 'holidays' || $title == 'leave-management' || $title == 'leave-policy') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Leave Requests.png') }}" />
                                </span>
                                <span class="menu-title">Leave</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion menu-active-bg">

                                @if(!empty($permission) && $permission[23]['module_accessibility'] == 1 && $permission[23]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'employees-leaves') active @endif" href="{{url('employees-leaves')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Leave Requests.png') }}" />
                                            </span>
                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                                <span class="menu-title">All Leaves Requests</span>                                         
                                            @else
                                                <span class="menu-title">Team Leaves Requests</span>
                                            @endif  
                                            
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[3]['module_accessibility'] == 1 && $permission[3]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-leaves') active @endif" href="{{url('/my-leaves')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Leave Requests.png') }}" />
                                            </span>
                                            <span class="menu-title">My Leaves Requests</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[63]['module_accessibility'] == 1 && $permission[63]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'leave-management') active @endif" href="{{url('leave-management')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Leave Entilement.png') }}" />
                                            </span>
                                            <span class="menu-title">Manage Leaves</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[24]['module_accessibility'] == 1 && $permission[24]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-leaves') active @endif" href="{{url('manage-leaves')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Leave Entilement.png') }}" />
                                            </span>
                                            <span class="menu-title">Leaves Entitlement</span>
                                        </a>
                                    </div>

                                @endif
                                
                                @if(!empty($permission) && $permission[74]['module_accessibility'] == 1 && $permission[74]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'leave-policy') active @endif" href="{{ url('leave-policy') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Leave Entilement.png') }}" />
                                            </span>
                                            <span class="menu-title">Leaves Policy</span>
                                        </a>
                                    </div>
                                @endif
                               
                                <!-- @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2 || ($login_user[0]['employee_department'] == 7 &&  $login_user[0]['employee_is_manager'] == 1))
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'rpl-leaves') active @endif" href="{{url('rpl-leaves')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Leave Entilement.png') }}" />
                                            </span>
                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                                <span class="menu-title">All RPL Leaves</span>                                         
                                            @else
                                                <span class="menu-title">Team RPL Leaves</span>
                                            @endif
                                            <span class="menu-title"></span>
                                        </a>
                                    </div>
                                
                                @endif -->
                                <!-- @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 7 )
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-rpl-leaves') active @endif" href="{{url('my-rpl-leave')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Leave Entilement.png') }}" />
                                            </span>
                                            <span class="menu-title">My RPL Leaves</span>
                                        </a>
                                    </div>
                                @endif -->

                                @if(!empty($permission) && $permission[31]['module_accessibility'] == 1 && $permission[31]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'holidays') active @endif" href="{{url('holidays')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Holidays.png') }}" />
                                            </span>
                                            <span class="menu-title">Holidays</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    @endif

                    @if(!empty($permission) && ($permission[4]['permission_status'] == 1 || $permission[25]['permission_status'] == 1 || $permission[26]['permission_status'] == 1 || $permission[51]['permission_status'] == 1 || $permission[40]['permission_status'] == 1 || $permission[41]['permission_status'] == 1) && ($permission[4]['module_accessibility'] == 1 || $permission[25]['module_accessibility'] == 1 || $permission[26]['module_accessibility'] == 1 || $permission[51]['module_accessibility'] == 1 || $permission[40]['module_accessibility'] == 1 || $permission[41]['module_accessibility'] == 1))
                        
                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'manage-interviews' || $title == 'interview-candidate-list' || $title == 'my-interviews' || $title == 'employee-on-boarding' || $title == 'all-requisition' || $title == 'my-requisition') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Interviews.png') }}" />
                                </span>
                                <span class="menu-title">Recruitment</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion menu-active-bg">

                                @if(!empty($permission) && $permission[25]['module_accessibility'] == 1 && $permission[25]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-interviews') active @endif" href="{{url('interviews')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Interviews.png') }}" />
                                            </span>
                                            <span class="menu-title">Manage Interviews</span>
                                        </a>
                                    </div>

                                @endif
                                
                                @if(!empty($permission) && $permission[51]['module_accessibility'] == 1 && $permission[51]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link  @if($title == 'interview-candidate-list') active @endif" href="{{ url('/candiddate-interviews') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Interviews.png') }}" />
                                            </span>
                                            <span class="menu-title">Interview Candidates</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[4]['module_accessibility'] == 1 && $permission[4]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-interviews') active @endif" href="{{url('my-interviews')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Interviews.png') }}" />
                                            </span>
                                            <span class="menu-title">My Interviews</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[26]['module_accessibility'] == 1 && $permission[26]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link  @if($title == 'employee-on-boarding') active @endif" href="{{ url('/employee-onboarding') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Interviews.png') }}" />
                                            </span>
                                            <span class="menu-title">Onboard Employees</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[40]['module_accessibility'] == 1 && $permission[40]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'all-requisition') active @endif" href="{{url('requisition')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Requisition.png') }}" />
                                            </span>
                                            <span class="menu-title">All Requisition</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[41]['module_accessibility'] == 1 && $permission[41]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-requisition') active @endif" href="{{url('my-requisition')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Requisition.png') }}" />
                                            </span>
                                            <span class="menu-title">My Requisition</span>
                                        </a>
                                    </div>
                                @endif 
                            </div>
                        </div>

                    @endif

                    @if(!empty($permission) && ($permission[27]['permission_status'] == 1 || $permission[5]['permission_status'] == 1 || $permission[9]['permission_status'] == 1 || $permission[43]['permission_status'] == 1 || $permission[47]['permission_status'] == 1 || $permission[48]['permission_status'] == 1 || $permission[28]['permission_status'] == 1 || $permission[29]['permission_status'] == 1 || $permission[55]['permission_status'] == 1 || $permission[58]['permission_status'] == 1|| $permission[68]['permission_status'] == 1 || $permission[75]['permission_status'] == 1 || $permission[78]['permission_status'] == 1  ) && ($permission[27]['module_accessibility'] == 1 || $permission[5]['module_accessibility'] == 1 || $permission[9]['module_accessibility'] == 1 || $permission[43]['module_accessibility'] == 1 || $permission[47]['module_accessibility'] == 1 || $permission[48]['module_accessibility'] == 1 || $permission[28]['module_accessibility'] == 1 || $permission[29]['module_accessibility'] == 1 || $permission[55]['module_accessibility'] == 1 || $permission[58]['module_accessibility'] == 1 || $permission[68]['module_accessibility'] == 1 || $permission[75]['module_accessibility'] == 1 || $permission[78]['permission_status'] == 1))
                    
                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'Payroll' || $title == 'Add Payroll' || $title == 'my-payroll' || $title == 'all-salary-request' || $title == 'my-salary-request' || $title == 'Tax Slab' || $title == 'Tax Collection' || $title == 'cash' || $title == 'expense' || $title == 'employee-expense' || $title == 'my-expense' || $title == 'My Payslip' || $title == 'daily-wages' || $title == 'deduction policy' || $title == 'employee_funds') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Payslips.png') }}" />
                                </span>
                                <span class="menu-title">Payroll</span>
                                <span class="menu-arrow"></span></span>

                                <div class="menu-sub menu-sub-accordion menu-active-bg">
                                    @if(!empty($permission) && $permission[27]['module_accessibility'] == 1 && $permission[27]['permission_status'] == 1)
                                    <div data-kt-menu-trigger="click" class="menu-item menu-accordion @if($title == 'Payroll' || $title == 'Add Payroll' || $title == 'daily-wages') show @endif">
                                        <span class="menu-link">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Payslips.png') }}" />
                                            </span>
                                            <span class="menu-title">Manage Payroll</span>
                                            <span class="menu-arrow"></span>
                                        </span>
                                        <div class="menu-sub menu-sub-accordion">
                                            @if(!empty($permission) && $permission[27]['module_accessibility'] == 1 && $permission[27]['permission_status'] == 1)
                                            <div class="menu-item">
                                                <a class="menu-link nav-link @if($title == 'Payroll') active @endif" href="{{url('payroll')}}">
                                                    <span class="menu-icon">
                                                        <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Payslips.png') }}" />
                                                    </span>
                                                    <span class="menu-title">Monthly Payroll</span>
                                                </a>
                                            </div>
                                            @endif
                                            @if(!empty($permission) && $permission[68]['module_accessibility'] == 1 && $permission[68]['permission_status'] == 1)
                                            <div class="menu-item">
                                                <a class="menu-link nav-link @if($title == 'daily-wages') active @endif" href="{{url('daily-wages-payroll')}}">
                                                    <span class="menu-icon">
                                                        <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Payslips.png') }}" />
                                                    </span>
                                                    <span class="menu-title">Daily Wages</span>
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                @endif

                                @if(!empty($permission)  && $permission[78]['module_accessibility'] == 1 && $permission[78]['permission_status'] == 1)
                                    <div class="menu-item search-item">

                                        <a class="menu-link nav-link  @if($title == 'employee-funds') active @endif" href="{{ url('/employee-fund') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Gadgets Inventory.png') }}" />
                                            </span>
                                            <span class="menu-title">Employee Funds</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[5]['module_accessibility'] == 1 && $permission[5]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'My Payslip') active @endif" href="{{ url('my-payroll') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Payslips.png') }}" />
                                            </span>
                                            <span class="menu-title">My Payslips</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[43]['module_accessibility'] == 1 && $permission[43]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'all-salary-request') active @endif" href="{{url('all-salary-request')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Requisition.png') }}" />
                                            </span>
                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                                <span class="menu-title">All Advance Salary Request</span>                                         
                                            @else
                                                <span class="menu-title">Team Advance Salary Request</span>
                                            @endif
                                            
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[9]['module_accessibility'] == 1 && $permission[9]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-salary-request') active @endif" href="{{url('my-salary-request')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Requisition.png') }}" />
                                            </span>
                                            <span class="menu-title">My Salary Request</span>
                                        </a>
                                    </div>
                                @endif 

                                @if(!empty($permission) && $permission[47]['module_accessibility'] == 1 && $permission[47]['permission_status'] == 1 )
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'Tax Slab') active @endif" href="{{url('/tax-slab')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Income Tax.png') }}" />
                                            </span>
                                            <span class="menu-title">Manage Income Tax</span>
                                        </a>
                                    </div>
                                @endif
                                
                                @if(!empty($permission) && $permission[75]['module_accessibility'] == 1 && $permission[75]['permission_status'] == 1)
                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'deduction policy') active @endif" href="{{url('/deduction-policy')}}">
                                        <span class="menu-icon">
                                            <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Income Tax.png') }}" />
                                        </span>
                                        <span class="menu-title">Attendance Deduction Policy</span>
                                    </a>
                                </div>
                                @endif

                                @if(!empty($permission) && $permission[48]['module_accessibility'] == 1 && $permission[48]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'Tax Collection') active @endif" href="{{url('/tax-collection')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Tax Collection.png') }}" />
                                            </span>
                                            <span class="menu-title">Income Tax Collection</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[28]['module_accessibility'] == 1 && $permission[28]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'cash') active @endif" href="{{url('petty-cash')}}">
                                        <span class="menu-icon">
                                            <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Cash Inventory.png') }}" />
                                        </span>
                                        <span class="menu-title">Cash Inventory</span>
                                    </a>
                                </div>

                            @endif

                            @if(!empty($permission) && $permission[29]['module_accessibility'] == 1 && $permission[29]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'expense') active @endif" href="{{url('daily-expense')}}">
                                        <span class="menu-icon">
                                            <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Daily Expenses.png') }}" />
                                        </span>
                                        <span class="menu-title">Daily Expenses</span>
                                    </a>
                                </div>

                            @endif

                            @if(!empty($permission) && $permission[55]['module_accessibility'] == 1 && $permission[55]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'employee-expense') active @endif" href="{{url('employee-expense')}}">
                                        <span class="menu-icon">
                                            <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Daily Expenses.png') }}" />
                                        </span>
                                        @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                            <span class="menu-title">All Reimbursement Request</span>                                         
                                        @else
                                            <span class="menu-title">Team Reimbursement Request</span>
                                        @endif
                                        
                                    </a>
                                </div>

                            @endif

                            @if(!empty($permission) && $permission[58]['module_accessibility'] == 1 && $permission[58]['permission_status'] == 1)
                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'my-expense') active @endif" href="{{url('my-expense')}}">
                                        <span class="menu-icon">
                                            <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Daily Expenses.png') }}" />
                                        </span>
                                        <span class="menu-title">My Reimbursement Request</span>
                                    </a>
                                </div>

                            @endif
                        
                            </div>
                        </div>
                    @endif

                    @if(!empty($permission) && ($permission[10]['permission_status'] == 1 || $permission[30]['permission_status'] == 1 || $permission[44]['permission_status'] == 1) && ($permission[10]['module_accessibility'] == 1 || $permission[30]['module_accessibility'] == 1 || $permission[44]['module_accessibility'] == 1))
                    
                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'manage-loan' || $title == 'all-loan-request' || $title == 'my-loan') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Loans.png') }}" />
                                </span>
                                <span class="menu-title">Loan</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                @if(!empty($permission) && $permission[30]['module_accessibility'] == 1 && $permission[30]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-loan') active @endif" href="{{url('loans')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Loans.png') }}" />
                                            </span>
                                            <span class="menu-title">Manage Loans</span>
                                        </a>
                                    </div>

                                @endif
                                @if(!empty($permission) && $permission[44]['module_accessibility'] == 1 && $permission[44]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'all-loan-request') active @endif" href="{{url('all-loan-request')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Requisition.png') }}" />
                                            </span>
                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                               <span class="menu-title">All Loan Requests</span>                                         
                                            @else
                                                <span class="menu-title">Team Loan Requests</span>
                                            @endif
                                            
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[10]['module_accessibility'] == 1 && $permission[10]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-loan') active @endif" href="{{url('my-loan')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Requisition.png') }}" />
                                            </span>
                                            <span class="menu-title">My Loan Requests</span>
                                        </a>
                                    </div>
                            
                                @endif 
                                
                            </div>
                        </div>
                    @endif

                                            
                    @if(!empty($permission) && ($permission[32]['permission_status'] == 1 || $permission[33]['permission_status'] == 1 || $permission[49]['permission_status'] == 1 || $permission[50]['permission_status'] == 1 || $permission[36]['permission_status'] == 1 ) && ($permission[32]['module_accessibility'] == 1 || $permission[33]['module_accessibility'] == 1 || $permission[49]['module_accessibility'] == 1 || $permission[50]['module_accessibility'] == 1 || $permission[36]['module_accessibility'] == 1 ))
                        
                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'manage-departments' || $title == 'manage-designations' || $title == 'manage-rooms' || $title == 'manage-locations' || $title == 'system-settings' || $title == 'manage-companies') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/System Settings.png') }}" />
                                </span>
                                <span class="menu-title">Settings </span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion menu-active-bg">

                                @if(!empty($permission) && $permission[76]['module_accessibility'] == 1 && $permission[76]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-companies') active @endif" href="{{url('companies')}}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title">Manage Companies</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[32]['module_accessibility'] == 1 && $permission[32]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-departments') active @endif" href="{{url('departments')}}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title">Manage Departments</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[33]['module_accessibility'] == 1 && $permission[33]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-designations') active @endif" href="{{url('designations')}}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title">Manage Designations</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission)  && $permission[49]['module_accessibility'] == 1 && $permission[49]['permission_status'] == 1)
                                        
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-rooms') active @endif" href="{{url('/rooms')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Rooms.png') }}" />
                                            </span>
                                            <span class="menu-title">All Rooms</span>
                                        </a>
                                    </div>
                            
                                @endif

                                @if(!empty($permission)  && $permission[50]['module_accessibility'] == 1 && $permission[50]['permission_status'] == 1)
                                
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-locations') active @endif" href="{{url('/locations')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Location.png') }}" />
                                            </span>
                                            <span class="menu-title">All Locations</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission)  && $permission[36]['module_accessibility'] == 1 && $permission[36]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link @if($title == 'system-settings') active @endif" href="{{url('system-settings')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/System Settings.png') }}" />
                                            </span>
                                            <span class="menu-title">System Settings</span>
                                        </a>
                                    </div>

                                @endif
                                
                            </div>
                        </div>
                    @endif

                    @if(!empty($permission) && ($permission[16]['permission_status'] == 1 || $permission[77]['permission_status'] == 1 || $permission[17]['permission_status'] == 1  || $permission[42]['permission_status'] == 1 || $permission[18]['permission_status'] == 1 || $permission[6]['permission_status'] == 1 || $permission[38]['permission_status'] == 1 || $permission[8]['permission_status'] == 1 || $permission[39]['permission_status'] == 1 || $permission[33]['permission_status'] == 1 || $permission[34]['permission_status'] == 1 || $permission[46]['permission_status'] == 1 || $permission[11]['permission_status'] == 1  || $permission[53]['permission_status'] == 1 || $permission[57]['permission_status'] == 1 || $permission[35]['permission_status'] == 1 || $permission[37]['permission_status'] == 1 || $permission[61]['permission_status'] == 1  || $permission[60]['permission_status'] == 1 || $permission[62]['permission_status'] == 1 ||  $permission[64]['permission_status'] || $permission[69]['permission_status'] == 1 ) && ($permission[16]['module_accessibility'] == 1 || $permission[77]['module_accessibility'] == 1 || $permission[17]['module_accessibility'] == 1  || $permission[42]['module_accessibility'] == 1 || $permission[18]['module_accessibility'] == 1 || $permission[6]['module_accessibility'] == 1 || $permission[38]['module_accessibility'] == 1 || $permission[8]['module_accessibility'] == 1 || $permission[39]['module_accessibility'] == 1 || $permission[33]['module_accessibility'] == 1 || $permission[34]['module_accessibility'] == 1 || $permission[46]['module_accessibility'] == 1 || $permission[11]['module_accessibility'] == 1  || $permission[53]['module_accessibility'] == 1 || $permission[57]['module_accessibility'] == 1 || $permission[35]['module_accessibility'] == 1 || $permission[37]['module_accessibility'] == 1 || $permission[61]['module_accessibility'] == 1  || $permission[60]['module_accessibility'] == 1 || $permission[62]['module_accessibility'] == 1 ||  $permission[64]['module_accessibility'] || $permission[69]['module_accessibility'] == 1))
                        
                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'gadgets-inventory' || $title == 'gadgets-allotment' || $title == 'letters' || $title == 'all-meetings' || $title == 'my-meetings' || $title == 'all-resignation' || $title == 'resignation' || $title == 'Alerts' || $title == 'manage-departments' || $title == 'manage-designations' || $title == 'manage-vehicles' || $title == 'help-desk' || $title == 'my-help-desk' || $title == 'link-post' || $title == 'organogram' || $title == 'system-errors' || $title == 'login-history' || $title == 'letter-templates' || $title == 'employee-letters' || $title == 'machine-logs' || $title == 'polling' || $title == 'employment-status' || $title == 'manage-funds') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Gadgets Inventory.png') }}" />
                                </span>
                                <span class="menu-title">Others</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                @if(!empty($permission)  && $permission[69]['module_accessibility'] == 1 && $permission[69]['permission_status'] == 1)
                                    <div class="menu-item search-item">

                                        <a class="menu-link nav-link  @if($title == 'employment-status') active @endif" href="{{ url('/employment-status') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Gadgets Inventory.png') }}" />
                                            </span>
                                            <span class="menu-title">Employment Status</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission)  && $permission[77]['module_accessibility'] == 1 && $permission[77]['permission_status'] == 1)
                                    <div class="menu-item search-item">

                                        <a class="menu-link nav-link  @if($title == 'manage-funds') active @endif" href="{{ url('/funds') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Gadgets Inventory.png') }}" />
                                            </span>
                                            <span class="menu-title">All Funds</span>
                                        </a>
                                    </div>

                                @endif
                               
                                @if(!empty($permission)  && $permission[16]['module_accessibility'] == 1 && $permission[16]['permission_status'] == 1)
                                    <div class="menu-item search-item">

                                        <a class="menu-link nav-link  @if($title == 'gadgets-inventory') active @endif" href="{{ url('/gadgets-inventory') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Gadgets Inventory.png') }}" />
                                            </span>
                                            <span class="menu-title">Gadgets Inventory</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[17]['module_accessibility'] == 1 && $permission[17]['permission_status'] == 1)
                                    <div class="menu-item search-item">

                                        <a class="menu-link nav-link  @if($title == 'gadgets-allotment') active @endif" href="{{ url('/gadgets-allotment') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Gadgets Allotment.png') }}" />
                                            </span>
                                            <span class="menu-title">Gadgets Allotment</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[42]['module_accessibility'] == 1 && $permission[42]['permission_status'] == 1)
                            
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link  @if($title == 'letters') active @endif" href="{{ url('/letters') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Letters.png') }}" />
                                            </span>
                                            <span class="menu-title">All Letters</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[61]['module_accessibility'] == 1 && $permission[61]['permission_status'] == 1)
                            
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link  @if($title == 'letter-templates') active @endif" href="{{ url('/letter-template') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Letters.png') }}" />
                                            </span>
                                            <span class="menu-title">Letter Template</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[60]['module_accessibility'] == 1 && $permission[60]['permission_status'] == 1)
                            
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link  @if($title == 'employee-letters') active @endif" href="{{ url('/employee-letter') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Letters.png') }}" />
                                            </span>
                                            <span class="menu-title">Employee Letter Template</span>
                                        </a>
                                    </div>
                                @endif
                                
                                @if(!empty($permission) && $permission[18]['module_accessibility'] == 1 && $permission[18]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'all-meetings') active @endif" href="{{ url('/meetings') }}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Meetings.png') }}" />
                                            </span>
                                            <span class="menu-title">All Meetings</span>
                                        </a>
                                    </div>

                                @endif
                                @if(!empty($permission) && $permission[6]['module_accessibility'] == 1 && $permission[6]['permission_status'] == 1)

                                    <div class="menu-item search-item"> 
                                        <a class="menu-link nav-link @if($title == 'my-meetings') active @endif" href="{{ url('/my-meetings') }}">
                                            @if($meetings == 1)  
                                                <span class="menu-icon pulse pulse-primary">
                                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Meetings.png') }}" />
                                                    <span class="pulse-ring"></span>
                                                </span>
                                            @else
                                                    <span class="menu-icon">
                                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Meetings.png') }}" />
                                                </span>
                                            @endif  
                                            <span class="menu-title">My Meetings</span> 
                                        </a>
                                    </div>

                                @endif
                                @if(!empty($permission) && $permission[38]['module_accessibility'] == 1 && $permission[38]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'all-resignation') active @endif" href="{{url('resignations')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Resignation.png') }}" />
                                            </span>
                                            @if($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                               <span class="menu-title">All Resignations</span>                                         
                                            @else
                                                <span class="menu-title">Team Resignations</span>
                                            @endif
                                            
                                        </a>
                                    </div>

                                @endif


                                @if(!empty($permission) && $permission[8]['module_accessibility'] == 1 && $permission[8]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'resignation') active @endif" href="{{url('employee-resignation')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Resignation.png') }}" />
                                            </span>
                                            <span class="menu-title">Resignation</span>
                                        </a>
                                    </div>

                                @endif
                                @if(!empty($permission) && $permission[39]['module_accessibility'] == 1 && $permission[39]['permission_status'] == 1)
                            
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'Alerts') active @endif" href="{{url('alerts')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/All Alerts.png') }}" />
                                            </span>
                                            <span class="menu-title">All Alerts</span>
                                        </a>
                                    </div>
                                @endif
                                
                                @if(!empty($permission) && $permission[34]['module_accessibility'] == 1 && $permission[34]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'manage-vehicles') active @endif" href="{{url('vehicles')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Manage Vehicles.png') }}" />
                                            </span>
                                            <span class="menu-title">Manage Vehicles</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[35]['module_accessibility'] == 1 && $permission[35]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link @if($title == 'login-history' || $title == 'login-activity') active @endif" href="{{url('login-history')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Login History.png') }}" />
                                            </span>
                                            <span class="menu-title">Login History</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[37]['module_accessibility'] == 1 && $permission[37]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link @if($title == 'system-errors') active @endif" href="{{url('system-error-logs')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/System Error Logs.png') }}" />
                                            </span>
                                            <span class="menu-title">System Error Logs</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[62]['module_accessibility'] == 1 && $permission[62]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link @if($title == 'machine-logs') active @endif" href="{{url('machine-attendance-logs')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/System Error Logs.png') }}" />
                                            </span>
                                            <span class="menu-title">Attendance Machine Logs</span>
                                        </a>
                                    </div>

                                @endif

                                @if(!empty($permission) && $permission[46]['module_accessibility'] == 1 && $permission[46]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'help-desk') active @endif" href="{{url('/all-tickets')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Help Desk.png') }}" />
                                            </span>
                                            <span class="menu-title">All Help Desk</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[11]['module_accessibility'] == 1 && $permission[11]['permission_status'] == 1)
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'my-help-desk') active @endif" href="{{url('my-tickets')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Help Desk.png') }}" />
                                            </span>
                                            <span class="menu-title">My Help Desk</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[53]['module_accessibility'] == 1 && $permission[53]['permission_status'] == 1)
                                
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'link-post') active @endif"  href="{{url('/linked-in-post')}}" >
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Linkedin.png') }}" />
                                            </span>
                                            <span class="menu-title">LinkedIn Post</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[57]['module_accessibility'] == 1 && $permission[57]['permission_status'] == 1 )
                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'organogram') active @endif"  href="{{url('/organogram')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Linkedin.png') }}" />
                                            </span>
                                            <span class="menu-title">Organogram</span>
                                        </a>
                                    </div>
                                @endif

                                @if(!empty($permission) && $permission[64]['module_accessibility'] == 1 && $permission[64]['permission_status'] == 1 )

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'polling') active @endif" href="{{url('/polling')}}">
                                            <span class="menu-icon">
                                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/My Meetings.png') }}" />
                                            </span>
                                            <span class="menu-title">Manage Polls</span>
                                        </a>
                                    </div>
                                @endif
                            
                            </div>
                        </div>

                    @endif
                    @if(!empty($permission) && ($permission[71]['permission_status'] == 1) && ($permission[71]['module_accessibility'] == 1 ))
                   
                        <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion  main-menu-employee @if($title == 'employee-report') show @endif">

                            <span class="menu-link">
                                <span class="menu-icon">
                                    <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Employees MDM.png') }}" />
                                </span>
                                <span class="menu-title">Reports & Analytics</span>
                                <span class="menu-arrow"></span>
                            </span>

                            <div class="menu-sub menu-sub-accordion menu-active-bg">
                                @if(!empty($permission) && $permission[71]['module_accessibility'] == 1 && $permission[71]['permission_status'] == 1)

                                    <div class="menu-item search-item">
                                        <a class="menu-link nav-link @if($title == 'employee-report') active @endif" href="{{ url('/reports/employee-report') }}">
                                            <span class="menu-bullet">
                                                <i class="far fa-circle nav-icon"></i>
                                            </span>
                                            <span class="menu-title ">Employee Report</span>
                                        </a>
                                    </div>

                                @endif

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'attendance-report') active @endif" href="{{ url('/reports/attendance-report') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>
                                        <span class="menu-title">Attendance Visualization</span>
                                    </a>
                                </div>
                                
                            </div>


                        </div>
                    @endif

                    {{-- <div class="menu-item search-item">
                        <a class="menu-link nav-link  @if($title == 'index') active @endif" href="{{ url('/') }}">
                            <span class="menu-icon">
                                <span class="svg-icon svg-icon-2">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="2" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                        <rect opacity="0.3" x="13" y="2" width="9" height="9" rx="2" fill="currentColor" />
                                        <rect opacity="0.3" x="13" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                        <rect opacity="0.3" x="2" y="13" width="9" height="9" rx="2" fill="currentColor" />
                                    </svg>
                                </span>
                            </span>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </div>

                    <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'employee' || $title == 'manage-designations' || $title == 'manage-departments' || $title == 'manage-locations' || $title == 'manage-companies') show @endif">

                        <span class="menu-link">
                            <span class="menu-icon">
                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Employees MDM.png') }}" />
                            </span>
                            <span class="menu-title">MDM</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion menu-active-bg">

                            @if(!empty($permission) && $permission[76]['module_accessibility'] == 1 && $permission[76]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'manage-companies') active @endif" href="{{ url('/companies') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>
                                        <span class="menu-title ">Companies</span>
                                    </a>
                                </div>

                            @endif

                            @if(!empty($permission) && $permission[50]['module_accessibility'] == 1 && $permission[50]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link @if($title == 'manage-locations') active @endif" href="{{ url('/locations') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>

                                        <span class="menu-title ">Locations</span>
                                    </a>
                                </div>

                            @endif



                            @if(!empty($permission) && $permission[32]['module_accessibility'] == 1 && $permission[32]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'manage-departments') active @endif" href="{{ url('/departments') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>
                                        <span class="menu-title">Departments</span>
                                    </a>
                                </div>

                            @endif


                            @if(!empty($permission) && $permission[33]['module_accessibility'] == 1 && $permission[33]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'manage-designations') active @endif" href="{{ url('/designations') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>
                                        <span class="menu-title">Designations</span>
                                    </a>
                                </div>

                            @endif

                            @if(!empty($permission) && $permission[12]['module_accessibility'] == 1 && $permission[12]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'employee') active @endif" href="{{ url('/employees') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>
                                        <span class="menu-title">Employees</span>
                                    </a>
                                </div>

                            @endif

                            
                        </div>


                    </div>




                    <div data-kt-menu-trigger="click" class="menu-item search-item menu-accordion main-menu-employee @if($title == 'monthly-attendance' || $title == 'manage-attendance' || $title == 'payroll' || $title == 'manage-locations' || $title == 'manage-companies') show @endif">

                        <span class="menu-link">
                            <span class="menu-icon">
                                <img class="sidebar-icon" src="{{ asset('portal_assets/sidebar_icons/Employees MDM.png') }}" />
                            </span>
                            <span class="menu-title">Transaction</span>
                            <span class="menu-arrow"></span>
                        </span>

                        <div class="menu-sub menu-sub-accordion menu-active-bg">

                            @if(!empty($permission) && $permission[67]['module_accessibility'] == 1 && $permission[67]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link @if($title == 'monthly-attendance') active @endif" href="{{ url('/monthly-attendance') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>

                                        <span class="menu-title ">Monthly Attendance</span>
                                    </a>
                                </div>

                            @endif

                            @if(!empty($permission) && $permission[19]['module_accessibility'] == 1 && $permission[19]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link @if($title == 'manage-attendance') active @endif" href="{{ url('/attendance') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>

                                        <span class="menu-title ">Detailed Attendance</span>
                                    </a>
                                </div>

                            @endif

                            @if(!empty($permission) && $permission[27]['module_accessibility'] == 1 && $permission[27]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link @if($title == 'payroll') active @endif" href="{{ url('/payroll') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>

                                        <span class="menu-title ">Payroll</span>
                                    </a>
                                </div>

                            @endif



                            @if(!empty($permission) && $permission[30]['module_accessibility'] == 1 && $permission[30]['permission_status'] == 1)

                                <div class="menu-item search-item">
                                    <a class="menu-link nav-link @if($title == 'manage-loan') active @endif" href="{{ url('/loans') }}">
                                        <span class="menu-bullet">
                                            <i class="far fa-circle nav-icon"></i>
                                        </span>
                                        <span class="menu-title">Loans</span>
                                    </a>
                                </div>

                            @endif
                            
                        </div>


                    </div> --}}
    
                </div>
            </div>
        </div>

    </div>
        
@push('js-link')
    <script>
    $("#serachMenu").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".search-item").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            let get_value = $('#serachMenu').val();
           
            if(get_value.length > 0){
                $('.main-menu-employee').addClass('show');
                $('.main-menu-dpt-desgn').addClass('show');
            }else{
                $('.main-menu-employee').removeClass('show');
                $('.main-menu-dpt-desgn').removeClass('show');
            }   
        });
    });
        
    </script>
        
@endpush