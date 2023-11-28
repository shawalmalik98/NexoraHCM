<!DOCTYPE html>
<html lang="en">

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
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="canonical" href="{{ url('/') }}" />
    <link rel="icon" href="{{ url('portal_assets/images/fav-icons.png') }}"/>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.2.10/dist/css/tempus-dominus.min.css" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    </head>

<body id="kt_body" class="app-blank app-blank">
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-column-fluid">
            <div class="scroll-y flex-column-fluid px-10 py-10" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_header_nav" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true" style="background-color:#D5D9E2; --kt-scrollbar-color: #d9d0cc; --kt-scrollbar-hover-color: #d9d0cc">
            
                <style>html,body { padding:0; margin:0; font-family: Inter, Helvetica, "sans-serif"; } a:hover { color: #009ef7; }</style>
                <div id="#kt_app_body_content" style="background-color:#D5D9E2; font-family:Arial,Helvetica,sans-serif; line-height: 1.5; min-height: 100%; font-weight: normal; font-size: 15px; color: #2F3044; margin:0; padding:0; width:100%;">
                    <div style="background-color:#ffffff; padding: 45px 0 34px 0; border-radius: 24px; margin:40px auto; max-width: 600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" height="auto" style="border-collapse:collapse">
                            <tbody>
                                <tr>
                                    <td align="center" valign="center" style="text-align:center; padding-bottom: 10px">
                                        <div style="text-align:center; margin:0 15px 34px 15px">
                                            <div style="margin-bottom: 10px">
                                            </div>
                                            <div style="margin-bottom: 15px">
                                                <img alt="Logo" src="{{ url('/portal_assets/images/default-img.png') }}" style="width: 30%;" draggable="false"/>
                                            </div>
                                            <div style="font-size: 14px; font-weight: 500; margin-bottom: 27px; font-family:Arial,Helvetica,sans-serif;">
                                                <p style="margin-bottom:9px; color:#181C32; font-size: 22px; font-weight:700"> Verification Code</p>
                                                <p style="margin-bottom:2px; color:#7E8299">This tab contains confidential data that can't be share without profile identification.<br> To view payroll management please enter security code.</p>
                                            </div>

                                            <form id="kt_sing_in_two_factor_form"  novalidate="novalidate">
                                                <div class="row pb-10" >
                                                    <div class="col-1" >
                                                    </div>
                                                    <div class="col-10" >
                                                        <div class="text-center align-items-center">
                                                            <div class="d-flex flex-wrap flex-stack">                      
                                                                <input type="text" name="verification_code[]" id="code_1"  data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2 verification_code" value="" autocomplete="off" data-id="1" />
                                                                <input type="text" name="verification_code[]" id="code_2" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2 verification_code" value="" autocomplete="off" data-id="2" />
                                                                <input type="text" name="verification_code[]" id="code_3" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2 verification_code" value="" autocomplete="off" data-id="3" /> 
                                                                <input type="text" name="verification_code[]" id="code_4" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2 verification_code" value="" autocomplete="off" data-id="4" /> 
                                                                <input type="text" name="verification_code[]" id="code_5" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2 verification_code" value="" autocomplete="off" data-id="5" />                     
                                                                <input type="text" name="verification_code[]" id="code_6" data-inputmask="'mask': '9', 'placeholder': ''" maxlength="1" class="form-control bg-transparent h-60px w-60px fs-2qx text-center mx-1 my-2 verification_code" value="" autocomplete="off" data-id="6" /> 
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-1" >
                                                    </div>
                                                </div>
                                                <button type="reset" class="btn btn-light me-3" id="cancel-btn" >Cancel</button>
                                                <button type="submit" id="kt_sing_in_two_steps_submit" class="btn btn-primary">
                                                    <span class="indicator-label">Submit</span>
                                                    <span class="indicator-progress">Please wait... 
                                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                                                </button>
                                                
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    
    </div>

    <script type="text/javascript" src="{{ url('assets/plugins/global/plugins.bundle.js') }}"></script>

    <script type="text/javascript" src="{{ url('assets/js/scripts.bundle.js') }}"></script>
    <script type="text/javascript" src="{{ url('assets/js/custom/two-steps.js') }}"></script>
    <script type="text/javascript" src="{{ url('assets/js/fetch-api.js') }}"></script>

    <script>
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });

        $('.verification_code').on('keydown', function (e) {
            if(e.keyCode  == 8){
                var id = $(this).data('id');
                id = "code_"+(id-1);
                $("#"+id).val('');
                $("#"+id).focus();
            }
        });

        var base_url = "{{url('/')}}";
        $('#kt_sing_in_two_factor_form').submit(function(e){
            e.preventDefault();
            send_server_request( 'POST' ,'payroll-identification' , this ,'kt_sing_in_two_steps_submit', 'Yes' , '' ,3000);
        });
        $('input[name="verification_code[]"]').keyup(function(){
            var verification_code = $("input[name='verification_code[]']").map(function(){return $(this).val();}).get();
            if(verification_code.includes("") == false){
                $('#kt_sing_in_two_factor_form').submit();
            }
        })

        $('#cancel-btn').click(function(){
            window.location.href = '/';
        });
    </script>

</body>

</html>