// ===== Department Wise Active/Unactive Employeee Start ======

// Class definition
// var departmentEmployeeGraph = function() {
//     var chart = {
//         self: null,
//         rendered: false
//     };

//     // Private methods
//     var initChart = function(chart, active, deactive, total, department, max_y) {
//         var element = document.getElementById("department_employee_graph");

//         if (!element) {
//             return;
//         }


//         var height = parseInt(KTUtil.css(element, 'height'));
//         var labelColor = KTUtil.getCssVariableValue('--kt-gray-500');
//         var borderColor = KTUtil.getCssVariableValue('--kt-gray-200');

//         var baseColor = KTUtil.getCssVariableValue('--kt-primary');
//         var baseLightColor = KTUtil.getCssVariableValue('--kt-primary-light');
//         var secondaryColor = KTUtil.getCssVariableValue('--kt-info');

//         var options = {
//             series: [{
//                 name: 'Active',
//                 type: 'bar',
//                 stacked: true,
//                 data: active
//             }, {
//                 name: 'Deactive',
//                 type: 'bar',
//                 stacked: true,
//                 data: deactive
//             }, {
//                 name: 'Total',
//                 type: 'area',
//                 data: total
//             }],
//             chart: {
//                 fontFamily: 'inherit',
//                 stacked: true,
//                 height: 350,
//                 toolbar: {
//                     show: false
//                 }
//             },
//             plotOptions: {
//                 bar: {
//                     stacked: true,
//                     horizontal: false,
//                     borderRadius: 4,
//                     columnWidth: ['90%']
//                 },
//             },
//             legend: {
//                 show: false
//             },
//             dataLabels: {
//                 enabled: false
//             },
//             stroke: {
//                 curve: 'smooth',
//                 show: true,
//                 width: 2,
//                 colors: ['transparent']
//             },
//             xaxis: {
//                 categories: department,
//                 axisBorder: {
//                     show: false,
//                 },
//                 axisTicks: {
//                     show: false
//                 },
//                 labels: {
//                     style: {
//                         colors: '#0A8AB4',
//                         fontSize: '12px'
//                     }
//                 }
//             },
//             yaxis: {
//                 max: max_y,
//                 labels: {
//                     style: {
//                         colors: '#0A8AB4',
//                         fontSize: '12px'
//                     }
//                 }
//             },
//             fill: {
//                 opacity: 1
//             },
//             states: {
//                 normal: {
//                     filter: {
//                         type: 'none',
//                         value: 0
//                     }
//                 },
//                 hover: {
//                     filter: {
//                         type: 'none',
//                         value: 0
//                     }
//                 },
//                 active: {
//                     allowMultipleDataPointsSelection: false,
//                     filter: {
//                         type: 'none',
//                         value: 0
//                     }
//                 }
//             },
//             tooltip: {
//                 style: {
//                     fontSize: '12px'
//                 },
//                 y: {
//                     formatter: function(val) {
//                         return "" + val + " Employee"
//                     }
//                 }
//             },
//             colors: [baseColor, secondaryColor, baseLightColor],
//             grid: {
//                 borderColor: borderColor,
//                 strokeDashArray: 4,
//                 yaxis: {
//                     lines: {
//                         show: true
//                     }
//                 },
//                 padding: {
//                     top: 0,
//                     right: 0,
//                     bottom: 0,
//                     left: 0
//                 }
//             }
//         };

//         chart.self = new ApexCharts(element, options);


//         setTimeout(function() {
//             chart.self.render();
//             chart.rendered = true;
//         }, 200);
//     }

//     // Public methods
//     return {
//         init: function(active, deactive, total, date_time, max_y) {
//             initChart(chart, active, deactive, total, date_time, max_y);

//             // Update chart on theme mode change
//             KTThemeMode.on("kt.thememode.change", function() {
//                 if (chart.rendered) {
//                     chart.self.destroy();
//                 }

//                 initChart(chart, active, deactive, total, date_time, max_y);
//             });
//         }
//     }
// }();


// function fetch_api_department_employee_graph() {
//     $('#department_employee_graph').html('<div class="spinner spinner-primary spinner-lg mr-15"></div>');
//     url = base_url + '/' + 'department-employee-graph';

//     fetch(url, {

//         // Adding method type
//         method: "POST",

//         // Adding body or contents to send
//         body: JSON.stringify({}),

//         // Adding headers to the request
//         headers: {
//             "Content-type": "application/json; charset=UTF-8",
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         }
//     })

//     // Converting to JSON
//     .then(response => {
//         // response.json()
//         if (response.ok) {
//             return response.json();
//         }
//         throw new Error('Something went wrong')
//     })


