<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LegacyRole extends Model
{
    protected $table = 'pmieducar.funcao';
    protected $primaryKey = 'cod_funcao';
    protected $fillable = [
        'cod_funcao',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'nm_funcao',
        'abreviatura',
        'professor',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'ref_cod_instituicao',
    ];
    public $timestamps = false;

    public function scopeAtivo(Builder $query) : Builder
    {
        return $query->where('ativo', 1);
    }

    public function scopeProfessor(Builder $query) : Builder
    {
        return $query->where('professor', 1);
    }

    public function getIdAttribute() : int
    {
        return $this->cod_funcao;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->data_cadastro = now();
        });
    }
}
