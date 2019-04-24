<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Servidores\Model\FuncaoExercida;

class Registro50 implements RegistroEducacenso
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
     * @var string
     * Campo 9
     */
    public $componente1;

    /**
     * @var string
     * Campo 10
     */
    public $componente2;

    /**
     * @var string
     * Campo 11
     */
    public $componente3;

    /**
     * @var string
     * Campo 12
     */
    public $componente4;

    /**
     * @var string
     * Campo 13
     */
    public $componente5;

    /**
     * @var string
     * Campo 14
     */
    public $componente6;

    /**
     * @var string
     * Campo 15
     */
    public $componente7;

    /**
     * @var string
     * Campo 16
     */
    public $componente8;

    /**
     * @var string
     * Campo 17
     */
    public $componente9;

    /**
     * @var string
     * Campo 18
     */
    public $componente10;

    /**
     * @var string
     * Campo 19
     */
    public $componente11;

    /**
     * @var string
     * Campo 20
     */
    public $componente12;

    /**
     * @var string
     * Campo 21
     */
    public $componente13;

    /**
     * @var string
     * Campo 22
     */
    public $componente14;

    /**
     * @var string
     * Campo 23
     */
    public $componente15;

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
     * @return bool
     */
    public function isTitularOrTutor()
    {
        return $this->funcaoDocente == FuncaoExercida::DOCENTE_TITULAR_EAD ||
            $this->funcaoDocente == FuncaoExercida::DOCENTE_TUTOR_EAD;
    }
}
