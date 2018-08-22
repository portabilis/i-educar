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
        $this->SetTitulo($this->_instituicao . ' i-Educar - Consulta de movimentação geral');
        $this->processoAp = 561; // TODO: mudar para o id real do menu
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

        $this->breadcrumb('Consulta de movimentação geral', ['educar_index.php' => 'Escola']);

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

        $this->addCabecalhos([
            'Ed. inf. integral',
            'Ed. inf. parcial',
            '1° Ano',
            '2° Ano',
            '3° Ano',
            '4° Ano',
            '5° Ano',
            '6° Ano',
            '7° Ano',
            '8° Ano',
            '9° Ano',
            'Admitidos',
            'Abandonos',
            'Transferidos',
            'Rem',
            'Recla',
            'Óbito',
            'Localização'
        ]);

        $linkTemplate = '<a href="#" class="mostra-consulta" data-escola="%d" data-ano="%s" data-inicial="%s" data-final="%s" data-curso="%s" data-tipo="%s">%d</a>';
        $dataCurso = empty($params['curso']) ? '' : join(',', $params['curso']);

        foreach ($data as $d) {
            $this->addLinhas($d['escola']);

            $this->addLinhas([
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ed_inf_int', $d['ed_inf_int']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ed_inf_parc', $d['ed_inf_parc']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_1', $d['ano_1']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_2', $d['ano_2']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_3', $d['ano_3']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_4', $d['ano_4']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_5', $d['ano_5']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_6', $d['ano_6']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_7', $d['ano_7']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_8', $d['ano_8']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'ano_9', $d['ano_9']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'admitidos', $d['admitidos']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'aband', $d['aband']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'transf', $d['transf']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'rem', $d['rem']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'recla', $d['recla']),
                sprintf($linkTemplate, $d['cod_escola'], $params['ano'], $params['data_inicial'], $params['data_final'], $dataCurso, 'obito', $d['obito']),
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
