<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyPeriod extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.turma_turno';

    /**
     * @var array
     */
    protected $fillable = [
        'nome', 'ativo',
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
        return $this->nome;
    }
}
