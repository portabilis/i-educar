<?php

namespace App\Models;

use App\Models\Builders\LogUnificationBuilder;
use App\Traits\LegacyAttribute;
use Exception;
use iEducar\Modules\Unification\LogUnificationTypeInterface;
use iEducar\Modules\Unification\PersonLogUnification;
use iEducar\Modules\Unification\StudentLogUnification;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class LogUnification extends Model
{
    use LegacyAttribute;

    /**
     * Builder dos filtros
     */
    protected string $builder = LogUnificationBuilder::class;

    /**
     * @return BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(LegacyIndividual::class, 'updated_by');
    }

    /**
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(LegacyIndividual::class, 'created_by');
    }

    /**
     * @return HasMany
     */
    public function oldData()
    {
        return $this->hasMany(LogUnificationOldData::class, 'unification_id', 'id');
    }

    /**
     * @return MorphTo
     */
    public function main()
    {
        return $this->morphTo(null, 'type', 'main_id');
    }

    /**
     * Abordagem para permitir usar whereHas ou has em relacionamentos polimórficos
     * https://github.com/laravel/framework/issues/5429
     *
     * @return BelongsTo
     */
    public function studentMain()
    {
        return $this->belongsTo(Student::class, 'main_id')
            ->where('type', Student::class);
    }

    /**
     * Abordagem para permitir usar whereHas ou has em relacionamentos polimórficos
     * https://github.com/laravel/framework/issues/5429
     *
     * @return BelongsTo
     */
    public function personMain()
    {
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
     * @return string
     *
     * @throws Exception
     */
    public function getMainName()
    {
        return $this->getAdapter()->getMainPersonName($this);
    }

    /**
     * @return array
     *
     * @throws Exception
     */
    public function getDuplicatesName()
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
            return new PersonLogUnification();
        }

        if ($this->type == Student::class) {
            return new StudentLogUnification();
        }

        throw new Exception('Tipo de unificação inválido');
    }
}
