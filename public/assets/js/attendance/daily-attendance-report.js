$(function () {
    dataTable = $('#tableDailyAttendance').DataTable({
        order: false,
        search: { regex: true },
        aLengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
        ],
        iDisplayLength: 500,
    });

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


});
