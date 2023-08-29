var effected_date_old;
var shift_start_old;
var shift_end_old;
var employee_id = [];
var record = '';
$('#employee_all_calculate').attr("disabled", true);
$('#department_all_calculate').click(function () {

    var checkbox = $('#department_all_calculate').prop('checked');

    if (checkbox) {
        $('#employee_all_calculate').removeAttr("disabled");
        $('#departments_all option').prop('selected', true);
        $('#selectDepartmentCalculate').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-departments_all-container-choice-5nle-Human Resource">All Departments</span>');
        $('#selectDepartmentCalculate').find('.select2-search__field').attr('placeholder', '');

    } else {

        $('#employee_all_calculate').attr("disabled", true);
        $('#employee_all_calculate').prop('checked', false);
        $('#employees option').prop('selected', false);
        $('#selectEmployeeCalculate').find('.select2-selection__rendered').html('');
        $('#selectEmployeeCalculate').find('.select2-search__field').attr('placeholder', 'Select Employees');
        $('#departments_all option').prop('selected', false);
        $('#selectDepartmentCalculate').find('.select2-selection__rendered').html('');
        $('#selectDepartmentCalculate').find('.select2-search__field').attr('placeholder', 'Select Department');
        $('#selectDepartmentCalculate').find('.select2-search__field').css('width', '100%');

    }

});

$('#employee_all_calculate').click(function () {

    var checkbox = $('#employee_all_calculate').prop('checked');

    if (checkbox) {
        $('#employees option').prop('selected', true);
        $('#selectEmployeeCalculate').find('.select2-selection__rendered').html('<span class="select2-selection__choice__display" id="select2-employees-container-choice-5nle-Zain Shaikh">All Employees</span>');
        $('#selectEmployeeCalculate').find('.select2-search__field').attr('placeholder', '');
    } else {
        $('#employees option').prop('selected', false);
        $('#selectEmployeeCalculate').find('.select2-selection__rendered').html('');
        $('#selectEmployeeCalculate').find('.select2-search__field').attr('placeholder', 'Select Employees');
        $('#selectEmployeeCalculate').find('.select2-search__field').css('width', '100%');

    }

});

$('#responseEmployeeShiftForm').submit(function (e) {
    e.preventDefault()
    send_server_request('POST', 'shift-management/add-employee-shift', this, 'responseEmployeeShiftFormButton', "No", 'shift-management', 3000)
});

$('.departments_all').change(function (e) {

    e.preventDefault();
    $('#employee_all_calculate').removeAttr("disabled");
    $('#employees').html('');
    var dept = $('#departments_all').val();
    if (dept.length == 0) {
        $('#employee_all_calculate').attr("disabled", true);
        $('#employee_all_calculate').prop('checked', false);
    }
    $.ajax({

        url: base_url + '/get-employee-department',
        type: 'GET',

        data: {
            department: dept
        },
        success: function (response) {
            $('#employees').append(response);

        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                alert("Internet Connection Problem");
            } else {
                alert("Try Again. Error Code " + errorStatus);
            }

        }
    });
});

$('#shiftEmployeeForm').change(function (e) {

    e.preventDefault();

    var url = base_url + '/shift-management/get-employee-shift';

    $('#responseEmployeeShiftSubmitDiv').addClass('d-none')

    let form_data = new FormData(this);

    body_data = {

        // Adding method type
        method: 'POST',

        // Adding body or contents to send
        // body: JSON.stringify({form_data}),
        body: form_data,

        // Adding headers to the request
        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

                $('.odd').remove()
                record = '';

                var current_employee = [];
                var fillDate = $('#fillDate').val();
                var fillStartTime = $('#fillStartTime').val();
                var fillEndTime = $('#fillEndTime').val();



                $.each(json.data, function (key, employee_shift) {

                    current_employee.push(employee_shift.employee_id);

                    if (employee_id.indexOf(employee_shift.employee_id) === -1) {

                        record += `<tr class="dataRow${employee_shift.employee_id}" >
            <td class="ps-4" >
                <div class="form-check form-check-sm form-check-custom form-check-solid">
                    <input class="form-check-input checked"  type="checkbox" checked onClick="checkedBox(this)"  />
                </div>
            </td>
            <td><input type="hidden" name="employee_id[]" value="${employee_shift.employee_id}"/> ${employee_shift.employee_name} - ${employee_shift.employee_code}</td>
            <td>${employee_shift.shift_start_from_date}</td>
            <td>${employee_shift.shift_start_time}</td>
            <td>${employee_shift.shift_end_time}</td>
            <td><input type="date" class="form-control effected_date_input ${(fillDate == '') ? '' : 'has-time'}" name="effected_date[]" placeholder="" required="" min="${json.date_max}" onInput="borderClassDate(this)" value="${fillDate}" ></td>
            <td><input type="time" class="form-control shift_start_time_input ${(fillStartTime == '') ? '' : 'has-time'}" name="shift_start_time[]" placeholder="Shift Start Time" required="" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" onInput="borderClassStartTime(this);calculateDuration(this)"  value="${fillStartTime}"></td>
            <td><input type="time" class="form-control shift_end_time_input ${(fillEndTime == '') ? '' : 'has-time'}" name="shift_end_time[]" placeholder="Shift End Time" required="" pattern="^([0-1]?[0-9]|2[0-4]):([0-5][0-9])(:[0-5][0-9])?$" onInput="borderClassEndTime(this);calculateDuration(this)"  value="${fillEndTime}" ></td>
            <td><span> </span></td>
            </tr>`;

                    }
                })


                $('#responseEmployeeShift').append(record)

                $('#responseEmployeeShiftSubmitDiv').removeClass('d-none')

                $.each(employee_id, function (key, value) {
                    if (current_employee.indexOf(value) == -1) {
                        removeTd(value)
                    }
                });

                employee_id = current_employee;
                $(".effected_date_input,.shift_start_time_input,.shift_end_time_input").trigger("input");

            } else {
                $('#responseEmployeeShift').html('')
                employee_id = [];
            }

        }).catch(response => {

        });


});

