<?php

namespace App\Events;

use App\Models\LegacyTransferRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var LegacyTransferRequest
     */
    public $transfer;

    /**
     * Create a new event instance.
     *
     * @param $transfer
     */
    public function __construct($transfer)
    {
        $this->transfer = $transfer;
    }
}
