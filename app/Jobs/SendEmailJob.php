<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $email;
    private string $text;
    private string $senders_name;
    private string $link;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $email, string $text, string $senders_name = '', string $link = '')
    {
        $this->email = $email;
        $this->text = $text;
        $this->senders_name = $senders_name;
        $this->link = $link;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)
            ->send(new SendEmail($this->text, $this->senders_name, $this->link));
    }
}
