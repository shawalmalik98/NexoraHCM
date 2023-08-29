const firebaseConfig = {
    apiKey: "AIzaSyCdcNo1C_ktUsH8jMCrV4EwQRgOVVB518c",
    authDomain: "fir-laravel-7b8ce.firebaseapp.com",
    databaseURL: "https://fir-laravel-7b8ce-default-rtdb.firebaseio.com",
    projectId: "fir-laravel-7b8ce",
    storageBucket: "fir-laravel-7b8ce.appspot.com",
    messagingSenderId: "567820782772",
    appId: "1:567820782772:web:4d12c5206a0323d6069be1",
    measurementId: "G-RRD2F6FG6S"
}

const app = firebase.initializeApp(firebaseConfig);
var firebase_db = firebase.database();

$.ajax({
    url: base_url + '/user-detail/' + user_id,
    type: 'GET',
    dataType: 'json',
    contentType: false,
    cache: false,
    processData: false,
    success: function(data) {

        response = JSON.parse(JSON.stringify(data));
        if (response.status == "TRUE") {

            let department = response.data.employee_department;
            var get_notification = firebase_db.ref("tbl_notifications").limitToLast(10)
            callNotification(department,'false',get_notification);
           


        } else {
            console.warn(response.msg);
        }

    },
    error: function(jqXHR, textStatus) {
        var errorStatus = jqXHR.status;


        if (errorStatus == 0) {
            console.warn("Internet Connection Problem");
        } else {
            console.warn("Try Again. Error Code " + errorStatus);
        }
    }
});

function notification_fetch(){
    $("#notifications-container").css("height", "15vh");
    $.ajax({
        url: base_url + '/user-detail/' + user_id,
        type: 'GET',
        dataType: 'json',
        contentType: false,
        cache: false,
        processData: false,
        success: function(data) {
    
            response = JSON.parse(JSON.stringify(data));
            if (response.status == "TRUE") {
    
                let department = response.data.employee_department;
                var get_notification = firebase_db.ref("tbl_notifications").limitToLast(100)
                callNotification(department,'true',get_notification);
               
    
    
            } else {
                console.warn(response.msg);
            }
    
        },
        error: function(jqXHR, textStatus) {
            var errorStatus = jqXHR.status;
    
    
            if (errorStatus == 0) {
                console.warn("Internet Connection Problem");
            } else {
                console.warn("Try Again. Error Code " + errorStatus);
            }
        }
    });
}

var unReadNotification = [];
var viewed_by = 'Read';

