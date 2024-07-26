<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<meta name="csrf-token" content="{{ csrf_token() }}" />
<head>
	<title>IIL - HCM</title>
	<meta charset="utf-8" />
	<meta name="description" content="Ismail Industries Limited - HCM" />
	<meta name="keywords" content="IIL - HCM, IIL - HRM, HCM, HRM, Human Capital Management, Human Resource Management." />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:locale" content="en_US" />
	<meta property="og:type" content="article" />
	<meta property="og:title" content="Intelligent and Efficient HCM System" />
	<meta property="og:url" content="{{ url('/') }}" />
	<meta property="og:site_name" content="IIL - HCM" />
	<link rel="canonical" href="{{ url('/') }}" />
	<link rel="shortcut icon" href="{{ url('portal_assets/images/fav-icons.png') }}" />
	{{--begin::Fonts--}}
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
	{{--end::Fonts--}}
	{{--begin::Global Stylesheets Bundle(used by all pages)--}}
	<link href="{{ url('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
	<link href="{{ url('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
	{{--end::Global Stylesheets Bundle--}}
	<script src="https://code.jquery.com/jquery-3.6.1.js"></script>
	<script src="https://unpkg.com/sweetalert2@11"></script>

	<style>
		.vertical-center {
			margin: 0;
			position: fixed;
			height: 100vh;
			width: 100%;
			padding-top:20%;
			display:block;
			text-align:center;

		}
	</style>

</head>
	{{--end::Head--}}
	{{--begin::Body--}}
	<body id="kt_body" class="app-blank app-blank bgi-size-cover bgi-position-center bgi-no-repeat">

		@php $setting =  CustomHelper::hcm_default_settings() @endphp

		<div class="page-loader flex-column vertical-center">
			<div class="spinner-border text-primary" role="status"></div>
			<div class="text-muted fs-6 fw-semibold mt-5">Loading...</div>
		</div>
	
		<div class="d-flex flex-column flex-root" id="kt_app_root">
			<style>body { background-image: url('/assets/media/auth/bg10.jpg'); }</style>
			<div class="d-flex flex-column flex-lg-row flex-column-fluid">
				
				<div class="d-flex flex-lg-row-fluid">
				
					<div class="d-flex flex-column flex-center pb-0 pb-lg-10 p-10 w-100">
						
						<img class="theme-light-show mx-auto mw-100 w-150px w-lg-300px mb-10 mb-lg-20" src="{{ URL('/portal_assets/images/main-login.png') }}" alt="" />
						<h1 class="text-gray-800 fs-2qx fw-bold text-center mb-7">The Ultimate HR Management Solution</h1>
						
						<div class="text-gray-600 fs-base text-center fw-semibold">At IIL, we have a reliable, secure and adaptable HR management built from the ground up. 
						    <br />We are determined to help our employees to give their best efforts every day to achieve the goals of their job.
					    </div>
						
					</div>
					
				</div>

				<div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
					
					<div class="bg-body d-flex flex-center rounded-4 w-md-600px p-10">
						
						<div class="w-md-400px">
						
							<form class="form w-100"  id="loginForm" autocomplete="off" >
								
								
								
                                <div class="text-center mb-11">
									<img alt="Logo" src="{{ url('portal_assets/images/default-img.png') }}" class="theme-light-show" style="width: 40%;" />
																	
								</div>

								<div class="text-center mb-11">
									
									<h1 class="text-dark fw-bolder mb-3">Ismail Industries Limited - HCM</h1>
									<div class="text-gray-500 fw-semibold fs-6">To log in to IIL-HCM, enter your employee ID and password below.</div>
									
								</div>
								
								<div class="fv-row mb-8 ">
								
									<input type="number" name="employee_id" class="form-control bg-transparent" placeholder="Employee ID" min="0"  required/>
									
								</div>
								
								<div class="fv-row mb-3">
									
									<input  type="password" name="employee_password" placeholder="Password"  class="form-control bg-transparent" required/>
							
								</div>
								
								<div class="d-flex flex-stack flex-wrap gap-3 fs-base fw-semibold mb-8">
									<div></div>
									
								</div>
								
								<div class="d-grid mb-10">

									<div id="gl_div" >
									</div>

									<button type="submit" id="loginForm_submit" class="btn btn-primary">
										
										<span class="indicator-label">LOG IN</span>
										
										<span class="indicator-progress">Please wait... 
										<span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
										
									</button>
								</div>

								{{-- <div class="text-center mb-11">
									<img alt="Logo" src="{{ url('portal_assets/images/main-login2.png') }}" class="theme-light-show" style="width: 40%;" />
																	
								</div> --}}

								<div class="d-grid mb-10">
								    {{-- <span class="text-center">© {{ date('Y') }} MegaHCM. All Rights Reserved</span> --}}
									<a href="{{ url('/forgot-password') }}" class="text-center">Forgot Password</a>
								</div>
								
						
							</form>
							
						</div>
					
					</div>
					
				</div>
				
			</div>
			
		</div>
		<script src="{{url('assets/js/authentication/signin.js')}}"></script>
		<script>
		 var url = "{{ url('/') }}";
		 var sap_url = "{{ url('/sap-login') }}";
		</script>

		{{-- Get Current Location --}}
		{{-- <script src="{{url('assets/js/geo-location.js')}}?js_v=2"></script> --}}
	</body>
	{{--end::Body--}}
</html>