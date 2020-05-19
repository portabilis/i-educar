<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Throwable;

class NewUserMail extends Mailable
{
    use SerializesModels;

    /**
     * @var Throwable
     */
    private $exception;

    /**
     * @var string
     */
    public $login;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $url;

    /**
     * Create a new message instance.
     *
     * @param $login
     * @param $email
     * @param $password
     * @param null $url
     */
    public function __construct($login, $email, $password, $url = null)
    {
        $this->to($email);
        $this->subject('Bem-vindo ao i-Educar!');

        $this->login = $login;
        $this->password = $password;
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mails.new-user');
    }
}
