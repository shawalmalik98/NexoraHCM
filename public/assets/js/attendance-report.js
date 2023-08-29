

// head count office wise ends here //



var ctx = document.getElementById('attendance_check_in_device_source_wise');
var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
var successColor = KTUtil.getCssVariableValue('--kt-success');
var warningColor = KTUtil.getCssVariableValue('--kt-warning');
var infoColor = KTUtil.getCssVariableValue('--kt-info');
var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');
const labels = att_count_check_in_wise_label;
const data = {
    labels: labels,
    datasets: [{
        label: 'Dataset 1',
        data: att_count_check_in_wise_data,
        backgroundColor: [primaryColor,successColor,dangerColor,infoColor]
    }, ]
};

// Chart config
const config = {
    type: 'doughnut',
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
            defaultFont: 'inherit'
        }
    }
};

var myChart = new Chart(ctx, config);



var ctx2 = document.getElementById('attendance_check_out_device_source_wise');
var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
var successColor = KTUtil.getCssVariableValue('--kt-success');
var warningColor = KTUtil.getCssVariableValue('--kt-warning');
var infoColor = KTUtil.getCssVariableValue('--kt-info');
var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');
const labels2 = att_count_check_out_wise_label;
const data2 = {
    labels: labels2,
    datasets: [{
        label: 'Dataset 1',
        data: att_count_check_out_wise_data,
        backgroundColor: [primaryColor,successColor,dangerColor,infoColor]
    }, ]
};

// Chart config
const config2 = {
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

var myChart = new Chart(ctx2, config2);




var ctx3 = document.getElementById('attendance_over_all_device_source_wise');
var primaryColor = KTUtil.getCssVariableValue('--kt-primary');
var dangerColor = KTUtil.getCssVariableValue('--kt-danger');
var successColor = KTUtil.getCssVariableValue('--kt-success');
var warningColor = KTUtil.getCssVariableValue('--kt-warning');
var infoColor = KTUtil.getCssVariableValue('--kt-info');
var fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif');
const labels3 = over_all_attendance_wise_label;
const data3 = {
    labels: labels3,
    datasets: [{
        label: 'Dataset 1',
        data: over_all_attendance_wise_data,
        backgroundColor: [infoColor,successColor,dangerColor,primaryColor]
    },]
};

// Chart config
const config3 = {
    type: 'doughnut',
    data: data3,
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

var myChart = new Chart(ctx3, config3);