<?php

namespace App\Models;

use App\Models\Enums\ComponentBatchStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComponentBatchOperation extends Model
{
    protected $table = 'component_batch_operations';

    protected $fillable = [
        'user_id',
        'status_id',
        'data',
        'backup',
        'error_message',
    ];

    protected $casts = [
        'data' => 'array',
        'backup' => 'array',
    ];

    public function status(): ComponentBatchStatus
    {
        return ComponentBatchStatus::from($this->status_id);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(LegacyUser::class, 'user_id');
    }
}
