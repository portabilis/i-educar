<?php

class BibliotecaController extends ApiCoreController
{
    protected function canGetBibliotecas()
    {
        return $this->validatesId('escola');
    }

    protected function getBibliotecas()
    {
        if ($this->canGetBibliotecas()) {
            $escolaId = $this->getRequest()->escola_id;

            $sql = 'SELECT cod_biblioteca AS id,
                     nm_biblioteca AS nome
                FROM pmieducar.biblioteca
               WHERE ativo = 1
                 AND ref_cod_escola = $1
               ORDER BY nm_biblioteca ASC';

            $bibliotecas = $this->fetchPreparedQuery($sql, $escolaId);

            $options = [];
            foreach ($bibliotecas as $biblioteca) {
                $options['__' . $biblioteca['id']] = mb_strtoupper($biblioteca['nome']);
            }

            return ['options' => $options];
        }
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'bibliotecas')) {
            $this->appendResponse($this->getBibliotecas());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
