<?php

use iEducar\Modules\Reports\QueryFactory\MovimentoGeralQueryFactory;

return new class extends clsListagem
{
    public function Gerar()
    {
        $params = [];
        $params['ano'] = $this->getQueryString('ano');
        $params['curso'] = array_map('intval', (array) $this->getQueryString('curso')) ?: null;
        $params['data_inicial'] = $this->getQueryString('data_inicial');
        $params['data_final'] = $this->getQueryString('data_final');

        $this->breadcrumb(currentPage: 'Consulta de movimento geral', breadcrumbs: ['educar_index.php' => 'Escola']);

        $required = [
            'ano',
            'data_inicial',
            'data_final',
        ];

        foreach ($required as $req) {
            if (empty($params[$req])) {
                $this->simpleRedirect('/intranet/educar_index.php');
            }
        }

        $params['data_inicial'] = Portabilis_Date_Utils::brToPgSQL($params['data_inicial']);
        $params['data_final'] = Portabilis_Date_Utils::brToPgSQL($params['data_final']);
        $params['seleciona_curso'] = empty($params['curso']) ? 0 : 1;

        $base = new clsBanco;
        $base->FraseConexao();
        $connectionString = 'pgsql:' . $base->getFraseConexao();
        $data = (new MovimentoGeralQueryFactory(connection: new PDO($connectionString), params: $params))
            ->getData();

        $this->titulo = 'Parâmetros';
        $this->acao = 'go("/intranet/educar_consulta_movimento_geral.php")';
        $this->nome_acao = 'Nova consulta';
        $cursos = $this->nomesDosCursos($params['curso']);

        $this->addCabecalhos([
            'Ano',
            'Cursos',
            'Data inicial',
            'Data final',
        ]);

        $this->addLinhas([
            filter_var(value: $params['ano'], filter: FILTER_SANITIZE_STRING),
            implode(separator: '<br>', array: $cursos),
            filter_var(value: $this->getQueryString('data_inicial'), filter: FILTER_SANITIZE_STRING),
            filter_var(value: $this->getQueryString('data_final'), filter: FILTER_SANITIZE_STRING),
        ]);

        $params['curso'] = empty($params['curso']) ? '' : implode(separator: ',', array: $params['curso']);
        $data = json_encode($this->adicionarLinksDasEscolas(data: $data, params: $params));

        $tableScript = <<<JS
(function () {
  let paramsTable = document.querySelectorAll('#form_resultado .tablelistagem')[0];
  paramsTable.setAttribute('style', 'width: 100%;');

  let data = {$data};
  let table = [];

  table.push('<table class="tablelistagem" style="width: 100%; margin-bottom: 100px;" cellspacing="1" cellpadding="4" border="0">');
    table.push('<tr>');
      table.push('<td class="titulo-tabela-listagem" colspan="19">Resultados</td>');
    table.push('</tr>');

    table.push('<tr>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Escola</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Ed. Inf. Integ.</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Ed. Inf. Parc..</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">1° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">2° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">3° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">4° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">5° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">6° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">7° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">8° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">9° Ano</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="3">Movimentações</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Rem.</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Recla.</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Óbito</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Localização</td>');
    table.push('</tr>');

    table.push('<tr>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">AD</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">DF</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">TR</td>');
    table.push('</tr>');

  for (let i = 0; i < data.length; i++) {
    let item = data[i];
    let cellClass = ((i % 2) === 0) ? 'formlttd' : 'formmdtd';

    table.push('<tr>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.escola + ' ' + item.ciclo + item.aee + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ed_inf_int + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ed_inf_parc + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_1 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_2 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_3 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_4 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_5 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_6 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_7 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_8 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.ano_9 + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.admitidos + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.aband + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.transf + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.rem + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.recla + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.obito + '</td>');
      table.push('<td class="' + cellClass + '" valign="top" align="left">' +  item.localizacao + '</td>');
    table.push('</tr>');
  }

    table.push('<tr>');
      table.push('<td class="formmttd" valign="top" align="left" colspan="19"><strong>Legenda</strong><br>* Escola possui AEE<br>** Escola possui regime por ciclo<br>*** Escola possui regime por ciclo e AEE</td>');
    table.push('</tr>');
  table.push('</table>');

  let base = document.querySelectorAll('#corpo')[0];
  let wrapper= document.createElement('div');
  wrapper.innerHTML = table.join('');
  let tableObj = wrapper.firstChild;

  base.appendChild(tableObj);
})();
JS;

        Portabilis_View_Helper_Application::embedJavascript(viewInstance: $this, script: $tableScript, afterReady: false);
        Portabilis_View_Helper_Application::loadJavascript(viewInstance: $this, files: ['/intranet/scripts/consulta_movimentos.js']);
    }

    private function nomesDosCursos(?array $cursoIds): array
    {
        if (empty($cursoIds)) {
            return ['Todos'];
        }

        $dadosCursos = (array) Portabilis_Utils_Database::fetchPreparedQuery(
            'select nm_curso from pmieducar.curso where cod_curso in (' . implode(separator: ', ', array: $cursoIds) . ');'
        );

        return array_column(array: $dadosCursos, column_key: 'nm_curso');
    }

    /**
     * Troca os totais pelos links da consulta detalhada, só nas escolas que o
     * usuário acessa. As colunas de identificação da escola ficam como vieram.
     */
    private function adicionarLinksDasEscolas(array $data, array $params): array
    {
        $colunasSemLink = ['cod_escola', 'escola', 'ciclo', 'aee', 'localizacao'];
        $linkTemplate = '<a href="#" class="mostra-consulta" style="font-weight: bold;" data-api="ConsultaMovimentoGeral" data-params=\'%s\' data-tipo="%s">%d</a>';

        foreach ($data as $key => $value) {
            if (!Portabilis_Utils_User::canAccessEscola(id: $value['cod_escola'])) {
                continue;
            }

            $paramsEscola = json_encode([...$params, 'escola' => $value['cod_escola']]);

            foreach ($value as $k => $v) {
                if (!in_array($k, $colunasSemLink, true)) {
                    $data[$key][$k] = sprintf($linkTemplate, $paramsEscola, $k, $v);
                }
            }
        }

        return $data;
    }

    public function Formular()
    {
        $this->title = 'Consulta de movimento geral';
        $this->processoAp = 9998900;
    }
};
