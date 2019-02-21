<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class RegistrationScore extends Pivot
{
    /**
     * @var string
     */
    protected $table = 'modules.nota_aluno';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var string
     */
    protected $foreignKey = 'cod_matricula';

    /**
     * @var string
     */
    protected $relatedKey = 'matricula_id';

    /**
     * @var array
     */
    protected $fillable = [
        'matricula_id'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
