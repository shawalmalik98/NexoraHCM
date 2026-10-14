<!DOCTYPE html>
<html lang="en">

    <head>
    <title>Nexora HCM</title>
    <meta charset="utf-8" />
    <meta name="description" content="Nexora HCM" />
    <meta name="keywords" content="Nexora HCM, IIL - HRM, HCM, HRM, Human Capital Management, Human Resource Management." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="Intelligent and Efficient HCM System" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:site_name" content="Nexora HCM" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="canonical" href="{{ url('/') }}" />
    <link rel="icon" href="{{ url('portal_assets/images/hcm-img.png') }}"/>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="{{ url('assets/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ url('assets/css/manez-theme.css') }}" rel="stylesheet" type="text/css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.2.10/dist/css/tempus-dominus.min.css" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.1.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    </head>

<body id="kt_body" class="app-blank app-blank">
    {{--begin::Theme mode setup on page load--}}
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        {{--begin::Wrapper--}}
        <div class="d-flex flex-column flex-column-fluid">
            <div class="scroll-y flex-column-fluid px-10 py-10" data-kt-scroll="true" data-kt-scroll-activate="true" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_header_nav" data-kt-scroll-offset="5px" data-kt-scroll-save-state="true" style="background-color:#D5D9E2; --kt-scrollbar-color: #d9d0cc; --kt-scrollbar-hover-color: #d9d0cc">
            
                <style>html,body { padding:0; margin:0; font-family: Inter, Helvetica, "sans-serif"; } a:hover { color: #009ef7; }</style>
                <div id="#kt_app_body_content" style="background-color:#D5D9E2; font-family:Arial,Helvetica,sans-serif; line-height: 1.5; min-height: 100%; font-weight: normal; font-size: 15px; color: #2F3044; margin:0; padding:0; width:100%;">
                    <div style="background-color:#ffffff; padding: 45px 0 34px 0; border-radius: 24px; margin:40px auto; max-width: 600px;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" height="auto" style="border-collapse:collapse">
                            <tbody>
                                <tr>
                                    <td align="center" valign="center" style="text-align:center; padding-bottom: 10px">
                                        {{--begin:Email content--}}
                                        <div style="text-align:center; margin:0 15px 34px 15px">
                                            {{--begin:Logo--}}
                                            <div style="margin-bottom: 10px">
                                                {{-- <a href="https://keenthemes.com/" rel="noopener" target="_blank"> --}}
                                                    {{-- <img alt="Logo" src="{{$companySetting['company_logo']}}" style="height: 35px" /> --}}
                                                {{-- </a> --}}
                                            </div>
                                            {{--end:Logo--}}
                                            {{--begin:Media--}}
                                            <div style="margin-bottom: 15px">
                                                <img alt="Nexora HCM" src="{{ url('/portal_assets/images/hcm-logo.png') }}" style="width: 62%;" draggable="false"/>
                                            </div>
                                            {{--end:Media--}}
                                            {{--begin:Text--}}
                                            <div style="font-size: 14px; font-weight: 500; margin-bottom: 27px; font-family:Arial,Helvetica,sans-serif;">
                                                <p style="margin-bottom:9px; color:#181C32; font-size: 22px; font-weight:700">Welcome to Nexora HCM !</p>
                                                <p style="margin-bottom:2px; color:#7E8299">Welcome to the team! We are beyond thrilled to have you at our office.<br> We know how much of an asset you will be within our company,<br> and we can’t wait to see all that you accomplish.</p>
                                            </div>
                                            {{--end:Text--}}
                                            {{--begin:Action--}}
                                            
                                            <a href='{{ url('procced-to-dashboard') }}' style="background-color:#50cd89; border-radius:6px;display:inline-block; padding:11px 19px; color: #FFFFFF; font-size: 14px; font-weight:500;">Procced to Dashboard</a>
                                            {{--begin:Action--}}
                                        </div>
                                        {{--end:Email content--}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{--end::Email template--}}
            </div>
            {{--end::Body--}}
        </div>
    
    </div>

    <script type="text/javascript" src="{{ url('assets/plugins/global/plugins.bundle.js') }}"></script>
    {{-- <script src="../../assets/plugins/global/plugins.bundle.js"></script> --}}

    <script type="text/javascript" src="{{ url('assets/js/scripts.bundle.js') }}"></script>

</body>

</html>
    
        
  