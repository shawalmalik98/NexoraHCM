$('#proxy_employee_data').change(function(){
    var selected_val = $('#proxy_employee_data').find(":selected").val();
    $('#proxy_by_name').text('');
    $.ajax({
        url: base_url+'/get_employees_proxy/'+selected_val,
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            status: selected_val
        },
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
            // $('.search-employee').select2()
           $('#proxy_by_name').append(data); 
           
        },
        error: function(jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                alert("Internet Connection Problem");
            } else {
                alert("Try Again. Error Code " + errorStatus);
            }
        }
    });
});

function proxy_employee_data_trigger(){
    $('#proxy_employee_data').trigger('change');
}


$('#proxy_user_form').submit(function (e) {
    $('#proxy_form_btn').prop('disabled', true);
    $('#proxy_form_btn').attr('data-kt-indicator', 'on');
    $('#proxy_form_btn').css('cursor', 'not-allowed');
    $('#gl_div').html(gl);
    e.preventDefault();
    $.ajax({
        url: base_url + '/proxy-user',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            $('#gl_div').html('');
            if (data.status == 'TRUE') {

                Toast.fire({
                    icon: 'success',
                    title: data.msg,
                    timer: 3000,
                })

                setTimeout(() => {

                    window.location.href = "/";
                    
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

                $('#proxy_form_btn').prop('disabled', false);
                $('#proxy_form_btn').removeAttr('data-kt-indicator');
                $('#proxy_form_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            $('#password_form_btn').prop('disabled', false);
            $('#password_form_btn').removeAttr('data-kt-indicator');
            $('#password_form_btn').css('cursor', 'pointer');
            $('#gl_div').html('');
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