<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    /**
     * @var string
     */
    protected $table = 'public.notifications';

    protected $fillable = [
        'text',
        'link',
        'read_at',
        'user_id',
        'type_id',
    ];

    /**
     * @return BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(NotificationType::class);
    }

    public function needsPresignerUrl()
    {
        return in_array($this->type_id, [
            NotificationType::EXPORT_STUDENT,
            NotificationType::EXPORT_TEACHER,
        ]);
    }
}
