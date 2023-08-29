function shiftTimeLog(request_id){

    var url = base_url + '/' + 'log-shift-time';
    var form_data = {'request_id':request_id}
    body_data = {
        // Adding method type
        method: 'POST',
        // Adding body or contents to send
        body: JSON.stringify(form_data),

        // Adding headers to the request
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Content-Type' : 'application/json'
        }
    }
    fetch(url,body_data)
    // Converting to JSON
    .then(response =>{
        if (response.ok) {
            return response.json();
        }
        return Promise.reject(response);
    })
    // Displaying results to console
    .then(json =>{

    if (json.status == 'TRUE') {
        $('#request-log-response').html('');
        var record = '';
        if (json.status == "TRUE") {
            
            record += `<thead>
                <tr class="fw-bold text-muted bg-light">
                    <th class="ps-7">Employee Name</th>
                    <th>Status</th>
                    <th>Shift Time Start</th>
                    <th>Shift Time End</th>
                    <th>Shift Effect Date</th>
                    
                </tr>
            </thead> <tbody>`;
            $.each(json.data, function(key, request_log) {
                if(request_log.shift_log_type == 1){
                   request_status =  '<span class="badge badge-light-success text-center font-weight-bold">Actual</span>';
                }else{
                    request_status = '<span class="badge badge-light-danger text-center font-weight-bold">Updated</span>';
                }
                record +=`<tr>
                    <td class="ps-6 "><b>${request_log.employee_name} - (${request_log.employee_code})</b></td>
                    <td class="ps-6">${request_status}</td>
                    <td class="ps-6">${request_log.shift_log_start}</td>
                    <td class="ps-6">${request_log.shift_log_end}</td>
                    <td class="ps-6">${request_log.shift_log_create_at}</td>
                </tr>`; 
            });
            record+=` </tbody>`;
            $('#request-log-response').html(record);
        }else{
            record = 'No hr/manager responded to this request yet.';
            $('#request-log-response').html(record);
        }

        $('#log_modal_status').modal('show');

    } else {
        Toast.fire({
            icon: 'warning',
            title: json.msg,
            timer: 3000,
        })
    }

    }).catch(response => {

        Toast.fire({
            icon: 'warning',
            title: response.statusText,
            timer: 3000,
        })
    });
}