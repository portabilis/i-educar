<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Schooling
 *
 * @property string $$descricao Descrição da Escolaridade
 */
class Schooling extends Model
{
    /**
     * @var string
     */
    protected $table = 'cadastro.escolaridade';

    /**
     * @var int
     */
    protected $primaryKey = 'idesco';

    /**
     * @var array
     */
    protected $fillable = [
        'escolaridade',
    ];

    /**
     * @return int
     */
    public function getIdAttribute()
    {
        return $this->idesco;
    }

    /**
     * @return string
     */
    public function getDescriptionAttribute()
    {
        return $this->descricao;
    }
}
