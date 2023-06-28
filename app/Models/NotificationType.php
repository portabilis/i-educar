<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationType extends Model
{
    public const TRANSFER = 1;

    public const OTHER = 2;

    public const EXPORT_STUDENT = 3;

    public const EXPORT_TEACHER = 4;

    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'public.notification_type';

    protected $fillable = [
        'name',
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'type_id');
    }
}