//     // Displaying results to console
//     .then(json => {
//         departmentEmployeeGraph.init(json.active, json.deactive, json.total, json.department, json.max_y);


//     }).catch(response => {
//         // console.log(response.status, response.statusText);

//     });
// }

// call department employee graph on default values
// fetch_api_department_employee_graph();


// head count office wise starts here //

var element2 = document.getElementById('head_count_office_wise');
var height2 = parseInt(KTUtil.css(element2, 'height'));
var labelColor2 = KTUtil.getCssVariableValue('--kt-gray-500');
var borderColor2 = KTUtil.getCssVariableValue('--kt-gray-200');
var baseColor2 = KTUtil.getCssVariableValue('--kt-warning');
var secondaryColor2 = KTUtil.getCssVariableValue('--kt-gray-300');

var options2 = {
    series: [{
        name: 'Total Employees',
        data: total_employees,
    }, {
        name: 'Male(s)',
        data: office_males,
    }, {
        name: 'Female(s)',
        data: office_females,
    }],
    chart: {
        fontFamily: 'inherit',
        type: 'bar',
        height: height2,
        toolbar: {
            show: false
        }
    },
    plotOptions: {
        bar: {
            horizontal: true,
            columnWidth: ['30%'],
            endingShape: 'rounded'
        },
    },
    legend: {
        show: false
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: employees_office,
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false
        },
        labels: {
            style: {
                colors: '#0A8AB4',
                fontSize: '12px'
            }
        }
    },
    yaxis: {
        labels: {
            style: {
                colors: '#0A8AB4',
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
            formatter: function(val) {
                return val + ' employees'
            }
        }
    },
    colors: ['#0A8AB4', '#0A8AB4'],
    grid: {
        borderColor: '#0A8AB4',
        strokeDashArray: 4,
        yaxis: {
            lines: {
                show: true
            }
        }
    }
};

var chart2 = new ApexCharts(element2, options2);
chart2.render();

// head count office wise ends here //



var ctx = document.getElementById('head_count_by_employment_wise');
var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
var successColor = KTUtil.getCssVariableValue('--kt-success');
var warningColor = KTUtil.getCssVariableValue('--kt-warning');
var infoColor = KTUtil.getCssVariableValue('--kt-info');
var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');
const labels = employement_type;
const data2 = {
    labels: labels,
    datasets: [{
        label: 'Dataset 1',
        data: employe_type_count,
        backgroundColor: ['#0A8AB4', '#0A8AB4', '#0A8AB4', '#0A8AB4', '#0A8AB4']
    }, ]
};

// Chart config
const config = {
    type: 'doughnut',
    data: data2,
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
            defaultFont: 'inherit'
        }
    }
};

// Init ChartJS -- for more info, please visit: https://www.chartjs.org/docs/latest/
var myChart = new Chart(ctx, config);












var ctx11 = document.getElementById('head_count_by_management_level');

// Define colors
var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
var successColor = KTUtil.getCssVariableValue('--kt-success');
var warningColor = KTUtil.getCssVariableValue('--kt-warning');
var infoColor = KTUtil.getCssVariableValue('--kt-info');

// Define fonts
var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');

// Chart labels
const labels11 = get_managers_wise_employee;

// Chart data
const data11 = {
    labels: labels11,
    datasets: [{
        label: 'Dataset 1',
        data: get_managers_wise_employee_count,
        backgroundColor: ['#0A8AB4', '#0A8AB4', '#0A8AB4', '#0A8AB4', '#0A8AB4']
    }, ]
};

// Chart config
const config11 = {
    type: 'doughnut',
    data: data11,
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
            defaultFont: 'inherit'
        }
    }
};

// Init ChartJS -- for more info, please visit: https://www.chartjs.org/docs/latest/
var myChart = new Chart(ctx11, config11);







































var ctx6 = document.getElementById('head_count_by_age_range');

// Define colors
var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
var successColor = KTUtil.getCssVariableValue('--kt-success');

// Define fonts
var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');

// Chart labels
const labels6 = ['1-10', '11-20', '21-30', '31-40', '41-50', '51-60', '61-70', '71-80'];

// Chart data
const data6 = {
    labels: labels6,
    datasets: [{
        label: ['Total Employees'],
        data: [employee_age_range[0].between_10_years, employee_age_range[0].between_20_years, employee_age_range[0].between_30_years, employee_age_range[0].between_40_years, employee_age_range[0].between_50_years, employee_age_range[0].between_60_years, employee_age_range[0].between_70_years, employee_age_range[0].between_80_years],
        backgroundColor: ['#0A8AB4']
    }]
};