var callNotification = (department,fetchNotification,get_notification) => {

    get_notification.on('value', function(snapshot) {

        var notifications = snapshot.val();
        var notificationArray = [];
        var notificationArrayData = [];
        let notificationCount = 0;
        let allnotificationCount = 0;
        var notificationPopup = [];
        $.each(notifications, function(key, notification) {
            let allowNotification = false;
            if ((notification.notification_to_hr && notification.notification_to_hr == 'yes') || (notification.notification_to_manager1 || notification.notification_to_manager1 == true) || (notification.notification_to_manager2 || notification.notification_to_manager2 == true)) {
                if (department == 1 || department == 2 || (notification.notification_to_manager1 == user_id || notification.notification_to_manager2 == user_id)) {
                    allnotificationCount++;
                    if (!notification.viewed_by) {
                        let read_notificaiton = "read_notification('" + notification.notification_url + "', '" + key + "')";
                        viewed_by = "UnRead";
                        unReadNotification.push(key);
                        notificationArrayData.push('<tr class="clickable-row notification-open"\
                        style="background:lightgray; cursor:pointer">\
                        <td class="ps-10">' + allnotificationCount + '</td>\
                        <td>\
                            <h4>' + notification.notification_title + '</h4>\
                            <i class="far fa-clock text-gray"></i> &nbsp;\
                            ' + notification.notification_time + ':\
                            ' + notification.notification_message + '\
                            <p class="text-right"></p>\
                        </td>\
                        <td class="ps-8">\
                        <a href="javascript:;" onclick=" ' + read_notificaiton + '">\
                            ' + viewed_by + '\
                        </td>\
                    </tr>');

                    } else {
                        viewed_by = "Read";
                        notificationArrayData.push('<tr class="clickable-row"\
                        style="cursor:pointer">\
                        <td  class="ps-10">' + allnotificationCount + '</td>\
                        <td>\
                            <h4>' + notification.notification_title + '</h4>\
                            <i class="far fa-clock text-gray"></i> &nbsp;\
                            ' + notification.notification_time + ':\
                            ' + notification.notification_message + '\
                            <p class="text-right"></p>\
                        </td>\
                        <td class="ps-8">\
                        <a href="' + base_url + '/' + notification.notification_url + '" >\
                        ' + viewed_by + '</a>\
                        </td>\
                    </tr>');
                    }
                    allowNotification = true;
                   
                   
                }
                if (notification.notification_to_manager1 == user_id || notification.notification_to_manager2 == user_id || (notification.notification_to_employee == user_id || notification.notification_to_employee == '*') ) {
                   
                    allowNotification = true;
                }

            }

            if (!allowNotification && notification.notification_to_employee && (notification.notification_to_employee == user_id || notification.notification_to_employee == '*')) {
                allnotificationCount++;
                if (!notification.viewed_by) {
                    let read_notificaiton = "read_notification('" + notification.notification_url + "', '" + key + "')";
                    viewed_by = "UnRead";
                    unReadNotification.push(key);
                    notificationArrayData.push('<tr class="clickable-row notification-open"\
                    style="background:lightgray; cursor:pointer">\
                    <td  class="ps-8">' + allnotificationCount + '</td>\
                    <td>\
                        <h4>' + notification.notification_title + '</h4>\
                        <i class="far fa-clock text-gray"></i> &nbsp;\
                        ' + notification.notification_time + ':\
                        ' + notification.notification_message + '\
                        <p class="text-right"></p>\
                    </td>\
                    <td class="ps-8">\
                    <a href="javascript:;" onclick=" ' + read_notificaiton + '">\
                        ' + viewed_by + '\
                    </td>\
                </tr>');

                } else {
                    viewed_by = "Read";
                    notificationArrayData.push('<tr class="clickable-row"\
                    style="cursor:pointer">\
                    <td class="ps-10">' + allnotificationCount + '</td>\
                    <td>\
                        <h4>' + notification.notification_title + '</h4>\
                        <i class="far fa-clock text-gray"></i> &nbsp;\
                        ' + notification.notification_time + ':\
                        ' + notification.notification_message + '\
                        <p class="text-right"></p>\
                    </td>\
                    <td class="ps-8">\
                    <a href="' + base_url + '/' + notification.notification_url + '" >\
                    ' + viewed_by + '</a>\
                    </td>\
                </tr>');
                }
                
                allowNotification = true;
             
            }

            if (allowNotification && notification) {
              
                var add_class = "text-gray-400";
                if (!(notification.viewed_by && notification.viewed_by.includes(user_id))) {
                    add_class = "text-dark";
                    notificationCount++;
                }
                let read_notificaiton = "read_notification('" + notification.notification_url + "', '" + key + "')";
                if(fetchNotification == 'true'){
                    notificationArray.unshift(`
                        <div class="d-flex flex-stack">
                            <div class="d-flex align-items-center " >
                                <div class="notification-count-div" >
                                    <a href="javascript:;" onclick="${read_notificaiton}"  class="${add_class} fw-bold text-hover-primary">
                                        <p class="fs-4 mb-1">${notification.notification_title}</p>
                                        <p class="fs-7 mb-1">${notification.notification_message}</p>
                                        <p class="fs-7 mb-0"><i class="far fa-clock mr-1"></i> 
                                        ${notification.notification_time}</p>
                                    </a>
                            
                                </div>
                            </div>
                        </div>
                    <hr>`);

                }
               
            }
          
        });
  
        if (notificationCount > 0) {

            notificationPopup.push(`
                <span class="bullet bullet-dot bg-success h-9px w-9px position-absolute translate-middle top-0 start-50 animation-blink"></span>
            `);
        }
        if (allnotificationCount >= 100) {

            notificationArray.push(`<div class="py-3 text-center border-top">\
                                           
            <a href="${base_url}/all-notifications" class="btn btn-color-gray-600 btn-active-color-primary">View All
    
                <span class="svg-icon svg-icon-5">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect opacity="0.5" x="18" y="13" width="13" height="2" rx="1" transform="rotate(-180 18 13)" fill="currentColor" />
                        <path d="M15.4343 12.5657L11.25 16.75C10.8358 17.1642 10.8358 17.8358 11.25 18.25C11.6642 18.6642 12.3358 18.6642 12.75 18.25L18.2929 12.7071C18.6834 12.3166 18.6834 11.6834 18.2929 11.2929L12.75 5.75C12.3358 5.33579 11.6642 5.33579 11.25 5.75C10.8358 6.16421 10.8358 6.83579 11.25 7.25L15.4343 11.4343C15.7467 11.7467 15.7467 12.2533 15.4343 12.5657Z" fill="currentColor" />
                    </svg>
                </span>
            </a>
            
        </div>`);


        } else if (allnotificationCount == 0) {
            notificationArray.push(`<div class="tab-content">
            <div class="tab-pane fade show active" id="kt_topbar_notifications_5" role="tabpanel">
                <div class="scroll-y mh-325px my-5 px-8">
                    <div class="d-flex flex-stack py-4">
                        <div class="d-flex align-items-center">
                            <div class="mb-0 me-2">
                                <a href="javascript:void(0)" class="fs-6 text-gray-800 text-hover-primary fw-bold">
                                    No Notifications To Show
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`);
        }

        if(fetchNotification == 'true'){
            $('#notifications-container').html(notificationArray);
            $("#notifications-container").css("height", "auto");
        }
        $('#notification-popup').html(notificationPopup);
        $('#notification-all').html(notificationArrayData);
        if ($("#tableNotification").length > 0) {
            $('#tableNotification').DataTable().destroy();
            $("#tableNotification").DataTable({
                "responsive": false,
                "lengthChange": true,
                "autoWidth": true,
                "ordering": false,
                "pageLength": 100,
            });
        }
       
        if(fetchNotification == 'false'){
            if (notificationCount > localStorage.getItem("count")) {
                var audio = new Audio(base_url + "/google_notification.mp3");
    
                var resp = audio.play();
    
                if (resp !== undefined) {
                    resp.then(_ => {
                        // Toast.fire({
                        //     icon: 'success',
                        //     title: 'New Notification Please Check',
                        //     timer: 3000,
                        // });
                    }).catch(error => {
                        //show error
                    });
                }
    
            }
    
            localStorage.setItem("count", notificationCount);
    
        }
    });
}


