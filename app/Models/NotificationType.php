<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class NotificationType extends Model
{
    public const TRANSFER = 1;

    public const OTHER = 2;

    public const EXPORT_STUDENT = 3;

    public const EXPORT_TEACHER = 4;

    public const ACTIVE_LOOKING = 5;

    public $timestamps = false;

    protected $table = 'public.notification_type';

    protected $fillable = [
        'name',
    ];

    /**
     * @return HasMany<Notification, $this>
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'type_id');
    }
}
