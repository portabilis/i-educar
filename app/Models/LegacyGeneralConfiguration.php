<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LegacyGeneralConfiguration extends Model
{
    /**
     * @var string
     */
    protected $table = 'pmieducar.configuracoes_gerais';

    /**
     * @var string
     */
    protected $primaryKey = 'ref_cod_instituicao';

    /**
     * @var array
     */
    protected $fillable = [
        'ref_usuario_exc',
        'ref_usuario_cad',
        'ref_idtlog',
        'ref_sigla_uf',
        'cep',
        'cidade',
        'bairro',
        'logradouro',
        'numero',
        'complemento',
        'nm_responsavel',
        'ddd_telefone',
        'telefone',
        'data_cadastro',
        'data_exclusao',
        'ativo',
        'nm_instituicao',
        'data_base_remanejamento',
        'data_base_transferencia',
        'controlar_espaco_utilizacao_aluno',
        'percentagem_maxima_ocupacao_salas',
        'quantidade_alunos_metro_quadrado',
        'exigir_vinculo_turma_professor',
        'gerar_historico_transferencia',
        'matricula_apenas_bairro_escola',
        'restringir_historico_escolar',
        'coordenador_transporte',
        'restringir_multiplas_enturmacoes',
        'permissao_filtro_abandono_transferencia',
        'data_base_matricula',
        'multiplas_reserva_vaga',
        'reserva_integral_somente_com_renda',
        'data_expiracao_reserva_vaga',
        'data_fechamento',
        'componente_curricular_turma',
        'reprova_dependencia_ano_concluinte',
        'controlar_posicao_historicos',
        'data_educacenso',
        'bloqueia_matricula_serie_nao_seguinte',
        'permitir_carga_horaria',
        'exigir_dados_socioeconomicos',
        'altera_atestado_para_declaracao',
        'orgao_regional',
        'obrigar_campos_censo',
        'obrigar_documento_pessoa',
        'exigir_lancamentos_anteriores',
        'exibir_apenas_professores_alocados',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;
}
