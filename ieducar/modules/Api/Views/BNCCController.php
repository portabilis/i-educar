<?php

class BNCCController extends ApiCoreController
{
    public function getBNCC()
    {
        $bncc = [];
        $bncc_temp = [];
        $obj = new clsModulesBNCC();

        if ($bncc_temp = $obj->lista($this->getRequest()->cod_serie, $this->getRequest()->cod_componente_curricular)) {
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