// Chart config
const config6 = {
    type: 'bar',
    data: data6,
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

// Init ChartJS -- for more info, please visit: https://www.chartjs.org/docs/latest/
var myChart = new Chart(ctx6, config6);









var ctx7 = document.getElementById('head_count_by_designation_wise');

// Define colors
var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
var successColor = KTUtil.getCssVariableValue('--kt-success');

// Define fonts
var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');

// Chart labels
// const labels7 = ['1-10', '11-20', '21-30', '31-40', '41-50', '51-60', '61-70', '71-80'];
const labels7 = designation_wise_employees;

// Chart data
const data7 = {
    labels: labels7,
    datasets: [{
        label: ['Total Employees'],
        data: designation_wise_employees_count,
        backgroundColor: ['#0A8AB4']
    }]
};

// Chart config
const config7 = {
    type: 'bar',
    data: data7,
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

// Init ChartJS -- for more info, please visit: https://www.chartjs.org/docs/latest/
var myChart = new Chart(ctx7, config7);


















// Init ChartJS -- for more info, please visit: https://www.chartjs.org/docs/latest/
// var myChart = new Chart(ctx6, config6);









var ctx8 = document.getElementById('head_count_by_hiring_wise');

// Define colors
var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
var successColor = KTUtil.getCssVariableValue('--kt-success');

// Define fonts
var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');

// Chart labels
// const labels7 = ['1-10', '11-20', '21-30', '31-40', '41-50', '51-60', '61-70', '71-80'];
const labels8 = hiring_wise_employees;

// Chart data
const data8 = {
    labels: labels8,
    datasets: [{
        label: ['Total Employees Hired'],
        data: hiring_wise_employees_count,
        backgroundColor: ['#0A8AB4']
    }]
};

// Chart config
const config8 = {
    type: 'bar',
    data: data8,
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

// Init ChartJS -- for more info, please visit: https://www.chartjs.org/docs/latest/
var myChart = new Chart(ctx8, config8);






















var element3 = document.getElementById('head_count_tenure_wise');
var height3 = parseInt(KTUtil.css(element3, 'height'));
var labelColor3 = KTUtil.getCssVariableValue('--kt-gray-500');
var borderColor3 = KTUtil.getCssVariableValue('--kt-gray-200');
var baseColor3 = KTUtil.getCssVariableValue('--kt-warning');
var secondaryColor3 = KTUtil.getCssVariableValue('--kt-gray-300');

// if (!element2) {
//     return;
// }

var options3 = {
    series: [{
        name: 'Male(s)',
        data: [
            tenure_wise_employees[0].below_1_years_male,
            tenure_wise_employees[0].below_2_years_male,
            tenure_wise_employees[0].below_3_years_male,
            tenure_wise_employees[0].below_4_years_male,
            tenure_wise_employees[0].below_5_years_male,
            tenure_wise_employees[0].below_6_years_male,
            tenure_wise_employees[0].below_7_years_male
        ]
    }, {
        name: 'Female(s)',
        data: [
            tenure_wise_employees[0].below_1_years_female,
            tenure_wise_employees[0].below_2_years_female,
            tenure_wise_employees[0].below_3_years_female,
            tenure_wise_employees[0].below_4_years_female,
            tenure_wise_employees[0].below_5_years_female,
            tenure_wise_employees[0].below_6_years_female,
            tenure_wise_employees[0].below_7_years_female
        ]
    }],
    chart: {
        fontFamily: 'inherit',
        type: 'bar',
        height: height2,
        toolbar: {
            show: false
        }
    },
    plotOptions: {
        bar: {
            horizontal: true,
            columnWidth: ['30%'],
            endingShape: 'rounded'
        },
    },
    legend: {
        show: false
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        show: true,
        width: 2,
        colors: ['transparent']
    },
    xaxis: {
        categories: ['0-1 Yrs', '1-2 Yrs', '2-3 Yrs', '3-4 Yrs', '4-5 Yrs', '5-6 Yrs', '6-7 Yrs'],
        axisBorder: {
            show: false,
        },
        axisTicks: {
            show: false
        },
        labels: {
            style: {
                colors: '#0A8AB4',
                fontSize: '12px'
            }
        }
    },
    yaxis: {
        labels: {
            style: {
                colors: '#0A8AB4',
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
            formatter: function(val) {
                return val + ' employees'
            }
        }
    },
    colors: ['#0A8AB4', '#0A8AB4'],
    grid: {
        borderColor: '#0A8AB4',
        strokeDashArray: 4,
        yaxis: {
            lines: {
                show: true
            }
        }
    }
};

var chart3 = new ApexCharts(element3, options3);
chart3.render();