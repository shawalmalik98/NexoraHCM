@extends('layout.main')

@push('css-link')

@endpush

@section('main-section')
<style>
    .my-card-body{
        height : 360px ;
    }
</style>
<div class="d-flex flex-column flex-column-fluid" >

    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">

        <div class="app-container col-12 d-flex flex-stack">

            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">

                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0"> Login Activity</h1>
            </div>
        </div>

    </div>

    <div class="app-content flex-column-fluid">

        <div class="app-container">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-3" > 
                    <div class="card mb-6">
                        <div class="card-body pt-9 pb-0 my-card-body">
                            {{--begin::Details--}}
                            <div class="d-flex flex-wrap flex-sm-nowrap">
                                {{--begin: Pic--}}
                                <div class="me-7 mb-4">
                                    <div class="symbol symbol-100px symbol-lg-160px symbol-fixed position-relative">
                                        @if(!empty($image[0]['profile_avatar']) && $image[0]['profile_avatar'] != '')
                                           
                                            @if(file_exists('portal_assets/profile_pictures/'.$image[0]['profile_avatar']))
                                                <img src="{{url(asset('portal_assets/profile_pictures')).'/'.$image[0]['profile_avatar'] }}" class="" alt="" style="display: block" />
                                            @else
                                                <img src="{{url(asset('portal_assets/images/default-img.png'))}}"  style="display: block" />
                                            @endif
                                        @else
                                            <img src="{{url(asset('portal_assets/images/default-img.png'))}}"  style="display: block" />
                                        @endif

                                        <a href="#" class="text-gray-900 text-hover-primary fs-2 fw-bold me-1">{!! CustomHelper::getEmpProfileDiv($history[0]['employee_id']) !!}</a>

                                        @if($history[0]['login_history_source'] == 2)
                                            <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2 mt-3">
                                                <span class="svg-icon svg-icon-4 me-1">
                                                    <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">
                                                        <path d="M85.81 120H34.19a8.39 8.39 0 0 1-8.38-8.39V8.39A8.39 8.39 0 0 1 34.19 0h51.62a8.39 8.39 0 0 1 8.38 8.39v103.22a8.39 8.39 0 0 1-8.38 8.39zM34.19 3.87a4.52 4.52 0 0 0-4.51 4.52v103.22a4.52 4.52 0 0 0 4.51 4.52h51.62a4.52 4.52 0 0 0 4.51-4.52V8.39a4.52 4.52 0 0 0-4.51-4.52z"/><path d="M73.7 10.32H46.3L39.28 3.3 42.01.57l5.89 5.88h24.2L77.99.57l2.73 2.73-7.02 7.02zM47.1 103.23h25.81v3.87H47.1z"/>
                                                    </svg>

                                                </span>
                                                {{($history[0]['device_name'])}}
                                            </a>
                                        @endif    

                                        <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2 mt-3">
                                        {{--begin::Svg Icon | path: icons/duotune/communication/com006.svg--}}
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M16.5 9C16.5 13.125 13.125 16.5 9 16.5C4.875 16.5 1.5 13.125 1.5 9C1.5 4.875 4.875 1.5 9 1.5C13.125 1.5 16.5 4.875 16.5 9Z" fill="currentColor" />
                                                <path d="M9 16.5C10.95 16.5 12.75 15.75 14.025 14.55C13.425 12.675 11.4 11.25 9 11.25C6.6 11.25 4.57499 12.675 3.97499 14.55C5.24999 15.75 7.05 16.5 9 16.5Z" fill="currentColor" />
                                                <rect x="7" y="6" width="4" height="4" rx="2" fill="currentColor" />
                                            </svg>
                                        </span>
                                        {{--end::Svg Icon--}}{{($history[0]['department_name'])}}</a>
                                        <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mb-2 mt-3">
                                        {{--begin::Svg Icon | path: icons/duotune/general/gen018.svg--}}
                                        <span class="svg-icon svg-icon-4 me-1">
                                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path opacity="0.3" d="M18.0624 15.3453L13.1624 20.7453C12.5624 21.4453 11.5624 21.4453 10.9624 20.7453L6.06242 15.3453C4.56242 13.6453 3.76242 11.4453 4.06242 8.94534C4.56242 5.34534 7.46242 2.44534 11.0624 2.04534C15.8624 1.54534 19.9624 5.24534 19.9624 9.94534C20.0624 12.0453 19.2624 13.9453 18.0624 15.3453Z" fill="currentColor" />
                                                <path d="M12.0624 13.0453C13.7193 13.0453 15.0624 11.7022 15.0624 10.0453C15.0624 8.38849 13.7193 7.04535 12.0624 7.04535C10.4056 7.04535 9.06241 8.38849 9.06241 10.0453C9.06241 11.7022 10.4056 13.0453 12.0624 13.0453Z" fill="currentColor" />
                                            </svg>
                                        </span>
                                        {{--end::Svg Icon--}}{{($history[0]['designation_name'])}}</a>

                                        <a href="#" class="d-flex align-items-center text-gray-400 text-hover-primary me-5 mt-4">
                                            @if($history[0]['login_history_source'] == 1)
                                                <span class="badge badge-light-success fs-7 fw-bold">Web Application</span>
                                            @elseif($history[0]['login_history_source'] == 2)
                                                <span class="badge badge-light-info fs-7 fw-bold">Mobile Application</span>
                                            @else
                                                <span class="badge badge-light-warning fs-7 fw-bold">Proxy</span>
                                            @endif
                                        </a>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-6 col-lg-9" >
                    <div class="card mb-6">
                        <div class="card-body pt-9 pb-8 my-card-body"  >
                            <iframe src="https://maps.google.com/maps?q={{$history[0]['login_history_latitude']}},{{$history[0]['login_history_longitude']}}&output=embed" title="" style="width: 100%;" height="100%" ></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-6">
                <div class="card-header border-0 pt-5">
                    <div class="card-title">
                        <div class="d-flex align-items-center position-relative my-1">
                            <span class="svg-icon svg-icon-1 position-absolute ms-6">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <rect opacity="0.5" x="17.0365" y="15.1223" width="8.15546" height="2" rx="1" transform="rotate(45 17.0365 15.1223)" fill="currentColor"></rect>
                                    <path d="M11 19C6.55556 19 3 15.4444 3 11C3 6.55556 6.55556 3 11 3C15.4444 3 19 6.55556 19 11C19 15.4444 15.4444 19 11 19ZM11 5C7.53333 5 5 7.53333 5 11C5 14.4667 7.53333 17 11 17C14.4667 17 17 14.4667 17 11C17 7.53333 14.4667 5 11 5Z" fill="currentColor"></path>
                                </svg>
                            </span>
                            <input type="text" class="form-control  w-250px ps-15" placeholder="Search Detail" id="searchFilter">
                        </div>
                    </div>
                </div>   
                <div class="card-body py-3 pt-9 pb-0">

                    <div class="table-responsive popup-visible ">

                        <div id="tableEmployee_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer"><div class="table-responsive popup-visible ">
                            <table class="table align-middle gs-0 gy-4 dataTable no-footer table-row-bordered" id="system_datatable" aria-describedby="tableEmployee_info">

                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-4">Activity Detail</th>
                                        <th>Activity Request</th>
                                        <th>Activity URL</th>
                                        <th>Activity IP</th>
                                        <th>Activity Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($activity))
                                        @foreach ($activity as $activity)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="d-flex justify-content-start flex-column">
                                                            <span class="text-dark fw-bold text-hover-primary d-block mb-1 fs-6">{{ $activity['activity_detail'] }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-center">{{ $activity['activity_request_method'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-center">{{ $activity['activity_url'] }}</span>
                                                </td>
                                                <td>{{ $activity['activity_employee_ip_address'] }}</td>
                                                <td>{{ $activity['activity_created'] }}</td>
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
