<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $model_id
 * @property string $model_type
 * @property string $type
 * @property array $payload
 * @property Carbon $created_at
 */
class Timeline extends Model
{
    protected $table = 'timelines';

    protected $fillable = [
        'model_id',
        'model_type',
        'type',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public const UPDATED_AT = null;

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
