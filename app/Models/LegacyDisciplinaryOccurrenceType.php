<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyDisciplinaryOccurrenceType extends Model
{
    use Ativo;
    use HasFactory;

    CONST CREATED_AT = "data_cadastro";
    CONST UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.tipo_ocorrencia_disciplinar';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_tipo_ocorrencia_disciplinar';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'descricao',
        'max_ocorrencias',
        'data_exclusao',
        'ref_cod_instituicao',
        'ativo'
    ];
}
