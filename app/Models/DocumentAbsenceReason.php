<?php

namespace App\Models;

use App\Models\Builders\DocumentAbsenceReasonBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property array<int, string> $fillable
 * @property string $name
 * @property bool $requires_observation
 * @property bool $from_system
 */
class DocumentAbsenceReason extends Model
{
    /** @use HasBuilder<DocumentAbsenceReasonBuilder> */
    use HasBuilder;

    use SoftDeletes;

    protected static string $builder = DocumentAbsenceReasonBuilder::class;

    protected $fillable = [
        'name',
        'requires_observation',
        'from_system',
        'deleted_at',
    ];

    protected $casts = [
        'requires_observation' => 'boolean',
        'from_system' => 'boolean',
    ];
}
