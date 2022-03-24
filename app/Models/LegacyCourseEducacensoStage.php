<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LegacyCourseEducacensoStage extends Model
{
    use HasFactory;

    protected $table = 'modules.etapas_curso_educacenso';

    public static function getIdsByCourse(int $course): array
    {
        return static::query()->where('curso_id', $course)->pluck('etapa_id')?->toArray();
    }
}
