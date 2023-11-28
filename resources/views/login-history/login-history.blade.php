@extends('layout.main')

@section('main-section')
<div class="d-flex flex-column flex-column-fluid">

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">

                    @php $month = date('m', strtotime(Request::get('history_date'))); @endphp
                    @if(empty(Request::get('history_date')))
                        <h1>Login History ({{ date('d') .  '-' . date('F') . '-' . date('Y') }})</h1>
                    @else
                        <h1>Login History ({{  date('d - F- Y', strtotime(Request::get('history_date'))) }})</h1>
                    @endif

                </h1>
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
                            <input type="text" class="form-control  w-250px ps-15" placeholder="Search Login History" id="searchFilter">
                        </div>
                    </div>
                    <div class="card-toolbar">
                        @php $filter = 'btn-color-primary'; @endphp

                        <a href="#" class="btn btn-sm btn-flex btn-light fw-bold {{!empty(Request::get('history_date')) ? $filter : '' }} {{!empty(Request::get('history_records')) ? $filter : '' }}" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">


                        <span class="svg-icon svg-icon-6 svg-icon-muted me-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="currentColor"></path>
                            </svg>
                        </span>
                        Filter</a>
                        <div class="menu menu-sub menu-sub-dropdown w-250px w-md-300px" data-kt-menu="true" id="kt_menu_63347db386dd1">
                            <form action="{{ url('login-history') }}" method="GET">
                                <div class="px-7 py-5">
                                    <div class="fs-5 text-dark fw-bold">Filter Options</div>
                                </div>

                                <div class="separator border-gray-200"></div>
                                <div class="px-7 py-5">

                                    <div class="mb-3">

                                        <label class="form-label fw-semibold">History Date</label>

                                        <div class="position-relative d-flex align-items-center">
                                            <input type="date" class="form-control" placeholder="Select a date" name="history_date" max="{{ date('Y-m-d') }}" @if(!empty(Request::get('history_date'))) value="{{ Request::get('history_date') }}" @endif />
                                        </div>


                                    </div>

                                    <div class="mb-3">
                                        
                                        <label class="form-label fw-semibold">Select Duration </label>
                                        
                                        <div>
                                            <select class="form-select" data-kt-select2="true" name="history_records">
                                                <option value="">Select Duration</option>
                                                <option value="7" @if (Request::get('history_records') == '7') selected @endif>Last 7 Days</option>
                                                <option value="15" @if (Request::get('history_records') == '15') selected @endif>Last 15 Days</option>
                                                <option value="30" @if (Request::get('history_records') == '30') selected @endif>Last 30 Days</option>
                                            </select>
                                        </div>
                                        
                                    </div>
                                    <div class="d-flex justify-content-end">
                                            @if (!empty(Request::get('history_date')) || !empty(Request::get('history_records')))
                                                <a href="{{ url('login-history') }}" class="btn btn-sm btn-danger btn-active-light-danger me-2">Reset Filter</a>
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

                    <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">

                        <thead>
                            <tr class="fw-bold text-muted bg-light">
                                <th class="ps-4">Employee</th>
                                <th>Depart &amp; Desig</th>
                                <th>Time</th>
                                <th>IP</th>
                                <th>Location</th>
                                <th>Lat & Long</th>
                                <th>Login Type
                                    <i class="fas fa-exclamation-circle ms-2 fs-7" data-bs-toggle="tooltip" title="If any administrator login user acccount by proxy user login its show proxy otherwise web/mobile application"></i>
                                </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(!empty($history))
                                @foreach ($history as $history)
                                    <tr>
                                        <td class="ps-4">{{$history['employee_code']}} - {{$history['employee_name']}}</td>
                                        <td>
                                            <p class="text-dark fw-bold mb-1 fs-6"> {{ $history['department_name'] }}</p>
                                            <p class="text-muted fw-semibold text-muted fs-7">{{$history['designation_name'] }}</p>
                                        </td>
                                        <td><span class="text-center">{{ date('d, F Y h:i:s A',strtotime($history['login_history_time'])) }}</span></td>
                                        <td><span class="text-center">{{ $history['login_history_ip'] }}</span></td>
                                        <td>{{ $history['login_history_country'] }} - {{ $history['login_history_city'] }}</td>
                                        <td>{{ $history['login_history_latitude'] }} & {{ $history['login_history_longitude'] }}</td>
                                        <td>
                                            @if($history['login_history_source'] == 1)
                                                <span class="badge badge-light-success fs-7 fw-bold">Web Application</span>
                                            @elseif($history['login_history_source'] == 2)
                                                <span class="badge badge-light-info fs-7 fw-bold">Mobile Application</span>
                                            @else
                                                <span class="badge badge-light-warning fs-7 fw-bold">Proxy</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('login-history/login-activity') }}/{{ $history['login_history_session_token'] }}" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
                                                <span class="svg-icon svg-icon-3">
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path opacity="0.3" d="M21.4 8.35303L19.241 10.511L13.485 4.755L15.643 2.59595C16.0248 2.21423 16.5426 1.99988 17.0825 1.99988C17.6224 1.99988 18.1402 2.21423 18.522 2.59595L21.4 5.474C21.7817 5.85581 21.9962 6.37355 21.9962 6.91345C21.9962 7.45335 21.7817 7.97122 21.4 8.35303ZM3.68699 21.932L9.88699 19.865L4.13099 14.109L2.06399 20.309C1.98815 20.5354 1.97703 20.7787 2.03189 21.0111C2.08674 21.2436 2.2054 21.4561 2.37449 21.6248C2.54359 21.7934 2.75641 21.9115 2.989 21.9658C3.22158 22.0201 3.4647 22.0084 3.69099 21.932H3.68699Z" fill="currentColor"></path>
                                                        <path d="M5.574 21.3L3.692 21.928C3.46591 22.0032 3.22334 22.0141 2.99144 21.9594C2.75954 21.9046 2.54744 21.7864 2.3789 21.6179C2.21036 21.4495 2.09202 21.2375 2.03711 21.0056C1.9822 20.7737 1.99289 20.5312 2.06799 20.3051L2.696 18.422L5.574 21.3ZM4.13499 14.105L9.891 19.861L19.245 10.507L13.489 4.75098L4.13499 14.105Z" fill="currentColor"></path>
                                                    </svg>
                                                </span>
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

@endsection

@push('js-link')

@endpush
