<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyEmployeeRole extends Model
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
        'cod_servidor_funcao',
        'matricula',
        'ref_cod_funcao',
        'ref_cod_servidor',
        'ref_ref_cod_instituicao',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function getIdAttribute(): int
    {
        return $this->cod_servidor_funcao;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo('App\\Models\\LegacyRole', 'ref_cod_funcao');
    }
}
