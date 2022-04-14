<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;

class MessageSendingListener
{
    /**
     * @param MessageSending $event
     */
    public function handle(MessageSending $event)
    {
        $event->message
            ->getHeaders()
            ->addTextHeader('IsTransactional', 'True');
    }
}
