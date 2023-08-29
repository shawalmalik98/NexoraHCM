$(function () {
    $('#searchFilter').keyup(function () {
        dataTable.search($(this).val()).draw();
    })

    $('.vehicleselect').select2();
});

Dropzone.autoDiscover = false;
var myDropzone = new Dropzone("#upload", {
    maxFiles: 15,
    maxFilesize: 500,
    timeout: 60000,
    acceptedFiles: ".pdf,.docx,.doc,.txt,.png,.jpeg,.jpg,.gif",
    success: function (file, response) {

    },
    error: function (file, response) {
        return false;
    }
});

function car_image(path) {
    if (path != '') {
        $('#modal-image').modal('show');
        $("#car-image").attr("src", base_url + '/portal_assets/vehicle/' + path);
    } else {
        Toast.fire({
            icon: 'warning',
            title: 'No image Found',
            timer: 3000,
        })
    }
}

$('#update_vehicle_detail').submit(function (e) {


    $('#update_btn_vehicle').prop('disabled', true);
    $('#update_btn_vehicle').attr('data-kt-indicator', 'on');
    $('#update_btn_vehicle').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: base_url + '/vehicles/updatevehicledata',
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
                    window.location.href =
                        url_vehicle_id;
                }, 3000);
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 8000,
                })
                $('#update_btn_vehicle').prop('disabled', false);
                $('#update_btn_vehicle').removeAttr('data-kt-indicator');
                $('#update_btn_vehicle').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            $('#update_btn_vehicle').prop('disabled', false);
            $('#update_btn_vehicle').removeAttr('data-kt-indicator');
            $('#update_btn_vehicle').css('cursor', 'pointer');
            errorStatus = jqXHR.status;
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
var count_maintenance_length = $("input[name=maintenance_length]").val();
var maintenance_length = $("input[name=maintenance_length]").val();
var sub_maintenance_id;

function add_maintenance() {
    count_maintenance_length++;
    maintenance_length++;
    $("input[name=maintenance_length]").val(maintenance_length);
    let html = `<tr id="maintenance_${maintenance_length}">
           <td><input type="text" name="maintenance_item${maintenance_length}" class="form-control " placeholder="Item" required></td>
           <td><input type="number" name="maintenance_value${maintenance_length}" class="form-control " placeholder="Value" required></td>
           <td><button type="button" class="btn btn-primary" onclick="add_maintenance()">+</button></td>
           <td><button type="button" class="btn btn-danger" onclick="sub_maintenance_modal(${maintenance_length})">-</button></td>
       </tr>
   `;
    $("#maintenance_tbody").append(html);
}

function sub_maintenance_modal(id) {
    if (count_maintenance_length == 1) {
        Toast.fire({
            icon: 'warning',
            title: "You can't delete this item",
            timer: 8000,
        })
    } else {
        $('#modal-danger-maintenance').modal('show');
        sub_maintenance_id = id;
    }

}

function sub_maintenance() {

    count_maintenance_length--
    $('#modal-danger-maintenance').modal('hide');
    $("#maintenance_sub").val($("#maintenance_sub").val() + ',' + sub_maintenance_id);
    $("#maintenance_" + sub_maintenance_id).remove();
    if (maintenance_length == sub_maintenance_id) {
        maintenance_length--;
    }
}

var maintenance_date = maintenance_date_value;
maintenance_date = maintenance_date.split(",");

function check_maintenance_date(e) {
    if (maintenance_date.indexOf(e.value) != -1) {
        Toast.fire({
            icon: 'warning',
            title: 'Maintainance record already exists on this date. Please update it from maintainance details tab.',
            timer: 6000,
        })
        $(e).val('');

    }


}

