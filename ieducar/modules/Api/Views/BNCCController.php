<?php

class BNCCController extends ApiCoreController
{
    public function getBNCC()
    {
        $frequencia = $this->getRequest()->frequencia;
        $campoExperiencia = $this->getRequest()->campoExperiencia;

        if (is_numeric($frequencia)) {
            $db = new clsBanco();

            $sql = "
                SELECT
                    CASE
                        WHEN t.etapa_educacenso = 1 THEN 1
                        WHEN t.etapa_educacenso = 2 THEN 1
                        WHEN t.etapa_educacenso = 3 THEN 1
                        ELSE 0
                    END
                FROM
                    pmieducar.turma as t
                JOIN modules.frequencia as f
                ON (f.ref_cod_turma = t.cod_turma)
                WHERE f.id = {$frequencia}
            ";

            $db->Consulta($sql);
            $db->ProximoRegistro();

            $resultado = $db->Tupla()[0];

            if ($resultado == 1 && $campoExperiencia == '')
                return [];

            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->lista($frequencia, $campoExperiencia)) {
                foreach ($bncc_temp as $bncc_item) {
                    $id = $bncc_item['id'];
                    $codigo = $bncc_item['codigo'];
                    $habilidade = $bncc_item['habilidade'];

                    $bncc[$id] = $codigo . ' - ' . $habilidade;
                }
            }

            return ['bncc' => $bncc];
        }

        return [];
    }

    public function getBNCCTurma()
    {
        $turma = $this->getRequest()->turma;
        $componente_curricular = $this->getRequest()->componente_curricular;

        $objTipoTurma = new clsPmieducarTurma($turma);
        $resultTipoTurma = $objTipoTurma->getTipoTurma();

        if ($resultTipoTurma == 0) {
            if (is_numeric($turma)) {
                $obj = new clsPmieducarTurma($turma);
                $resultado = $obj->getGrau();

                $bncc = [];
                $bncc_temp = [];
                $obj = new clsModulesBNCC();

                if ($bncc_temp = $obj->listaTurma($resultado, $turma, $componente_curricular)) {
                    foreach ($bncc_temp as $bncc_item) {
                        $id = $bncc_item['id'];
                        $codigo = $bncc_item['codigo'];
                        $habilidade = $bncc_item['habilidade'];

                        $bncc[$id] = $codigo . ' - ' . $habilidade;
                    }
                }

                return ['bncc' => $bncc];
            }
        } else {

            if (is_numeric($turma)) {
                $bncc = [];
                $bncc_temp = [];
                $obj = new clsModulesBNCC();

                if ($bncc_temp = $obj->listaTurmaAee($turma, $componente_curricular)) {
                    foreach ($bncc_temp as $bncc_item) {
                        $id = $bncc_item['id'];
                        $codigo = $bncc_item['codigo'];
                        $habilidade = $bncc_item['habilidade'];

                        $bncc[$id] = $codigo . ' - ' . $habilidade;
                    }
                }

                return ['bncc' => $bncc];
            }
        }

        return [];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'bncc')) {
            $this->appendResponse($this->getBNCC());
        } else if ($this->isRequestFor('get', 'bncc_turma')) {
            $this->appendResponse($this->getBNCCTurma());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
