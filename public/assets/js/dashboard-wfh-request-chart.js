function wfh_request_graph(total_request, total_approve_request, total_disapprove_request) {
    var ctx = document.getElementById('wfh_chart_js');

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
                label: 'Total Remote Request',
                data: total_request,
                backgroundColor: primaryColor,
                stack: 'Stack 0',
            },
            {
                label: 'Total Remote Approve Request',
                data: total_approve_request,
                backgroundColor: successColor,
                stack: 'Stack 1',
            },
            {
                label: 'Total Remote DisApprove Request',
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



function fetch_api_wfh_request_graph(filter_wfh) {
    url = base_url + '/' + 'wfh-request-graph';
    var dept_name = $('#select-department-wfh').val();
    var employee_id = $('#employees-wfh').val();
    var wfh_request_year = $('#wfh_request_year').val();

    fetch(url, {

        // Adding method type
        method: "POST",

        // Adding body or contents to send
        body: JSON.stringify({
            emp_id: employee_id,
            department: dept_name,
            year: wfh_request_year,
            filter_wfh: filter_wfh
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
        wfh_request_graph(json.total_request, json.total_approve_request, json.total_disapprove_request);


    }).catch(response => {
        console.log(response);

    });


}

var filter_wfh = 'Null';
fetch_api_wfh_request_graph(filter_wfh);

$('#select-department-wfh,#employfilter_wfhees-wfh, #wfh_request_year').on('change', function() {
    filter_wfh = 'yes';
    $('#wfh-request-chart').html('');
    $('#wfh-request-chart').html('<canvas id="wfh_chart_js" class="mh-400px"></canvas>');
    $('#wfh_filter').addClass('btn-color-primary');
    fetch_api_wfh_request_graph(filter_wfh);
})