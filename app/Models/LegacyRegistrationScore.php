<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyRegistrationScore extends Model
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
