<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ErrorTrackerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var Throwable
     */
    private $exception;

    /**
     * @var array
     */
    private $data;

    /**
     * Create a new message instance.
     *
     * @param $to
     * @param $subject
     * @param $content
     */
    public function __construct($to, $subject, $content)
    {
        $this->to($to);
        $this->subject($subject);
        $this->with('content', $content);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('errors.email');
    }
}
