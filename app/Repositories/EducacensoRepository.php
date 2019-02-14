<?php

namespace App\Services;

use Portabilis_Utils_Database;

class EducacensoRepository
{
    protected function fetchPreparedQuery($sql, $params = [], $hideExceptions = true, $returnOnly = '')
    {
        $options = [
            'params' => $params,
            'show_errors' => !$hideExceptions,
            'return_only' => $returnOnly,
            'messenger' => $this->messenger
        ];

        return Portabilis_Utils_Database::fetchPreparedQuery($sql, $options);
    }

    public function getDataForRecord00($school, $year)
    {
        $sql = "
            SELECT
                educacenso_cod_escola.cod_escola_inep AS inep,
                escola.cod_escola AS cod_escola,
                fisica_gestor.cpf AS cpf_gestor_escolar,
                pessoa_gestor.nome AS nome_gestor_escolar,
                escola.cargo_gestor AS cargo_gestor_escolar,
                escola.email_gestor AS email_gestor_escolar,
                pessoa_gestor.idpes AS idpes_gestor_escolar,
                escola.dependencia_administrativa,
                escola.situacao_funcionamento,
                escola.categoria_escola_privada,
                escola.conveniada_com_poder_publico,
                escola.mantenedora_escola_privada[1],
                escola.cnpj_mantenedora_principal,
                EXTRACT(YEAR FROM modulo1.data_inicio) AS data_inicio,
                EXTRACT(YEAR FROM modulo2.data_fim) AS data_fim,
                escola.latitude AS latitude,
                escola.longitude AS longitude,
                municipio.idmun AS id_municipio,
                municipio.cod_ibge AS inep_municipio,
                uf.cod_ibge AS inep_uf,
                uf.sigla_uf AS sigla_uf,
                distrito.iddis AS id_distrito,
                distrito.cod_ibge AS inep_distrito,
                juridica.fantasia AS nome_escola,
                instituicao.orgao_regional AS orgao_regional,
                instituicao.cod_instituicao AS cod_instituicao
            FROM pmieducar.escola
            JOIN pmieducar.instituicao ON TRUE 
                AND instituicao.cod_instituicao = escola.ref_cod_instituicao
            INNER JOIN cadastro.juridica ON TRUE 
                AND juridica.idpes = escola.ref_idpes
            INNER JOIN pmieducar.escola_ano_letivo ON TRUE 
                AND escola_ano_letivo.ref_cod_escola = escola.cod_escola
            INNER JOIN pmieducar.ano_letivo_modulo modulo1 ON TRUE 
                AND modulo1.ref_ref_cod_escola = escola.cod_escola
                AND modulo1.ref_ano = escola_ano_letivo.ano
                AND modulo1.sequencial = 1
            INNER JOIN pmieducar.ano_letivo_modulo modulo2 ON TRUE 
                AND modulo2.ref_ref_cod_escola = escola.cod_escola
                AND modulo2.ref_ano = escola_ano_letivo.ano
                AND modulo2.sequencial = (
                    SELECT MAX(sequencial)
                    FROM pmieducar.ano_letivo_modulo
                    WHERE ref_ano = escola_ano_letivo.ano
                    AND ref_ref_cod_escola = escola.cod_escola
                )
            LEFT JOIN cadastro.pessoa pessoa_gestor ON TRUE 
                AND pessoa_gestor.idpes = escola.ref_idpes_gestor
            LEFT JOIN cadastro.fisica fisica_gestor ON TRUE 
                AND fisica_gestor.idpes = escola.ref_idpes_gestor
            LEFT JOIN modules.educacenso_cod_escola ON TRUE 
                AND educacenso_cod_escola.cod_escola = escola.cod_escola
            LEFT JOIN cadastro.endereco_pessoa ON TRUE 
                AND endereco_pessoa.idpes = escola.ref_idpes
            LEFT JOIN cadastro.endereco_externo ON TRUE 
                AND endereco_externo.idpes = escola.ref_idpes
            LEFT JOIN public.bairro ON TRUE 
            AND bairro.idbai = COALESCE(endereco_pessoa.idbai, (
                SELECT b.idbai
                FROM public.bairro b
                INNER JOIN cadastro.endereco_externo ee ON (UPPER(ee.bairro) = UPPER(b.nome))
                WHERE ee.idpes = escola.ref_idpes
                LIMIT 1
            ))
            LEFT JOIN public.municipio ON TRUE 
                AND municipio.idmun = bairro.idmun
            LEFT JOIN public.uf ON TRUE 
                AND uf.sigla_uf = COALESCE(municipio.sigla_uf, endereco_externo.sigla_uf)
            LEFT JOIN public.distrito ON TRUE 
                AND distrito.idmun = bairro.idmun
            WHERE TRUE 
                AND escola.cod_escola = $1
                AND escola_ano_letivo.ano = $2
        ";

        return $this->fetchPreparedQuery($sql, [
            $school, $year
        ]);
    }

