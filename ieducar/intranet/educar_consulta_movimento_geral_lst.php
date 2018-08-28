<?php

use iEducar\Modules\Reports\QueryFactory\MovimentoGeralQueryFactory;

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Consulta de movimento geral');
        $this->processoAp = 9998900;
    }
}

class indice extends clsListagem
{
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

        $this->addCabecalhos(['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']);

        $this->addLinhas([
            'tipo' => 'html-puro',
            'conteudo' => '
                <tr>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Ed. Inf. Integ.</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Ed. Inf. Parc..</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">1° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">2° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">3° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">4° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">5° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">6° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">7° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">8° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">9° Ano</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="3">Eliminados</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Rem.</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Recla.</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Óbito</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">Localização</td>
                </tr>
                
                <tr>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">AD</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">AB</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">TR</td>
                </tr>
            '
        ]);

        $params['curso'] = empty($params['curso']) ? '' : join(',', $params['curso']);
        $linkTemplate = '<a href="#" class="mostra-consulta" style="font-weight: bold;" data-api="ConsultaMovimentoGeral" data-params=\'%s\' data-tipo="%s">%d</a>';

        foreach ($data as $item) {
            $this->addLinhas($item['escola']);

            $paramsCopy = $params;
            $paramsCopy['escola'] = $item['cod_escola'];
            $paramsCopy = json_encode($paramsCopy);

            $this->addLinhas([
                sprintf($linkTemplate, $paramsCopy, 'ed_inf_int', $item['ed_inf_int']),
                sprintf($linkTemplate, $paramsCopy, 'ed_inf_parc', $item['ed_inf_parc']),
                sprintf($linkTemplate, $paramsCopy, 'ano_1', $item['ano_1']),
                sprintf($linkTemplate, $paramsCopy, 'ano_2', $item['ano_2']),
                sprintf($linkTemplate, $paramsCopy, 'ano_3', $item['ano_3']),
                sprintf($linkTemplate, $paramsCopy, 'ano_4', $item['ano_4']),
                sprintf($linkTemplate, $paramsCopy, 'ano_5', $item['ano_5']),
                sprintf($linkTemplate, $paramsCopy, 'ano_6', $item['ano_6']),
                sprintf($linkTemplate, $paramsCopy, 'ano_7', $item['ano_7']),
                sprintf($linkTemplate, $paramsCopy, 'ano_8', $item['ano_8']),
                sprintf($linkTemplate, $paramsCopy, 'ano_9', $item['ano_9']),
                sprintf($linkTemplate, $paramsCopy, 'admitidos', $item['admitidos']),
                sprintf($linkTemplate, $paramsCopy, 'aband', $item['aband']),
                sprintf($linkTemplate, $paramsCopy, 'transf', $item['transf']),
                sprintf($linkTemplate, $paramsCopy, 'rem', $item['rem']),
                sprintf($linkTemplate, $paramsCopy, 'recla', $item['recla']),
                sprintf($linkTemplate, $paramsCopy, 'obito', $item['obito']),
                $item['localizacao']
            ]);
        }

        Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/consulta_movimentos.js']);
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
