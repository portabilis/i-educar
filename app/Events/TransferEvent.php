<?php

namespace App\Events;

use App\Models\LegacyTransferRequest;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class TransferEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
