jQuery('#personal_information_form').validate({
    rules: {
        // personal form validation
        fathersname: "required",
        permanentaddress: "required",
        currentaddress: "required",
        martial_status: "required",
        gender: "required",
        religion: "required",
        birthdate: "required",
        // bank details validation
        acc_num: "required",
        ibn_num: "required",
        bcode: "required",
        accountitle: "required",

        // empployee history validation
        employername1: "required",
        designation1: "required",
        salaryandbenefits1: "required",
        durationstartdate1: "required",
        durationenddate1: "required",
        reasonforleaving1: "required",



        //  Academic History Validation
        degree1: "required",
        intitution1: "required",
        majors1: "required",
        passingyear1: "required",
        grade1: "required",

    },
    messages: {
        gender: "Please select gender",
        fathersname: "Please Enter Your Father Name",
        permanentaddress: "Please Enter Your Permanent Address",
        currentaddress: "Please Enter Your Current Address",
        acc_num: "Please Enter Your Account Number",
        ibn_num: "Please Enter Your IBAN Number",
        bcode: "Please Mention Your Bank Code",
        accountitle: "Please Mention Your Account Title",
        employername1: "Atleast one employment history is must.",
        designation1: "Atleast one employment history is must.",
        salaryandbenefits1: "Atleast one employment history is must.",
        durationstartdate1: "Atleast one employment history is must.",
        durationenddate1: "Atleast one employment history is must.",
        reasonforleaving1: "Atleast one employment history is must.",
        degree1: "Atleast one academic credential is must.",
        intitution1: "Atleast one academic credential is must.",
        majors1: "Atleast one academic credential is must.",
        passingyear1: "Atleast one academic credential is must.",
        grade1: "Atleast one academic credential is must.",
        reson_leave: "Reason Required",
        martial_status: "Select Your Martial Status",
        religion: "Select Your Religion",
        birthdate: "Select Your Date of Birth",
        cnic: {
            required: "Please Enter CNIC",
            minlength: "Please Enter Valid CNIC e.g 4220154259245"
        },

    },

    submitHandler: function(form) {
        $('#personal_information_form').submit(function(e) {
            $('.submit-btn-form').prop('disabled', true);
            $('.submit-btn-form').html('Please Wait . . .');
            $('.submit-btn-form').css('cursor', 'not-allowed');
            e.preventDefault();
            $.ajax({
                url: '/savemypersonalinformation',
                type: 'POST',
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(data) {
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
                            timer: 8000,
                        })
                        $('.submit-btn-form').prop('disabled', false);
                        $('.submit-btn-form').html('SAVE INFORMATION');
                        $('.submit-btn-form').css('cursor', 'pointer');
                    }
                },
                error: function(jqXHR, textStatus) {
                    var errorStatus = jqXHR.status;
                    if (errorStatus == 0) {

                        Toast.fire({
                            icon: 'error',
                            title: 'Internet Connection Problem',
                            timer: 8000,
                        })
                       
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: "Try Again. Error Code " + errorStatus,
                            timer: 8000,
                        });
                    }
                }
            });
        });
    }

});


// jQuery('#personal_information_form').validate({
// 	rules:{
// 		fathersname:"required",
// 		email:{
// 			required:true,
// 			email:true
// 		},
// 		password:{
// 			required:true,
// 			minlength:5
// 		},
// 	},messages:{
// 		fathersname:"Please Enter Your Father Name",
// 		email:{
// 			required:"Please enter email",
// 			email:"Please enter valid email",
// 		},
// 		password:{
// 			required:"Please enter your password",
// 			minlength:"Password must be 5 char long"
// 		},
// 	},
// 	submitHandler:function(form){
// 		form.submit();
// 	}
// });