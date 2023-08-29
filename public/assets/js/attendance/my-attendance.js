$(function () {

    const present = present_attendance;
    const absent_count = absent_count_attendance;
    const late_coming = late_coming_attendance;
    const early_out = early_out_attendance;
    const halfday = halfday_attendance;
    const total_offDays_holiday = total_offDays_holiday_attendance;
    const total_late_early = late_coming + early_out;

    $('#present').html(present);
    $('#absent').html(absent_count);
    $('#lateComing').html(total_late_early);
    $('#HalfDay').html(halfday);
    $('#offdays').html(total_offDays_holiday);

    // $('.filter-record').on('click', function () {
    //     var value_search = '';
    //     value = $(this).attr('data-value');
    //     value = value.split(',');

    //     for (let i = 0; i < value.length; i++) {
    //         if (i == value.length - 1) {
    //             value_search += value[i];
    //         } else {
    //             value_search += value[i] + '|';
    //         }

    //         dataTable.search(`${value_search}`, true, false).draw();
    //     }

    // });

});

function get_forty_days_future_date(from_date) {
    var date = new Date(from_date);
    var current_date = new Date();
    date.setDate(date.getDate() + 40);
    var future_date = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
    if (future_date < current_date) {
        return future_date;
    }

}

$('#to_date,#from_date').change(function () {
    var from_selected_date = $(this).val();
    var to_selected_date = $(this).val();
    var start = moment($('#from_date').val());
    $('#to_date').attr('min', $('#from_date').val());
    var end = moment($(this).val());
    if ($('#to_date').val().length > 0 && $('#from_date').val() > $('#to_date').val()) {
        $('#to_date').val('');

    }
    if (from_selected_date != '' || to_selected_date != '') {

        $('#label-month').removeClass('required');
        $('#select-month option').prop('selected', false);
        $('#select-month').prop("required", false);
        $('#main-div-select-month').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-employees-container-choice-5nle-Zain Shaikh">Select Month</span>');
        $('#main-div-select-month').find('.select2-search__field').attr('placeholder', '');

        $('#label-year').removeClass('required');
        $('#select-year option').prop('selected', false);
        $('#select-year').prop("required", false);
        $('#main-div-select-year').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-employees-container-choice-5nle-Zain Shaikh">Select Year</span>');
        $('#main-div-select-year').find('.select2-search__field').attr('placeholder', '');

        $('#label-from-date').addClass('required');
        $('#label-to-date').addClass('required');

    }

});

$('#from_date').change(function () {
    var from_selected_date = $(this).val();
    if (from_selected_date != '') {

        $('#to_date').prop('required', true);
        $('#to_date').attr('min', from_selected_date);
        $('#to_date').attr('max', get_forty_days_future_date(from_selected_date));
    } else {

        $('#to_date').prop('disabled', true);
        $('#to_date').removeAttr('min');
        $('#to_date').val('');
    }
});

$('#select-month,#select-year').change(function () {
    var selected_month = $(this).val();
    var selected_year = $(this).val();
    if (selected_month != '' || selected_year != '') {

        $('#from_date').prop('required', false);
        $('#to_date').prop('required', false);
        $('#from_date').val('');
        $('#to_date').val('');
        $('#label-from-date').removeClass('required');
        $('#label-to-date').removeClass('required');

        $('#label-month').addClass('required');
        $('#select-month').prop("required", true);

        $('#label-year').addClass('required');
        $('#select-year').prop("required", true);

    }
});
