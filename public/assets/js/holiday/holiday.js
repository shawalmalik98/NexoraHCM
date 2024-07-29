$('#overtime_applicable').click(function () {

    var checkbox = $('#overtime_applicable').prop('checked');

    if (checkbox) {
        $('#payment_type').removeClass('d-none');
        $('#overtime_payment').prop('required', true);
        $('#payment_type').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-overtime_payment-container">Select Payment Type</span>');
        $('#payment_type').find('.select2-search__field').attr('placeholder', '');

    } else {

        $('#overtime_amount').addClass('d-none');
        $('#holiday_overtime').prop('required', false);
        $('#payment_type').addClass('d-none');
        $('#overtime_payment').prop('required', false);
        $('#overtime_payment').val('');
        $('#payment_type').find('.select2-selection__rendered').html('');
        $('#payment_type').find('.select2-search__field').attr('placeholder', 'Select Payment Type');

    }

});

$('#overtime_payment').change(function () {
    var payment_type = $(this).val();

    if (payment_type == 1) {
        $('#overtime_amount').removeClass('d-none');
        $('#holiday_overtime').prop('required', true);
        $('#overtime_label').text('Holiday Overtime Amount');
        $('#holiday_overtime').val('').attr('placeholder', 'Enter Holiday Overtime Amount');
    } else if (payment_type == 0) {
        $('#overtime_amount').removeClass('d-none');
        $('#holiday_overtime').prop('required', true);
        $('#overtime_label').text('No of days');
        $('#holiday_overtime').val('').attr('placeholder', 'Enter No of days i.e : (1 1.5 2 2.5)');
    } else {
        $('#overtime_amount').addClass('d-none');
        $('#holiday_overtime').prop('required', false);

    }
});

var total_days;
$('#to_date,#from_date').change(function () {

    var start = moment($('#from_date').val());
    $('#to_date').attr('min', $('#from_date').val());
    var end = moment($('#to_date').val());
    total_days = end.diff(start, "days");
    if (isNaN(total_days)) {
        $('#days').val('');
    } else {
        $('#days').val(total_days + 1);
    }

});

$("#days_assign").change(function () {

    var value = $(this).children("option:selected").val();

    if (value == "single") {

        $('.only_date_input').removeClass('d-none');
        $('.from_to_date_input').addClass('d-none');
        $('.date-control').val('');
        $("#holiday_date").prop('required', true);
        $(".date-control").prop('required', false);
        $('#present_employees_dates').addClass('d-none');

    } else {
        $('#holiday_date').val('');
        $('.only_date_input').addClass('d-none');
        $('.from_to_date_input').removeClass('d-none');
        $(".date-control").prop('required', true);
        $("#holiday_date").prop('required', false);
        $('#present_employees_dates').addClass('d-none');
    }
});

$('#holiday_date').change(function () {

    var selected_date = new Date($(this).val());
    var current_date = new Date();

    if (selected_date > current_date) {
        $('#present_employees_dates').addClass('d-none');
    } else {
        $('#present_employees_dates').removeClass('d-none');
    }


});


$('#from_date').change(function () {

    var from_selected_date = $(this).val();
    var selected_date = new Date($(this).val());
    var current_date = new Date();

    if (selected_date > current_date) {
        $('#present_employees_dates').addClass('d-none');
    } else {
        $('#present_employees_dates').removeClass('d-none');
    }

    if (from_selected_date != '') {
        $('#to_date').prop('disabled', false);
        $('#to_date').attr('min', from_selected_date);

    } else {
        $('#to_date').prop('disabled', true);
        $('#to_date').removeAttr('min');
        $('#to_date').val('');

    }
});

// $('#department_all').click(function () {

//     var checkbox = $('#department_all').prop('checked');

//     if (checkbox) {
//         $('#departments_all option').prop('selected', true);
//         $('#selectedepartment').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
//         $('#selectedepartment').find('.select2-search__field').attr('placeholder', '');
//     } else {
//         $('#departments_all option').prop('selected', false);
//         $('#selectedepartment').find('.select2-selection__rendered').html('');
//         $('#selectedepartment').find('.select2-search__field').attr('placeholder', 'Select Department');
//         $('#selectedepartment').find('.select2-search__field').css('width', '100%');

//     }

// });

// $('#departments_all').change(function () {

//     var departments = $('#departments_all').val();
//     var all_length = count;
//     if (departments.length == all_length) {
//         $('#department_all').prop('checked', true);
//     } else {
//         $('#department_all').prop('checked', false);

