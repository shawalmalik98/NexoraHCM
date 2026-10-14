<!DOCTYPE html>
<html lang="en">

<head>
    <title>Nexora HCM</title>
    <meta charset="utf-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="Nexora HCM" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Intelligent and Efficient HCM System" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:site_name" content="Nexora HCM" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="canonical" href="{{ url('/') }}" />
    <link rel="shortcut icon" href="{{ url('portal_assets/images/hcm-img.png') }}" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />

    <link href="{{ url('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/manez-theme.css') }}" rel="stylesheet" type="text/css" />
    <script src="https://code.jquery.com/jquery-3.6.1.js"></script>
	<script src="https://unpkg.com/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js"></script>
</head>

<body id="kt_body" class="app-blank bgi-size-cover bgi-attachment-fixed bgi-position-center">

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <style>body { background-image: url('/assets/media/auth/bg10.jpg'); }</style>
        {{--end::Page bg image--}}

        {{--begin::Authentication - Password reset --}}
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-lg-row-fluid">
                <div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
                    <img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ URL('/portal_assets/images/main-login.png') }}" alt="" />
                    <img class="theme-dark-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ URL('/portal_assets/images/main-login.png') }}" alt="" />

                    <h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">
                        The Ultimate HR Management Solution
                    </h1>

                    <div class="text-gray-600 fs-base text-center fw-semibold">At Nexora HCM, we have a reliable, secure and adaptable HR management built from the ground up. 
                        <br />We are determined to help our employees to give their best efforts every day to achieve the goals of their job.
                    </div>
                </div>
            </div>

            {{--begin::Body--}}
            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
                {{--begin::Wrapper--}}
                <div class="bg-body d-flex flex-column flex-center rounded-4 w-md-600px p-10">
                    {{--begin::Content--}}
                    <div class="d-flex flex-center flex-column align-items-stretch h-lg-100 w-md-400px">
                        {{--begin::Wrapper--}}
                        <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">


                            {{--begin::Form--}}
                            <form class="form w-100" id="password_reset_form">

                                <div class="text-center mb-11">
									<img alt="Nexora HCM" src="{{ url('portal_assets/images/hcm-logo.png') }}" class="theme-light-show" style="width: 72%;" />
																	
								</div>

                                <div class="text-center mb-10">
                                    <h1 class="text-gray-900 fw-bolder mb-3">
                                        Forgot Password ?
                                    </h1>

                                    <div class="text-gray-500 fw-semibold fs-6">
                                        Enter your NIC & Employee Id to reset your password.
                                    </div>
                                </div>

                                <div class="fv-row mb-8">
                                    <input type="text" placeholder="CNIC" name="employee_cnic" required="required" autocomplete="off" class="form-control bg-transparent cnic" data-inputmask="'mask': '99999-9999999-9'"/>
                                    <input type="text" placeholder="Employee ID" name="employee_id" required="required" autocomplete="off" class="form-control bg-transparent mt-2 cnic" data-inputmask="'mask': '9999999'"/>
                                </div>

                                {{--begin::Actions--}}
                                <div class="d-flex flex-wrap justify-content-center pb-lg-0">
                                    <button type="submit" id="password_reset_form_submit" class="btn btn-primary me-4">

                                        <span class="indicator-label">Send</span>

                                        <span class="indicator-progress">Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>

                                    <a href="{{ url('/sap-login') }}" class="btn btn-light">Cancel</a>
                                </div>
                                {{--end::Actions--}}
                            </form>
                            {{--end::Form--}}
                        </div>

                    </div>
                    {{--end::Content--}}
                </div>
                {{--end::Wrapper--}}
            </div>
            {{--end::Body--}}
        </div>
        {{--end::Authentication - Password reset--}}
    </div>
    {{--end::Root--}}

    {{--begin::Javascript--}}
    <script>
        var hostUrl = "/metronic8/demo1/assets/";        
    </script>

    {{--begin::Global Javascript Bundle(mandatory for all pages)--}}
    {{-- <script src="{{ url('assets/plugins/global/global/plugins.bundle.js') }}"></script> --}}
    {{-- <script src="{{ url('assets/js/scripts.bundle.js') }}"></script> --}}
    <script>

        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        $(document).ready(function () {
            $('.cnic').inputmask();
        });

        $("#password_reset_form").submit(function (e) {

            $('#password_reset_form_submit').prop('disabled', true);
            $('#password_reset_form_submit').css('cursor', 'not-allowed');
            $("#password_reset_form_submit").attr('data-kt-indicator', 'on');
            e.preventDefault();
            $.ajax({
                url: '/reset-password',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function (data) {
                    response = JSON.parse(JSON.stringify(data));
                    console.log(response);
                    if (response.status == "TRUE") {

                        Toast.fire({
                            icon: 'success',
                            title: response.msg,
                            timer: 5000,
                        })
                        setTimeout(() => {
                            window.location.href = "{{ url('/sap-login') }}"; 
                        }, 5000);  

                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: response.msg,
                            timer: 6000,
                        })
                        $('#password_reset_form_submit').prop('disabled', false);
                        $('#password_reset_form_submit').css('cursor', 'pointer');
                        $("#password_reset_form_submit").removeAttr('data-kt-indicator');

                    }
                },
                error: function (jqXHR, textStatus) {
                    $('#password_reset_form_submit').prop('disabled', false);
                    $('#password_reset_form_submit').css('cursor', 'pointer');
                    $("#password_reset_form_submit").removeAttr('data-kt-indicator');
                    $('#gl_div').html('');
                    var errorStatus = jqXHR.status;

                    if (errorStatus == 0) {
                        Toast.fire({
                            icon: 'warning',
                            title: 'Internet Connection Problem',
                            timer: 6000,
                        });
                    } else {
                        if (errorStatus == 419) {
                            location.reload();
                        } else {
                            Toast.fire({
                                icon: 'warning',
                                title: 'Try Again. Error Code ' + errorStatus,
                                timer: 3000,
                            })
                        }
                    }
                }
            });
            });

    </script>

</body>
{{--end::Body--}}

</html>