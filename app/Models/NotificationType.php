<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    const TRANSFER = 1;
    const OTHER = 2;
    const EXPORT_STUDENT = 3;
    const EXPORT_TEACHER = 4;

    /**
     * @var string
     */
    protected $table = 'public.notification_type';

    protected $fillable = [
        'name',
    ];
}