//     }

// });

$('#designation_all').click(function () {
    var isChecked = $(this).is(':checked');
    var designationsSelect = $('#designations_all');
    var allDesignations = designationsSelect.find('option');

    if (isChecked) {
        // Check all options
        allDesignations.prop('selected', true);
        designationsSelect.trigger('change'); // Trigger change event to update Select2

        // Update the Select2 display
        var select2Container = $('#selectedesignation').find('.select2-selection__rendered');
        select2Container.html('<span class="select2-selection__choice__display">All Designations</span>');
        $('#selectedesignation').find('.select2-search__field').attr('placeholder', '');
    } else {
        // Uncheck all options
        allDesignations.prop('selected', false);
        designationsSelect.trigger('change'); // Trigger change event to update Select2

        // Update the Select2 display
        var select2Container = $('#selectedesignation').find('.select2-selection__rendered');
        select2Container.html('');
        $('#selectedesignation').find('.select2-search__field').attr('placeholder', 'Select Designation');
        $('#selectedesignation').find('.select2-search__field').css('width', '100%');
    }
});

// When the designations select is changed
$('#designations_all').change(function () {
    var selectedDesignations = $(this).val();
    var totalDesignationsCount = $(this).find('option').length;
    var selectedCount = selectedDesignations ? selectedDesignations.length : 0;

    if (selectedCount == totalDesignationsCount) {
        $('#designation_all').prop('checked', true);
        $('#selected_designations_count').val(totalDesignationsCount);
    } else {
        $('#designation_all').prop('checked', false);
        $('#selected_designations_count').val('');
    }
});

$('#departments_all').change(function (e) {
    e.preventDefault();
    var selectedDepartments = $(this).val(); // Get all selected department IDs
    
    $('#designations_all').html('');
    $.ajax({
        url: base_url + '/employees/get_all_designations',
        type: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            departments: selectedDepartments
        },
        success: function (data) {
            $('#designations_all').append(data);

        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Internet Connection Problem',
                    timer: 3000,
                });
            } else {
                if (errorStatus == 419) {
                    location.reload();
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Try Again. Error Code ' + errorStatus,
                        timer: 3000,
                    });
                }
            }
        }
    });
});
    
$('#addHolidayForm').submit(function (e) {
    e.preventDefault();

    $('#submit_btn').prop('disabled', true);
    $("#submit_btn").attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');

    var message;

    if (total_days >= 1) {
        var message = 'Are you sure to submit ' + (total_days + 1) + ' day(s) holiday(s). You can\'t revert this action anymore.'
    } else {
        var message = 'This action can\'t revert, are you sure to submit holiday ?'
    }

    swal.fire({
        title: 'Confirmation Alert !',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes',
        cancelButtonText: 'Cancel',
        allowOutsideClick: false,
    }).then(function (value) {

        if (value.isConfirmed == true) {
            $.ajax({
                url: base_url + '/holidays/addholiday',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $('#addHolidayForm').serialize(),
                success: function (data) {
                    if (data.status == 'TRUE') {
                        swal.fire({
                            title: 'Acknowledge !',
                            text: data.msg,
                            icon: 'success',
                            confirmButtonText: 'Okay',
                            allowOutsideClick: false,
                        }).then(function (value) {

                            if (value.isConfirmed == true) {
                                location.reload();
                            }
                        });
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: data.msg,
                            timer: 5000,
                        })
                        $('#submit_btn').prop('disabled', false);
                        $("#submit_btn").removeAttr('data-kt-indicator');
                        $('#submit_btn').css('cursor', 'pointer');
                    }
                },
                error: function (jqXHR, textStatus) {
                    var errorStatus = jqXHR.status;
                    $('#submit_btn').prop('disabled', false);
                    $("#submit_btn").removeAttr('data-kt-indicator');
                    $('#submit_btn').css('cursor', 'pointer');
                    if (errorStatus == 0) {
                        Toast.fire({
                            icon: 'warning',
                            title: 'Internet Connection Problem',
                            timer: 3000,
                        })
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
        } else {
            location.reload();
        }
    });
});

$("#kt_daterangepicker_4").daterangepicker({
    locale: {
        format: 'YYYY-MM-DD'
    },
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 1901,
    maxYear: parseInt(moment().format("YYYY"), 12)
}, function (start, end, label) {
    var years = moment().diff(start, "years");
});