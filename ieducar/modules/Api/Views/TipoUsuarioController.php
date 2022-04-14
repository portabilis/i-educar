<?php

class TipoUsuarioController extends ApiCoreController
{
    public function getDadosTipoUsuarioProfessor ()
    {
        $obj = new clsPmieducarTipoUsuario();
        $tipo_usuario_professor = $obj->detalheProfessor();

        return ['result' => $tipo_usuario_professor];
    }

    public function Gerar()
    {
        if ($this->isRequestFor('get', 'dados-tipo-usuario-professor')) {
            $this->appendResponse($this->getDadosTipoUsuarioProfessor());
        }
    }
}
