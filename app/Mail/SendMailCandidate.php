<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\HrmSetting;

class SendMailCandidate extends Mailable
{
    use Queueable, SerializesModels;
    public $subject;
    public $body;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$body)
    {
        //
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $company_name = HrmSetting::get(['company_name'])->toArray();
        if(!empty($company_name)){
            $company_name = $company_name[0]['company_name'];
        }else{
            $company_name = 'Nexora HCM';
        }
        return new Content(
            view: 'email-template.candidate-email',
            with: [
                'body' => $this->body,
                'company_name' => $company_name,
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
