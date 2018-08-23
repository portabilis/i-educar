<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use \iEducar\Modules\Reports\QueryFactory\MovimentoMensalQueryFactory;

require_once 'include/clsBase.inc.php';
require_once 'include/clsBanco.inc.php';
require_once 'include/clsListagem.inc.php';
require_once 'lib/Portabilis/Date/Utils.php';
require_once 'Portabilis/View/Helper/Application.php';

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo($this->_instituicao . ' i-Educar - Consulta de movimentação mensal');
        $this->processoAp = 561; // TODO: mudar para o id real do menu
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

        $this->breadcrumb('Consulta de movimentação mensal', ['educar_index.php' => 'Escola']);

        $required = [
            'ano',
            'instituicao',
            'escola',
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

        foreach ($data as $d) {
            $p = $params;
            $p['serie'] = $d['cod_serie'];
            $p['turma'] = $d['cod_turma'];

            $this->addLinhas([
                $d['nm_serie'],
                $d['nm_turma'],
                $d['turno'],
                sprintf($template, json_encode($p), 'mat_ini_m', $d['mat_ini_m']),
                sprintf($template, json_encode($p), 'mat_ini_f', $d['mat_ini_f']),
                $d['mat_ini_t'],
                sprintf($template, json_encode($p), 'mat_transf_m', $d['mat_transf_m']),
                sprintf($template, json_encode($p), 'mat_transf_f', $d['mat_transf_f']),
                sprintf($template, json_encode($p), 'mat_aband_m', $d['mat_aband_m']),
                sprintf($template, json_encode($p), 'mat_aband_f', $d['mat_aband_f']),
                sprintf($template, json_encode($p), 'mat_admit_m', $d['mat_admit_m']),
                sprintf($template, json_encode($p), 'mat_admit_f', $d['mat_admit_f']),
                sprintf($template, json_encode($p), 'mat_falecido_m', $d['mat_falecido_m']),
                sprintf($template, json_encode($p), 'mat_falecido_f', $d['mat_falecido_f']),
                sprintf($template, json_encode($p), 'mat_reclassificados_m', $d['mat_reclassificados_m']),
                sprintf($template, json_encode($p), 'mat_reclassificados_f', $d['mat_reclassificados_f']),
                sprintf($template, json_encode($p), 'mat_trocae_m', $d['mat_trocae_m']),
                sprintf($template, json_encode($p), 'mat_trocae_f', $d['mat_trocae_f']),
                sprintf($template, json_encode($p), 'mat_trocas_m', $d['mat_trocas_m']),
                sprintf($template, json_encode($p), 'mat_trocas_f', $d['mat_trocas_f']),
                $d['mat_final_m'],
                $d['mat_final_f'],
                $d['mat_final_t'],
            ]);

            Portabilis_View_Helper_Application::loadJavascript($this, ['/intranet/scripts/consulta_movimentos.js']);
        }
    }
}

$pagina = new clsIndex();
$miolo = new indice();
$pagina->addForm($miolo);
$pagina->MakeAll();
