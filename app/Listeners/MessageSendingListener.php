<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;

class MessageSendingListener
{
    public function handle(MessageSending $event)
    {
        $event->message
            ->getHeaders()
            ->addTextHeader('IsTransactional', 'True');
    }
}
