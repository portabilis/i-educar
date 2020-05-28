<?php

namespace App\Mail;

use App\User;
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
    public $password;

    /**
     * @var string
     */
    public $url;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new message instance.
     *
     * @param User $user
     * @param $password
     * @param null $url
     */
    public function __construct($user, $password, $url = null)
    {
        $this->to($user->email);
        $this->subject('Bem-vindo ao i-Educar!');

        $this->user = $user;
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
