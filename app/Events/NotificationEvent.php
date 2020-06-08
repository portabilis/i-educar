<?php

namespace App\Events;

use App\Models\Notification;
use App\Models\NotificationType;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\DB;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var Notification
     */
    public $notification;

    /**
     * @var string
     */
    private $tenant;

    /**
     * Create a new event instance.
     *
     * @param Notification $notification
     * @param string $tenant
     */
    public function __construct(Notification $notification, $tenant)
    {
        $this->notification = $notification;
        $this->tenant = $tenant;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel($this->tenant . '-notification-' . md5($this->notification->user_id));
    }

    public function tags()
    {
        return [
            $this->tenant,
            'notification',
            $this->notification->type->name,
        ];
    }
}
