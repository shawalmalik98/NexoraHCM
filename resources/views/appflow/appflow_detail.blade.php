@extends('layout.main')

@push('css-link')

@endpush

@section('main-section')
<style>
    .has-time{
        border: 1px solid greenyellow;
    }
</style>
<div class="d-flex flex-column flex-column-fluid">

    <form id="appflow_detail_form">
							
        <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
            
            <div class="app-container col-12 d-flex flex-stack">
                
                <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                    
                    <h1 class="page-heading d-flex text-dark fw-bold fs-3 flex-column justify-content-center my-0">AppFlow Detail</h1>
                </div>  
                <div class="d-flex align-items-center gap-2 gap-lg-3">
                    
                    <div class="m-0">
                        <button type="submit" name="submit" class="btn btn-sm btn-primary" id="appflow_detail_form_btn">Save Details</button>
                    </div>
            
                </div>          
            </div>
        
        </div>
        
        <div class="app-content flex-column-fluid">
            @if(Session::has('error'))
                <script>
                    swal.fire("Error", "{{Session::get('error') }}", "error");
                </script>
            @endif
            <div class="app-container">
                <div class="card mb-5 mb-xl-8">
                    <div class="card-body py-3">
                        {{-- <form class="form" autocomplete="off"> --}}

                            @if(!empty($login_user))
                                <div class="row mt-5 mb-5">
                                    <div class="col-md-6 fv-row" id="deduction_source" >
                                        <label class="fs-6 fw-semibold mb-2">Module Name</label>
                                        <input type="text" class="form-control" readonly value="{{ $appflow_module[0]['appflow_module_name'] }}">
                                        <input type="hidden" class="form-control" readonly value="{{ $appflow_module[0]['appflow_module_id'] }}" name="module_id">
                                    </div>
                                </div>
                            @endif
                        {{-- </form> --}}
                    </div>
                </div>
                <div class="card mb-5 mb-xl-8" id="tableForm" >

                    <div class="card-body py-3">
                        {{-- <form id="updateEmployeeDeductionPolicy"> --}}
                            
                            <table class="table table-bordered  table-row-bordered" id="responseAttendanceRuleTable">
                                <thead>
                                    <tr class="fw-bold text-muted bg-light">
                                        <th class="ps-6">Sequentional No</th>
                                        <th>Authorized Department</th>
                                    </tr>
                                </thead>
                                <tbody id="responseAttendanceRule">
                                    @if(!empty($appflow_detail))
                                        @foreach($appflow_detail as $appflow_detail)
                                            <tr>
                                                <td><input type="number" class="form-control" readonly value="{{ $appflow_detail['appflow_seq_no'] }}" name="sequence_number[]"></td>
                                                <td>
                                                    <select class="form-select" id="departments_all" data-close-on-select="false" data-control="select2" data-hide-search="true" data-placeholder="Select Department" name="departments[]" required>
                                                        @if(!empty($departments))
                                                            @foreach ($departments as $department)
                                                                <option value="{{$department['department_id']}}" @if($department['department_id'] == $appflow_detail['appflow_department']) selected @endif>{{ $department['department_name']}}</option>
                                                            @endforeach
                                                        @endif
                                                    </select>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td><input type="number" class="form-control" readonly value="1" name="sequence_number[]"></td>
                                            <td>
                                                <select class="form-select" id="departments_all" data-close-on-select="false" data-control="select2" data-hide-search="true" data-placeholder="Select Department" name="departments[]" required>
                                                    @if(!empty($departments))
                                                        @foreach ($departments as $department)
                                                            <option value="{{$department['department_id']}}">{{ $department['department_name']}}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            
                        </div>

                        <div class="card-footer text-center" id="submit_button_div">
                            <button type="button" class="btn btn-primary" id="add_new_sequence">Add New Sequence</button>
                            <button type="button" class="btn btn-danger" id="delete_last_sequence">Delete Last Sequence</button>
                        </div>
                    {{-- </form> --}}
                
                </div>
            
            </div>
        
        </div>

    </form>

</div>


@endsection



@push('js-link')

{{-- <script src="{{url('assets/js/appflow/appflow-detail.js')}}"></script> --}}

<script>

    var app_flow_detail = '{{ $appflow_detail_count }}';

    if(app_flow_detail > 0){
        count = app_flow_detail;
    }else{
        count = 1;
    }
    

    $('#add_new_sequence').click(function(){
        $('#responseAttendanceRule').append(`
            <tr>
                <td><input type="number" class="form-control" readonly value="${++count}" name="sequence_number[]"></td>
                <td>
                    <select class="form-select" id="departments_all" data-close-on-select="false" data-control="select2" data-hide-search="true" data-placeholder="Select Department" name="departments[]" required>
                        @if(!empty($departments))
                            @foreach ($departments as $department)
                                <option value="{{$department['department_id']}}">{{ $department['department_name']}}</option>
                            @endforeach
                        @endif
                    </select>
                </td>
            </tr>
        `);
    });

    $('#delete_last_sequence').click(function(){
        if(count >= 1){
            count--;
            $('#responseAttendanceRuleTable tr:last').remove();
        }
    });

    $('#appflow_detail_form').submit(function (e) {
        $('#appflow_detail_form_btn').prop('disabled', true);
        // $('#appflow_detail_form_btn').attr('data-kt-indicator', 'on');
        $('#appflow_detail_form_btn').css('cursor', 'not-allowed');
        e.preventDefault();
        $.ajax({
            url: "{{ url('appflow/update-appflow') }}",
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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
                        location.reload();
                    }, 3000);

                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 3000,
                    })

                    setTimeout(() => {
                        location.reload();
                    }, 3000);

                    $('#appflow_detail_form_btn').prop('disabled', false);
                    // $('#appflow_detail_form_btn').removeAttr('data-kt-indicator');
                    $('#appflow_detail_form_btn').css('cursor', 'pointer');
                }
            },
            error: function (jqXHR, textStatus) {
                $('#appflow_detail_form_btn').prop('disabled', false);
                // $('#appflow_detail_form_btn').removeAttr('data-kt-indicator');
                $('#appflow_detail_form_btn').css('cursor', 'pointer');
                var errorStatus = jqXHR.status;
                
                if(errorStatus == 419){

                    Toast.fire({
                        icon: 'warning',
                        title: 'You Don\'t have the access',
                        timer: 3000,
                    })

                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                }
                else if (errorStatus == 0) {
                    alert("Internet Connection Problem");
                } else {
                    alert("Try Again. Error Code " + errorStatus);
                }
            }
        });
    });

</script>


@endpush