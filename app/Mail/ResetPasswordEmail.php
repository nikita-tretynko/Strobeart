<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    private User $user;
    private string $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $user = $this->user;
        $token = $this->token;
        $link=getenv("LINK_APP");
        $url = "{$link}/set-new-password?t={$token}";
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->markdown('emails.reset_password')
            ->with(compact('user', 'url'))
            ->subject('Password reset');
    }
}
