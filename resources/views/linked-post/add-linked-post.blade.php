@extends('layout.main')

@section('main-section')

<div class="d-flex flex-column flex-column-fluid">
							
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        
        <div class="app-container col-12 d-flex flex-stack">
            
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                
                <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">Add Linked Post</h1>   
            </div>
            
        </div>
       
    </div>
    
    <div class="app-content flex-column-fluid">
        
        <div class="app-container">
           
            <div class="card mb-5 mb-xl-8">
               
                <div class="card-body py-3">
                    
                    <form id="linked_post_form" class="form">
                        @csrf
                        <input type="hidden" name="setting_id" value="{{$setting[0]['setting_id'] ?? ''}}" />
                        <div class="row mb-8 mt-5">
                              <div class="col-md-12 fv-row">
                                  <label class="required fs-6 fw-semibold mb-4">Linked Post 1</label>
                                  <div class="position-relative d-flex align-items-center">
                                      <textarea class="form-control " rows="3" name="linked_post_1"  placeholder="Linked Post 1" required>{!!$setting[0]['linked_post1']!!}</textarea>
                                  </div>
                              </div>
                          </div>
                          <div class="row g-9 mb-8" >
                            <div class="col-md-12 fv-row">
                              <label class="required fs-6 fw-semibold mb-4">Linked Post 2</label>
                                <div class="position-relative d-flex align-items-center">
                                  <textarea class="form-control " rows="3" name="linked_post_2"  placeholder="Linked Post 2" required>{!!$setting[0]['linked_post2']!!}</textarea>
                                   
                                </div>
                            </div>
                        </div>
                       
                          <div class="text-center">
                          
                              <button type="submit" id="linked_post_btn" class="btn btn-primary">
                                  <span class="indicator-label">Submit</span>
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

<script src="{{url('assets/js/linked-post/add-linked-post.js')}}"></script>
{{-- <script>
    $('#linked_post_form').submit(function (e) {
        $('#linked_post_btn').prop('disabled', true);
        $('#linked_post_btn').attr('data-kt-indicator', 'on');
        $('#linked_post_btn').css('cursor', 'not-allowed');
        e.preventDefault();
        $.ajax({
            url: "{{ url('/linked-in-post/add-linked-post') }}",
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if (data.status == 'TRUE') {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg,
                        timer: 3000,
                    })
                    setTimeout(() => {
                            window.location.href = "{{ url('/') }}";
                        }, 3000);
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 5000,
                    })
                    $('#linked_post_btn').prop('disabled', false);
                    $("#linked_post_btn").removeAttr('data-kt-indicator');
                    $('#linked_post_btn').css('cursor', 'pointer');

                }
            },
            error: function (jqXHR, textStatus) {
                $('#linked_post_btn').prop('disabled', false);
                $('#linked_post_btn').removeAttr('data-kt-indicator');
                $('#linked_post_btn').css('cursor', 'pointer');
                var errorStatus = jqXHR.status;
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
                } else {
                    if(errorStatus == 419){
                        location.reload();
                    }else{
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

</script> --}}
@endpush
