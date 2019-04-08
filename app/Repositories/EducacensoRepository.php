<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class EducacensoRepository
{
    /**
     * @param $sql
     * @param array $params
     * @return array
     */
    protected function fetchPreparedQuery($sql, $params = [])
    {
        return DB::select(DB::raw($sql), $params);
    }

    /**
     * @param $school
     * @param $year
     * @return array
     */
    public function getDataForRecord00($school, $year)
    {
        $sql = <<<'SQL'
            SELECT
            '00' AS registro,
            ece.cod_escola_inep AS "codigoInep",
            e.situacao_funcionamento AS "situacaoFuncionamento",
            (SELECT min(ano_letivo_modulo.data_inicio)
              FROM pmieducar.ano_letivo_modulo
              WHERE ano_letivo_modulo.ref_ano = :year AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS "inicioAnoLetivo",
            (SELECT max(ano_letivo_modulo.data_fim)
              FROM pmieducar.ano_letivo_modulo
              WHERE ano_letivo_modulo.ref_ano = :year AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS "fimAnoLetivo",
            j.fantasia AS nome,
            COALESCE(ep.cep, ee.cep) AS cep,
            municipio.cod_ibge AS "codigoIbgeMunicipio",
            distrito.cod_ibge AS "codigoIbgeDistrito",
            COALESCE(l.idtlog || l.nome, ee.idtlog || ee.logradouro) AS logradouro,
            COALESCE(ep.numero, ee.numero) AS numero,
            COALESCE(ep.complemento, ee.complemento) AS complemento,
            COALESCE(bairro.nome, ee.bairro) AS bairro,
            (SELECT COALESCE(
              (SELECT min(fone_pessoa.ddd)
                    FROM cadastro.fone_pessoa
                    WHERE j.idpes = fone_pessoa.idpes),
              (SELECT min(ddd_telefone)
                FROM pmieducar.escola_complemento
                WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS ddd,
            (SELECT COALESCE(
              (SELECT min(fone_pessoa.fone)
                    FROM cadastro.fone_pessoa
                    WHERE j.idpes = fone_pessoa.idpes),
              (SELECT min(telefone)
                FROM pmieducar.escola_complemento
                WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS telefone,
            (SELECT COALESCE(
              (SELECT min(fone_pessoa.fone)
                    FROM cadastro.fone_pessoa
                    WHERE j.idpes = fone_pessoa.idpes AND fone_pessoa.tipo = 2),
              (SELECT min(fax)
                FROM pmieducar.escola_complemento
                WHERE escola_complemento.ref_cod_escola = e.cod_escola))) AS "telefoneOutro",
            (SELECT COALESCE(p.email,(SELECT email FROM pmieducar.escola_complemento WHERE ref_cod_escola = e.cod_escola))) AS email,
            i.orgao_regional AS "orgaoRegional",
            e.zona_localizacao AS "zonaLocalizacao",
            e.localizacao_diferenciada AS "localizacaoDiferenciada",
            e.dependencia_administrativa AS "dependenciaAdministrativa",
            (ARRAY[1] <@ e.orgao_vinculado_escola)::INT AS "orgaoOutro",
            (ARRAY[2] <@ e.orgao_vinculado_escola)::INT AS "orgaoEducacao",
            (ARRAY[3] <@ e.orgao_vinculado_escola)::INT AS "orgaoSeguranca",
            (ARRAY[4] <@ e.orgao_vinculado_escola)::INT AS "orgaoSaude",
            (ARRAY[1] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraEmpresa",
            (ARRAY[2] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraSindicato",
            (ARRAY[3] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraOng",
            (ARRAY[4] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraInstituicoes",
            (ARRAY[5] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraSistemaS",
            (ARRAY[6] <@ e.mantenedora_escola_privada)::INT AS "mantenedoraOscip",
            e.categoria_escola_privada AS "categoriaEscolaPrivada",
            e.conveniada_com_poder_publico AS "conveniadaPoderPublico",
            e.cnpj_mantenedora_principal AS "cnpjMantenedoraPrincipal",
            j.cnpj AS "cnpjEscolaPrivada",
            e.regulamentacao AS "regulamentacao",
            CASE WHEN e.esfera_administrativa = 1 THEN 1 ELSE 0 END AS "esferaFederal",
            CASE WHEN e.esfera_administrativa = 2 THEN 1 ELSE 0 END AS "esferaEstadual",
            CASE WHEN e.esfera_administrativa = 3 THEN 1 ELSE 0 END AS "esferaMunicipal",
            e.unidade_vinculada_outra_instituicao AS "unidadeVinculada",
            e.inep_escola_sede AS "inepEscolaSede",
            e.codigo_ies AS "codigoIes",

            e.mantenedora_escola_privada[1] AS "mantenedoraEscolaPrivada",
            e.orgao_vinculado_escola AS "orgaoVinculado",
            e.esfera_administrativa AS "esferaAdministrativa",
            e.cod_escola AS "idEscola",
            municipio.idmun AS "idMunicipio",
            distrito.iddis AS "idDistrito",
            i.cod_instituicao AS "idInstituicao",
            uf.sigla_uf AS "siglaUf",
            (SELECT EXTRACT(YEAR FROM min(ano_letivo_modulo.data_inicio))
              FROM pmieducar.ano_letivo_modulo
              WHERE ano_letivo_modulo.ref_ano = :year AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS "anoInicioAnoLetivo",
            (SELECT EXTRACT(YEAR FROM max(ano_letivo_modulo.data_fim))
              FROM pmieducar.ano_letivo_modulo
              WHERE ano_letivo_modulo.ref_ano = :year AND ano_letivo_modulo.ref_ref_cod_escola = e.cod_escola) AS "anoFimAnoLetivo"

            FROM pmieducar.escola e
            JOIN pmieducar.instituicao i ON i.cod_instituicao = e.ref_cod_instituicao
            INNER JOIN modules.educacenso_cod_escola ece ON (e.cod_escola = ece.cod_escola)
            INNER JOIN cadastro.pessoa p ON (e.ref_idpes = p.idpes)
            INNER JOIN cadastro.juridica j ON (j.idpes = p.idpes)
            LEFT JOIN cadastro.endereco_externo ee ON (ee.idpes = p.idpes)
            LEFT JOIN cadastro.endereco_pessoa ep ON (ep.idpes = p.idpes)
            LEFT JOIN public.bairro ON (bairro.idbai = COALESCE(ep.idbai, (SELECT b.idbai
                                                                       FROM public.bairro b
                                                                           INNER JOIN cadastro.endereco_externo ee
                                                                               ON (UPPER(ee.bairro) = UPPER(b.nome))
                                                                       WHERE ee.idpes = e.ref_idpes
                                                                       LIMIT 1)))
            LEFT JOIN public.municipio ON (municipio.idmun = bairro.idmun)
            LEFT JOIN public.uf ON (uf.sigla_uf = COALESCE(municipio.sigla_uf, ee.sigla_uf))
            LEFT JOIN public.distrito ON (distrito.idmun = bairro.idmun)

            LEFT JOIN urbano.cep_logradouro_bairro clb ON (clb.idbai = ep.idbai AND clb.idlog = ep.idlog AND clb.cep = ep.cep)
            LEFT JOIN urbano.cep_logradouro cl ON (cl.idlog = clb.idlog AND clb.cep = cl.cep)
            LEFT JOIN public.logradouro l ON (l.idlog = cl.idlog)
            WHERE e.cod_escola = :school
SQL;

        return $this->fetchPreparedQuery($sql, [
            'school' => $school,
            'year' => $year,
        ]);
    }

    /**
     * @param $school
     * @return array
     */
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
