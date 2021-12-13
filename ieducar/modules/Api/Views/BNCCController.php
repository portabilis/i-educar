<?php

class BNCCController extends ApiCoreController
{
    public function getBNCC()
    {
        $obj = new clsModulesFrequencia();
        $dados_frequencia = $obj->detalhe($this->getRequest()->frequencia);
        $cod_serie = $dados_frequencia['detalhes']['ref_cod_serie'];
        $cod_componente_curricular = $dados_frequencia['detalhes']['ref_cod_componente_curricular'];

        $bncc = [];
        $bncc_temp = [];
        $obj = new clsModulesBNCC();

        if ($bncc_temp = $obj->lista($cod_serie, $cod_componente_curricular)) {
            foreach ($bncc_temp as $bncc_item) {
                $id = $bncc_item['id'];
                $code = $bncc_item['code'];
                $description = $bncc_item['description'];

                $bncc[$id] = $code . ' - ' . $description;
            }
        }

        return ['bncc' => $bncc];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'bncc')) {
            $this->appendResponse($this->getBNCC());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
