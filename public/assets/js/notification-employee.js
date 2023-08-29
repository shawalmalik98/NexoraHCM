setInterval(function () {
    var notification_count = $('.notification-count-div').data('total_notifications');
    $.ajax({
        url: 'http://192.168.2.105:100/push-notification',
        type: 'GET',
        data: {
            notification_count: notification_count
        },
        success: function (data) {
            if (data.notification_checker == 'yes') {
                $('.notification-refresher').html(data.not);
                $('.notification_counter').html(data.unread_count);
                // var audio = new Audio("http://127.0.0.1:8000/google_notification.mp3");
                // audio.play();
                Toast.fire({
                    icon: 'success',
                    title: 'New Notification Please Check',
                    timer: 3000,
                })
            }
        }
    });
}, 10000);