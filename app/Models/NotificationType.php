<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    public const TRANSFER = 1;
    public const OTHER = 2;
    public const EXPORT_STUDENT = 3;
    public const EXPORT_TEACHER = 4;
    public const VALIDATION_CLASS = 5;

    /**
     * @var string
     */
    protected $table = 'public.notification_type';

    protected $fillable = [
        'name',
    ];
}
