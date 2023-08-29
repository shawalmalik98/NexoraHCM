


function send_server_request( method_type ,action_url , form_data_this ,button_id, screen_reload_same = "Yes" , target_url=null , reload_seconds = 3000){

    url = base_url + '/' + action_url;


    $('#'+button_id).prop('disabled', true);
    $('#'+button_id).attr('data-kt-indicator', 'on');
    $('#'+button_id).css('cursor', 'not-allowed');
    
    let form_data = new FormData(form_data_this);

    var body_data ;

    if(method_type == 'GET'){

        body_data = {
     
            // Adding method type
            method: method_type
            
        }

    }else{

        body_data = {
     
            // Adding method type
            method: method_type,

            // Adding body or contents to send
            // body: JSON.stringify({form_data}),
            body : form_data,
         
            // Adding headers to the request
            headers: {
    
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        }

    }
    

    fetch(url,body_data)
 
    // Converting to JSON
    .then(response =>{
        // response.json()
        if (response.ok) {
            return response.json();
          }

          return Promise.reject(response);
    })
    
 
    // Displaying results to console
    .then(json =>{
        // console.log(json);

        if (json.status == 'TRUE') {

            Toast.fire({
                icon: 'success',
                title: json.msg,
                timer: reload_seconds,
            })

            if(screen_reload_same == "Yes"){
                
                setTimeout(() => {
                    location.reload();
                }, reload_seconds);

            }else{

                setTimeout(() => {

                    window.location.href = base_url+"/"+target_url;
                    
                }, reload_seconds);
                
            }

            

        } else {

            Toast.fire({
                icon: 'warning',
                title: json.msg,
                timer: reload_seconds,
            })

            $('#'+button_id).prop('disabled', false);
            $('#'+button_id).removeAttr('data-kt-indicator');
            $('#'+button_id).css('cursor', 'pointer');

        }


    
    }).catch(response => {

        $('#'+button_id).prop('disabled', false);
        $('#'+button_id).removeAttr('data-kt-indicator');
        $('#'+button_id).css('cursor', 'pointer');

        Toast.fire({
            icon: 'warning',
            title: response.statusText,
            timer: reload_seconds,
        })

    });

}   