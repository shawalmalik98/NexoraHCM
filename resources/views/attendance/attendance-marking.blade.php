<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mark Attendance</title>
		
	<meta name="description" content="More than five years back a group of few members started SourceCode Aiming to provide website solutions and solutions to businesses through daily needed performing software like HRM/ERP to ease management businesses.">


	<meta itemprop="name" content="Portal || SourceCode">
	<meta itemprop="description" content="More than five years back a group of few members started SourceCode Aiming to provide website solutions and solutions to businesses through daily needed performing software like HRM/ERP to ease management businesses.">
	{{-- <meta itemprop="image" content="http://localhostpublic/banner-sc.png"> --}}


	<meta property="og:url" content="https://sourcecodeemployees.com">
	<meta property="og:type" content="website">
	<meta property="og:title" content="Portal || SourceCode">
	<meta property="og:description" content="More than five years back a group of few members started SourceCode Aiming to provide website solutions and solutions to businesses through daily needed performing software like HRM/ERP to ease management businesses.">
	{{-- <meta property="og:image" content="http://localhostpublic/banner-sc.png"> --}}


	<meta name="twitter:card" content="summary_large_image">
	<meta name="twitter:title" content="Portal || SourceCode">
	<meta name="twitter:description" content="More than five years back a group of few members started SourceCode Aiming to provide website solutions and solutions to businesses through daily needed performing software like HRM/ERP to ease management businesses.">
	{{-- <meta name="twitter:image" content="http://localhostpublic/banner-sc.png">
	<link rel="canonical" href="http://localhost"> --}}
	
	<link rel="icon" href="{{ url('portal_assets/images/fav-icons.png') }}" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="{{ URL('portal_assets/attendance/style.css') }}">
	
	<style>
	
		img {
            -drag: none;
            user-select: none;
            -moz-user-select: none;
            -webkit-user-drag: none;
            -webkit-user-select: none;
            -ms-user-select: none;
		}

	
	</style>
</head>
<body>
<div class="sc_section1">
	<div class="sc_shapes">
		<img src="{{ URL('portal_assets/attendance/images/shape1.png') }}" class="shape sc_shape1" alt="">
		<img src="{{ URL('portal_assets/attendance/images/shape2.png') }}" class="shape sc_shape2" alt="">
	</div>
	<div class="container">
		<div class="row sc_s1_row">
			<div class="col-lg-6 offset-lg-3">
				<div class="sec1_box">
					<div class="sec1_logo text-center">
						<img src="{{ URL('portal_assets/attendance/images/logo.png') }}" alt="">
					</div>
					<div class="sec1_form text-center text-white">
						<h1>Welcome</h1>
						<p>Enter your credentials to mark your attendance.</p>
						<form class="mt-4" autocomplete="off" id="attendance-marking-form">
							@csrf
							<div class="mb-lg-4 mb-md-3 mb-2">
								<label class="d-block text-start mb-2">Enter your ID</label>
								<input type="number" class="form-control" name="employee_id" required="required" placeholder="Enter Your ID. for e.g 0876" autocomplete="none">
							</div>
							<div class="mb-lg-4 mb-md-3 mb-2">
								<label class="d-block text-start mb-2">Enter your password</label>
								<input type="password" class="form-control" name="employee_password" required="required" placeholder="Enter Your Password" autocomplete="none">
							</div>
							<div class="mb-lg-4 mb-md-3 mb-2">
								<div id="gl_div" >
								</div>
								<div class="row">
									<div class="col-lg-6 col-md-6 col-6">
										<div class="d-grid">
											<button type="submit" class="checkin_btn btn btn-block">Check In</button>
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-6">
										<div class="d-grid">
											<button type="submit" class="checkout_btn btn btn-block">Check Out</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade"  data-bs-dismiss="modal" aria-label="Close" id="checkin_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center checkin_modal_box px-3 py-4">
        <img src="{{ URL('portal_assets/attendance/images/checkin-icon.png') }}" alt="">
        <h3>Thank you</h3>
        <p>Checkin Successfully</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade"  data-bs-dismiss="modal" aria-label="Close" id="checkout_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center checkout_modal_box px-3 py-4">
        <img src="{{ URL('portal_assets/attendance/images/checkout-icon.png') }}" alt="">
        <h3>Thank you</h3>
        <p>Checkout Successfully</p>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" data-bs-dismiss="modal" aria-label="Close" id="close_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center close_modal_box px-3 py-4">
        <img src="{{ URL('portal_assets/attendance/images/error.png') }}" alt="">
        <h3>Error</h3>
        <p id="error_message"></p>
      </div>
    </div>
  </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="{{url('assets/js/attendance/attendance-marking.js')}}"></script>
<script>
	var base_url = "{{url('/')}}";
</script>

{{-- Get Current Location --}}
<script src="{{url('assets/js/geo-location.js')}}?js_v=2"></script>

</body>
</html>