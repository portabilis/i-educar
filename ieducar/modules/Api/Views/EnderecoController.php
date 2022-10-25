<?php

use Illuminate\Support\Facades\Gate;

class EnderecoController extends ApiCoreController
{
    protected function getPermissaoEditar()
    {
        return ['permite_editar' => Gate::allows('modify', 999878)];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'permissao_editar')) {
            $this->appendResponse($this->getPermissaoEditar());
        } else {
            $this->notImplementedOperationError();
        }
    }
}
