// ===== Department Wise Active/Unactive Employeee Start ======

// Class definition
var departmentEmployeeGraph = function () {
    var chart = {
        self: null,
        rendered: false
    };

    // Private methods
    var initChart = function (chart, active, deactive, total, department,max_y) {
        var element = document.getElementById("department_employee_graph");

        if (!element) {
            return;
        }


        var height = parseInt(KTUtil.css(element, 'height'));
        var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
        var borderColor = KTUtil.getCssVariableValue('--kt-gray-200');

        var baseColor = KTUtil.getCssVariableValue('--kt-primary');
        var baseLightColor = KTUtil.getCssVariableValue('--kt-primary-light');
        var secondaryColor = KTUtil.getCssVariableValue('--kt-info');

        var options = {
            series: [{
                name: 'Active',
                type: 'bar',
                stacked: true,
                data: active
            }, {
                name: 'Deactive',
                type: 'bar',
                stacked: true,
                data: deactive
            }, {
                name: 'Total',
                type: 'area',
                data: total
            }],
            chart: {
                fontFamily: 'inherit',
                stacked: true,
                height: 350,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    stacked: true,
                    horizontal: false,
                    borderRadius: 4,
                    columnWidth: ['90%']
                },
            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: department,
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                }
            },
            yaxis: {
                max: max_y,
                labels: {
                    style: {
                        colors: labelColor,
                        fontSize: '12px'
                    }
                }
            },
            fill: {
                opacity: 1
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
                    formatter: function (val) {
                        return "" + val + " Employee"
                    }
                }
            },
            colors: [baseColor, secondaryColor, baseLightColor],
            grid: {
                borderColor: borderColor,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 0
                }
            }
        };

        chart.self = new ApexCharts(element, options);


        setTimeout(function () {
            chart.self.render();
            chart.rendered = true;
        }, 200);
    }

    // Public methods
    return {
        init: function (active, deactive, total, date_time, max_y) {
            initChart(chart, active, deactive, total, date_time, max_y);

            // Update chart on theme mode change
            KTThemeMode.on("kt.thememode.change", function () {
                if (chart.rendered) {
                    chart.self.destroy();
                }

                initChart(chart, active, deactive, total, date_time, max_y);
            });
        }
    }
}();


function fetch_api_department_employee_graph() {
    $('#department_employee_graph').html('<div class="spinner spinner-primary spinner-lg mr-15"></div>');
    url = base_url + '/' + 'department-employee-graph';

    fetch(url, {

        // Adding method type
        method: "POST",

        // Adding body or contents to send
        body: JSON.stringify({}),

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
            departmentEmployeeGraph.init(json.active, json.deactive, json.total, json.department, json.max_y);


        }).catch(response => {
            console.log(response.status, response.statusText);

        });
}

// call department employee graph on default values
fetch_api_department_employee_graph();

// ===== Department Wise Active/Unactive Employeee End ======
