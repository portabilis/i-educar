<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class EducacensoImport extends Model
{
    protected $table = 'educacenso_imports';

    protected $fillable = [
        'year',
        'school',
        'user_id',
        'finished',
        'registration_date',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'registration_date' => 'date',
    ];

    /**
     * @return BelongsTo<Individual, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Individual::class, 'user_id', 'id');
    }

    public function getDateFormat(): string
    {
        return 'Y-m-d H:i:s';
    }
}
