<?php

namespace iEducar\Reports;

use Exception;
use Portabilis_Utils_Database;

trait JsonDataSource
{
    /**
     * @inheritdoc
     */
    public function useJson()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getJsonData()
    {
        $queryMainReport = $this->getSqlMainReport();
        $queryHeaderReport = $this->getSqlHeaderReport();

        return [
            'main' => Portabilis_Utils_Database::fetchPreparedQuery($queryMainReport),
            'header' => Portabilis_Utils_Database::fetchPreparedQuery($queryHeaderReport),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getJsonQuery()
    {
        return 'main';
    }

    /**
     * Retorna o SQL para buscar os dados que serão adicionados ao cabeçalho.
     *
     * @throws Exception
     *
     * @return string
     */
    public function getSqlHeaderReport()
    {
        $instituicao = $this->args['instituicao'] ?: 0;
        $escola = $this->args['escola'] ?: 0;
        $notSchool = empty($this->args['escola']) ? 'true' : 'false';

        $sql = "

            SELECT
                public.fcn_upper(instituicao.nm_instituicao) AS nm_instituicao,
                public.fcn_upper(instituicao.nm_responsavel) AS nm_responsavel,
                (CASE WHEN {$notSchool} THEN 'SECRETARIA DE EDUCAÇÃO' ELSE fcn_upper(view_dados_escola.nome) END) AS nm_escola,
                (CASE WHEN {$notSchool} THEN instituicao.bairro ELSE view_dados_escola.bairro END),
                (CASE WHEN {$notSchool} THEN instituicao.ddd_telefone ELSE view_dados_escola.telefone_ddd END) AS fone_ddd,
                (CASE WHEN {$notSchool} THEN 0 ELSE view_dados_escola.celular_ddd END) AS cel_ddd,
                (CASE WHEN {$notSchool} THEN to_char(instituicao.telefone, '99999-9999') ELSE view_dados_escola.telefone END) AS fone,
                (CASE WHEN {$notSchool} THEN ' ' ELSE view_dados_escola.celular END) AS cel,
                (CASE WHEN {$notSchool} THEN ' ' ELSE view_dados_escola.email END),
                instituicao.ref_sigla_uf AS uf,
                instituicao.cidade,
                (CASE WHEN {$notSchool} THEN instituicao.logradouro ELSE a.address END) AS logradouro,
                (CASE WHEN {$notSchool} THEN instituicao.numero::text ELSE a.number END) AS numero,
                (CASE WHEN {$notSchool} THEN instituicao.cep::text ELSE a.postal_code END) AS cep,
                (CASE WHEN {$notSchool} THEN NULL ELSE view_dados_escola.inep END) AS inep,
                escola.ato_autorizativo,
                escola.ato_criacao,
                configuracoes_gerais.emitir_ato_autorizativo,
                configuracoes_gerais.emitir_ato_criacao_credenciamento AS emitir_ato_criacao
            FROM
                pmieducar.instituicao
            INNER JOIN pmieducar.configuracoes_gerais ON TRUE
                AND configuracoes_gerais.ref_cod_instituicao = instituicao.cod_instituicao
            INNER JOIN pmieducar.escola ON TRUE
                AND (instituicao.cod_instituicao = escola.ref_cod_instituicao)
            INNER JOIN relatorio.view_dados_escola ON TRUE
                AND (escola.cod_escola = view_dados_escola.cod_escola)
            LEFT JOIN person_has_place php ON TRUE
                AND php.person_id = escola.ref_idpes AND php.type = 1
            LEFT JOIN addresses a ON TRUE
                AND a.id = php.place_id
            WHERE TRUE
                AND instituicao.cod_instituicao = {$instituicao}
                AND
                (
                    CASE WHEN {$notSchool} THEN
                        TRUE
                    ELSE
                        view_dados_escola.cod_escola = {$escola}
                    END
                )
            LIMIT 1

        ";

        return $sql;
    }

    /**
     * Retorna o SQL para buscar os dados do relatório principal.
     *
     * @throws Exception
     *
     * @return string
     */
    public function getSqlMainReport()
    {
        throw new Exception('Missing implementation.');
    }
}
