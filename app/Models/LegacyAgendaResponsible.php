<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A chave primária é composta por ref_cod_agenda e ref_ref_cod_pessoa_fj, mas o Eloquent considera
 * apenas a primeira coluna em find, save, update, delete e refresh, o que atingiria os demais
 * responsáveis da agenda. As escritas devem passar sempre por where() ou create().
 *
 * @property int $ref_cod_agenda
 * @property int $ref_ref_cod_pessoa_fj
 * @property int|null $principal
 */
class LegacyAgendaResponsible extends LegacyModel
{
    protected $table = 'portal.agenda_responsavel';

    protected $primaryKey = 'ref_cod_agenda';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'ref_cod_agenda',
        'ref_ref_cod_pessoa_fj',
        'principal',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(LegacyAgenda::class, 'ref_cod_agenda');
    }
}
