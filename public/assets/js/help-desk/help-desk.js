$('#select-department').change(function (e) {
    e.preventDefault();
    $('#employees').html('');
    var dept = $('.multi-depart').val();

    $.ajax({
        url: base_url + '/get-employee-department-hd',
        type: 'GET',

        data: {
            department: dept
        },

        success: function (response) {
            $('#employees').append(response);


        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                alert("Internet Connection Problem");
            } else {
                alert("Try Again. Error Code " + errorStatus);
            }

        }
    });


})

$('.message_btn').click(function () {
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});