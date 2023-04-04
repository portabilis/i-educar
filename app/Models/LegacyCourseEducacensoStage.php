<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyCourseEducacensoStage extends Model
{
    protected $table = 'modules.etapas_curso_educacenso';

    protected $primaryKey = 'curso_id';

    protected $fillable = [
        'etapa_id',
        'curso_id',
    ];

    public $timestamps = false;

    public static function getIdsByCourse(int $course): array
    {
        return static::query()->where('curso_id', $course)->pluck('etapa_id')?->toArray();
    }
}
