<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyRemedialRule extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.regra_avaliacao_recuperacao';

    /**
     * @var array
     */
    protected $casts = [
        'nota_maxima' => 'float',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return array
     */
    public function getStages()
    {
        return explode(';', $this->etapas_recuperadas);
    }

    /**
     * @return int
     */
    public function getLastStage()
    {
        return max($this->getStages());
    }
}
