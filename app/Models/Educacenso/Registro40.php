<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;

class Registro40 implements ItemOfRegistro30, RegistroEducacenso
{
    public $registro;

    public $inepEscola;

    public $codigoPessoa;

    public $inepGestor;

    public $cargo;

    public $criterioAcesso;

    public $especificacaoCriterioAcesso;

    public $tipoVinculo;

    public $dependenciaAdministrativa;

    public $situacaoFuncionamento;

    public function isDependenciaAdministrativaPublica()
    {
        return $this->dependenciaAdministrativa == DependenciaAdministrativaEscola::MUNICIPAL ||
            $this->dependenciaAdministrativa == DependenciaAdministrativaEscola::ESTADUAL ||
            $this->dependenciaAdministrativa == DependenciaAdministrativaEscola::FEDERAL;
    }

    public function getCodigoPessoa()
    {
        return $this->codigoPessoa;
    }

    public function getCodigoAluno()
    {
        return null;
    }

    public function getCodigoServidor()
    {
        return $this->codigoPessoa;
    }

    /**
     * Retorna a propriedade da classe correspondente ao dado no arquivo do censo
     *
     * @param int $column
     * @return string
     */
    public function getProperty($column)
    {
        $map = [
            1 => 'registro',
            2 => 'inepEscola',
            3 => 'codigoPessoa',
            4 => 'inepGestor',
            5 => 'cargo',
            6 => 'criterioAcesso',
            7 => 'tipoVinculo',
        ];

        if (array_key_exists($column, $map)) {
            $property = $map[$column];
            return $this->$property;
        }

        return null;
    }
    
}