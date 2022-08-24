<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyRegistrationDisciplinaryOccurrenceType extends Model
{
    use HasFactory;
    use Ativo;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.matricula_ocorrencia_disciplinar';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_ocorrencia_disciplinar';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_matricula',
        'ref_cod_tipo_ocorrencia_disciplinar',
        'sequencial',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'observacao',
        'data_exclusao',
        'ativo',
        'visivel_pais',
        'updated_at'
    ];
}
