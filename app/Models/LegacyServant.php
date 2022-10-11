<?php

namespace App\Models;

use App\Traits\HasInstitution;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegacyServant extends LegacyModel
{
    use HasInstitution;

    /**
     * @var string
     */
    protected $table = 'pmieducar.servidor';

    /**
     * @var string
     */
    protected $primaryKey = 'cod_servidor';

    /**
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'cod_servidor',
        'data_cadastro',
        'carga_horaria',
    ];

    /**
     * @return HasMany
     */
    public function disciplines()
    {
        return $this->hasMany(LegacyServantDiscipline::class, 'ref_cod_servidor');
    }

    /**
     * @return HasMany
     */
    public function courses()
    {
        return $this->hasMany(LegacyServantTeachCourse::class, 'ref_cod_servidor');
    }
}
