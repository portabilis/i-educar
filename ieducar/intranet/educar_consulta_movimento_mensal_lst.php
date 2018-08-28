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

        $this->addCabecalhos(['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']);

        $this->addLinhas([
            'tipo' => 'html-puro',
            'conteudo' => '
                <tr>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">Série</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">Turma</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="3">Turno</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="3">Matrícula inicial</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="14">Alunos</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="3">Matrícula final</td>
                </tr>
                
                <tr>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">F</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">T</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">Transf.</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">Aband.</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">Admitido</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">Óbito</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">Reclassif.</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">Troca (entrou)</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" colspan="2">Troca (saiu)</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">F</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;" rowspan="2">T</td>
                </tr>
                
                <tr>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">F</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">F</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">F</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">F</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">F</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">F</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">M</td>
                    <td class="formdktd" style="font-weight: bold; text-align: center;">F</td>
                </tr>
            '
        ]);

        $template = '<a href="#" class="mostra-consulta" style="font-weight: bold;" data-api="ConsultaMovimentoMensal" data-params=\'%s\' data-tipo="%s">%d</a>';

        foreach ($data as $item) {
            $paramsCopy = $params;
            $paramsCopy['serie'] = $item['cod_serie'];
            $paramsCopy['turma'] = $item['cod_turma'];
            $paramsCopy = json_encode($paramsCopy);

            $this->addLinhas([
                $item['nm_serie'],
                $item['nm_turma'],
                $item['turno'],
                sprintf($template, $paramsCopy, 'mat_ini_m', $item['mat_ini_m']),
                sprintf($template, $paramsCopy, 'mat_ini_f', $item['mat_ini_f']),
                $item['mat_ini_t'],
                sprintf($template, $paramsCopy, 'mat_transf_m', $item['mat_transf_m']),
                sprintf($template, $paramsCopy, 'mat_transf_f', $item['mat_transf_f']),
                sprintf($template, $paramsCopy, 'mat_aband_m', $item['mat_aband_m']),
                sprintf($template, $paramsCopy, 'mat_aband_f', $item['mat_aband_f']),
                sprintf($template, $paramsCopy, 'mat_admit_m', $item['mat_admit_m']),
                sprintf($template, $paramsCopy, 'mat_admit_f', $item['mat_admit_f']),
                sprintf($template, $paramsCopy, 'mat_falecido_m', $item['mat_falecido_m']),
                sprintf($template, $paramsCopy, 'mat_falecido_f', $item['mat_falecido_f']),
                sprintf($template, $paramsCopy, 'mat_reclassificados_m', $item['mat_reclassificados_m']),
                sprintf($template, $paramsCopy, 'mat_reclassificados_f', $item['mat_reclassificados_f']),
                sprintf($template, $paramsCopy, 'mat_trocae_m', $item['mat_trocae_m']),
                sprintf($template, $paramsCopy, 'mat_trocae_f', $item['mat_trocae_f']),
                sprintf($template, $paramsCopy, 'mat_trocas_m', $item['mat_trocas_m']),
                sprintf($template, $paramsCopy, 'mat_trocas_f', $item['mat_trocas_f']),
                $item['mat_final_m'],
                $item['mat_final_f'],
                $item['mat_final_t'],
            ]);

            Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/consulta_movimentos.js']);
        }
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