    public function getDataForRecord10($school)
    {
        $sql = "
            SELECT 
                escola.cod_escola AS cod_escola,
                escola.local_funcionamento AS local_funcionamento,
                escola.condicao AS condicao,
                escola.agua_consumida AS agua_consumida,
                (ARRAY[1] <@ escola.abastecimento_agua)::int AS agua_rede_publica,
                (ARRAY[2] <@ escola.abastecimento_agua)::int AS agua_poco_artesiano,
                (ARRAY[3] <@ escola.abastecimento_agua)::int AS agua_cacimba_cisterna_poco,
                (ARRAY[4] <@ escola.abastecimento_agua)::int AS agua_fonte_rio,
                (ARRAY[5] <@ escola.abastecimento_agua)::int AS agua_inexistente,
                (ARRAY[1] <@ escola.abastecimento_energia)::int AS energia_rede_publica,
                (ARRAY[2] <@ escola.abastecimento_energia)::int AS energia_gerador,
                (ARRAY[3] <@ escola.abastecimento_energia)::int AS energia_outros,
                (ARRAY[4] <@ escola.abastecimento_energia)::int AS energia_inexistente,
                (ARRAY[1] <@ escola.esgoto_sanitario)::int AS esgoto_rede_publica,
                (ARRAY[2] <@ escola.esgoto_sanitario)::int AS esgoto_fossa,
                (ARRAY[3] <@ escola.esgoto_sanitario)::int AS esgoto_inexistente,
                (ARRAY[1] <@ escola.destinacao_lixo)::int AS lixo_coleta_periodica,
                (ARRAY[2] <@ escola.destinacao_lixo)::int AS lixo_queima,
                (ARRAY[3] <@ escola.destinacao_lixo)::int AS lixo_joga_outra_area,
                (ARRAY[4] <@ escola.destinacao_lixo)::int AS lixo_recicla,
                (ARRAY[5] <@ escola.destinacao_lixo)::int AS lixo_enterra,
                (ARRAY[6] <@ escola.destinacao_lixo)::int AS lixo_outros,
                escola.dependencia_sala_diretoria AS dependencia_sala_diretoria,
                escola.dependencia_sala_professores AS dependencia_sala_professores,
                escola.dependencia_sala_secretaria AS dependncia_sala_secretaria,
                escola.dependencia_laboratorio_informatica AS dependencia_laboratorio_informatica,
                escola.dependencia_laboratorio_ciencias AS dependencia_laboratorio_ciencias,
                escola.dependencia_sala_aee AS dependencia_sala_aee,
                escola.dependencia_quadra_coberta AS dependencia_quadra_coberta,
                escola.dependencia_quadra_descoberta AS dependencia_quadra_descoberta,
                escola.dependencia_cozinha AS dependencia_cozinha,
                escola.dependencia_biblioteca AS dependencia_biblioteca,
                escola.dependencia_sala_leitura AS dependencia_sala_leitura,
                escola.dependencia_parque_infantil AS dependencia_parque_infantil,
                escola.dependencia_bercario AS dependencia_bercario,
                escola.dependencia_banheiro_fora AS dependencia_banheiro_fora,
                escola.dependencia_banheiro_dentro AS dependencia_banheiro_dentro,
                escola.dependencia_banheiro_infantil AS dependencia_banheiro_infantil,
                escola.dependencia_banheiro_deficiente AS dependencia_banheiro_deficiente,
                escola.dependencia_banheiro_chuveiro AS dependencia_banheiro_chuveiro,
                escola.dependencia_refeitorio AS dependencia_refeitorio,
                escola.dependencia_dispensa AS dependencia_dispensa,
                escola.dependencia_aumoxarifado AS dependencia_aumoxarifado,
                escola.dependencia_auditorio AS dependencia_auditorio,
                escola.dependencia_patio_coberto AS dependencia_patio_coberto,
                escola.dependencia_patio_descoberto AS dependencia_patio_descoberto,
                escola.dependencia_alojamento_aluno AS dependencia_alojamento_aluno,
                escola.dependencia_alojamento_professor AS dependencia_alojamento_professor,
                escola.dependencia_area_verde AS dependencia_area_verde,
                escola.dependencia_lavanderia AS dependencia_lavanderia,
                escola.dependencia_nenhuma_relacionada AS dependencia_nenhuma_relacionada,
                escola.dependencia_numero_salas_existente AS dependencia_numero_salas_existente,
                escola.dependencia_numero_salas_utilizadas AS dependencia_numero_salas_utilizadas,
                escola.televisoes AS televisoes,
                escola.videocassetes AS videocassetes,
                escola.dvds AS dvds,
                escola.antenas_parabolicas AS antenas_parabolicas,
                escola.copiadoras AS copiadoras,
                escola.retroprojetores AS retroprojetores,
                escola.impressoras AS impressoras,
                escola.aparelhos_de_som AS aparelhos_de_som,
                escola.projetores_digitais AS projetores_digitais,
                escola.faxs AS faxs,
                escola.maquinas_fotograficas AS maquinas_fotograficas,
                escola.computadores AS computadores,
                escola.computadores_administrativo AS computadores_administrativo,
                escola.computadores_alunos AS computadores_alunos,
                escola.impressoras_multifuncionais AS impressoras_multifuncionais,
                escola.total_funcionario AS total_funcionario,
                escola.atendimento_aee AS atendimento_aee,
                escola.atividade_complementar AS atividade_complementar,
                escola.localizacao_diferenciada AS localizacao_diferenciada,
                escola.materiais_didaticos_especificos AS materiais_didaticos_especificos,
                escola.lingua_ministrada AS lingua_ministrada,
                escola.educacao_indigena AS educacao_indigena,
                juridica.fantasia AS nome_escola
            FROM pmieducar.escola
            INNER JOIN cadastro.juridica ON TRUE 
                AND juridica.idpes = escola.ref_idpes
            WHERE TRUE 
                AND escola.cod_escola = $1
        ";

        return $this->fetchPreparedQuery($sql, [$school]);
    }
}
