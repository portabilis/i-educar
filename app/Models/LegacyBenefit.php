<?php

namespace App\Models;

use App\Traits\Ativo;
use App\Traits\HasLegacyDates;
use App\Traits\HasLegacyUserAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property array<int, string> $fillable
 */
class LegacyBenefit extends LegacyModel
{
    use Ativo;
    use HasLegacyDates;
    use HasLegacyUserAction;

    protected $table = 'pmieducar.aluno_beneficio';

    protected $primaryKey = 'cod_aluno_beneficio';

    protected $fillable = [
        'nm_beneficio',
        'desc_beneficio',
        'data_exclusao',
        'ativo',
    ];

    /**
     * @var array<string, string>
     */
    public array $legacy = [
        'id' => 'cod_aluno_beneficio',
        'name' => 'nm_beneficio',
        'description' => 'desc_beneficio',
        'deleted_at' => 'data_exclusao',
        'active' => 'ativo',
    ];

    /**
     * @return BelongsToMany<LegacyStudent, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(LegacyStudent::class, 'pmieducar.aluno_aluno_beneficio', 'aluno_beneficio_id', 'aluno_id');
    }
}
