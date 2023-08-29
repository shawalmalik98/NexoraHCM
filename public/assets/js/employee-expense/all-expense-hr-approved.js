
$('#modalApprovedForm').submit(function (e) {
    e.preventDefault()
    send_server_request('POST', 'employee-expense/approved-expense', this, 'modalApprovedFormButton', "No", 'employee-expense', 3000)
});


$('#modalDisapprovedForm').submit(function (e) {
    e.preventDefault()
    send_server_request('POST', 'employee-expense/disapproved-expense', this, 'modalDisapprovedFormButton', "No", 'employee-expense', 3000)
});

