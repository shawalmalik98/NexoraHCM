function employee_monthly_strength_graph(total_present, total_absent, total_leave) {
    var ctx = document.getElementById('monthly-strength-chart');

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
                label: 'Total Present',
                data: total_present,
                backgroundColor: successColor,
                stack: 'Stack 0',
            },
            {
                label: 'Total Absent',
                data: total_absent,
                backgroundColor: dangerColor,
                stack: 'Stack 1',
            },

            {
                label: 'Total Leaves',
                data: total_leave,
                backgroundColor: primaryColor,
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



function fetch_employee_monthly_strength(filter_emp_strength) {
    url = base_url + '/' + 'employee-monthly-strength';
    var dept_name = $('#select-department-employee').val();
    var employee_name = $('#employee_list').val();
    var employee_strength_year = $('#employee_strength_year').val();

    fetch(url, {

        // Adding method type
        method: "POST",

        // Adding body or contents to send
        body: JSON.stringify({
            emp_id: employee_name,
            department: dept_name,
            year: employee_strength_year,
            filter_emp_strength: filter_emp_strength,
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
        //throw new Error('Something went wrong')
    })


    // Displaying results to console
    .then(json => {
        $('#employee-monthly-strength-chart').html('<canvas id="monthly-strength-chart" style="height:250px"></canvas>');
        employee_monthly_strength_graph(json.total_present, json.total_absent, json.total_leave);


    })
}
$('#employee_strength_filter').removeClass('btn-color-primary');
var filter_emp_strength = 'Null';
fetch_employee_monthly_strength(filter_emp_strength);

$('#select-department-employee,#employee_list,#employee_strength_year').on('change', function() {
    filter_emp_strength = 'yes';
    $('#employee_strength_filter').addClass('btn-color-primary');
    $('#employee-monthly-strength-chart').html('<div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>');
    fetch_employee_monthly_strength(filter_emp_strength);
})