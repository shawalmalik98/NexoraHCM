$(function () {

    dataTable = $('#tableEmployeeOverTime').DataTable({
        order: false,
        aLengthMenu: [
            [10, 25, 50, 100, 500, -1],
            [10, 25, 50, 100, 500, "All"]
        ],
        iDisplayLength: 500,
    });
});

var download_overtime = download_button;
if (download_overtime == false) {
    document.querySelector('#overtime_button').style.display = 'none';
}