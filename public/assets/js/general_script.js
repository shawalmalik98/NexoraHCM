$('#downloadbutton').submit(function () {
    $('#download_btn').prop('disabled', true);
    $('#download_btn').attr('data-kt-indicator', 'on');
    $('#download_btn').css('cursor', 'not-allowed');
    $('#download_message').removeClass('d-none');
});

$(function () {
    dataTable = $('#system_datatable').DataTable({
        order: false,
        search: { regex: true },
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: 50

    });

    $('#searchFilter').keyup(function () {
        dataTable.search($(this).val()).draw();
    });

    $(".showpassword").on("input", function () {
        var inputVal = $(this).val();
        if (inputVal.length > 10) {
            // If the input exceeds 8 characters, truncate it to 8 characters
            $(this).val(inputVal.slice(0, 10));
        }
    });

})

function show_password_function(element, id) {
    var x = document.getElementById(id);
    if (x.type === "password") {
        $(element).find('i').attr('class', 'fa fa-eye-slash');
        x.type = "text";
    } else {
        $(element).find('i').attr('class', 'fa fa-eye');
        x.type = "password";
    }
}

$('#change_password_form').submit(function (e) {
    $('#password_form_btn').prop('disabled', true);
    $('#password_form_btn').attr('data-kt-indicator', 'on');
    $('#password_form_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/change_password_method',
        type: 'POST',
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            if (data.status == 'TRUE') {
                Toast.fire({
                    icon: 'success',
                    title: data.msg,
                    timer: 3000,
                })
                setTimeout(() => {
                    location.reload();
                }, 2000);

            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 8000,
                })
                $('#password_form_btn').prop('disabled', false);
                $("#password_form_btn").removeAttr('data-kt-indicator');
                $('#password_form_btn').css('cursor', 'pointer');

            }
        },
        error: function (jqXHR, textStatus) {
            $('#password_form_btn').prop('disabled', false);
            $('#password_form_btn').removeAttr('data-kt-indicator');
            $('#password_form_btn').css('cursor', 'pointer');
            var errorStatus = jqXHR.status;
            if (errorStatus == 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Internet Connection Problem',
                    timer: 3000,
                })
            } else {
                if (errorStatus == 419) {
                    location.reload();
                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Try Again. Error Code ' + errorStatus,
                        timer: 3000,
                    })
                }
            }
        }
    });
});


$(function () {

    var is_modal_show = localStorage.getItem('alreadyShows');
    if (is_modal_show == '' || is_modal_show != 'alredy shown') {
        $('#attendance_alert').modal('show');
        localStorage.setItem('alreadyShows', 'alredy shown');
    }

});

