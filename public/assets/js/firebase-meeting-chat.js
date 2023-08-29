var lastIndex = 1;
$.ajax({
    url: base_url + '/fetch-firebase-configuration',
    type: 'GET',
    headers: { 'authentication-token': 'h(mF!reba$e!<ey' },
    success: function(data) {
        response = JSON.parse(JSON.stringify(data));
        if(response.status == 'TRUE'){
            let firebaseConfig = {
                apiKey: response.data.apiKey,
                authDomain: response.data.authDomain,
                databaseURL: response.data.databaseURL,
                projectId: response.data.projectId,
                storageBucket: response.data.storageBucket,
                messagingSenderId: response.data.messagingSenderId,
                appId: response.data.appId,
                measurementId: response.data.measurementId,
            };
            load_fireabase(firebaseConfig);
        }
    },
});

function load_fireabase(firebaseConfig){
    const app = firebase.initializeApp(firebaseConfig);
    var firebase_db = firebase.database();
    var checkIfAnyMessage = 0;

    firebase_db.ref('chats/MeetingsChat/').limitToLast(50).on('value', function(snapshot) {
        var chat_messages = snapshot.val();
        var chatMessagesArray = [];
        if(chat_messages == null){
            
            $('.direct-chat-messages').html('<h4 class="text-center mt-5 text-success">No Chat Message To Display</h4>');
        }
    
        if (chat_messages) {
            //$('#chat-message').scrollTop($('.direct-chat-messages')[0].scrollHeight);
            $.each(chat_messages, function(key, chat) {
                
                if (chat) {
                
                    chatMessagesArray.push(sortChatMessages(chat));
                
                    $('.direct-chat-messages').html(chatMessagesArray);
                
                    // lastIndex = key;
                    // lastIndex++;
                    // $('#index_input').val(lastIndex);
                } 
                // else {
                //     $('#index_input').val('1');
                
                    
                // }
            });
        
            
            if(chat_messages.length==checkIfAnyMessage+1){
                $('.direct-chat-messages').html('<h4 class="text-center mt-5 text-success">No Chat Message To Display</h4>');
            }else{
                $('#chat-message').scrollTop($('.direct-chat-messages')[0].scrollHeight);
            
            }

            
        } else {
            $('#index_input').val('1');
        }
    });

    function sortChatMessages(chatMessage=Null) {
        var message = '';
    
        var login_employee_id = user_id;
    
        if(meeting_id == chatMessage.meeting_id){
        
            if (chatMessage.sender_id == login_employee_id) {
        
                message += '\
                <div class="d-flex justify-content-end mb-10">\
                    <div class="d-flex flex-column align-items-end">\
                        <div class="d-flex align-items-center mb-2">\
                            <div class="symbol symbol-35px symbol-circle ">\
                                <img alt="" src="' + base_url + '/portal_assets/profile_pictures/' + chatMessage.sender_avatar + '" alt="' + chatMessage.sender_name + '">\
                            </div>\
                            <div class="ms-3">\
                                <span class="fs-5 fw-bold text-gray-900 text-hover-primary me-1 ">' + chatMessage.sender_name + '</span>\
                                <span class="text-muted fs-7 mb-1">' + chatMessage.date + ' - ' + chatMessage.time + '</span>\
                            </div>\
                        </div>\
                    <div class="p-5 rounded bg-light-primary text-dark fw-semibold mw-lg-400px text-end " data-kt-element="message-text">'+chatMessage.message+'</div>\
                    </div>\
                </div>';
        
            } else {
                message += '\
                <div class="d-flex justify-content-start mb-10">\
                    <div class="d-flex flex-column align-items-start">\
                        <div class="d-flex align-items-center mb-2">\
                            <div class="symbol symbol-35px symbol-circle ">\
                                <img alt="" src="' + base_url + '/portal_assets/profile_pictures/' + chatMessage.sender_avatar + '" alt="' + chatMessage.sender_name + '">\
                            </div>\
                            <div class="ms-3">\
                                <span class="fs-5 fw-bold text-gray-900 text-hover-primary me-1">' + chatMessage.sender_name + '</span>\
                                <span class="text-muted fs-7 mb-1">' + chatMessage.date + ' - ' + chatMessage.time + '</span>\
                            </div>\
                        </div>\
                    <div class="p-5 rounded bg-light-info text-dark fw-semibold mw-lg-400px text-start" data-kt-element="message-text">'+chatMessage.message+'</div>\
                    </div>\
                </div>';
            }

            
        }else{
        
            checkIfAnyMessage++;

        }
        return message;
    
    }
}

function loadAllchat(TotalChatMessages) {
    var loadMoreBtnHtml = '';
    if (TotalChatMessages > 4) {
        loadMoreBtnHtml = '<button class="btn btn-primary text-center">Load More</button>';
    }
    return loadMoreBtnHtml;
}

$("#sendChatMessage").submit(function(e) {
    e.preventDefault();
    var messageTxt = $("#messageInputBox").val();
    
    var fileName = $("#fileHolder").val();
    if (messageTxt.trim().length === 0 && fileName == '') {
        Toast.fire({
            icon: 'warning',
            title: 'You can\'t send an empty message',
            timer: 6000,
        })
    } else {
        $('#sendMessageBtn').attr('disabled', true);
        $('#sendMessageBtn').attr('data-kt-indicator', 'on');;
        $.ajax({
            url: base_url + '/' + 'send-chat-message',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(data) {
                $("#messageInputBox").val("");
                $("#fileHolder").val("");
                $('#sendMessageBtn').attr('disabled', false);
                $('#sendMessageBtn').removeAttr('data-kt-indicator');
            },error: function(jqXHR, textStatus) {
                var errorStatus = jqXHR.status;
                $('#sendMessageBtn').prop('disabled', false);
                $("#sendMessageBtn").removeAttr('data-kt-indicator');
                $('#sendMessageBtn').css('cursor', 'pointer');
                if (errorStatus == 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Internet Connection Problem',
                        timer: 3000,
                    })
                } else {
                    if(errorStatus == 419){
                        location.reload();
                    }else{
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
});


$("#messageInputBox").on('keyup', function (e) {
            
    var value = $(this).val();
    

    if (e.key === 'Enter' && e.keyCode === 13 && e.shiftKey) {
       
    }
    else if (e.key === 'Enter' || e.keyCode === 13) {
        $("#sendMessageBtn").click();
        $('#messageInputBox').val('');
    }
    
     
});