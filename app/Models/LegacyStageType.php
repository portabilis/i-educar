<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegacyStageType extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.modulo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_modulo';

    /**
     * @var array
     */
    protected $fillable = [
        'cod_modulo',
        'ref_usuario_cad',
        'nm_tipo',
        'data_cadastro',
        'ref_cod_instituicao',
        'num_etapas',
        'descricao'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return sprintf('%s - %d etapa(s)', $this->nm_tipo, $this->num_etapas);
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', 1);
    }

    /**
     * Indica se já existe um registro cadastrado com o mesmo nome e o mesmo
     * número de etapa(s).
     *
     * @param string   $name
     * @param int      $stagesNumber
     * @param int|null $id
     *
     * @return bool
     */
    public static function alreadyExists($name, $stagesNumber, $id = null)
    {
        return self::query()
            ->where('ativo', 1)
            ->where('nm_tipo', $name)
            ->where('num_etapas', $stagesNumber)
            ->when($id, function ($query) use ($id) {
                $query->where('cod_modulo', '<>', $id);
            })
            ->exists();
    }

    public function getDescricaoAttribute()
    {
        return str_replace(["\r\n", "\r", "\n"], '<br />', $this->attributes['descricao']);
    }
}
