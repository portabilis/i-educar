<?php

namespace App\Models;

use App\Models\Builders\LegacyAgendaCommitmentBuilder;
use Illuminate\Database\Eloquent\HasBuilder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A chave primária é composta por cod_agenda_compromisso, versao e ref_cod_agenda, mas o Eloquent
 * considera apenas a primeira coluna em find, save, update, delete e refresh, o que atingiria as
 * demais versões do compromisso. As escritas devem passar sempre por where() ou create().
 *
 * Os campos de data não possuem cast porque as telas dependem do valor cru do banco: a exibição da
 * hora de início é decidida pelo comprimento da string.
 *
 * @property int $cod_agenda_compromisso
 * @property int $versao
 * @property int $ref_cod_agenda
 * @property int $ref_ref_cod_pessoa_cad
 * @property int|null $ativo
 * @property string|null $data_inicio
 * @property string|null $titulo
 * @property string|null $descricao
 * @property int $importante
 * @property int $publico
 * @property string $data_cadastro
 * @property string|null $data_fim
 *
 * @method static LegacyAgendaCommitmentBuilder query()
 */
class LegacyAgendaCommitment extends LegacyModel
{
    /** @use HasBuilder<LegacyAgendaCommitmentBuilder> */
    use HasBuilder;

    protected static string $builder = LegacyAgendaCommitmentBuilder::class;

    protected $table = 'portal.agenda_compromisso';

    protected $primaryKey = 'cod_agenda_compromisso';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'cod_agenda_compromisso',
        'versao',
        'ref_cod_agenda',
        'ref_ref_cod_pessoa_cad',
        'ativo',
        'data_inicio',
        'titulo',
        'descricao',
        'importante',
        'publico',
        'data_cadastro',
        'data_fim',
    ];

    public function agenda(): BelongsTo
    {
        return $this->belongsTo(LegacyAgenda::class, 'ref_cod_agenda');
    }
}
