<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyProject extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'pmieducar.projeto';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_projeto';

    protected $fillable = [
        'nome',
        'observacao',
    ];

    public function studentProjects(): HasMany
    {
        return $this->hasMany(LegacyStudentProject::class, 'ref_cod_projeto');
    }
}
