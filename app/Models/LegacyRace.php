<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LegacyRace extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;

    /**
     * @var string
     */
    protected $table = 'cadastro.raca';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_raca';

    /**
     * @var array
     */
    protected $fillable = [
        'idpes_cad',
        'nm_raca',
        'raca_educacenso',
    ];

    public function individual(): BelongsToMany
    {
        return $this->belongsToMany(LegacyIndividual::class, 'fisica_raca', 'ref_cod_raca', 'ref_idpes');
    }
}
