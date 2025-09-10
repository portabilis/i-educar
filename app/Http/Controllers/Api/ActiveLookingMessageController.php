<?php

namespace App\Http\Controllers\Api;

use App\Services\MessageService;

class ActiveLookingMessageController extends MessageController
{
    protected string $messageLabel = 'observação';

    public function __construct(MessageService $messageService)
    {
        parent::__construct($messageService);
    }
}