function calculateShiftDuration() {
    var start_time = $('#shift_start_timings').val();
    var end_time = $('#shift_end_timings').val();
    var time_start = new Date();
    var time_end = new Date();
    var value_start = start_time.split(':');
    var value_end = end_time.split(':');
    time_start.setHours(value_start[0], value_start[1], 0, 0)
    time_end.setHours(value_end[0], value_end[1], 0, 0)
    var duration_in_ms = time_end - time_start;
    var duration_period = moment.utc(duration_in_ms).format('HH:mm:ss');

    if (duration_period != 'Invalid date') {
        if (duration_period.substring(0, 2) >= '12') {
            $('#duration').css({ 'color': 'red' });
        } else {
            $('#duration').css({ 'color': 'black' });
        }
        $('#duration').val(duration_period);
    }
    if (duration_period == '00:00:00') {
        $('#duration').val('24:00:00');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    $('#employee_data, #employee_location').change(function () {
        var selected_val = $('#employee_data').find(":selected").val();
        var employee_location = $('#employee_location').find(":selected").val();
        $('#company_emp_name').text('');
        $.ajax({
            url: base_url + '/get_employees',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                status: selected_val,
                employee_location: employee_location
            },
            success: function (data) {
                $('#company_emp_name').append(data)
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
});







$('.notification-open').click(function () {
    var notificationid = $(this).data('notificationid');
    $.ajax({
        url: base_url + '/read-notification',
        type: 'GET',
        data: { notificationid: notificationid },
    });
});

$('.clickable-row').click(function () {
    window.location = base_url + '/' + $(this).data("href");
});


runPopover();

function runPopover() {
    document.querySelectorAll('.employee-hover').forEach(item => {
        let profile, name;
        item.addEventListener('mouseenter', function (event) {
            this.timeout = window.setTimeout(function () {

                profile = event.target.dataset.profile;
                name = event.target.dataset.name;
                department = event.target.dataset.department;
                designation = event.target.dataset.designation;

                let profile_img;
                if (!profile || profile != "null") {
                    profile_img = `<img src="${base_url}/portal_assets/profile_pictures/${profile}" class="img-circle elevation-2" alt="${name} - Profile Picture" style="height: 83px;width: 80px; object-fit: cover;">`;
                } else {
                    profile_img = `<img src="${base_url}/portal_assets/images/default-img.png" class="img-circle elevation-2" alt="${name} - Profile Picture" style="height: 83px;width: 80px; object-fit: cover;">`;
                }

                let popover_html = `
                <div class="popover-item-box">
                    <div class="qsae">
                        <div class="tdf1">
                            ${profile_img}
                        </div>
                        <div class="tdf2">
                            <h3> ${name}</h3>
                            <h5> ${department} - ${designation}</h5>
                        </div>
                    </div>
                </div>`;

                $(popover_html).insertAfter(item);

                setTimeout(() => {
                    $(".popover-item-box").css({
                        'visibility': 'visible',
                        'opacity': '1',
                        'transition': 'visibility 0s linear 0s, opacity 600ms'
                    });
                }, 200);

            }, 200)
        });
        item.addEventListener('mouseleave', function () {
            if (this.timeout) {
                window.clearTimeout(this.timeout);
                $(".popover-item-box").css({
                    'visibility': 'hidden',
                    'opacity': '0',
                    'transition': 'visibility 0s linear 600ms, opacity 600ms'

                });

                setTimeout(() => {
                    $(".popover-item-box").remove();
                }, 0);

            }
        });
    })
}

runPopoverCandidate();

function runPopoverCandidate() {
    document.querySelectorAll('.candidate-hover').forEach(item => {
        let contact, name;
        item.addEventListener('mouseenter', function (event) {
            this.timeout = window.setTimeout(function () {

                name = event.target.dataset.name;
                contact = event.target.dataset.contact;

                let popover_html = `
                <div class="popover-item-box">
                    <div class="qsae">
                        <div class="tdf2">
                            <h5> Candidate Name : ${name}</h5>
                            <h5> Candidate Phone No : ${contact}</h5>
                        </div>
                    </div>
                </div>`;

                $(popover_html).insertAfter(item);

                setTimeout(() => {
                    $(".popover-item-box").css({
                        'visibility': 'visible',
                        'opacity': '1',
                        'transition': 'visibility 0s linear 0s, opacity 600ms'
                    });
                }, 200);

            }, 200)
        });
        item.addEventListener('mouseleave', function () {
            if (this.timeout) {
                window.clearTimeout(this.timeout);
                $(".popover-item-box").css({
                    'visibility': 'hidden',
                    'opacity': '0',
                    'transition': 'visibility 0s linear 600ms, opacity 600ms'

                });

                setTimeout(() => {
                    $(".popover-item-box").remove();
                }, 0);

            }
        });
    })
}


let timeOut = {};

function checkValidationExists(inputElement, dataType, source, employee_id) {
    const statusElement = inputElement.nextElementSibling;
    const value = inputElement.value;

    clearTimeout(timeOut[inputElement.id]);

    timeOut[inputElement.id] = setTimeout(() => {

        let apiUrl = '';
        let resource = '';
        switch (dataType) {
            case 'email':
                resource = source == 1 ? 'email' : 'email_update';
                console.log(resource);
                apiUrl = base_url + `/employees/check-validation?${resource}=${encodeURIComponent(value)}&employee_id=${employee_id}`;
                break;
            case 'contact no':
                resource = source == 1 ? 'contact' : 'contact_update';
                apiUrl = base_url + `/employees/check-validation?${resource}=${encodeURIComponent(value)}&employee_id=${employee_id}`;
                break;
            case 'cnic no':
                resource = source == 1 ? 'cnic' : 'cnic_update';
                apiUrl = base_url + `/employees/check-validation?${resource}=${encodeURIComponent(value)}&employee_id=${employee_id}`;
                break;

            default:
                return;
        }
        const xhr = new XMLHttpRequest();

        xhr.open('GET', apiUrl, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);

                    if (response.exists) {
                        statusElement.textContent = `This ${dataType} already exists.`;
                        statusElement.style.color = 'red';
                    } else {
                        statusElement.textContent = '';

                    }
                }
            }
        };
        xhr.send();
    }, 1000);
}

let previousCard = null;
function handleCardClick(event) {

    const card = event.target.closest('.card'); // Find the closest ancestor with class 'card'
    if (!card) {
        return;
    }

    if (previousCard !== null) {
        resetCardStyles(previousCard);
    }
    card.style.border = '1px solid #009ef7';
    card.style.borderRadius = '10px';
    previousCard = card;
}

const cardContainer = document.getElementById('card-container');
if (cardContainer != null) {
    cardContainer.addEventListener('click', handleCardClick);
}


function resetCardStyles(card) {
    card.style.border = '';
    card.style.borderRadius = '';

}


$('.filter-record').on('click', function () {
    var value_search = '';
    value = $(this).attr('data-value');
    value = value.split(',');

    for (let i = 0; i < value.length; i++) {
        if (i == value.length - 1) {
            value_search += value[i];
        } else {
            value_search += value[i] + '|';
        }

        dataTable.search(`${value_search}`, true, false).draw();
    }

});

$('.manager_modal_status_btn').click(function () {
    request_id = $(this).attr('data-request-id');
    request_response_log(request_id, 7);
});

function get_one_month_future_date(from_date) {
    var date = new Date(from_date);
    date.setDate(date.getDate() + 30);
    var future_date = date.getFullYear() + '-' + ('0' + (date.getMonth() + 1)).slice(-2) + '-' + ('0' + date.getDate()).slice(-2);
    return future_date;
}

function get_two_month_future_date() {
    var dates = new Date();
    dates.setDate(dates.getDate());
    var future_dates = dates.getFullYear() + '-' + ('0' + (dates.getMonth() + 3)).slice(-2) + '-' + ('0' + dates.getDate()).slice(-2);
    return future_dates;
}

if (document.querySelector('#meeting_description') != null) {
    ClassicEditor
        .create(document.querySelector('#meeting_description'))
        .then(meeting_description => {
        })
        .catch(error => {
            console.error(error);
        });
}    
