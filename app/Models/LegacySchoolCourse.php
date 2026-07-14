<?php

namespace App\Models;

use App\Models\Builders\LegacySchoolCourseBuilder;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 *
 * @method static LegacySchoolCourseBuilder query()
 */
class LegacySchoolCourse extends LegacyModel
{
    /** @use HasBuilder<LegacySchoolCourseBuilder> */
    use HasBuilder;

    use HasLegacyDates;

    public const CREATED_AT = 'data_cadastro';

    protected $table = 'pmieducar.escola_curso';

    protected $primaryKey = 'ref_cod_escola';

    protected static string $builder = LegacySchoolCourseBuilder::class;

    protected $fillable = [
        'ref_cod_escola',
        'ref_cod_curso',
        'ref_usuario_exc',
        'ref_usuario_cad',
        'data_exclusao',
        'ativo',
        'autorizacao',
        'anos_letivos',
        'updated_at',
    ];

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'ref_cod_escola');
    }

    /**
     * @return BelongsTo<LegacyCourse, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(LegacyCourse::class, 'ref_cod_curso');
    }
}
