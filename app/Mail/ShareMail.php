<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShareMail extends Mailable
{
    use Queueable, SerializesModels;
    public $details;
    /**
     * Create a new message instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

        /**
     * Build the message.
     *
     * @return $this
     */

    public function build()
    {
        return $this->from("donotreply@engagedlearning.net", 'Fed Benefit Analyzer')
                    ->subject('Shared Access Notification')
                    ->view('admin.mail.shareAccess')
                    ->with([
                        'appName' => 'Fed Benefit Analyzer',
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
