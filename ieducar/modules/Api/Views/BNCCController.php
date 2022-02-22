<?php

class BNCCController extends ApiCoreController
{
    public function getBNCC()
    {
        $frequencia = $this->getRequest()->frequencia;

        if (is_numeric($frequencia)) {
            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->lista($frequencia)) {
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

        if (is_numeric($turma)) {
            $bncc = [];
            $bncc_temp = [];
            $obj = new clsModulesBNCC();

            if ($bncc_temp = $obj->listaTurma($turma, $componente_curricular)) {
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

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'bncc')) {
            $this->appendResponse($this->getBNCC());
        }
        else if ($this->isRequestFor('get', 'bncc_turma')) {
            $this->appendResponse($this->getBNCCTurma());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
