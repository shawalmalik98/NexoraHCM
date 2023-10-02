$("#fund_policy").change(function (e) {

    var fund_id = $(this).val();
    
    e.preventDefault();
    $.ajax({
        url: base_url + '/funds/get-source-data',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {fund_id : fund_id},
        success: function (data) {
            response = JSON.parse(JSON.stringify(data));
            if (response.status == "TRUE") {
                $('#responsefundname').html('');
                record = '';
                if(response.data.length > 0){
                    $.each(response.data, function (key, fund_rule) {
                        record += `<tr >
                            <td class="ps-6">${fund_rule.employee_code}</td>
                            <td class="ps-6">${fund_rule.employee_name}</td>
                            <td class="ps-6">${fund_rule.fund_name}</td>
                            <td class="ps-6">${fund_rule.ef_amount}</td>
                            <td class="ps-6">${fund_rule.ef_created_at}</td>
                            </tr>`;
        
                    })
                    $('#responsefundname').append(record)
                
                }
              
               
            } 
        },
        error: function (jqXHR, textStatus) {
            $('#responsefundname').html('');
          
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