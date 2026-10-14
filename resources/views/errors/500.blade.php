<html>
<head>
	<title>Nexora HCM</title>
	<link rel="icon" href="{{ url('portal_assets/images/hcm-img.png') }}"/>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	
</head>
<style type="text/css">
.sec-404 {
    background: linear-gradient(357deg, #0072FF, #00C6FF);
    padding: 100px 0px;
    height: 100vh;
}
    .sec-404 .row{
    	background: #fff;
    }
    .sec-404 .row {
    background: #fff;
    padding: 50px 50px;
    border-radius: 10px;
    align-items: center;
}
.ops-text img {
    max-width: 375px;
    width: 100%;
    margin-bottom: 40px;
}
.ops-image img {
    max-width: 340px;
    width: 100%;
}
.ops-text h4 {
    color: #707070;
    font-weight: 700;
}
.ops-text p {
    font-weight: 500;
}
.ops-image {
    text-align: center;
}
.ops-text a {
    padding: 10px 20px;
    background: linear-gradient(357deg, #2C5364, #0F2027);
    color: #fff;
    text-decoration: none;
    border-radius: 7px;
    display: inline-block;
    margin-top: 10px;
}
@media screen and (max-width:767px){
	.ops-text{
		text-align: center;
	}
}
</style>
<body>
	<section class="sec-404">
		<div class="container">
			<div class="row">
				<div class="col-lg-6 ops-text">
					<img src="{{ asset('portal_assets/images/opps.png') }}">
					<h4>500 - INTERNAL SERVER ERROR</h4>
					<p>Minor error occured. Please try again after sometime.</p>
					<a href="{{ url('/') }}">Go To Homepage</a>
				</div>
				<div class="col-lg-6 ops-image">
					<img src="{{ asset('portal_assets/images/robot.png') }}">
				</div>
			</div>
		</div>
	</section>
</body>
</html>