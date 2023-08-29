@extends('layout.main')

@section('main-section')

    <div class="d-flex flex-column flex-column-fluid">

        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

            <div class="app-container col-12 d-flex flex-stack">

                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">My
                        Notifications</h1>
                </div>
                <div class="d-flex align-items-center gap-2 gap-lg-3">

                    <div class="m-0">
                        <a href="{{ url('read-my-notifications') }}" class="btn btn-sm btn-primary">
                            <span class="svg-icon svg-icon-2">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="11.364" y="20.364" width="16" height="2"
                                        rx="1" transform="rotate(-90 11.364 20.364)" fill="currentColor"></rect>
                                    <rect x="4.36396" y="11.364" width="16" height="2" rx="1"
                                        fill="currentColor"></rect>
                                </svg>
                            </span>
                            Read All Notifications</a>
                    </div>

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
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2"
                                            rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor">
                                        </rect>
                                        <path
                                            d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z"
                                            fill="currentColor"></path>
                                    </svg>
                                </span>
                                <input type="text" class="form-control  w-250px ps-15" placeholder="Search Record ..."
                                    id="searchFilter">
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-3">

                        <div class="table-responsive popup-visible ">

                            <div id="tableEmployee_wrapper"
                                class="dataTables_wrapper dt-bootstrap4 no-footer table-row-bordered">
                                <div class="table-responsive popup-visible ">
                                    <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="tableNotification" aria-describedby="tableEmployee_info">

                                        <thead>
                                            <tr class="fw-bold text-muted bg-light">
                                                <th class="ps-4">Notification ID</th>
                                                <th>Notification Message</th>
                                                <th>Notification Status</th>
                                            </tr>
                                        </thead>
                                        <tbody >
                                            @if(!empty($notifications))
                                                @foreach ($notifications['notification'] as $notification)
                                                    @if (empty($notification['viewer_history']))
                                                        <tr class="clickable-row notification-open"
                                                            data-notificationid="{{ $notification['notification_id'] }}"
                                                            data-href="{{ $notification['notification_url'] }}"
                                                            style="background:lightgray; cursor:pointer">
                                                        @else
                                                        <tr class="clickable-row"
                                                            data-href="{{ $notification['notification_url'] }}"
                                                            style="cursor:pointer">
                                                    @endif

                                                    <td class="ps-8">{{ $notification['notification_id'] }}</td>
                                                    <td>
                                                        <h4>{{ $notification['notification_title'] }}</h4>
                                                        <i class="far fa-clock text-gray"></i> &nbsp;
                                                        {{ $notification['notification_time'] }} :
                                                        {{ $notification['notification_message'] }}
                                                        <p class="text-right"></p>
                                                    </td>
                                                    <td>
                                                        @if (empty($notification['viewer_history']))
                                                            Unread
                                                        @else
                                                            Read
                                                        @endif
                                                    </td>
                                                    </tr>
                                                    </a>
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

    @push('js-link')
        <script>
            function read_all_notificaiton() {
                read_all_notification_btn();
            }
        </script>
    @endpush
