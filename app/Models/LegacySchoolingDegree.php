<?php

namespace App\Models;

class LegacySchoolingDegree extends LegacyModel
{
    /**
     * @var string
     */
    protected $table = 'cadastro.escolaridade';

    /**
     * @var string
     */
    protected $primaryKey = 'idesco';

    /**
     * @var array
     */
    protected $fillable = [
        'idesco',
        'descricao',
        'escolaridade',
    ];

    public array $legacy = [
        'id' => 'idesco',
        'description' => 'descricao',
        'schooling' => 'escolaridade',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
