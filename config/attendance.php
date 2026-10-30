<?php

return [

    /*
    | Hours (0-23) in which employees may check in from the mobile app.
    | Leave a value empty to remove that limit (e.g. for night shifts).
    */
    'mobile_check_in_from' => env('MOBILE_CHECK_IN_FROM', 7),
    'mobile_check_in_until' => env('MOBILE_CHECK_IN_UNTIL', 13),

];
