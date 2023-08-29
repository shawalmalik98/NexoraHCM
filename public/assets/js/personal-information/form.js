$(function () {
    $("#fileupload").change(function (event) {
        var x = URL.createObjectURL(event.target.files[0]);
        $("#upload-img").attr("src", x);
    });
});

const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
});


function timer_ajax() {

    let myform = $("#personal_information_form")[0];
    $.ajax({
        url: base_url + '/savemypersonalinformation',
        type: 'POST',
        data: new FormData(myform),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
            console.log("success")
        },
        error: function (jqXHR, textStatus) {
            console.log(textStatus);
        }
    });
}

function runner() {
    timer_ajax();
    setTimeout(function () {
        runner();
    }, 20000);
}
var disabled = disabled_option;
if (disabled == 'TRUE') {
    $(".btn-group,.submit-btn-form,.custom-file").hide();
    $('input').attr('readonly', true);
    $('option:not(:selected)').attr('disabled', true);
} else {
    runner();
}


function appliedBefore(e) {
    if (e == 'Yes') {
        $(".appliedBeforeDiv").removeClass('d-none');
    } else {
        $(".appliedBeforeDiv").addClass('d-none');
    }
}


// Employement History ==============================
var employement_history;

function addEmployeeRow() {

    employement_history = $("input[name=employement_history_length]").val();
    if (employement_history >= 5) {
        toastFire();
        return true;
    }
    employement_history++;
    $("input[name=employement_history_length]").val(employement_history);

    $("#employee_history_container").append(`
        <tr id="employee_row${employement_history}">
            <td scope="row"><input type="text" class="form-control" name="employername${employement_history}" value=""></td>
            <td><input type="text" class="form-control" name="designation${employement_history}" value=""></td>
            <td><input type="text" class="form-control" name="salaryandbenefits${employement_history}" value=""></td>
            <td>
                <table>
                    <tr>
                        <td>
                                <input type="date" class="form-control" name="durationstartdate${employement_history}" value="">
                        </td>
                        <td>
                                <input type="date" class="form-control" name="durationenddate${employement_history}" value="">
                        </td>
                    </tr>
                </table>

            </td>
            <td><input type="text" class="form-control" name="reasonforleaving${employement_history}" value=""></td>
        </tr>
    `);
}

function deleteEmployeeRow() {
    employement_history = $("input[name=employement_history_length]").val();
    if (employement_history > 1) {
        if (confirm("Are you sure to delete this last record") == true) {
            $("#employee_row" + employement_history).remove();
            employement_history--;
            $("input[name=employement_history_length]").val(employement_history);
        }

    }
}

// Academic Credential History ==============================
var academic_credential;

function addAcademicRow() {
    academic_credential = $("input[name=academic_credential_length]").val();
    if (academic_credential >= 5) {
        toastFire();
        return true;
    }
    academic_credential++;
    $("input[name=academic_credential_length]").val(academic_credential);

    $("#academic_credential_container").append(`
        <tr id="academic_row${academic_credential}">
            <td scope="row"><input type="text" class="form-control" name="degree${academic_credential}" value=""></td>
            <td><input type="text" class="form-control" name="intitution${academic_credential}" value=""></td>
            <td><input type="text" class="form-control" name="majors${academic_credential}" value=""></td>
            <td><input type="text" class="form-control" name="passingyear${academic_credential}" value=""></td>
            <td><input type="text" class="form-control" name="grade${academic_credential}" value=""></td>

        </tr>
    `);
}

function deleteAcademicRow() {
    academic_credential = $("input[name=academic_credential_length]").val();
    if (academic_credential > 1) {
        if (confirm("Are you sure to delete this last record") == true) {
            $("#academic_row" + academic_credential).remove();
            academic_credential--;
            $("input[name=academic_credential_length]").val(academic_credential);
        }
    }
}



// Professional Qualification History ==============================
var professional_qualification;

function addQualificationRow() {
    professional_qualification = $("input[name=professional_qualification_length]").val();

    if (professional_qualification >= 5) {
        toastFire();
        return true;
    }
    professional_qualification++;
    $("input[name=professional_qualification_length]").val(professional_qualification);

    $("#professional_qualification_container").append(`
        <tr id="qualification_row${professional_qualification}">
            <td scope="row"><input type="text" class="form-control" name="type${professional_qualification}" value=""></td>
            <td><input type="text" class="form-control" name="details${professional_qualification}" value=""></td>
            <td><input type="text" class="form-control" name="institute${professional_qualification}" value=""></td>
            <td><input type="text" class="form-control" name="from${professional_qualification}" value=""></td>
            <td><input type="text" class="form-control" name="till${professional_qualification}" value=""></td>

        </tr>
    `);
}

function deleteQualificationRow() {
    professional_qualification = $("input[name=professional_qualification_length]").val();
    if (professional_qualification > 1) {
        if (confirm("Are you sure to delete this last record") == true) {
            $("#qualification_row" + professional_qualification).remove();
            professional_qualification--;
            $("input[name=professional_qualification_length]").val(professional_qualification);

        }
    }
}

function toastFire() {
    Toast.fire({
        icon: 'warning',
        title: 'You are not allowed to enter more than 5 records',
        timer: 3000,
    })
}