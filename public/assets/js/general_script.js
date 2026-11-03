$('#downloadbutton').submit(function () {
    $('#download_btn').prop('disabled', true);
    $('#download_btn').attr('data-kt-indicator', 'on');
    $('#download_btn').css('cursor', 'not-allowed');
    $('#download_message').removeClass('d-none');
});

$(function () {
    // tables marked data-server-paginated get their rows page by page from the server:
    // no client paging, and the search box searches on the server
    var serverPaginated = $('table[data-server-paginated]').length > 0;

    dataTable = $('#system_datatable').DataTable(serverPaginated ? {
        order: false,
        paging: false,
        info: false,
        searching: false
    } : {
        order: false,
        search: { regex: true },
        aLengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        iDisplayLength: 50

    });

    if (serverPaginated) {
        var searchParams = new URLSearchParams(window.location.search);
        $('#searchFilter').val(searchParams.get('search') || '');
        var searchTimer;
        $('#searchFilter').on('keyup', function (e) {
            var value = $(this).val();
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                var params = new URLSearchParams(window.location.search);
                if (value) { params.set('search', value); } else { params.delete('search'); }
                params.delete('page');
                window.location.search = params.toString();
            }, e.key === 'Enter' ? 0 : 600);
        });
    } else {
        $('#searchFilter').keyup(function () {
            dataTable.search($(this).val()).draw();
        });
    }

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







// unread notification clicked: show it read (dark grey, no bisque) at once, save it as read, then open its page
// (opening the page first could cancel the save, leaving it unread)
$(document).on('click', '.notification-open', function (e) {
    var link = $(this);
    var notificationid = link.data('notificationid');
    var href = link.attr('href');
    e.preventDefault();
    link.removeClass('text-dark notification-open notification-unread').addClass('text-gray-600 notification-read');
    link.find('i.fa-clock').css('color', '');
    var opened = false;
    var open = function () {
        if (!opened) {
            opened = true;
            window.location = href;
        }
    };
    $.ajax({
        url: base_url + '/read-notification',
        type: 'GET',
        data: { notificationid: notificationid },
        complete: open,
    });
    setTimeout(open, 1500);
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
                    profile_img = `<img src="${base_url}/portal_assets/images/hcm-img.png" class="img-circle elevation-2" alt="${name} - Profile Picture" style="height: 83px;width: 80px; object-fit: cover;">`;
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
    // server paginated tables filter on the server: clicking a card toggles ?status=
    if ($('table[data-server-paginated]').length > 0) {
        var params = new URLSearchParams(window.location.search);
        var status = $(this).attr('data-value');
        if (params.get('status') === status) { params.delete('status'); } else { params.set('status', status); }
        params.delete('page');
        window.location.search = params.toString();
        return;
    }
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

// Employee dropdowns (<select data-employee-select data-scope="team|active|managers" data-status="1|0|all">)
// search employees on the server instead of rendering every employee as an option.
function initEmployeeSelect(element) {
    var $select = $(element);
    if ($select.data('employee-select-ready')) {
        return;
    }
    if ($select.hasClass('select2-hidden-accessible')) {
        $select.select2('destroy');
    }
    var $modal = $select.closest('.modal');
    $select.select2({
        dropdownParent: $modal.length ? $modal : $(document.body),
        placeholder: $select.data('placeholder') || 'Search employee',
        allowClear: !$select.prop('required') && !$select.prop('multiple'),
        minimumInputLength: 0,
        ajax: {
            url: base_url + '/employee-search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var request = {
                    q: params.term || '',
                    page: params.page || 1,
                    scope: $select.data('scope') || 'team',
                    status: $select.data('status') || '1'
                };
                // data-departments-from="#select": only employees of the departments picked there
                if ($select.data('departments-from')) {
                    request.departments = $($select.data('departments-from')).val() || [];
                }
                return request;
            },
            processResults: function (data, params) {
                var results = data.results || [];
                // keep the "all" style empty option of filters selectable
                var $empty = $select.find('option[value=""]');
                if ($empty.length && (params.page || 1) == 1 && !params.term) {
                    results = [{ id: '', text: $empty.text() }].concat(results);
                }
                return { results: results, pagination: { more: data.pagination && data.pagination.more } };
            }
        }
    });
    element.setAttribute('data-kt-initialized', '1');
    $select.data('employee-select-ready', true);
}

function initEmployeeSelects(context) {
    $(context || document).find('select[data-employee-select]').each(function () {
        initEmployeeSelect(this);
    });
}

// Edit forms set employees with .val(id): add the option first (labelled from the server) so the value sticks
(function ($) {
    var originalVal = $.fn.val;
    $.fn.val = function (value) {
        if (arguments.length && value !== null && value !== undefined) {
            this.each(function () {
                if (this.tagName !== 'SELECT' || !this.hasAttribute('data-employee-select')) {
                    return;
                }
                var $select = $(this);
                var values = $.isArray(value) ? value : [value];
                var missing = [];
                values.forEach(function (v) {
                    if (v === '' || v === null) { return; }
                    if (!$select.find('option').filter(function () { return this.value == v; }).length) {
                        $select.append(new Option('#' + v, v, false, false));
                        missing.push(v);
                    }
                });
                if (missing.length) {
                    $.getJSON(base_url + '/employee-search', { ids: missing.join(','), scope: 'active', status: 'all' }, function (data) {
                        (data.results || []).forEach(function (row) {
                            $select.find('option').filter(function () { return this.value == row.id; }).text(row.text);
                        });
                        $select.trigger('change.select2');
                    });
                }
            });
        }
        return originalVal.apply(this, arguments);
    };
})(jQuery);

$(function () {
    initEmployeeSelects();
    // Metronic may initialise select2 after us; take the employee selects back
    setTimeout(function () {
        $('select[data-employee-select]').each(function () {
            $(this).data('employee-select-ready', false);
            initEmployeeSelect(this);
        });
    }, 0);
});
