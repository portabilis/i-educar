<?php

use \iEducar\Modules\Reports\QueryFactory\MovimentoGeralQueryFactory;

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
                $this->redirect('/intranet/educar_index.php');
                die();
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

        foreach ($data as $d) {
            $this->addLinhas($d['escola']);

            $p = $params;
            $p['escola'] = $d['cod_escola'];
            $p = json_encode($p);

            $this->addLinhas([
                sprintf($linkTemplate, $p, 'ed_inf_int', $d['ed_inf_int']),
                sprintf($linkTemplate, $p, 'ed_inf_parc', $d['ed_inf_parc']),
                sprintf($linkTemplate, $p, 'ano_1', $d['ano_1']),
                sprintf($linkTemplate, $p, 'ano_2', $d['ano_2']),
                sprintf($linkTemplate, $p, 'ano_3', $d['ano_3']),
                sprintf($linkTemplate, $p, 'ano_4', $d['ano_4']),
                sprintf($linkTemplate, $p, 'ano_5', $d['ano_5']),
                sprintf($linkTemplate, $p, 'ano_6', $d['ano_6']),
                sprintf($linkTemplate, $p, 'ano_7', $d['ano_7']),
                sprintf($linkTemplate, $p, 'ano_8', $d['ano_8']),
                sprintf($linkTemplate, $p, 'ano_9', $d['ano_9']),
                sprintf($linkTemplate, $p, 'admitidos', $d['admitidos']),
                sprintf($linkTemplate, $p, 'aband', $d['aband']),
                sprintf($linkTemplate, $p, 'transf', $d['transf']),
                sprintf($linkTemplate, $p, 'rem', $d['rem']),
                sprintf($linkTemplate, $p, 'recla', $d['recla']),
                sprintf($linkTemplate, $p, 'obito', $d['obito']),
                $d['localizacao']
            ]);
        }

        Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/consulta_movimentos.js']);
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
