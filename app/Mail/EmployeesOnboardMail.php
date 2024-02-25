<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeesOnboardMail extends Mailable
{
    use Queueable, SerializesModels;
    public $emailData;
    public $emailError;
   
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($emailData = null, $emailError = null)
    {
        $this->emailData = $emailData;
        $this->emailError = $emailError;

    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Employees Onboard',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {

        return new Content(
            view: 'email-template.employees-onboard',
            with: [
                'emailData' => $this->emailError ? null : $this->emailData,
                'emailError' => $this->emailData ? null : $this->emailError,
            ],
        );
    }


    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
