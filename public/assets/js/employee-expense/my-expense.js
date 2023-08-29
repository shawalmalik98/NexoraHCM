$(function () {
    $('.expenseselect').select2({
        "enable": false,
        dropdownParent: $('#addMyExpense')
    })

    $('.expenseselectupdate').select2({
        "enable": false,
        dropdownParent: $('#updateMyExpense')
    })
})

$('#addMyExpenseForm').submit(function (e) {

    e.preventDefault()
    send_server_request('POST', 'add-my-expense', this, 'addMyExpenseFormButton', "No", 'my-expense', 3000)

})

$('#modalDeleteForm').submit(function (e) {

    e.preventDefault()
    send_server_request('POST', 'delete-my-expense', this, 'modalDeleteFormButton', "No", 'my-expense', 3000)

})

$('#updateMyExpenseForm').submit(function (e) {

    e.preventDefault()
    send_server_request('POST', 'update-my-expense', this, 'updateMyExpenseFormButton', "No", 'my-expense', 3000)

})


let amountLog = (expense_id) => {

    // alert(expense_id);
    $('#expenseAmountTable').html('');
    url = base_url + '/' + 'expense-amount-log';

    let form_data = { 'expense_id': expense_id }

    var body_data;



    body_data = {

        // Adding method type
        method: 'POST',

        // Adding body or contents to send
        body: JSON.stringify(form_data),
        // body : form_data,

        // Adding headers to the request
        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type': 'application/json'

        }
    }

    fetch(url, body_data)

        // Converting to JSON
        .then(response => {
            // response.json()
            if (response.ok) {
                return response.json();
            }

            return Promise.reject(response);
        })


        // Displaying results to console
        .then(json => {


            if (json.status == 'TRUE') {

                var record;
                record += `<thead>
                    <tr class="fw-bold text-muted bg-light">
                        <th class="ps-7">Employee Name</th>
                        <th >Amount</th>
                        <th >Status</th>
                        <th >Update Time</th>
                       
                    </tr>
                </thead> <tbody>`;
                var i = 0;
                $.each(json.data, function (key, amount_log) {
                    i++;
                    if (i == 1) {

                        record += `<tr>
                    <td class="ps-6 "><b>${amount_log.employee_name} - (${amount_log.employee_code})</b></td>
                    <td class="ps-6">${amount_log.emp_expense_amount}</td>
                    <td class="ps-6"><span class="badge badge-light-success font-weight-bold">Actual</span></td>
                    <td class="ps-6">${amount_log.emp_expense_create_at}</td>
                    </tr>`;

                    } else {

                        record += `<tr>
                    <td class="ps-6 "><b>${amount_log.employee_name} - (${amount_log.employee_code})</b></td>
                    <td class="ps-6">${amount_log.emp_expense_amount}</td>
                    <td class="ps-6"><span class="badge badge-light-danger font-weight-bold">Updated</span></td>
                    <td class="ps-6">${amount_log.emp_expense_create_at}</td>
                    </tr>`;

                    }


                });
                record += ` </tbody>`;
                $('#expenseAmountTable').html(record);
                $('#expenseAmountModal').modal('show');


            } else {

                Toast.fire({
                    icon: 'warning',
                    title: json.msg,
                    timer: 3000,
                })



            }



        }).catch(response => {


            Toast.fire({
                icon: 'warning',
                title: response.statusText,
                timer: 3000,
            })

        });
}

$('.kt_docs_maxlength_threshold2').maxlength({
    threshold: 500,
    warningClass: "badge badge-primary",
    limitReachedClass: "badge badge-success"
});

$('.message_btn').click(function () {
    var message = $(this).attr('data-bs-message');
    $('#message_body').html(message);
});


$('.reason_btn').click(function () {
    var message = $(this).attr('data-bs-reason');
    $('#reason_body').html(message);
});



let deleteExpenseId = (delete_id) => {
    $("input[name='expense_id']").val(delete_id)
}

let expenseEditModelFunc = (data) => {

    $("#updateMyExpenseForm :input[name='expense_amount']").val(data.emp_expense_amount)
    $("#updateMyExpenseForm :input[name='expense_id']").val(data.emp_expense_id)
    $("#updateMyExpenseForm :input[name='expense_date']").val(data.emp_expense_date)
    $("#updateMyExpenseForm :input[name='expense_type']").val(data.emp_expense_type).trigger('change')
    $("#textarea_update").html(data.emp_expense_description)
    $('#updateMyExpense').modal('show')

}

$('.manager_modal_status_btn').click(function () {

    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 7);

});

$(function () {
    $('#req_status').on('change', function () {
        var value = $(this).val();
        if (value == '') {
            dataTable.column(7).search('', false, true).draw();
        } else {
            dataTable.column(7).search("^" + value + "$", true, false).draw();
        }
    });
}); 
