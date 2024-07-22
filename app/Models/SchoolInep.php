<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property array<int, string> $fillable
 * @property int $cod_escola_inep
 */
class SchoolInep extends Model
{
    protected $table = 'modules.educacenso_cod_escola';

    protected $fillable = [
        'cod_escola',
        'cod_escola_inep',
        'nome_inep',
        'fonte',
        'created_at',
        'updated_at',
    ];

    protected function number(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->cod_escola_inep,
        );
    }

    /**
     * @return BelongsTo<LegacySchool, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(LegacySchool::class, 'cod_escola', 'cod_escola');
    }
}
