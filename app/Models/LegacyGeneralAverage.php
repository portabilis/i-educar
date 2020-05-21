<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegacyGeneralAverage extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.media_geral';

    /**
     * @var array
     */
    protected $fillable = [
        'nota_aluno_id',
        'media',
        'media_arredondada',
        'etapa',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;


    /**
     * @return BelongsTo
     */
    public function studentScore()
    {
        return $this->belongsTo(LegacyStudentScore::class, 'nota_aluno_id');
    }
}
