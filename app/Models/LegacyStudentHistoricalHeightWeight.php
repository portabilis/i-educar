<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyStudentHistoricalHeightWeight extends Model
{
    public $table = 'pmieducar.aluno_historico_altura_peso';

    public $timestamps = false;

    /**
     * @var array<string, string>
     */
    public $casts = [
        'data_historico' => 'date',
    ];

    /**
     * @var array<int, string>
     */
    public $fillable = [
        'ref_cod_aluno',
        'data_historico',
        'altura',
        'peso',
    ];

    /**
     * @return BelongsTo<LegacyStudent, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(LegacyStudent::class, 'ref_cod_aluno');
    }
}
