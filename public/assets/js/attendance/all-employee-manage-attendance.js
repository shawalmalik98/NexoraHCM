$(function () {

    let d = new Date();
    let monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    let month = monthNames[d.getMonth()];
    let year = d.getFullYear();
    if ($("select[name=m]").val()) {
        month = monthNames[$("select[name=m]").val() - 1];
        year = $("select[name=y]").val();
    }
   
    dataTable = $('#tableAllEmployeeAttendance').DataTable({
        order: false,
        aLengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
        ],
        iDisplayLength: 500,

    });


});
