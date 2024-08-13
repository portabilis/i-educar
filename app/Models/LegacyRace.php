<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property array<int, string> $fillable
 */
class LegacyRace extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    protected $table = 'cadastro.raca';

    protected $primaryKey = 'cod_raca';

    protected $fillable = [
        'idpes_cad',
        'nm_raca',
        'raca_educacenso',
    ];

    /**
     * @return BelongsToMany<LegacyIndividual, $this>
     */
    public function individual(): BelongsToMany
    {
        return $this->belongsToMany(LegacyIndividual::class, 'fisica_raca', 'ref_cod_raca', 'ref_idpes');
    }
}
