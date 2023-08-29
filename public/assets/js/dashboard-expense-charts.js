// ====== Expense Graph Start =====
// Class definition
var KTChartsWidget3W = function() {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function(chart, values, date_time) {
        var element = document.getElementById("expense_graph");

        if (!element) {
            return;
        }

        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-border-dashed-color');
        var baseColor = KTUtil.getCssVariableValue('--kt-success');
        var lightColor = KTUtil.getCssVariableValue('--kt-success');

        var options = {
            series: [{
                name: 'Expense',
                data: values
            }],
            chart: {
                fontFamily: 'inherit',
                type: 'area',
                height: height,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {

            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            fill: {
                type: "gradient",
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.4,
                    opacityTo: 0,
                    stops: [0, 80, 100]
                }
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 3,
                colors: [baseColor]
            },
            xaxis: {
                categories: date_time,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                tickAmount: 6,
                labels: {
                    rotate: 0,
                    rotateAlways: true,
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                },
                crosshairs: {
                    position: 'front',
                    stroke: {
                        color: baseColor,
                        width: 1,
                        dashArray: 3
                    }
                },
                tooltip: {
                    enabled: true,
                    formatter: undefined,
                    offsetY: 0,
                    style: {
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                tickAmount: 4,
                max: Math.max.apply(Math, values),
                min: Math.min.apply(Math, values),
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    },
                    formatter: function(val) {
                        return 'Rs.' + val
                    }
                }
            },
            states: {
                normal: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: 'none',
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: 'none',
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: '12px'
                },
                y: {
                    formatter: function(val) {
                        return "Rs." + val
                    }
                }
            },
            colors: [lightColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            markers: {
                strokeColor: baseColor,
                strokeWidth: 3
            }
        };

        chart.self = new ApexCharts(element, options);

        // Set timeout to properly get the parent elements width
        setTimeout(function() {
            chart.self.render();
            chart.rendered = true;
        }, 200);
    }

    // Public methods
    return {
        init: function(values, date_time) {
            initChart(chart, values, date_time);

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function() {
                if (chart.rendered) {
                    chart.self.destroy();
                }

                initChart(chart);
            });
        }
    }
}();


function fetch_api_expense_graph(filter_expense_month) {
    $('#expense_graph').html('<div class="spinner spinner-primary spinner-lg mr-15"></div>');
    url = base_url + '/' + 'expense-graph';
    var expense_month = $('#expense_month').val();
    var expense_year = $('#expense_year').val();

    fetch(url, {

        // Adding method type
        method: "POST",

        // Adding body or contents to send
        body: JSON.stringify({
            month: expense_month,
            year: expense_year,
            filter_expense_month: filter_expense_month
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
        KTChartsWidget3W.init(json.values, json.date_time);
        document.getElementById("total_expense").innerHTML = json.total_expense;

    }).catch(response => {
        console.log(response.status, response.statusText);

    });
}

// call expense graph on default values

var filter_expense_month = "Null";
fetch_api_expense_graph(filter_expense_month);
$('#expense_filter').removeClass('btn-color-primary');
// call expense graph on change values
$('#expense_month, #expense_year').on('change', function() {
    $('#expense_filter').addClass('btn-color-primary');
    filter_expense_month = 'yes';
    fetch_api_expense_graph(filter_expense_month);
})

// ====== Expense Graph End =====