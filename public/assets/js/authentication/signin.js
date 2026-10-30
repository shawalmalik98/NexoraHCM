const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});

setTimeout(() => {
    $('.page-loader').fadeOut()
})

var gl;

    $("#loginForm").submit(function (e) {

        $('#loginForm_submit').prop('disabled', true);
        $('#loginForm_submit').css('cursor', 'not-allowed');
        // $('.btn_loader').removeClass('d-none');
        $("#loginForm_submit").attr('data-kt-indicator', 'on');
        $('#gl_div').html(gl);
        e.preventDefault();

        $.ajax({
            url: url + '/authenticate',
            
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                $('#gl_div').html('');
                response = JSON.parse(JSON.stringify(data));
                console.log(response);
                if (response.status == "TRUE") {

                    Toast.fire({
                        icon: 'success',
                        title: response.message,
                        timer: 1000,
                    })
                    setTimeout(() => {
                        window.location.href = url;
                    }, 500);

                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: response.message,
                        timer: 6000,
                    })
                    $('#loginForm_submit').prop('disabled', false);
                    $('#loginForm_submit').css('cursor', 'pointer');
                    $("#loginForm_submit").removeAttr('data-kt-indicator');

                }
            },
            error: function (jqXHR, textStatus) {
                $('#loginForm_submit').prop('disabled', false);
                $('#loginForm_submit').css('cursor', 'pointer');
                $("#loginForm_submit").removeAttr('data-kt-indicator');
                $('#gl_div').html('');
                // $('.btn_loader').addClass('d-none');
                var errorStatus = jqXHR.status;

                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 6000,
                    });
                } else {
                    if (errorStatus == 419) {
                        location.reload();
                    } else {
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
   
    // $("#loginForm").submit(function (e) {

    //     $('#loginForm_submit').prop('disabled', true);
    //     $('#loginForm_submit').css('cursor', 'not-allowed');
    //     // $('.btn_loader').removeClass('d-none');
    //     $("#loginForm_submit").attr('data-kt-indicator', 'on');
    //     $('#gl_div').html(gl);
    //     e.preventDefault();
    //     $.ajax({
    //         url: url + '/authenticate',
    //         type: 'POST',
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: new FormData(this),
    //         contentType: false,
    //         cache: false,
    //         processData: false,
    //         success: function (data) {
    //             $('#gl_div').html('');
    //             response = JSON.parse(JSON.stringify(data));
    //             if (response.status == "TRUE") {
    
    //                 Toast.fire({
    //                     icon: 'success',
    //                     title: response.message,
    //                     timer: 3000,
    //                 })
    //                 setTimeout(() => {
    //                     window.location.href = url;
    //                 }, 3000);
    
    
    //             } else {
    //                 Toast.fire({
    //                     icon: 'warning',
    //                     title: response.message,
    //                     timer: 6000,
    //                 })
    //                 $('#loginForm_submit').prop('disabled', false);
    //                 $('#loginForm_submit').css('cursor', 'pointer');
    //                 $("#loginForm_submit").removeAttr('data-kt-indicator');
    
    //             }
    //         },
    //         error: function (jqXHR, textStatus) {
    //             $('#loginForm_submit').prop('disabled', false);
    //             $('#loginForm_submit').css('cursor', 'pointer');
    //             $("#loginForm_submit").removeAttr('data-kt-indicator');
    //             $('#gl_div').html('');
    //             // $('.btn_loader').addClass('d-none');
    //             var errorStatus = jqXHR.status;
    
    //             if (errorStatus == 0) {
    //                 Toast.fire({
    //                     icon: 'warning',
    //                     title: 'Internet Connection Problem',
    //                     timer: 6000,
    //                 });
    //             } else {
    //                 if (errorStatus == 419) {
    //                     location.reload();
    //                 } else {
    //                     Toast.fire({
    //                         icon: 'warning',
    //                         title: 'Try Again. Error Code ' + errorStatus,
    //                         timer: 3000,
    //                     })
    //                 }
    //             }
    //         }
    //     });
    // });


