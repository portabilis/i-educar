<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyEmployeeRole extends LegacyModel
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.servidor_funcao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_servidor_funcao';

    /**
     * @var array
     */
    protected $fillable = [
        'matricula',
        'ref_cod_funcao',
        'ref_cod_servidor',
        'ref_ref_cod_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    protected function id(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_servidor_funcao
        );
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(LegacyRole::class, 'ref_cod_funcao');
    }
}
