<?php

use iEducar\Modules\Reports\QueryFactory\MovimentoMensalQueryFactory;

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Consulta de movimento mensal');
        $this->processoAp = 9998910;
    }
}

class indice extends clsListagem
{
    public function Gerar()
    {
        $params = [];

        $params['ano'] = $this->getQueryString('ano');
        $params['instituicao'] = $this->getQueryString('ref_cod_instituicao');
        $params['escola'] = $this->getQueryString('ref_cod_escola');
        $params['curso'] = $this->getQueryString('ref_cod_curso');
        $params['serie'] = $this->getQueryString('ref_cod_serie');
        $params['turma'] = $this->getQueryString('ref_cod_turma');
        $params['data_inicial'] = $this->getQueryString('data_inicial');
        $params['data_final'] = $this->getQueryString('data_final');

        $this->breadcrumb('Consulta de movimento mensal', ['educar_index.php' => 'Escola']);

        $required = [
            'ano',
            'instituicao',
            'escola',
            'data_inicial',
            'data_final'
        ];

        foreach ($required as $req) {
            if (empty($params[$req])) {
                $this->simpleRedirect('/intranet/educar_index.php');
            }
        }

        $params['data_inicial'] = Portabilis_Date_Utils::brToPgSQL($params['data_inicial']);
        $params['data_final'] = Portabilis_Date_Utils::brToPgSQL($params['data_final']);

        $base = new clsBanco();
        $base->FraseConexao();
        $connectionString = 'pgsql:' . $base->getFraseConexao();
        $data = (new MovimentoMensalQueryFactory(new \PDO($connectionString), $params))
            ->getData();

        $this->titulo = 'Parâmetros';
        $this->acao = 'go("/intranet/educar_consulta_movimento_mensal.php")';
        $this->nome_acao = "Nova consulta";

        $escola = 'Todas';
        $curso = 'Todos';
        $serie = 'Todas';
        $turma = 'Todas';

        if (!empty($params['escola'])) {
            $dados = (array)Portabilis_Utils_Database::fetchPreparedQuery("
                select
                    juridica.fantasia
                from
                    pmieducar.escola
                inner join
                    cadastro.juridica on juridica.idpes = escola.ref_idpes
                where true
                    and escola.cod_escola = {$params['escola']}
                limit 1;
            ");

            $escola = $dados[0]['fantasia'];
        }

        if (!empty($params['curso'])) {
            $dados = (array)Portabilis_Utils_Database::fetchPreparedQuery(
                "select nm_curso from pmieducar.curso where cod_curso = {$params['curso']};"
            );

            $curso = $dados[0]['nm_curso'];
        }

        if (!empty($params['serie'])) {
            $dados = (array)Portabilis_Utils_Database::fetchPreparedQuery(
                "select nm_serie from pmieducar.serie where cod_serie = {$params['serie']};"
            );

            $serie = $dados[0]['nm_serie'];
        }

        if (!empty($params['turma'])) {
            $dados = (array)Portabilis_Utils_Database::fetchPreparedQuery(
                "select nm_turma from pmieducar.turma where cod_turma = {$params['turma']};"
            );

            $turma = $dados[0]['nm_turma'];
        }

        $this->addCabecalhos([
            'Ano',
            'Escola',
            'Curso',
            'Série',
            'Turma',
            'Data inicial',
            'Data final'
        ]);

        $this->addLinhas([
            filter_var($params['ano'], FILTER_SANITIZE_STRING),
            $escola,
            $curso,
            $serie,
            $turma,
            filter_var($this->getQueryString('data_inicial'), FILTER_SANITIZE_STRING),
            filter_var($this->getQueryString('data_final'), FILTER_SANITIZE_STRING)
        ]);

        $linkTemplate = '<a href="#" class="mostra-consulta" style="font-weight: bold;" data-api="ConsultaMovimentoMensal" data-params=\'%s\' data-tipo="%s">%d</a>';

        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                switch ($k) {
                    case 'cod_serie':
                    case 'nm_serie':
                    case 'nm_turma':
                    case 'turno':
                    case 'mat_ini_t':
                    case 'mat_final_m':
                    case 'mat_final_f':
                    case 'mat_final_t':
                        continue;
                        break;
                    default:
                        $paramsCopy = $params;
                        $paramsCopy['serie'] = $value['cod_serie'];
                        $paramsCopy['turma'] = $value['cod_turma'];
                        $paramsCopy = json_encode($paramsCopy);
                        $data[$key][$k] = sprintf($linkTemplate, $paramsCopy, $k, $v);
                }
            }
        }

        $data = json_encode($data);

        $tableScript = <<<JS
(function () {
  let paramsTable = document.querySelectorAll('#form_resultado .tablelistagem')[0];
  paramsTable.setAttribute('style', 'width: 100%;');
  
  let data = {$data};
  let table = [];
  
  table.push('<table class="tablelistagem" style="width: 100%; margin-bottom: 100px;" cellspacing="1" cellpadding="4" border="0">');
    table.push('<tr>');
      table.push('<td class="titulo-tabela-listagem" colspan="25">Resultados</td>');
    table.push('</tr>');

    table.push('<tr>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="4">Série</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="4">Turma</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="4">Turno</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="3">Matrícula inicial</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="16">Alunos</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="3">Matrícula final</td>');
    table.push('</tr>');
    
    table.push('<tr>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">T</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2" rowspan="2">Transf.</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2" rowspan="2">Aband.</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2" rowspan="2">Admitido</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2" rowspan="2">Óbito</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="4">Reclassificado</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="4">Remanejado</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">T</td>');
    table.push('</tr>');
    
    table.push('<tr>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">saiu</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">entrou</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">saiu</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">entrou</td>');
    table.push('</tr>');
    
    table.push('<tr>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">F</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">M</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">F</td>');
    table.push('</tr>');

  for (let i = 0; i < data.length; i++) {
    let item = data[i];
    let cellClass = ((i % 2) === 0) ? 'formlttd' : 'formmdtd';

    table.push('<tr>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.nm_serie + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.nm_turma + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.turno + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_ini_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_ini_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_ini_t + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_transf_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_transf_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_aband_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_aband_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_admit_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_admit_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_falecido_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_falecido_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_reclassificados_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_reclassificados_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_reclassificadose_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_reclassificadose_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_trocas_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_trocas_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_trocae_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_trocae_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_final_m + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_final_f + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.mat_final_t + '</td>');
    table.push('</tr>');
  }

  table.push('</table>');
  
  let base = document.querySelectorAll('#corpo')[0];
  let wrapper= document.createElement('div');
  wrapper.innerHTML = table.join('');
  let tableObj = wrapper.firstChild;
  
  base.appendChild(tableObj);
})();
JS;

        Portabilis_View_Helper_Application::embedJavascript($this, $tableScript, false);
        Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/consulta_movimentos.js']);
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
