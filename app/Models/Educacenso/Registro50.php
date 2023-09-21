<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Servidores\Model\FuncaoExercida;

class Registro50 implements ItemOfRegistro30, RegistroEducacenso
{
    /**
     * @var string
     *             Campo 1
     */
    public $registro;

    /**
     * @var string
     *             Campo 2
     */
    public $inepEscola;

    /**
     * @var string
     *             Campo 3
     */
    public $codigoPessoa;

    /**
     * @var string
     *             Campo 4
     */
    public $inepDocente;

    /**
     * @var string
     *             Campo 5
     */
    public $codigoTurma;

    /**
     * @var string
     *             Campo 6
     */
    public $inepTurma;

    /**
     * @var string
     *             Campo 7
     */
    public $funcaoDocente;

    /**
     * @var string
     *             Campo 8
     */
    public $tipoVinculo;

    /**
     * @var array
     *            Campos 9 a 23
     */
    public $componentes;

    /**
     * @var array
     *            Campos 24 a 31
     */
    public $unidadesCurriculares;

    /**
     * @var string
     *             Campo usado somente na análise
     */
    public $nomeEscola;

    /**
     * @var string
     *             Campo usado somente na análise
     */
    public $nomeDocente;

    /**
     * @var int
     *              Campo usado somente na análise
     */
    public $idServidor;

    /**
     * @var int
     *              Campo usado somente na análise
     */
    public $idInstituicao;

    /**
     * @var int
     *              Campo usado somente na análise
     */
    public $idAlocacao;

    /**
     * @var int
     *              Campo usado somente na análise
     */
    public $tipoMediacaoTurma;

    /**
     * @var int
     *              Campo usado somente na análise
     */
    public $tipoAtendimentoTurma;

    /**
     * @var string
     *             Campo usado somente na análise
     */
    public $nomeTurma;

    /**
     * @var string
     *             Campo usado somente na análise
     */
    public $dependenciaAdministrativaEscola;

    /**
     * @var string
     *             Campo usado somente na análise
     */
    public $etapaEducacensoTurma;

    /**
     * @var array
     *            Campo usado somente na análise
     */
    public $estruturaCurricular;

    /**
     * @var int
     *          Campo usado somente na análise
     */
    public $outrasUnidadesCurricularesObrigatorias;

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

    /**
     * Retorna a propriedade da classe correspondente ao dado no arquivo do censo
     *
     * @param int $column
     * @return string
     */
    public function getProperty($column)
    {
        // TODO: Implement getProperty() method.
    }

    public function estruturasCurricularesDescritivas()
    {
        $estruturasCurriculares = EstruturaCurricular::getDescriptiveValues();

        $estruturaDescritiva = array_map(function ($key) use ($estruturasCurriculares) {
            return $estruturasCurriculares[$key];
        }, $this->estruturaCurricular);

        return implode('/', $estruturaDescritiva);
    }

    public function estapaEducacensoDescritiva()
    {
        $todasEtapasEducacenso = loadJson('educacenso_json/etapas_ensino.json');

        return $todasEtapasEducacenso[$this->etapaEducacensoTurma];
    }
}