function fillAllDate(e) {
    var effected_date = $(e).val();
    $('.effected_date_input').each(function (index, element) {
        var effected_date_input = $(this).val()
        if (effected_date_input == "" || effected_date_input == effected_date_old) {
            $(this).val(effected_date);
            $(this).addClass('has-time');
            $(e).addClass('has-time');
        } else {
            $(this).removeClass('has-time');
        }
    });
    effected_date_old = effected_date;
}

function fillAllStartTime(e) {
    var shift_start = $(e).val();
    $('.shift_start_time_input').each(function (index, element) {
        var check_shift_start = $(this).val()
        if (check_shift_start == "" || check_shift_start == shift_start_old) {
            $(this).val(shift_start);
            $(this).addClass('has-time');
            $(e).addClass('has-time');
        } else {
            $(this).removeClass('has-time');
        }
        calculateDuration(this);
    });
    shift_start_old = shift_start;
}

function fillAllEndTime(e) {
    var shift_end = $(e).val();
    $('.shift_end_time_input').removeClass('has-time')
    $('.shift_end_time_input').each(function (index, element) {
        var check_shift_end = $(this).val()
        if (check_shift_end == "" || check_shift_end == shift_end_old) {
            $(this).val(shift_end);
            $(this).addClass('has-time');
            $(e).addClass('has-time');
        } else {
            $(this).removeClass('has-time');
        }
        calculateDuration(this);
    });
    shift_end_old = shift_end;
}

function borderClassDate(e) {
    var effected_date = $(e).val();
    if (effected_date == effected_date_old) {
        $(e).addClass('has-time');
    } else {
        $(e).removeClass('has-time');
    }
}
function borderClassStartTime(e) {
    var shift_start = $(e).val();
    if (shift_start == shift_start_old) {
        $(e).addClass('has-time');
    } else {
        $(e).removeClass('has-time');
    }
}
function borderClassEndTime(e) {
    var shift_end = $(e).val();
    if (shift_end == shift_end_old) {
        $(e).addClass('has-time');
    } else {
        $(e).removeClass('has-time');
    }
}

function calculateDuration(e) {

    var main_class = $(e).attr("class");

    const substring = "shift_start_time_input";
    if (main_class.includes(substring)) {
        var start_time = $(e).val();
        var end_time = $(e).closest('td').next('td').find('input').val();
    } else {
        var end_time = $(e).val();
        var start_time = $(e).closest('td').prev('td').find('input').val();
    }

    var time_start = new Date();
    var time_end = new Date();
    var value_start = start_time.split(':');
    var value_end = end_time.split(':');

    time_start.setHours(value_start[0], value_start[1], 0, 0);
    time_end.setHours(value_end[0], value_end[1], 0, 0);
    var duration_in_ms = time_end - time_start;
    var duration_period = moment.utc(duration_in_ms).format('HH:mm:ss');
    if (main_class.includes(substring)) {
        if (duration_period != 'Invalid date') {
            $(e).closest('td').next('td').next('td').find('span').html(duration_period);
        }
    } else {
        if (duration_period != 'Invalid date') {
            $(e).closest('td').next('td').find('span').html(duration_period);
        }
    }


}

function checkedBox(e) {

    if ($(e).is(':checked')) {
        $(e).closest('td').next('td').find('input').prop('name', 'employee_id[]')

        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('required', true)
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('name', 'effected_date[]')

        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('required', true)
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('name', 'shift_start_time_input[]')

        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('required', true)
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('name', 'shift_end_time_input[]')
    } else {
        $(e).closest('td').next('td').find('input').prop('name', '')

        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('required', false)
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('name', '')
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').find('input').val('')
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').find('input').removeClass('has-time')

        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('required', false)
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('name', '')
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').val('')
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').removeClass('has-time')

        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('required', false)
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').prop('name', '')
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').val('')
        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').next('td').find('input').removeClass('has-time')

        $(e).closest('td').next('td').next('td').next('td').next('td').next('td').next('td').next('td').next('td').find('span').html('')
    }

}

function removeTd(value) {
    $('.dataRow' + value).remove();
}