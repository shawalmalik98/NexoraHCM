    var add_salary_counter;
    function addfileRow() {
        add_salary_counter = $("input[name=salary_length]").val();

        if (add_salary_counter >= 10) {
            Toast.fire({
                icon: 'warning',
                title: 'You can\'t add more than 10 options',
            });
            return true;
        }
        add_salary_counter++;
        $("input[name=salary_length]").val(add_salary_counter);

        $("#salary_div").append(`
            <div class="row mt-3" id="deletefile${add_salary_counter}"> 
                <div class="col-md-5 mb-4">
                    <label class="fs-6 fw-semibold mb-2 required">Salary Division Title </label>
                    <input type="text" class="form-control"  name="salary_division_title[]" placeholder="Salary Division title" required="">
                </div>

                <div class="col-md-5 mb-4">
                    <label class="fs-6 fw-semibold mb-2 required">Salary Division Percentage </label>
                    <div class="input-group">
                        <input type="number" class="form-control salarypercentage" min="0" max="100" step="0.01" name="salary_division_percentage[]" placeholder="Salary Division percentage" required="" onkeyup="maxpercentage()">
                        <span class="input-group-text">%</span>
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <label class="fs-6 fw-semibold mb-15"></label>
                    <button class="btn btn-icon btn-sm" type="button" onClick="removedDiv(this)">
                        <span class="svg-icon svg-icon-3">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                            </svg>
                        </span>   
                    </button>
                </div>
            </div>`
        );

    }

    function removedDiv(elem) {

        $(elem).parent('div').parent('div').remove();
    }

    function maxpercentage() {
        var percentage = 0;
        $('.salarypercentage').each(function () {
            if ($(this).val() != '') {
                percentage += parseFloat($(this).val());
            }
        });

        if (percentage <= 100) {
            $('#division_message').addClass('d-none');
        } else {
            $('#division_message').removeClass('d-none');
        }
    }

    var delete_division_salary = "";

    function delete_salary_division(salary_division_id, increment) {
        $('input[name=salary_division_id]').val(salary_division_id);
        $('#salary_division').modal('show');

        delete_division_salary = increment;
    }

    var delete_module_access = "";
    function delete_module_email_access(endpoint_id, ref_id, type) {

        $('input[name=endpoint_id]').val(endpoint_id);
        $('input[name=recipient_id]').val(ref_id);
        $('input[name=recipient_type]').val(type);
        $('#email-module-access-delete-modal').modal('show');
        // delete_module_access = increment;
    }

    $('#email_module_access_delete').submit(function (e) {
        $('#delete_module_submit_btn').prop('disabled', true);
        $("#delete_module_submit_btn").attr('data-kt-indicator', 'on');
        $('#delete_module_submit_btn').css('cursor', 'pointer');
        e.preventDefault();

        $('#email-module-access-delete-modal').modal('hide');

        $.ajax({
            url: base_url + '/system-settings/discard_email_access',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {
                if(data.status == 'TRUE'){
                    $.each(data.recipents_id, function(index,value) {
                        const rowToDelete = $('.email_reciept_row[data-recipient-id="' + value + '"]');
                        if (rowToDelete.length) {
                            
                            rowToDelete.remove(); // Remove the row from the table
                        }
                    });
                }else{
                    ref_id = $('input[name=recipient_id]').val();
                    const rowToDelete = $('.email_reciept_row[data-recipient-id="' + ref_id + '"]');
                    if (rowToDelete.length) {
                        rowToDelete.remove(); // Remove the row from the table
                    }
                }
                $('#delete_module_submit_btn').prop('disabled', false);
                $('#delete_module_submit_btn').css('cursor', 'pointer');
                $("#delete_module_submit_btn").removeAttr('data-kt-indicator');

            },
            error: function (jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#delete_module_submit_btn').prop('disabled', false);
                $('#delete_module_submit_btn').css('cursor', 'pointer');
                $("#delete_module_submit_btn").removeAttr('data-kt-indicator');
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

    $('#delete_salary_division').submit(function (e) {
        $('#delete_submit_btn').prop('disabled', true);
        $("#delete_submit_btn").attr('data-kt-indicator', 'on');
        $('#delete_submit_btn').css('cursor', 'pointer');
        e.preventDefault();

        $('#salary_division').modal('hide');

        $.ajax({
            url: base_url + '/system-settings/discard_salary_division',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {

                $('.division_row' + delete_division_salary).remove();

                $('#delete_submit_btn').prop('disabled', false);
                $('#delete_submit_btn').css('cursor', 'pointer');
                $("#delete_submit_btn").removeAttr('data-kt-indicator');

            },
            error: function (jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#delete_submit_btn').prop('disabled', false);
                $('#delete_submit_btn').css('cursor', 'pointer');
                $("#delete_submit_btn").removeAttr('data-kt-indicator');
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

    var delete_mac = "";
    function delete_mac_address(address_id, index) {
        $('input[name=address_id]').val(address_id);
        $('#modal-danger').modal('show');
        delete_mac = index;
    }

    $('#delete_ip_address').submit(function (e) {
        $('#delete_button').prop('disabled', true);
        $("#delete_button").attr('data-kt-indicator', 'on');
        $('#delete_button').css('cursor', 'pointer');
        e.preventDefault();

        $('#modal-danger').modal('hide');

        $.ajax({
            url: base_url + '/system-settings/discard_mac_address',
            type: 'POST',
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function (data) {

                $('.mac_row' + delete_mac).remove();

                $('#delete_button').prop('disabled', false);
                $('#delete_button').css('cursor', 'pointer');
                $("#delete_button").removeAttr('data-kt-indicator');

            },
            error: function (jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#delete_button').prop('disabled', false);
                $('#delete_button').css('cursor', 'pointer');
                $("#delete_button").removeAttr('data-kt-indicator');
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


    var ip_address_counter = length_count;
    $('#add_new_ip_address').click(function () {
        ip_address_counter = ++ip_address_counter;
        var row_for_append = `
            <div class="row mb-4">
                <label class="form-label">IP Address ${ip_address_counter}</label>
                <div class="col-sm-11">
                    <input type="text" class="form-control mb-2" name="mac_address[]" placeholder="Enter IP Address ${ip_address_counter}" required/>
                </div>
                <div class="col-sm-1">
                    <a href="javascript:void(0);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm" onClick="removeDiv(this)">
                        <span class="svg-icon svg-icon-3">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 9C5 8.44772 5.44772 8 6 8H18C18.5523 8 19 8.44772 19 9V18C19 19.6569 17.6569 21 16 21H8C6.34315 21 5 19.6569 5 18V9Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M5 5C5 4.44772 5.44772 4 6 4H18C18.5523 4 19 4.44772 19 5V5C19 5.55228 18.5523 6 18 6H6C5.44772 6 5 5.55228 5 5V5Z" fill="currentColor"></path>
                                <path opacity="0.5" d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4V4H9V4Z" fill="currentColor"></path>
                            </svg>
                        </span>
                    </a>
                </div>
            </div>
        `;
        $('#ip_div').append(row_for_append);

    });

    function removeDiv(elem) {
        $(elem).parent('div').parent('div').remove();
    }

    $('#submit_btn').click(function () {

        $('input:invalid').each(function () {

            var closest = $(this).closest('.tab-pane');
            var id = closest.attr('id');
            $('#' + id + 's').tab('show');
            return false;
        });

    })


    $('#hrm_setting').submit(function (e) {

        $('#submit_btn').prop('disabled', true);
        $("#submit_btn").attr('data-kt-indicator', 'on');
        $('#submit_btn').css('cursor', 'not-allowed');
        e.preventDefault();
        var setting_record = new FormData(this);
        if (payroll_date) {
            $.ajax({
                url: base_url + '/system-settings/update-hrm-settings',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: setting_record,
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
                            window.location.href = base_url + '/system-settings';
                        }, 3000);
                    } else {
                        Toast.fire({
                            icon: 'warning',
                            title: data.msg,
                            timer: 8000,
                        })
                        $('#submit_btn').prop('disabled', false);
                        $("#submit_btn").removeAttr('data-kt-indicator');
                        $('#submit_btn').css('cursor', 'pointer');
                    }
                },
                error: function (jqXHR, textStatus) {
                    var errorStatus = jqXHR.status;
                    $('#submit_btn').prop('disabled', false);
                    $("#submit_btn").removeAttr('data-kt-indicator');
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
        } else {
            swal.fire({
                title: 'Confirmation Alert !',
                text: 'Are you sure to set your Payroll Start Period ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel',
            }).then(function (value) {
                if (value.isConfirmed == true) {
                    $.ajax({
                        url: base_url + '/system-settings/update-hrm-settings',
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: setting_record,
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
                                    window.location.href = base_url + '/system-settings';
                                }, 3000);
                            } else {
                                Toast.fire({
                                    icon: 'warning',
                                    title: data.msg,
                                    timer: 8000,
                                })
                                $('#submit_btn').prop('disabled', false);
                                $("#submit_btn").removeAttr('data-kt-indicator');
                                $('#submit_btn').css('cursor', 'pointer');
                            }
                        },
                        error: function (jqXHR, textStatus) {
                            var errorStatus = jqXHR.status;
                            $('#submit_btn').prop('disabled', false);
                            $("#submit_btn").removeAttr('data-kt-indicator');
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
                } else {
                    $('#submit_btn').prop('disabled', false);
                    $("#submit_btn").removeAttr('data-kt-indicator');
                    $('#submit_btn').css('cursor', 'pointer');
                    $('#payroll-settings-tabs').tab('show');;
                }
            });
        }
    });


    $('#fiscal_start_date').change(function () {
        var date = $('#fiscal_start_date').val();

        const newDate = addOneYear(date);
        $('#fiscal_end_date').val(newDate);
    })

    function addOneYear(date) {
        var date = new Date(date);


        var year = date.getFullYear();
        var month = date.getMonth();
        var day = date.getDate();

        var fulldate = new Date(year + 1, month, day);

        var toDate = fulldate.toISOString().slice(0, 10);

        return toDate;

    }

    function sendTestEmail(e) {
        $(e).prop('disabled', true);
        $(e).attr('data-kt-indicator', 'on');
        $(e).css('cursor', 'not-allowed');
        var receiving_mail = $("input[name='receiving_mail']").val()
        $.ajax({
            url: base_url + '/system-settings/send-test-mail',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { receiving_mail: receiving_mail },
            success: function (data) {
                if (data.status == 'TRUE') {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg,
                        timer: 3000,
                    })
                    $(e).prop('disabled', false);
                    $(e).removeAttr('data-kt-indicator');
                    $(e).css('cursor', 'pointer');

                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 10000,
                    })
                    $(e).prop('disabled', false);
                    $(e).removeAttr('data-kt-indicator');
                    $(e).css('cursor', 'pointer');
                }
            },
            error: function (jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $(e).prop('disabled', false);
                $(e).removeAttr('data-kt-indicator');
                $(e).css('cursor', 'pointer');
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
    }

    function addEmail(e) {
        $(e).prop('disabled', true);
        $(e).attr('data-kt-indicator', 'on');
        $(e).css('cursor', 'not-allowed');
        var email_module = $("select[name='email_module[]']").val();
        var email_reciever = $("select[name='email_reciever[]']").val();
        var cc_recipients = $("select[name='cc_recipients[]']").val();
        $.ajax({
            url: base_url + '/system-settings/add-email',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { email_module: email_module, email_reciever: email_reciever, cc_recipients: cc_recipients },
            success: function (data) {
                if (data.status == 'TRUE') {
                    Toast.fire({
                        icon: 'success',
                        title: data.msg,
                        timer: 3000,
                    })
                    setTimeout(() => {
                        window.location.href = base_url + '/system-settings';
                    }, 3000);
                    $(e).prop('disabled', false);
                    $(e).removeAttr('data-kt-indicator');
                    $(e).css('cursor', 'pointer');
                    $("select[name='email_module[]']").val('').trigger('change');
                    $("select[name='manager']").val('').trigger('change');



                } else {
                    Toast.fire({
                        icon: 'warning',
                        title: data.msg,
                        timer: 10000,
                    })
                    $(e).prop('disabled', false);
                    $(e).removeAttr('data-kt-indicator');
                    $(e).css('cursor', 'pointer');
                }
            },
            error: function (jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $(e).prop('disabled', false);
                $(e).removeAttr('data-kt-indicator');
                $(e).css('cursor', 'pointer');
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
    }

    $('.email_configurationl_input').keyup(function () {
        $('.email_configurationl_input').prop('required', false);
        $('.email_configurationl_label').removeClass('required');
        $('.email_configurationl_input').each(function () {
            if ($(this).val()) {
                $('.email_configurationl_input').prop('required', true);
                $('.email_configurationl_label').addClass('required');
            }

        });
    });
