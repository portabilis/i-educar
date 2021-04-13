<?php

namespace App\Services;

use App\Models\Notification;

class NotificationUrlPresigner extends UrlPresigner
{
    public function getNotificationUrl(Notification $notification): string
    {
        return $notification->needsPresignerUrl()
            ? $this->getPresignedUrl($notification->link)
            : $notification->link;
    }
}
