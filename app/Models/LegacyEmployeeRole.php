<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $cod_servidor_funcao
 */
class LegacyEmployeeRole extends LegacyModel
{
    protected $table = 'pmieducar.servidor_funcao';

    protected $primaryKey = 'cod_servidor_funcao';

    protected $fillable = [
        'matricula',
        'ref_cod_funcao',
        'ref_cod_servidor',
        'ref_ref_cod_instituicao',
    ];

    public $timestamps = false;

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_servidor_funcao
        );
    }

    /**
     * @return BelongsTo<LegacyRole, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(LegacyRole::class, 'ref_cod_funcao');
    }
}
