<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array<int, string> $fillable
 */
class LegacyProject extends Model
{
    public $timestamps = false;

    protected $table = 'pmieducar.projeto';

    protected $primaryKey = 'cod_projeto';

    protected $fillable = [
        'nome',
        'observacao',
    ];

    /**
     * @return HasMany<LegacyStudentProject, $this>
     */
    public function studentProjects(): HasMany
    {
        return $this->hasMany(LegacyStudentProject::class, 'ref_cod_projeto');
    }
}
