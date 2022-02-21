<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegacySchoolAcademicYear extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.escola_ano_letivo';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_escola';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_cod_escola',
        'ano',
        'ref_usuario_cad',
        'ref_usuario_exc',
        'andamento',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'turmas_por_ano',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    public function scopeActive(Builder $builder)
    {
        return $builder->where('escola_ano_letivo.ativo', 1);
    }

    public function scopeLastYear(Builder $query): Builder
    {
        return $query->where('escola_ano_letivo.ano', date('Y') - 1);
    }

    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->where('escola_ano_letivo.ano', date('Y'));
    }
}
