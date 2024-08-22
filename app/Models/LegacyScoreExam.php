<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 */
class LegacyScoreExam extends Model
{
    protected $table = 'modules.nota_exame';

    protected $primaryKey = 'ref_cod_matricula';

    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_componente_curricular',
        'nota_exame',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return BelongsTo<LegacyRegistration, $this>
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'ref_cod_matricula');
    }
}
