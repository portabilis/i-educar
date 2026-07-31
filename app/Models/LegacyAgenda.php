<?php

namespace App\Models;

use App\Models\Builders\LegacyAgendaBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $cod_agenda
 * @property int $ref_ref_cod_pessoa_cad
 * @property int|null $ref_ref_cod_pessoa_own
 * @property int|null $ref_ref_cod_pessoa_exc
 * @property string $nm_agenda
 * @property int $publica
 * @property int $envia_alerta
 * @property string $data_cad
 * @property string|null $data_edicao
 *
 * @method static LegacyAgendaBuilder query()
 */
class LegacyAgenda extends LegacyModel
{
    /** @use HasBuilder<LegacyAgendaBuilder> */
    use HasBuilder;

    protected static string $builder = LegacyAgendaBuilder::class;

    protected $table = 'portal.agenda';

    protected $primaryKey = 'cod_agenda';

    public $timestamps = false;

    protected $fillable = [
        'ref_ref_cod_pessoa_cad',
        'ref_ref_cod_pessoa_own',
        'ref_ref_cod_pessoa_exc',
        'nm_agenda',
        'publica',
        'envia_alerta',
        'data_cad',
        'data_edicao',
    ];

    public function commitments(): HasMany
    {
        return $this->hasMany(LegacyAgendaCommitment::class, 'ref_cod_agenda');
    }

    public function responsibles(): HasMany
    {
        return $this->hasMany(LegacyAgendaResponsible::class, 'ref_cod_agenda');
    }
}
