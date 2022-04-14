<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolInep extends Model
{
    /**
     * @var string
     */
    protected $table = 'modules.educacenso_cod_escola';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_escola';

    protected $fillable = [
        'cod_escola',
        'cod_escola_inep',
        'nome_inep',
        'fonte',
        'created_at',
        'updated_at'
    ];

    public function getNumberAttribute()
    {
        return $this->cod_escola_inep;
    }

    /**
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(LegacySchool::class, 'cod_escola', 'cod_escola');
    }
}
