function leave_request_graph(total_request, total_approve_request, total_disapprove_request) {
    var ctx = document.getElementById('leave-chart');

    // Define colors
    var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
    var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
    var successColor = KTUtil.getCssVariableValue('--kt-success');

    // Define fonts
    var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');

    // Chart labels
    const labels = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

    // Chart data
    const data = {
        labels: labels,
        datasets: [{
                label: 'Total Leave Request',
                data: total_request,
                backgroundColor: primaryColor,
                stack: 'Stack 0',
            },
            {
                label: 'Total Approve Request',
                data: total_approve_request,
                backgroundColor: successColor,
                stack: 'Stack 1',
            },
            {
                label: 'Total DisApprove Request',
                data: total_disapprove_request,
                backgroundColor: dangerColor,
                stack: 'Stack 2',
            },
        ]
    };

    // Chart config
    const config = {
        type: 'bar',
        data: data,
        options: {
            plugins: {
                title: {
                    display: false,
                }
            },
            responsive: true,
            interaction: {
                intersect: false,
            },
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true
                }
            }
        },
        defaults: {
            global: {
                defaultFont: fontFamily
            }
        }
    };
    var myChart = new Chart(ctx, config);
}



function fetch_api_leave_request_graph(filter_leave_request) {
    url = base_url + '/' + 'leave-request-graph';
    var dept_name = $('#select-department').val();
    var employee_name = $('#employees').val();
    var leave_request_year = $('#leave_request_year').val();

    fetch(url, {

        // Adding method type
        method: "POST",

        // Adding body or contents to send
        body: JSON.stringify({
            emp_id: employee_name,
            department: dept_name,
            year: leave_request_year,
            filter_leave_request: filter_leave_request
        }),

        // Adding headers to the request
        headers: {
            "Content-type": "application/json; charset=UTF-8",
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    // Converting to JSON
    .then(response => {
        // response.json()
        if (response.ok) {
            return response.json();
        }
        throw new Error('Something went wrong')
    })


    // Displaying results to console
    .then(json => {
        $('#leave-request-chart').html('<canvas id="leave-chart" style="height:250px"></canvas>');
        leave_request_graph(json.total_request, json.total_approve_request, json.total_disapprove_request);


    }).catch(response => {
        console.log(response);

    });


}

var filter_leave_request = 'Null';
fetch_api_leave_request_graph(filter_leave_request);

$('#select-department,#employees, #leave_request_year').on('change', function() {
    filter_leave_request = 'yes';
    $('#leave_filter').addClass('btn-color-primary');
    $('#leave-request-chart').html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');
    fetch_api_leave_request_graph(filter_leave_request);

})