<?php

namespace App\Events;

use App\Models\LegacyActiveLooking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActiveLookingChanged
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public const ACTION_CREATED = 'created';

    public const ACTION_UPDATED = 'updated';

    /**
     * @var LegacyActiveLooking
     */
    public $activeLooking;

    /**
     * @var string
     */
    public $action;

    public function __construct(LegacyActiveLooking $activeLooking, string $action = self::ACTION_CREATED)
    {
        $this->activeLooking = $activeLooking;
        $this->action = $action;
    }
}
