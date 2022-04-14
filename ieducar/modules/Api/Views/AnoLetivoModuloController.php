<?php

class AnoLetivoModuloController extends ApiCoreController
{
    public function pegarEtapas ()
    {
        $etapas = [];

        $ano = $this->getRequest()->ano;
        $ref_cod_escola = $this->getRequest()->ref_cod_escola;
        
        if (is_numeric($ano) && is_numeric($ref_cod_escola)) {
            $obj = new clsPmieducarAnoLetivoModulo();
            $etapas = $obj->lista($ano, $ref_cod_escola);

            return ['etapas' => $etapas];
        }

        return [];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('post', 'pegar-etapas')) {
            $this->appendResponse($this->pegarEtapas());
        }
    }
}
