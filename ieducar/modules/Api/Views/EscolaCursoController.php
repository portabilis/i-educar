<?php

class EscolaCursoController extends ApiCoreController
{
    public function getAnosLetivos()
    {
        $anosLetivos = [];
        $objeto = new clsPmieducarEscolaCurso($this->getRequest()->cod_escola, $this->getRequest()->cod_curso);
        if ($escolaCurso = $objeto->detalhe()) {
            $anosLetivos = json_decode($escolaCurso['anos_letivos']);
        }

        return ['anos_letivos' => $anosLetivos];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'anos-letivos')) {
            $this->appendResponse($this->getAnosLetivos());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
