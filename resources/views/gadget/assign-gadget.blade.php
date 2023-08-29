@extends('layout.main')






@section('main-section')
<div class="d-flex flex-column flex-column-fluid">
    {{--begin::Toolbar--}}
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        {{--begin::Toolbar container--}}
        <div class="app-container col-12 d-flex flex-stack">
            {{--begin::Page title--}}
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                {{--begin::Title--}}
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Add New Gadget</h1>
                
            </div>
            <div class="gap-2 gap-lg-3">
                <a href="{{url()->previous()}}" class="btn btn-sm fw-bold btn-primary" >Go Back</a>
            </div>
        </div>
    </div>
    {{--end::Toolbar--}}
    {{--begin::Content--}}
    <div class="app-content flex-column-fluid">
        {{--begin::Content container--}}
        <div class="app-container">
            {{--begin::Tables Widget 11--}}
            <div class="card mb-5 mb-xl-8">
                {{--begin::Body--}}
                <div class="card-body py-3 mb-12">
                    {{--begin::Table container--}}
                    <form  id="add_new_gadget_form" class="form" autocomplete="off">
                        @csrf 

                        <div class="row g-9 mb-12 mt-3">

                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Gadget Type</label>
                                <select class="form-select " data-control="select2" data-hide-search="true" data-placeholder="Select Gadget Type" required name="gadget_type">
                                    <option value="">Select Gadget</option>
                                    <option value="Computer">Computer</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="Mobile">Mobile</option>                                        
                                </select>
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Brand Name</label>
                                <input type="text" class="form-control " name="gadget_brand"
                                placeholder="Enter Brand Name" required="required"/>
                            </div>
                           
                        </div>

                        <div class="row g-9 mb-8">
                             
                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Model No</label>
                                <input type="text" class="form-control "  name="gadget_model_no"
                                placeholder="Enter Model Number" required="required"/>
                            </div>

                            <div class="col-md-6 fv-row">
                               <label class="required fs-6 fw-semibold mb-2">Serial No</label>
                               <input type="text" class="form-control "name="gadget_serial_no"
                               placeholder="Enter Serial Number" required="required"/>
                           </div>
                          
                           
                        </div>

                        <div class="row g-9 mb-8">

                            <div class="col-md-6 fv-row">
                                <label class="required fs-6 fw-semibold mb-2">Configuration</label>
                                <input type="text" class="form-control " name="gadget_configuration" placeholder="Enter Configuration" required="required">
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Mouse</label>
                                <input type="text" class="form-control " name="gadget_mouse"
                                placeholder="Enter Mouse" />
                            </div>
                          
                        </div>

                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">Keyboard</label>
                                <input type="text" class="form-control "  name="gadget_keyboard"
                                placeholder="Enter KeyBoard" >
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">LCD</label>
                                <input type="text" class="form-control " name="gadget_lcd"
                                placeholder="Enter LCD" />
                            </div>
                        </div>

                        <div class="row g-9 mb-8">
                            
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2">HeadPhone</label>
                                <input type="text" class="form-control "name="gadget_headphone"
                                placeholder="Enter HeadPhone" />
                            </div>

                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2 required">MAC ADDRESS WIFI</label>
                                <input type="text" class="form-control " name="gadget_mac_wifi"
                                placeholder="Enter MAC ADDRESS WIFI" required/>
                            </div>
                        </div>

                        <div class="row g-9 mb-8">
                        
                            <div class="col-md-6 fv-row">
                                <label class="fs-6 fw-semibold mb-2 required">MAC ADDRESS LAN</label>
                                <input type="text" class="form-control " name="gadget_mac_lan"
                                placeholder="Enter MAC ADDRESS LAN" required />
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" id="submit_btn" class="btn btn-primary">
                                <span class="indicator-label">Add Gadget</span>
                                <span class="indicator-progress">Please wait... 
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            
        </div>
    </div>
</div>

@endsection



@push('js-link')

<script src="{{url('assets/js/gadget/assign-gadget.js')}}"></script>
    
@endpush