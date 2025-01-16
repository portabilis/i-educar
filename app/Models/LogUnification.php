<?php

namespace App\Models;

use App\Models\Builders\LogUnificationBuilder;
use Exception;
use iEducar\Modules\Unification\LogUnificationTypeInterface;
use iEducar\Modules\Unification\PersonLogUnification;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property string $type
 */
class LogUnification extends Model
{
    /** @use HasBuilder<LogUnificationBuilder> */
    use HasBuilder;

    /**
     * Builder dos filtros
     */
    protected static string $builder = LogUnificationBuilder::class;

    /**
     * @return BelongsTo<LegacyIndividual, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(LegacyIndividual::class, 'updated_by');
    }

    /**
     * @return BelongsTo<LegacyIndividual, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(LegacyIndividual::class, 'created_by');
    }

    /**
     * @return HasMany<LogUnificationOldData, $this>
     */
    public function oldData(): HasMany
    {
        return $this->hasMany(LogUnificationOldData::class, 'unification_id', 'id');
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function main(): MorphTo
    {
        return $this->morphTo(null, 'type', 'main_id');
    }

    /**
     * Abordagem para permitir usar whereHas ou has em relacionamentos polimórficos
     * https://github.com/laravel/framework/issues/5429
     *
     * @return BelongsTo<Student, $this>
     */
    public function studentMain(): BelongsTo
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsTo(Student::class, 'main_id')
            ->where('type', Student::class);
    }

    /**
     * Abordagem para permitir usar whereHas ou has em relacionamentos polimórficos
     * https://github.com/laravel/framework/issues/5429
     *
     * @return BelongsTo<LegacyPerson, $this>
     */
    public function personMain(): BelongsTo
    {
        /** @phpstan-ignore-next-line */
        return $this->belongsTo(LegacyPerson::class, 'main_id')
            ->where('type', Individual::class);
    }

    protected function duplicatesId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => json_decode($value, false)
        );
    }

    /**
     * @throws Exception
     */
    public function getMainName(): string
    {
        return $this->getAdapter()->getMainPersonName($this);
    }

    /**
     * @phpstan-ignore-next-line
     *
     * @throws Exception
     */
    public function getDuplicatesName(): array
    {
        return $this->getAdapter()->getDuplicatedPeopleName($this);
    }

    /**
     * @return LogUnificationTypeInterface
     *
     * @throws Exception
     */
    public function getAdapter()
    {
        if ($this->type == Individual::class) {
            return new PersonLogUnification;
        }

        if ($this->type == Student::class) {
            return new StudentLogUnification;
        }

        throw new Exception('Tipo de unificação inválido');
    }
}
