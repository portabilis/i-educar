<?php

namespace App\Jobs;

use App\Models\NotificationType;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class NotifyUserExporter implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int    $userId
     * @param string $message
     * @param string $url
     */
    public function __construct(
        private int    $userId,
        private string $message,
        private string $url
    ) {
    }

    /**
     * Execute the job.
     *
     * @param NotificationService $notification
     *
     * @return void
     */
    public function handle(NotificationService $notification)
    {
        $notification->createByUser(
            $this->userId,
            $this->message,
            $this->url,
            NotificationType::EXPORT_STUDENT
        );
    }
}
