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
    public $url;

    /**
     * @var User
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $name;

    /**
     * Create a new message instance.
     *
     * @param string $email
     * @param string $username
     * @param string $password
     * @param string $name
     * @param string|null $url
     */
    public function __construct($email, $username, $password, $name, $url = null)
    {
        $this->to($email);
        $this->subject('Bem-vindo ao i-Educar!');

        $this->username = $username;
        $this->password = $password;
        $this->url = $url;
        $this->name = $name;
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
