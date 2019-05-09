<?php

namespace App\Models\Educacenso;


class Registro30 implements RegistroEducacenso
{
    CONST TIPO_GESTOR = 'gestor';
    CONST TIPO_DOCENTE = 'docente';
    CONST TIPO_ALUNO = 'aluno';

    public $tipos = [];

    public $registro;

    public $inepEscola;

    public $codigoPessoa;

    public $cpf;

    public $nomePessoa;

    public $dataNascimento;

    public $filiacao;

    public $filiacao1;

    public $filiacao2;

    public $sexo;

    public $raca;

    public $nacionalidade;

    public $paisNacionalidade;

    public $municipioNascimento;

    public $deficiencia;

    public $deficienciaCegueira;

    public $deficienciaBaixaVisao;

    public $deficienciaSurdez;

    public $deficienciaAuditiva;

    public $deficienciaSurdoCegueira;

    public $deficienciaFisica;

    public $deficienciaIntelectual;

    public $deficienciaMultipla;

    public $deficienciaAltasHabilidades;

    public $deficienciaAutismo;

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
