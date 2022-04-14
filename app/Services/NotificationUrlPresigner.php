<?php

namespace App\Services;

use App\Models\Notification;

class NotificationUrlPresigner extends UrlPresigner
{
    public function getNotificationUrl(Notification $notification)
    {
        return $notification->needsPresignerUrl()
            ? $this->getPresignedUrl($notification->link)
            : $notification->link;
    }
}
