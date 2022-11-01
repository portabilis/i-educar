<?php

namespace App\Models;

use App\Traits\HasInstitution;

class LegacyGeneralConfiguration extends LegacyModel
{
    use HasInstitution;

    /**
     * @var string
     */
    protected $table = 'pmieducar.configuracoes_gerais';

    public $incrementing = false;

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_instituicao';

    /**
     * @var array
     */
    protected $fillable = [
        'permite_relacionamento_posvendas',
        'url_novo_educacao',
        'mostrar_codigo_inep_aluno',
        'justificativa_falta_documentacao_obrigatorio',
        'tamanho_min_rede_estadual',
        'modelo_boletim_professor',
        'custom_labels',
        'url_cadastro_usuariO',
        'active_on_ieducar',
        'ieducar_image',
        'ieducar_entity_name',
        'ieducar_login_footer',
        'ieducar_external_footer',
        'ieducar_internal_footer',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'ieducar_suspension_message',
        'bloquear_cadastro_aluno',
        'token_novo_educacao',
        'situacoes_especificas_atestados',
        'emitir_ato_autorizativo',
        'emitir_ato_criacao_credenciamento',
    ];

    public $timestamps = false;
}
