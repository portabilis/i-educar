<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 * @property int $type_id
 */
class Notification extends Model
{
    protected $table = 'public.notifications';

    protected $fillable = [
        'text',
        'link',
        'read_at',
        'user_id',
        'type_id',
    ];

    /**
     * @return BelongsTo<NotificationType, $this>
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(NotificationType::class);
    }

    /**
     * @return BelongsTo<LegacyUser, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'user_id');
    }

    public function needsPresignerUrl(): bool
    {
        return in_array($this->type_id, [
            NotificationType::EXPORT_STUDENT,
            NotificationType::EXPORT_TEACHER,
        ], true);
    }
}
