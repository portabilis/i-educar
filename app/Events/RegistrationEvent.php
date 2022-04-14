<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegistrationEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $registration;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($registration)
    {
        $this->registration = $registration;
    }
}
