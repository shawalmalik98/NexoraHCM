function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition, showError);
    } else {
        console.log("Geolocation is not supported by this browser.");
    }
}

function showPosition(position) {
    gl = '<input type="hidden" value="'+position.coords.latitude+'" name="login_history_longitude" id="login_history_longitude" /><input type="hidden" value="'+position.coords.longitude+'" name="login_history_latitude" id="login_history_latitude" />';
}

function showError() {
    gl = '<input type="hidden" value="" name="login_history_longitude" id="login_history_longitude" /><input type="hidden" value="" name="login_history_latitude" id="login_history_latitude" />';
}

// setInterval(function () {getLocation();}, 40);
  
  
