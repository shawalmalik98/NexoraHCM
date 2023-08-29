var record = '';

$('#updateEmployeeDeductionPolicy').submit(function (e) {
    e.preventDefault()
    send_server_request('POST', 'deduction-policy/update-deduction-policy', this, 'updateEmployeeDeductionPolicyButton', "No", 'deduction-policy', 3000)
});

$("#deduction_policy").change(function (e) {

    var deduction_source_id = $(this).val();
    let form_data = {deduction_source_id : deduction_source_id};
    e.preventDefault();
    $.ajax({
        url: base_url + '/deduction-policy/get-source-data',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {deduction_source_id : deduction_source_id},
        success: function (data) {
            response = JSON.parse(JSON.stringify(data));
            if (response.status == "TRUE") {
                $('#responseAttendanceRule').html('');
                record = '';
                if(response.data.length > 0){
                    $.each(response.data, function (key, deduction_rule) {
                        record += `<tr >
                            <td class="ps-6">${deduction_rule.attendance_deduction_rule_day_count} ${(deduction_rule.attendance_deduction_rule_type == 1)?'Late Check In / Early Check Out':'Half Day'}</td>
                            <td ><div class="d-flex align-items-center justify-content-between">Deduction<input type="number" name="deduction_days[]" class="form-control" value="${(deduction_rule.attendance_deduction_rule_deduction_count)?deduction_rule.attendance_deduction_rule_deduction_count:0}" min="0" max="30" required style="width:70%">Days</div></td>
                            </tr>`;
                        
                    })
                    record += `<input type="hidden" name="deduction_type" value="${response.type}" required/> `;
                    $('#responseAttendanceRule').append(record)
                    $('#responseAttendanceRule').append(`<div class="modal-footer justify-content-center" id="updateEmployeeDeductionPolicySubmitDiv" style="margin-top:10px;">
                        <button type="submit" id="updateEmployeeDeductionPolicyButton" class="btn btn-primary">
                        <span class="indicator-label">Update</span>
                        <span class="indicator-progress">Please wait... 
                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                        </button>
                    </div>`);
                }
              
               
            } 
        },
        error: function (jqXHR, textStatus) {
            $('#responseAttendanceRule').html('');
          
            var errorStatus = jqXHR.status;

            if (errorStatus == 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Internet Connection Problem',
                    timer: 6000,
                });
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
