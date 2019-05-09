<?php

namespace App\Models\Educacenso;


class Registro30 implements RegistroEducacenso
{
    CONST TIPO_GESTOR = 'gestor';
    CONST TIPO_DOCENTE = 'docente';
    CONST TIPO_ALUNO = 'aluno';

    public $tipo;

    public $codigoPessoa;

    /**
     * @return bool
     */
    public function isGestor()
    {
        return $this->tipo == self::TIPO_GESTOR;
    }

    /**
     * @return bool
     */
    public function isDocente()
    {
        return $this->tipo == self::TIPO_DOCENTE;
    }

    /**
     * @return bool
     */
    public function isAluno()
    {
        return $this->tipo == self::TIPO_ALUNO;
    }

}
