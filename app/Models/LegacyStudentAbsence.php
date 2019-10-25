<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyStudentAbsence extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.falta_aluno';

    /**
     * @var array
     */
    protected $fillable = [
        'matricula_id',
        'tipo_falta',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;


    /**
     * @return BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'matricula_id');
    }
}
