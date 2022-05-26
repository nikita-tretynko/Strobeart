<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    private string $text;
    private string $senders_name;
    private string $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $text, string $senders_name = '', string $link = '')
    {
        $this->text = $text;
        $this->senders_name = $senders_name;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $text = $this->text;
        $url = $this->link;
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('emails.send_message')
            ->with(compact('text','url'))
            ->subject('New message from '.$this->senders_name);
    }
}
