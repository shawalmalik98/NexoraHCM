$(function () {
    $('.select').select2({
        dropdownParent: $('#schedule_interview .modal-content')
    })
    $('#interview_type').trigger('change');
});

$('#add_interview').submit(function (e) {
    $('#submit_btn').prop('disabled', true);
    $('#submit_btn').attr('data-kt-indicator', 'on');
    $('#submit_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/interviews/scheduleinterview',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
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
                }, 3000);
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 5000,
                })
                $('#submit_btn').prop('disabled', false);
                $('#submit_btn').removeAttr('data-kt-indicator');
                $('#submit_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
            $('#submit_btn').prop('disabled', false);
            $('#submit_btn').removeAttr('data-kt-indicator');
            $('#submit_btn').css('cursor', 'pointer');
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

$("#candidate_interview_name").keydown(function () {
    var candidates = candidate_interviews;
    var candidate_name = [];
    if (candidates) {
        $.each(candidates, function (key, interview_candidate) {
            candidate_name.push(interview_candidate.candidate_name);
        });
        autocomplete(document.getElementById("candidate_interview_name"), candidate_name);
    }
});

function autocomplete(input, candidate_name) {

    var currentFocus;
    input.addEventListener("input", function (e) {
        var get_value = this.value;
        var a, b, i;

        closeDropDown();
        if (!get_value) { return false; }
        currentFocus = -1;

        a = document.createElement("DIV");
        a.setAttribute("id", this.id + "autocomplete-list");
        a.setAttribute("class", "autocomplete-items");

        this.parentNode.appendChild(a);

        for (i = 0; i < candidate_name.length; i++) {

            if (candidate_name[i].substr(1, get_value.length).toUpperCase() == get_value.toUpperCase()) {

                b = document.createElement("DIV");

                b.innerHTML = "<strong>" + candidate_name[i].substr(0, get_value.length) + "</strong>";
                b.innerHTML += candidate_name[i].substr(get_value.length);

                b.innerHTML += "<input type='hidden' value='" + candidate_name[i] + "'>";

                b.addEventListener("click", function (e) {

                    input.value = this.getElementsByTagName("input")[0].value;

                    closeDropDown();
                });
                a.appendChild(b);
            }
        }

    });

    function closeDropDown(elmnt) {
        var close_dropdown = document.getElementsByClassName("autocomplete-items");
        for (var i = 0; i < close_dropdown.length; i++) {
            if (elmnt != close_dropdown[i] && elmnt != input) {
                close_dropdown[i].parentNode.removeChild(close_dropdown[i]);
            }
        }
    }

    document.addEventListener("click", function (e) {
        closeDropDown(e.target);
    });

}


$("#interview_daterangepicker").daterangepicker({
    locale: {
        format: 'YYYY-MM-DD'
    },
    singleDatePicker: true,
    showDropdowns: true,
    minYear: 1901,
    minDate: new Date(),
    maxYear: parseInt(moment().format("YYYY"), 12)
}, function (start, end, label) {
    var years = moment().diff(start, "years");
}
);


$('#interview_type').change(function () {
    var interview_type = $('#interview_type').find(":selected").val();

    $('.child-div-interview-type').removeClass('d-none')

    if (interview_type == 'Online') {
        $('#label-interview-room').removeClass('required');
        $('#label-interview-link').addClass('required');
        $('#interview_room').prop('required', false);
        $('#interview_link').prop('required', true);

    } else {
        $('#label-interview-link').removeClass('required');
        $('#label-interview-room').addClass('required');
        $('#interview_link').prop('required', false);
        $('#interview_room').prop('required', true);

    }
})

$('.interview_request').on('change', function (e) {

    var interview_request = $(this).val();

    if (interview_request == "") {

        $('input[name="new_candidate"]').val('new');
        $('input[name="contact_number"]').prop('readonly', false);
        $('input[name="candidate_cv"]').prop('required', true);
        $('#candidate_cv').addClass('required');

        $('input[name="candidate_id"]').val('');
        $('#candidate_interview_name').val('');
        $('input[name="current_salary"]').val('');
        $('input[name="expected_salary"]').val('');
        $('input[name="candidate_email"]').val('');
        $('input[name="contact_number"]').val('');


    } else {

        var contact = $(this).find('option:selected').data('contact');

        $('input[name="new_candidate"]').val('old');

        if (contact == "") {
            $('input[name="contact_number"]').prop('readonly', false);
        } else {
            $('input[name="contact_number"]').prop('readonly', true);
        }

        $('input[name="candidate_cv"]').prop('required', false);
        $('#candidate_cv').removeClass('required');
     
        var name = $(this).find('option:selected').data('name');
        var current_salary = $(this).find('option:selected').data('current-sal');
        var expected_salary = $(this).find('option:selected').data('exp-sal');
        var candidate = $(this).find('option:selected').data('id');
        var candidate_email = $(this).find('option:selected').data('email');

        $('input[name="candidate_id"]').val(candidate);
        $('#candidate_interview_name').val(name);
        $('input[name="current_salary"]').val(current_salary);
        $('input[name="expected_salary"]').val(expected_salary);
        $('input[name="contact_number"]').val(contact);
        $('input[name="candidate_email"]').val(candidate_email);
    }

})

$('.requisition_request').on('change', function () {
    var title = $(this).find('option:selected').data('title');
    var department = $(this).find('option:selected').data('department');
    var employee = $(this).find('option:selected').data('employee');
    var requisition = $(this).find('option:selected').data('id');
    $('#requisition_id').val(requisition);
    $('input[name="interview_jobtitle"]').val(title);
    $('#requistion_department').val(department).trigger('change');
    $('#requisition_interviewer').val(employee).trigger('change');
});

$('#interview-date').change(function () {
    $('#interview-month').val('');
});

$('#interview-month').change(function () {
    $('#interview-date').val('');
});