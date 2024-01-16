
    </div>
    {{--end:::Main--}}
    </div>
    {{--end::Wrapper--}}
    </div>
    {{--end::Page--}}
    </div>
    {{--end::App--}}
   <script>

      var user_id = "{{session('user_session');}}";
      var base_url = "{{url('/')}}";
      var gl;
      var app_sc = "{{env('APP_SC')}}";
   
   </script>
   
    {{-- <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.0.0/firebase-database.js"></script> --}}

    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-database.js"></script>

    {{-- <script src="{{ url('assets/js/firebase-notification.js') }}"></script> --}}
    <script type="text/javascript" src="{{ url('assets/js/general_script.js') }}?js_v=2"></script>

    <script type="text/javascript" src="{{ url('assets/plugins/global/plugins.bundle.js') }}"></script>

    <script type="text/javascript" src="{{ url('assets/js/scripts.bundle.js') }}"></script>

    <script type="text/javascript" src="{{ url('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" crossorigin="anonymous"></script>
   {{-- Tempus Dominus JavaScript --}}
    <script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.2.10/dist/js/tempus-dominus.min.js" crossorigin="anonymous"></script>

    {{-- proxy user js --}}
    <script type="text/javascript" src="{{ url('assets/js/proxy-user.js') }}?js_v=2"></script>

    {{-- fetch api js --}}
    <script type="text/javascript" src="{{ url('assets/js/fetch-api.js') }}?js_v=2"></script>

    {{-- Get Current Location --}}
    <script type="text/javascript" src="{{url('assets/js/geo-location.js')}}?js_v=2"></script>  

    <script>
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3000,
          timerProgressBar: true,
        });

        setTimeout(()=>{
          $('.page-loader').fadeOut();
        });

        
    </script>
    
    
    {{-- Payslip Js --}}
    @stack('js-link')
    {{--end::Custom Javascript--}}
    {{--end::Javascript--}}
	</body>
	{{--end::Body--}}

</html>