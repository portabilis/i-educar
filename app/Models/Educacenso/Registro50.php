<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Servidores\Model\FuncaoExercida;

class Registro50 implements RegistroEducacenso, ItemOfRegistro30
{
    /**
     * @var string
     * Campo 1
     */
    public $registro;

    /**
     * @var string
     * Campo 2
     */
    public $inepEscola;

    /**
     * @var string
     * Campo 3
     */
    public $codigoPessoa;

    /**
     * @var string
     * Campo 4
     */
    public $inepDocente;

    /**
     * @var string
     * Campo 5
     */
    public $codigoTurma;

    /**
     * @var string
     * Campo 6
     */
    public $inepTurma;

    /**
     * @var string
     * Campo 7
     */
    public $funcaoDocente;

    /**
     * @var string
     * Campo 8
     */
    public $tipoVinculo;

    /**
     * @var array
     * Campos 9 a 23
     */
    public $componentes;

    /**
     * @var string
     * Campo usado somente na análise
     */
    public $nomeEscola;

    /**
     * @var string
     * Campo usado somente na análise
     */
    public $nomeDocente;

    /**
     * @var integer
     * Campo usado somente na análise
     */
    public $idServidor;

    /**
     * @var integer
     * Campo usado somente na análise
     */
    public $idInstituicao;

    /**
     * @var integer
     * Campo usado somente na análise
     */
    public $idAlocacao;

    /**
     * @var integer
     * Campo usado somente na análise
     */
    public $tipoMediacaoTurma;

    /**
     * @var integer
     * Campo usado somente na análise
     */
    public $tipoAtendimentoTurma;

    /**
     * @var string
     * Campo usado somente na análise
     */
    public $nomeTurma;

    /**
     * @var string
     * Campo usado somente na análise
     */
    public $dependenciaAdministrativaEscola;

    /**
     * @var string
     * Campo usado somente na análise
     */
    public $etapaEducacensoTurma;

    /**
     * @return bool
     */
    public function isTitularOrTutor()
    {
        return $this->funcaoDocente == FuncaoExercida::DOCENTE_TITULAR_EAD ||
            $this->funcaoDocente == FuncaoExercida::DOCENTE_TUTOR_EAD;
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
}
