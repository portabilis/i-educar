<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyStudentBenefit extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'pmieducar.aluno_aluno_beneficio';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'aluno_id',
        'aluno_beneficio_id',
    ];

    /**
     * @return BelongsTo<LegacyStudent, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'aluno_id');
    }

    /**
     * @return BelongsTo<LegacyBenefit, $this>
     */
    public function benefit(): BelongsTo
    {
        return $this->belongsTo(LegacyBenefit::class, 'aluno_beneficio_id');
    }
}
