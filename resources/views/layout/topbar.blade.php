@php
    
    $notifications = App\Traits\NotificationTrait::get_all_notifications(100);
    
    $alerts = CustomHelper::total_alerts_notification_records();
    
@endphp

<div id="kt_app_header" class="app-header">
    {{-- begin::Header container --}}
    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between"
        id="kt_app_header_container">

        <div class="d-flex align-items-center d-lg-none ms-n2 me-2" title="Show sidebar menu">
            <div class="btn btn-icon btn-active-color-primary w-35px h-35px" id="kt_app_sidebar_mobile_toggle">
                <span class="svg-icon svg-icon-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 7H3C2.4 7 2 6.6 2 6V4C2 3.4 2.4 3 3 3H21C21.6 3 22 3.4 22 4V6C22 6.6 21.6 7 21 7Z"
                            fill="currentColor" />
                        <path opacity="0.3"
                            d="M21 14H3C2.4 14 2 13.6 2 13V11C2 10.4 2.4 10 3 10H21C21.6 10 22 10.4 22 11V13C22 13.6 21.6 14 21 14ZM22 20V18C22 17.4 21.6 17 21 17H3C2.4 17 2 17.4 2 18V20C2 20.6 2.4 21 3 21H21C21.6 21 22 20.6 22 20Z"
                            fill="currentColor" />
                    </svg>
                </span>
            </div>
        </div>

        <div class="d-flex align-items-stretch justify-content-between flex-lg-grow-1" id="kt_app_header_wrapper">
            {{-- begin::Menu wrapper --}}
            <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
                data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
                data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="end"
                data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
                data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
                data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
            </div>
            {{-- end::Menu wrapper --}}
            {{-- begin::Navbar --}}
            <div class="app-navbar flex-shrink-0">

                {{-- begin::Notifications --}}

                <div class="app-navbar-item ms-1 ms-lg-3">

                    <div class="btn btn-icon btn-custom btn-icon-muted btn-active-light btn-active-color-primary w-35px h-35px w-md-40px h-md-40px position-relative"
                        data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end"
                        >
                        {{-- onclick="notification_fetch()" --}}
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.3"
                                    d="M20 3H4C2.89543 3 2 3.89543 2 5V16C2 17.1046 2.89543 18 4 18H4.5C5.05228 18 5.5 18.4477 5.5 19V21.5052C5.5 22.1441 6.21212 22.5253 6.74376 22.1708L11.4885 19.0077C12.4741 18.3506 13.6321 18 14.8167 18H20C21.1046 18 22 17.1046 22 16V5C22 3.89543 21.1046 3 20 3Z"
                                    fill="currentColor"></path>
                                <rect x="6" y="12" width="7" height="2" rx="1"
                                    fill="currentColor"></rect>
                                <rect x="6" y="7" width="12" height="2" rx="1"
                                    fill="currentColor"></rect>
                            </svg>
                        </span>

                        @if (!empty($notifications['notification_count']) && $notifications['notification_count'] > 0)
                            <span
                                class="bullet bullet-dot bg-success h-9px w-9px position-absolute translate-middle top-0 start-50 animation-blink"></span>
                        @endif
                    </div>
                    @if (!empty($notifications['notification']))
                        <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px" data-kt-menu="true">
                            <div class="d-flex flex-column bgi-no-repeat rounded-top"
                                style="background-image:url({{ asset('portal_assets/images/menu-header-bg.jpg') }})">
                                <h3 class="text-white fw-semibold px-9 mt-10 mb-6">Recent 100 Notifications
                                    @if (!empty($notifications['notification_count']) && $notifications['notification_count'] >= 0)
                                        <span class="fs-8 opacity-75 ps-1 notification_counter">(Out of
                                            {{ $notifications['notification_count'] }})</span>
                                    @endif
                                </h3>

                                <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9"
                                    role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active"
                                            data-bs-toggle="tab" href="#topbar_notifications" aria-selected="true"
                                            role="tab">Notifications</a>
                                    </li>
                                    @if ($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                        @if (
                                            $alerts['interviews'] > 0 ||
                                                $alerts['meetings'] > 0 ||
                                                $alerts['birthdays'] > 0 ||
                                                $alerts['employee_work_Ceremony'] > 0 ||
                                                $alerts['probation_completion'] > 0 ||
                                                $alerts['notice_period_ending'] > 0 ||
                                                 $alerts['onboard_employees'] > 0)
                                            <li class="nav-item" role="presentation">
                                                <a class="nav-link text-white opacity-75 opacity-state-100 pb-4"
                                                    data-bs-toggle="tab" href="#topbar_alerts" aria-selected="false"
                                                    role="tab" tabindex="-1">Alerts</a>
                                            </li>
                                        @endif
                                    @endif

                                </ul>
                            </div>

                            <div class="tab-content">
                                @if (!empty($notifications['notification']))
                                    <div class="tab-pane fade show active" id="topbar_notifications" role="tabpanel">

                                        <div class="scroll-y mh-325px my-5 px-8 notification-refresher"
                                            id="notifications-container">
                                            @foreach ($notifications['notification'] as $notification)
                                                <div class="d-flex flex-stack">
                                                    <div class="d-flex align-items-center ">
                                                        <div class="notification-count-div"
                                                            data-total_notifications="{{ $notifications['notification_count'] }}">
                                                            @if (!empty($notification['viewer_history']))
                                                                <a href="{{ url($notification['notification_url']) }}"
                                                                    class="text-gray-400  fw-bold text-hover-primary">
                                                                    <p class="fs-4 mb-1">
                                                                        {{ $notification['notification_title'] }}</p>
                                                                    <p class="fs-7 mb-1">
                                                                        {{ $notification['notification_message'] }}</p>
                                                                    <p class="fs-7 mb-0"><i
                                                                            class="far fa-clock mr-1"></i>
                                                                        {{ $notification['notification_time'] }}</p>
                                                                </a>
                                                            @else
                                                                <a href="{{ url($notification['notification_url']) }}"
                                                                    class="text-dark fw-bold notification-open text-hover-primary"
                                                                    data-notificationid="{{ $notification['notification_id'] }}">
                                                                    <p class="fs-4 mb-1">
                                                                        {{ $notification['notification_title'] }}</p>
                                                                    <p class="fs-7 mb-1">
                                                                        {{ $notification['notification_message'] }}</p>
                                                                    <p class="fs-7 mb-0">
                                                                        <i class="far fa-clock mr-1"
                                                                            style="color:black"></i>
                                                                        {{ $notification['notification_time'] }}
                                                                    </p>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <hr>
                                            @endforeach
                                        </div>
                                        <div class="py-3 text-center border-top">
                                            @if (!empty($notifications['notification']) && count($notifications['notification']) >= 10)
                                                <a href="{{ url('all-notifications') }}"
                                                    class="btn btn-color-gray-600 btn-active-color-primary">View All

                                                    <span class="svg-icon svg-icon-5">
                                                        <svg width="24" height="24" viewBox="0 0 24 24"
                                                            fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <rect opacity="0.5" x="18" y="13"
                                                                width="13" height="2" rx="1"
                                                                transform="rotate(-180 18 13)" fill="currentColor" />
                                                            <path
                                                                d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z"
                                                                fill="currentColor" />
                                                        </svg>
                                                    </span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                @if ($login_user[0]['employee_department'] == 1 || $login_user[0]['employee_department'] == 2)
                                    <div class="tab-pane fade" id="topbar_alerts" role="tabpanel">

                                        <div class="scroll-y mh-325px my-5 px-8 notification-refresher">
                                            <div class="flex-stack py-4">

                                                <div class="align-items-center">

                                                    <div class="mb-0 me-2">
                                                        @if (!empty($alerts['interviews']))
                                                            <div class="d-flex align-items-center mb-5">
                                                                <div class="symbol symbol-40px me-4">
                                                                    <span class="symbol-label bg-light">
                                                                        <i class="las la-comments fs-2 text-primary"><span
                                                                                class="path1"></span><span
                                                                                class="path2"></span></i>
                                                                    </span>
                                                                </div>
                                                                <a
                                                                    href="{{ url('alerts-notifications') }}#interviews">
                                                                    <div
                                                                        class="d-flex flex-column text-dark fw-bold text-hover-primary">
                                                                        <p class="fs-4 mb-1">Today Interview(s)</p>
                                                                        <p class="fs-7 mb-1">
                                                                            {{ $alerts['interviews'] }} interviews scheduled for today. Please click here to view.</p>
                                                                        <p class="fs-7 mb-0">{{ date('d M, Y') }}</p>

                                                                    </div>
                                                                </a>

                                                            </div>
                                                            <hr>
                                                        @endif

                                                        @if (!empty($alerts['meetings']))
                                                            <div class="d-flex align-items-center mb-5">
                                                                <div class="symbol symbol-40px me-4">
                                                                    <span class="symbol-label bg-light">
                                                                        <i class="las la-comments fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                                    </span>
                                                                </div>
                                                                <a href="{{ url('alerts-notifications') }}#meetings">
                                                                    <div class="d-flex flex-column text-dark fw-bold text-hover-primary">
                                                                        <p class="fs-4 mb-1">Today Meeting(s)</p>
                                                                        <p class="fs-7 mb-1">{{ $alerts['meetings'] }} meetings you've to attend. Please click here to view.</p>
                                                                        <p class="fs-7 mb-0">{{ date('d M, Y') }}</p>

                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <hr>
                                                        @endif

                                                        @if (!empty($alerts['birthdays']))
                                                            <div class="d-flex align-items-center mb-5">
                                                                <div class="symbol symbol-40px me-4">
                                                                    <span class="symbol-label bg-light">
                                                                        <i class="las la-comments fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                                    </span>
                                                                </div>
                                                                <a href="{{ url('alerts-notifications') }}#employee-birthday">
                                                                    <div class="d-flex flex-column text-dark fw-bold text-hover-primary">
                                                                        <p class="fs-4 mb-1">Today Employee(s) Birthday</p>
                                                                        <p class="fs-7 mb-1">{{ $alerts['birthdays'] }} employee(s) having their birthday today. Please click here to view.</p>
                                                                        <p class="fs-7 mb-0">{{ date('d M, Y') }}</p>
                                                                    </div>
                                                                </a>

                                                            </div>
                                                            <hr>
                                                        @endif

                                                        @if (!empty($alerts['employee_work_Ceremony']))
                                                            <div class="d-flex align-items-center mb-5">
                                                                <div class="symbol symbol-40px me-4">
                                                                    <span class="symbol-label bg-light">
                                                                        <i class="las la-comments fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                                    </span>
                                                                </div>
                                                                <a href="{{ url('alerts-notifications') }}#employee-work-aniversery">
                                                                    <div class="d-flex flex-column text-dark fw-bold text-hover-primary">

                                                                        <p class="fs-4 mb-1">Work Anniversary</p>
                                                                        <p class="fs-7 mb-1">{{ $alerts['employee_work_Ceremony'] }} employee(s) having their work anniversary today. Please click here to view.</p>
                                                                        <p class="fs-7 mb-0">{{ date('d M, Y') }}</p>

                                                                    </div>
                                                                </a>
                                                            </div>
                                                            <hr>
                                                        @endif

                                                        @if (!empty($alerts['probation_completion']))
                                                            <div class="d-flex align-items-center mb-5">
                                                                <div class="symbol symbol-40px me-4">
                                                                    <span class="symbol-label bg-light">
                                                                        <i class="las la-comments fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                                    </span>
                                                                </div>
                                                                <a href="{{ url('alerts-notifications') }}#probation_completed">
                                                                    <div class="d-flex flex-column text-dark fw-bold text-hover-primary">
                                                                        <p class="fs-4 mb-1">Probation Period Conclusion</p>
                                                                        <p class="fs-7 mb-1"> {{ $alerts['probation_completion'] }} employee(s) porbation period is going to be end today. Please click here to view.</p>
                                                                        <p class="fs-7 mb-0">{{ date('d M, Y') }}</p>

                                                                    </div>
                                                                </a>

                                                            </div>
                                                            <hr>
                                                        @endif

                                                        @if (!empty($alerts['notice_period_ending']))
                                                            <div class="d-flex align-items-center mb-5">
                                                                <div class="symbol symbol-40px me-4">
                                                                    <span class="symbol-label bg-light">
                                                                        <i class="las la-comments fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                                    </span>
                                                                </div>
                                                                <a href="{{ url('alerts-notifications') }}#notice_period">
                                                                    <div class="d-flex flex-column text-dark fw-bold text-hover-primary">

                                                                        <p class="fs-4 mb-1">Today Notice Period Ending</p>
                                                                        <p class="fs-7 mb-1 ">Notice period of {{ $alerts['notice_period_ending'] }} employee(s) comes to an end today. Please click here to view.</p>
                                                                        <p class="fs-7 mb-0 ">{{ date('d M, Y') }}</p>

                                                                    </div>
                                                                </a>

                                                            </div>
                                                            <hr>
                                                        @endif

                                                        @if (!empty($alerts['onboard_employees']))
                                                            <div class="d-flex align-items-center mb-5">
                                                                <div class="symbol symbol-40px me-4">
                                                                    <span class="symbol-label bg-light">
                                                                        <i class="las la-comments fs-2 text-primary"><span class="path1"></span><span class="path2"></span></i>
                                                                    </span>
                                                                </div>
                                                                <a href="{{ url('alerts-notifications') }}#onboard_employees">
                                                                    <div class="d-flex flex-column text-dark fw-bold text-hover-primary">

                                                                        <p class="fs-4 mb-1">Today Employee(s) Onboard</p>
                                                                        <p class="fs-7 mb-1 "> {{ $alerts['onboard_employees'] }} employee(s) onboard today. Please click here to view.</p>
                                                                        <p class="fs-7 mb-0 ">{{ date('d M, Y') }}</p>

                                                                    </div>
                                                                </a>

                                                            </div>
                                                            <hr>
                                                        @endif
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="menu menu-sub menu-sub-dropdown menu-column w-350px w-lg-375px"
                            data-kt-menu="true">
                            <div class="d-flex flex-column bgi-no-repeat rounded-top"
                                style="background-image:url({{ asset('portal_assets/images/menu-header-bg.jpg') }})">
                                <h3 class="text-white fw-semibold px-9 mt-10 mb-6">Notifications</h3>
                                <ul class="nav nav-line-tabs nav-line-tabs-2x nav-stretch fw-semibold px-9">
                                    <li class="nav-item">
                                        <a class="nav-link text-white opacity-75 opacity-state-100 pb-4 active" data-bs-toggle="tab" href="#kt_topbar_notifications_1">Notifications</a>
                                    </li>
                                </ul>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="kt_topbar_notifications_5" role="tabpanel">
                                    <div class="scroll-y mh-325px my-5 px-8">
                                        <div class="d-flex flex-stack py-4">
                                            <div class="d-flex align-items-center">
                                                <div class="mb-0 me-2">
                                                    <a href="javascript:void(0)" class="fs-6 text-gray-800 text-hover-primary fw-bold">
                                                        No Notifications To Show
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif



                </div>

                {{-- end::Notifications --}}
                {{-- begin::User menu --}}
                <div class="app-navbar-item ms-1 ms-lg-3" id="kt_header_user_menu_toggle">
                    @if (!empty($login_user))
                        <div class="cursor-pointer symbol symbol-35px symbol-md-40px" data-kt-menu-trigger="click"
                            data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                            @if (!empty($login_user[0]['profile_avatar']))
                                @if (file_exists('portal_assets/profile_pictures/' . $login_user[0]['profile_avatar']))
                                    <img src="{{ asset('portal_assets/profile_pictures') . '/' . $login_user[0]['profile_avatar'] }}" class="object-fit rounded-circle" alt="Profile Picture">
                                @else
                                    <img alt="Logo" src="{{ asset('portal_assets/images/default-img.png') }}" class="h-40px object-fit rounded-circle" />
                                @endif
                            @else
                                <img alt="Logo" src="{{ asset('portal_assets/images/default-img.png') }}" class="h-40px object-fit rounded-circle" />
                            @endif
                        </div>

                        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                            data-kt-menu="true">

                            <div class="menu-item px-3">
                                <div class="menu-content d-flex align-items-center px-3">

                                    <div class="symbol symbol-50px me-5">
                                        @if (!empty($login_user[0]['profile_avatar']))
                                            @if (file_exists('portal_assets/profile_pictures/' . $login_user[0]['profile_avatar']))
                                                <img src="{{ asset('portal_assets/profile_pictures') . '/' . $login_user[0]['profile_avatar'] }}" class="object-fit rounded-circle" alt="Profile Picture">
                                            @else
                                                <img alt="Logo" src="{{ asset('portal_assets/images/default-img.png') }}" alt="Profile Picture" class="h-40px object-fit rounded-circle" />
                                            @endif
                                        @else
                                            <img alt="Logo" src="{{ asset('portal_assets/images/default-img.png') }}" alt="Profile Picture" class="h-40px object-fit rounded-circle" />
                                        @endif
                                    </div>

                                    <div class="d-flex flex-column">
                                        @if (!empty($login_user[0]['employee_name']) || !empty($login_user[0]['employee_personal_email']))
                                            <div class="fw-bold d-flex align-items-center fs-5">
                                                <span class="fw-semibold fs-5">{{ $login_user[0]['employee_name'] }}</span>
                                            </div>
                                            <span class="fw-semibold text-muted fs-7">
                                                @if (strlen($login_user[0]['employee_personal_email']) < 27)
                                                    {{ $login_user[0]['employee_personal_email'] }}
                                                @else
                                                    @php
                                                        $email = explode('@', $login_user[0]['employee_personal_email']);
                                                    @endphp
                                                    {{ $email[0] }}<br>{{ '@' . $email['1'] }}
                                                @endif
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            </div>

                            <div class="separator my-2"></div>

                            <div class="menu-item px-5">
                                <a href="{{ url('personal-information') }}" class="menu-link px-5">Personal Information</a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ url('employee-personal-documents') }}" class="menu-link px-5 @if ($title == 'employee-personal-documents') active @endif">Personal Documents</a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ url('my-comapny-letters') }}" class="menu-link px-5 @if ($title == 'my-comapny-letters') active @endif">Company Letters</a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="{{ url('employee-company-assets') }}" class="menu-link px-5 @if ($title == 'employee-company-assets') active @endif">My Personal Assets</a>
                            </div>
                            <div class="menu-item px-5">
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#change_password_modal" class="menu-link px-5">Change Password</a>
                            </div>

                            @if (
                                (!empty($login_user[0]['employee_department']) && $login_user[0]['employee_department'] == 1) ||
                                    session('proxy_user') == 'TRUE')
                                <div class="menu-item px-5">
                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#proxy_user_model" class="menu-link px-5" onclick="proxy_employee_data_trigger()">Proxy User</a>
                                </div>
                            @endif

                            <div class="menu-item px-5">
                                <a href="{{ url('/sign-out') }}" onclick="localStorage.clear();" class="menu-link px-5">Sign Out</a>
                            </div>
                        </div>
                        {{-- end::User account menu --}}
                        {{-- end::Menu wrapper --}}
                    @endif
                </div>


                {{-- end::Header menu toggle --}}
            </div>


            {{-- end::Navbar --}}
        </div>
        {{-- end::Header wrapper --}}
    </div>
    {{-- end::Header container --}}
</div>
