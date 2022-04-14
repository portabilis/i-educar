<?php

use iEducar\Modules\Reports\QueryFactory\MovimentoGeralQueryFactory;

return new class extends clsListagem {
    public function Gerar()
    {
        $params = [];
        $params['ano'] = $this->getQueryString('ano');
        $params['curso'] = $this->getQueryString('curso');
        $params['data_inicial'] = $this->getQueryString('data_inicial');
        $params['data_final'] = $this->getQueryString('data_final');

        $this->breadcrumb('Consulta de movimento geral', ['educar_index.php' => 'Escola']);

        $required = [
            'ano',
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
        $params['seleciona_curso'] = empty($params['curso']) ? 0 : 1;

        $base = new clsBanco();
        $base->FraseConexao();
        $connectionString = 'pgsql:' . $base->getFraseConexao();
        $data = (new MovimentoGeralQueryFactory(new \PDO($connectionString), $params))
            ->getData();

        $this->titulo = 'Parâmetros';
        $this->acao = 'go("/intranet/educar_consulta_movimento_geral.php")';
        $this->nome_acao = 'Nova consulta';
        $cursos = [];

        if (empty($params['curso'])) {
            $cursos[] = 'Todos';
        } else {
            $cursoIds = join(', ', $params['curso']);

            $dadosCursos = (array)Portabilis_Utils_Database::fetchPreparedQuery(
                "select nm_curso from pmieducar.curso where cod_curso in ({$cursoIds});"
            );

            foreach ($dadosCursos as $curso) {
                $cursos[] = $curso['nm_curso'];
            }
        }

        $this->addCabecalhos([
            'Ano',
            'Cursos',
            'Data inicial',
            'Data final'
        ]);

        $this->addLinhas([
            filter_var($params['ano'], FILTER_SANITIZE_STRING),
            join('<br>', $cursos),
            filter_var($this->getQueryString('data_inicial'), FILTER_SANITIZE_STRING),
            filter_var($this->getQueryString('data_final'), FILTER_SANITIZE_STRING)
        ]);

        $params['curso'] = empty($params['curso']) ? '' : join(',', $params['curso']);
        $linkTemplate = '<a href="#" class="mostra-consulta" style="font-weight: bold;" data-api="ConsultaMovimentoGeral" data-params=\'%s\' data-tipo="%s">%d</a>';

        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                switch ($k) {
                    case 'cod_escola':
                    case 'escola':
                    case 'ciclo':
                    case 'aee':
                    case 'localizacao':
                        continue;
                        break;
                    default:
                        $paramsCopy = $params;
                        $paramsCopy['escola'] = $value['cod_escola'];
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
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" colspan="3">Eliminados</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Rem.</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Recla.</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Óbito</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Localização</td>');
    table.push('</tr>');

    table.push('<tr>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">AD</td>');
      table.push('<td class="formdktd" style="font-weight: bold; text-align: center;">AB</td>');
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

        Portabilis_View_Helper_Application::embedJavascript($this, $tableScript, false);
        Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/consulta_movimentos.js']);
    }

    public function Formular()
    {
        $this->title = 'Consulta de movimento geral';
        $this->processoAp = 9998900;
    }
};
