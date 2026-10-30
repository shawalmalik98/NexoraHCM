@if(CustomHelper::employee_welcome_status() == '1' && session('welcome_alert') ==  '')
    @include('include/welcome-alert')
@else
    @include('layout.header')

    @include('layout.sidebar')

    @yield('main-section')

    @include('include/attendance-alert')

    @include('include/change-password')

    @include('include/response-by-manager-request-modal')

    @if((!empty($login_user[0]['employee_department']) && CustomHelper::dept_has_role($login_user[0]['employee_department'], 'admin', $login_user[0]['employee_id'] ?? null)) || session('proxy_user') == "TRUE")
    @include('include/proxy-user')
    @endif

    @include('layout.footer')

@endif