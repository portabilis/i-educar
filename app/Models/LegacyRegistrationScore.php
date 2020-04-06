<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    /**
     * @return BelongsTo
     */
    public function registration()
    {
        return $this->belongsTo(LegacyRegistration::class, 'matricula_id');
    }
}
