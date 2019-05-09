<?php

namespace App\Models\Educacenso;


class Registro30 implements RegistroEducacenso
{
    CONST TIPO_GESTOR = 'gestor';
    CONST TIPO_DOCENTE = 'docente';
    CONST TIPO_ALUNO = 'aluno';

    public $tipos = [];

    public $codigoPessoa;

    public $data_nasc;

    /**
     * @return bool
     */
    public function isGestor()
    {
        return isset($this->tipos[self::TIPO_GESTOR]);
    }

    /**
     * @return bool
     */
    public function isDocente()
    {
        return isset($this->tipos[self::TIPO_DOCENTE]);
    }

    /**
     * @return bool
     */
    public function isAluno()
    {
        return isset($this->tipos[self::TIPO_ALUNO]);
    }

}
