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
     * @return string
     *
     * @throws Exception
     */
    public function getSqlHeaderReport()
    {
        $instituicao = $this->args['instituicao'] ?: 0;
        $escola = $this->args['escola'] ?: 0;
        $notSchool = empty($this->args['escola']) ? 'true' : 'false';

        $sql = "
        select public.fcn_upper(instituicao.nm_instituicao) as nm_instituicao,
           public.fcn_upper(instituicao.nm_responsavel) as nm_responsavel,
           (case when {$notSchool} then 'SECRETARIA DE EDUCAÇÃO' else fcn_upper(view_dados_escola.nome) end) as nm_escola,
           (case when {$notSchool} then instituicao.ref_idtlog else view_dados_escola.tipo_logradouro end),
	   (case when {$notSchool} then instituicao.logradouro else view_dados_escola.logradouro end),
	   (case when {$notSchool} then instituicao.bairro else view_dados_escola.bairro end),
	   (case when {$notSchool} then instituicao.numero else view_dados_escola.numero end),
	   (case when {$notSchool} then instituicao.ddd_telefone else view_dados_escola.telefone_ddd end) as fone_ddd,
	   (case when {$notSchool} then 0 else view_dados_escola.celular_ddd end) as cel_ddd,
	   (case when {$notSchool} then to_char(instituicao.cep, '99999-999') else to_char(view_dados_escola.cep, '99999-999') end) as cep,
	   (case when {$notSchool} then to_char(instituicao.telefone, '99999-9999') else view_dados_escola.telefone end) as fone,
	   (case when {$notSchool} then ' ' else view_dados_escola.celular end) as cel,
	   (case when {$notSchool} then ' ' else view_dados_escola.email end),
           instituicao.ref_sigla_uf as uf,
           instituicao.cidade,
           view_dados_escola.inep
      from pmieducar.instituicao
inner join pmieducar.escola on (instituicao.cod_instituicao = escola.ref_cod_instituicao)
inner join relatorio.view_dados_escola on (escola.cod_escola = view_dados_escola.cod_escola)
     where instituicao.cod_instituicao = {$instituicao}
       and (case when {$notSchool} then true else view_dados_escola.cod_escola = {$escola} end)
     limit 1
        ";

        return $sql;
    }

    /**
     * Retorna o SQL para buscar os dados do relatório principal.
     *
     * @return string
     *
     * @throws Exception
     */
    public function getSqlMainReport()
    {
        throw new Exception('Missing implementation.');
    }
}
