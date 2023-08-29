<!DOCTYPE html>
<html lang="en">
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<head>
		<title>IIL - HCM</title>
		<meta charset="utf-8" />
		<meta name="description" content="Streamline your HR management processes with SourceCode HCM, the fastest-growing HR software solution available. Our comprehensive On-Cloud Human Capital Management system is designed to meet the needs of modern businesses and strictly adheres to local and global best HR practices. Our diverse range of customers from various industries are benefiting from SourceCode HCM, making it the preferred choice for businesses looking to optimize their HR management processes." />
		<meta name="keywords" content="SourceCode - HCM, SourceCode - HRM, SourceCode, HCM, HRM, Human Capital Management, Human Resource Management." />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="en_US" />
		<meta property="og:type" content="article" />
		<meta property="og:title" content="The Ultimate HR Management Solution" />
		<meta property="og:url" content="{{ url('/') }}" />
		<meta property="og:site_name" content="SourceCode - HCM" />
		<meta name="csrf-token" content="{{ csrf_token() }}" />
		<link rel="canonical" href="{{ url('/') }}" />
		<link rel="icon" href="{{ url('portal_assets/images/fav-icons.png') }}"/>
	
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
		<link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ url('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ url('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.2.10/dist/css/tempus-dominus.min.css" crossorigin="anonymous">
	
		<script src="https://code.jquery.com/jquery-3.6.1.js"></script>

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/min/dropzone.min.css">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.0/dropzone.js"></script>
		<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

		@stack('cs-link')

		<style>

			input[type=date]::-ms-clear {
				display: none;
			}

			.autocomplete {
				position: relative;
				display: block;
			}

			.autocomplete-items {
				position: absolute;
				border: 1px solid #D4D4D4;
				border-bottom: none;
				border-top: none;
				z-index: 99;
				overflow: scroll;
				max-height: 250px;
				top: 100%;
				left: 0;
				right: 0;
			}

			.autocomplete-items div {
				padding: 10px;
				cursor: pointer;
				background-color: #fff;
				border-bottom: 1px solid #D4D4D4;
			}
			
			.autocomplete-items div:hover {
				background-color: #E9E9E9;
			}
			
			.autocomplete-active {
				background-color: DodgerBlue !important;
				color: #FFFFFF;
			}

			/* Popover */
			.popover-item-box {
				visibility: hidden;
				position: absolute;
				z-index: 1;
				align-items: center;
				background: #efefef;
				border-radius: 15px;
				padding: 20px;
				padding-bottom: 25px;
				margin-left: 10px;
				margin-right: 10px;
				margin-top: 15px;
				/*box-shadow: 5px 5px 15px #d1d9e6, -5px -5px 15px #ffffff;*/
				width: 365px;
				opacity: 0;
				transition: visibility 0s linear 600ms, opacity 600ms;
			}
			.tdf2 p {
				color: white;
			}
			.tdf2 h3 {
				color: #646464;
			}
			.tdf2 h5 {
				color: #646464;
			}
			.popover-item-box:hover{transition: all 0.5s ease-in-out; visibility: visible;}
			/* a.employee-hover:hover ~ .popover-item-box{
				visibility: visible; 
				transition: all 0.5s ease-in-out;
			} */
			.tdf1 img {
				width: 60px;
			}
			.qsae {
				display: flex;
			}
			.tdf2 {
				margin-left: 19px;
			}
			.wqas p {
				color: white;
				margin-left: 70px;
			}
			
			/* Popover Hover end */
			.object-fit{
				object-fit: cover;
			}
			.popup-visible .table-responsive{overflow:visible;}
			.popup-visible {overflow:visible;}

			.vertical-center {
				margin: 0;
				position: fixed;
				height: 100vh;
				width: 100%;
				padding-top:20%;
				display:block;
				text-align:center;

			}

			.notification-loader {
				margin: 0;
				position: fixed;
				height: 80vh;
				width: 80%;
				padding-top:0%;
				display:block;
				text-align:center;

			}

			.pulse-ring{
				width:30px;
				height:30px;
			}

			
    	</style>
		 
	</head>
	{{--end::Head--}}
	{{--begin::Body--}}
	<body id="kt_app_body" data-kt-app-layout="dark-sidebar" data-kt-app-header-fixed="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-hoverable="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" data-kt-app-toolbar-enabled="true" class="app-default" @if(Request::path() =="attendance" || Request::path() =="interviews" || Request::path() =="payroll" || Request::path() == "reports/employee-report" || Request::path() == "reports/attendance-report" || Request::path() == "employees"|| Request::path() == "attendance-requests" || Request::path() == "wfh-requests" || Request::path() == "employees-leaves" || Request::path() == "my-expense" || Request::path() == "employee-expense" || Request::path() == "daily-wages-payroll") data-kt-app-sidebar-minimize="on" @endif >
		
		<div class="page-loader flex-column vertical-center">
			<div class="spinner-border text-primary" role="status"></div>
			<div class="text-muted fs-6 fw-semibold mt-5">Loading...</div>
		</div>
	
		{{--begin::Theme mode setup on page load--}}
		<script>
			var defaultThemeMode = "light"; var themeMode; if ( document.documentElement ) { if ( document.documentElement.hasAttribute("data-theme-mode")) { themeMode = document.documentElement.getAttribute("data-theme-mode"); } else { if ( localStorage.getItem("data-theme") !== null ) { themeMode = localStorage.getItem("data-theme"); } else { themeMode = defaultThemeMode; } } if (themeMode === "system") { themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light"; } document.documentElement.setAttribute("data-theme", themeMode); }
		</script>

		<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
			{{--begin::Page--}}
			<div class="app-page flex-column flex-column-fluid" id="kt_app_page">
				{{--begin::Header--}}
					@include('layout.topbar')

				{{--end::Header--}}
				{{--begin::Wrapper--}}
				<div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">