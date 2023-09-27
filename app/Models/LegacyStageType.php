<?php

namespace App\Models;

use App\Traits\HasInstitution;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyStageType extends LegacyModel
{
    use HasInstitution;
    use HasLegacyDates;
    use HasLegacyUserAction;

    public const CREATED_AT = 'data_cadastro';

    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'pmieducar.modulo';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_modulo';

    /**
     * @var array
     */
    protected $fillable = [
        'cod_modulo',
        'ref_usuario_cad',
        'nm_tipo',
        'num_etapas',
        'descricao',
        'num_meses',
        'ref_cod_instituicao',
        'num_semanas',
        'ativo',
    ];

    public function academicYearStages(): HasMany
    {
        return $this->hasMany(LegacyAcademicYearStage::class, 'ref_cod_modulo');
    }

    public function schoolClassStage(): HasMany
    {
        return $this->hasMany(LegacySchoolClassStage::class, 'ref_cod_modulo');
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => sprintf('%s - %d etapa(s)', $this->nm_tipo, $this->num_etapas)
        );
    }

    /**
     * @param Builder $query
     */
    public function scopeActive($query): Builder
    {
        return $query->where('ativo', 1);
    }

    /**
     * Indica se já existe um registro cadastrado com o mesmo nome e o mesmo
     * número de etapa(s).
     *
     * @param string   $name
     * @param int      $stagesNumber
     * @param int|null $id
     */
    public static function alreadyExists($name, $stagesNumber, $id = null): bool
    {
        return self::query()
            ->where('ativo', 1)
            ->where('nm_tipo', $name)
            ->where('num_etapas', $stagesNumber)
            ->when($id, function ($query) use ($id) {
                $query->where('cod_modulo', '<>', $id);
            })
            ->exists();
    }

    protected function descricao(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => str_replace(["\r\n", "\r", "\n"], '<br />', $value)
        );
    }
}
