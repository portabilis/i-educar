<?php

namespace App\Models;

use App\Traits\Ativo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegacyQualification extends Model
{
    use Ativo;

    public const CREATED_AT = 'data_cadastro';
    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.habilitacao';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_habilitacao';

    /**
     * @var string[]
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_tipo',
        'descricao',
        'data_exclusao',
        'ref_cod_instituicao',
    ];

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(LegacyCourse::class, 'pmieducar.habilitacao_curso', 'ref_cod_habilitacao', 'ref_cod_curso');
    }
}