$('#maintenance_detail').submit(function (e) {

    if ($("input[name=maintenance_id]").val() == '') {

        url = base_url + '/vehicles/create_maintenance_detail';
    } else {
        url = base_url + '/vehicles/update_maintenance_detail';
    }
    $('#maintenance_btn').prop('disabled', true);
    $('#maintenance_btn').attr('data-kt-indicator', 'on');
    $('#maintenance_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: url,
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
                    window.location.href =
                        url_vehicle_id;
                }, 3000);
            } else if (data.status == 'TRUE') {
                Toast.fire({
                    icon: 'success',
                    title: data.msg,
                    timer: 3000,
                })
                setTimeout(() => {
                    window.location.href =
                        url_vehicle_id;
                }, 3000);
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 8000,
                })
                $('#maintenance_btn').prop('disabled', false);
                $('#maintenance_btn').removeAttr('data-kt-indicator');
                $('#maintenance_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            $('#maintenance_btn').prop('disabled', false);
            $('#maintenance_btn').removeAttr('data-kt-indicator');
            $('#maintenance_btn').css('cursor', 'pointer');
            errorStatus = jqXHR.message;
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

$(".maintenance_edit_icon").click(function (e) {
    $("#custom-tabs-vehicle-maintenance-tab2").click();
    maintenance_length = 1;
    $("#maintenance_tbody").html(`
       <tr id="maintenance_1">
           <td><input type="text" name="maintenance_item1" class="form-control " placeholder="Item"></td>
           <td><input type="text" name="maintenance_value1" class="form-control " placeholder="Value"></td>
           <td><button type="button" class="btn btn-primary" onclick="add_maintenance()">+</button> </td>
           <td><button type="button" class="btn btn-danger" onclick="sub_maintenance_modal(1)" disabled>-</button></td>
       </tr>
   `);
    let m_id = $(this).closest("li.list-group-item").find("input[name='m_id']").val();
    let obj = $(this).closest("li.list-group-item").find("span").html();
    let date = $(this).closest("li.list-group-item").find("b").html();

    $("input[name=maintenance_date]").val(date);

    obj = JSON.parse(obj);
    $("input[name=maintenance_id]").val(m_id);
    $("input[name=maintenance_length]").val(obj.length);
    $("input[name=maintenance_sub]").val('');

    for (let i = 1; i <= obj.length; i++) {
        const temp = obj[i - 1];
        if (i != 1) {
            add_maintenance();
        }
        $(`input[name=maintenance_item${i}]`).val(temp.item);
        $(`input[name=maintenance_value${i}]`).val(temp.value);

    }

    $('#maintenance_btn').html('Update');
    $('.tab-panel').removeClass('active');
    $('.tab-nav').removeClass('active');
    $('#custom-tabs-vehicle-maintenance-tab2').addClass('show active')
    $('#custom-tabs-vehicle-maintenance2').addClass('show active')
});

// Maintenance End============================================================

// Allotment Start ============================================================

$('#allotment_detail').submit(function (e) {

    if ($("input[name=allotment_id]").val() == '') {
        url = base_url + '/vehicles/create_allotment_detail';
    } else {

        url = base_url + '/vehicles/update_allotment_detail';
    }
    $('#allotment_btn').prop('disabled', true);
    $('#allotment_btn').attr('data-kt-indicator', 'on');
    $('#allotment_btn').css('cursor', 'not-allowed');
    e.preventDefault();
    $.ajax({
        url: url,
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
                    window.location.href =
                        url_vehicle_id;
                }, 3000);
            } else if (data.status == 'TRUE') {
                Toast.fire({
                    icon: 'success',
                    title: data.msg,
                    timer: 3000,
                })
                setTimeout(() => {
                    window.location.href =
                        url_vehicle_id;
                }, 3000);
            } else {
                Toast.fire({
                    icon: 'warning',
                    title: data.msg,
                    timer: 8000,
                })
                $('#allotment_btn').prop('disabled', false);
                $('#allotment_btn').removeAttr('data-kt-indicator');
                $('#allotment_btn').css('cursor', 'pointer');
            }
        },
        error: function (jqXHR, textStatus) {
            $('#allotment_btn').prop('disabled', false);
            $('#allotment_btn').removeAttr('data-kt-indicator');
            $('#allotment_btn').css('cursor', 'pointer');
            errorStatus = jqXHR.message;
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

//$(".allotment_edit_icon").click(function(e) {
function allotment_edit_icon(allotment_id, employee_id, allotment_date, allotment_cancellation_date) {
    $("#custom-tabs-vehicle-allotment-tab2").click();
    result = $(this).closest(".col-md-3").prevAll();
    $('#allotment_employee_id_show').off('select2:select');
    $('#allotment_employee_id_show').on('select2:select');
    // resultdate = $("#allotment_date1").text()
    // resultcancledate = $("#allotment_cancellation_date1").text()
    //$("select[name=allotment_employee_id]").select2("val", "2"); .val(result[2].textContent);
    $("input[name=allotment_vehicle_date]").val(allotment_date);
    $("input[name=allotment_cancellation_date]").val(allotment_cancellation_date);
    // $("input[name=maintenance_id]").val($(this).closest('.m_id').val());
    // let a_id = $(this).closest("li.list-group-item").find("input[name='a_id'").val();
    // let e_id = $(this).closest("li.list-group-item").find("input[name='e_id'").val();
    // console.log(a_id);
    // console.log(e_id);
    // console.log(e_text);
    $("input[name=allotment_id]").val(allotment_id);
    $("select[name=allotment_employee_id").val(employee_id);
    let e_text = $('#allotment_employee_id_show').select2('data')[0].text.replaceAll('\n', '').trim();
    // $('#allotment_employee_id_show').select2('destroy');
    console.log(e_text);




    //console.log(e_text);
    $("#allotment_employee_id_text").val(e_text);


    //$("#allotment_employee_id_text,#allotment_employee_container").toggleClass('d-none');
    $("#allotment_employee_id_text").removeClass('d-none');
    $("#allotment_employee_container").addClass('d-none');
    $('#allotment_btn').html('Update');
    $('#allotment_btn').prop('disabled', false);
    $('input[name=allotment_cancellation_date]').prop('disabled', false);

    $('#maintenance_btn').html('Update');
    $('.tab-panel').removeClass('active');
    $('.tab-nav').removeClass('active');
    $('#custom-tabs-vehicle-allotment-tab2').addClass('show active');
    $('#custom-tabs-vehicle-allotment2').addClass('show active');
}
//});


$(".allotment_trash_icon").click(function (e) {
    let a_id = $(this).closest("li.list-group-item").find("input[name='a_id'").val();
    $('#modal-danger').modal('show');
    $("#modal-text").html('Allotment Detail');
    document.getElementById('deactivate_button').setAttribute("href", base_url + '/delete_allotment_detail/' + a_id);
});

// Allotment End ============================================================
