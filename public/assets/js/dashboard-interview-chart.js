function interview_graph(overall_interviews, scheduled_interview, offerPlaced_interview, hired_interview, cancelled_interview) {

    var ctx = document.getElementById('kt_chartjs_3');

    if (overall_interviews == 0 && scheduled_interview == 0 && offerPlaced_interview == 0 && hired_interview == 0 && cancelled_interview == 0) {
        $('#interview-chart').html('<div class="alert alert-primary" role="alert">No Record Found</div>')
    }


    // Define colors
    var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
    var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
    var successColor = KTUtil.getCssVariableValue('--kt-success');
    var warningColor = KTUtil.getCssVariableValue('--kt-warning');
    var infoColor = KTUtil.getCssVariableValue('--kt-info');

    // Define fonts
    var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');

    // Chart labels
    const labels = ['Over All Interviews', 'Scheduled Interviews', 'Offer Placed Interviews', 'Hired Interviews', 'Cancel Interviews'];

    // Chart data
    const data = {
        labels: labels,
        datasets: [{
            label: 'Dataset 1',
            data: [overall_interviews, scheduled_interview, offerPlaced_interview, hired_interview, cancelled_interview],
            backgroundColor: [primaryColor, successColor, infoColor, warningColor, dangerColor]
        }, ]
    };

    // Chart config
    const config = {
        type: 'pie',
        data: data,
        options: {
            plugins: {
                title: {
                    display: false,
                }
            },
            responsive: true,
        },
        defaults: {
            global: {
                defaultFont: fontFamily
            }
        }
    };

    // Init ChartJS -- for more info, please visit: https://www.chartjs.org/docs/latest/
    var myChart = new Chart(ctx, config);
}



function fetch_api_interview_graph() {
    url = base_url + '/' + 'interview-graph';
    var interview_month = $('#interview_month').val();
    var interview_year = $('#interview_year').val();

    fetch(url, {

        // Adding method type
        method: "POST",

        // Adding body or contents to send
        body: JSON.stringify({
            month: interview_month,
            year: interview_year,
            filter_interview: filter_interview
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
        interview_graph(json.overall_interviews, json.scheduled_interview, json.offerPlaced_interview, json.hired_interview, json.cancelled_interview);


    })
    // .catch(response => {
    //     console.log(response);

    // });


}

var filter_interview = 'Null';
fetch_api_interview_graph(filter_interview);
$('#interview_filter').removeClass('btn-color-primary');
$('#interview_year,#interview_month').on('change', function() {
    $('#interview-chart').html('');
    $('#interview-chart').html('<canvas id="kt_chartjs_3" class="mh-300px"></canvas>');
    filter_interview = 'yes';
    $('#interview_filter').addClass('btn-color-primary');
    fetch_api_interview_graph(filter_interview);
})