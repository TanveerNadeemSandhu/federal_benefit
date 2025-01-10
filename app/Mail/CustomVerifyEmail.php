<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomVerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;
    public $email;
    /**
     * Create a new message instance.
     */
    public function __construct($url, $email)
    {
        $this->url = $url;
        $this->email = $email;
    }

    public function build()
    {
        return $this->from("donotreply@engagedlearning.net", 'Fed Benefit Analyzer')
            ->subject('Confirm Email Address')
            ->view('admin.mail.verifyEmail')
            ->with([
                'appName' => 'Fed Benefit Analyzer',
                'url' => $this->url
            ])->to($this->email);

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
