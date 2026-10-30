

    @if(CustomHelper::get_my_todays_attendance_status() === '0')
        <div class="modal fade" id="attendance_alert" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-top mw-600px">
                <div class="modal-content rounded">
                    <div class="modal-header">
                    <h4 class="modal-title pl-4">Attendance Alert</h4>
                    <button type="button" class="btn btn-sm btn-icon btn-active-color-primary" data-bs-dismiss="modal" aria-label="Close">
                        <span class="svg-icon svg-icon-1">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect opacity="0.5" x="6" y="17.3137" width="16" height="2" rx="1" transform="rotate(-45 6 17.3137)" fill="currentColor" />
                                <rect x="7.41422" y="6" width="16" height="2" rx="1" transform="rotate(45 7.41422 6)" fill="currentColor" />
                            </svg>
                        </span>
                    </button>
                    </div>
                    <div class="modal-body">
                        <p>Ignore this message if you've marked your attendance from biometric machine or mobile offline attendance. You haven't marked your todays attendance. Mark your attendance and be safe from your absent. Thankyou !</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    