var read_notification = (action_url, key) => {


    firebase_db.ref('tbl_notifications/' + key).once('value').then((snapshot) => {
        var notification = snapshot.val();

        if (notification.viewed_by) {
            let viewed_by = notification.viewed_by;
            if (!viewed_by.includes(user_id)) {
                viewed_by.push(user_id);
            }
            notification['viewed_by'] = viewed_by;
            firebase_db.ref('tbl_notifications/' + key).set(notification);

        } else {
            notification['viewed_by'] = [user_id];
            firebase_db.ref('tbl_notifications/' + key).set(notification);
        }
        console.log(action_url);
        window.location.href = base_url + '/' + action_url;
    });

}

var read_all_notification_btn = () => {

    unReadNotification.forEach(function(key) {

        firebase_db.ref('tbl_notifications/' + key).once('value').then((snapshot) => {
            var notification = snapshot.val();

            if (notification.viewed_by) {
                let viewed_by = notification.viewed_by;
                if (!viewed_by.includes(user_id)) {
                    viewed_by.push(user_id);
                }
                notification['viewed_by'] = viewed_by;
                firebase_db.ref('tbl_notifications/' + key).set(notification);

            } else {
                notification['viewed_by'] = [user_id];
                firebase_db.ref('tbl_notifications/' + key).set(notification);
            }

        });
        location.reload();

    });

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
    });

}