<?php

namespace App\Models\Educacenso;

use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use App_Model_TipoMediacaoDidaticoPedagogico;
use App_Model_LocalFuncionamentoDiferenciado;

class Registro20 implements RegistroEducacenso
{
    /**
      * @var string
      */
    public $codTurma;

    /**
      * @var string
      */
    public $codEscola;

    /**
      * @var string
      */
    public $codCurso;

    /**
      * @var string
      */
    public $codSerie;

    /**
      * @var string
      */
    public $nomeTurma;

    /**
      * @var string
      */
    public $horaInicial;

    /**
      * @var string
      */
    public $horaFinal;

    /**
      * @var array
      */
    public $diasSemana;

    /**
      * @var string
      */
    public $tipoAtendimento;

    /**
      * @var array
      */
    public $atividadesComplementares;

    /**
      * @var string
      */
    public $etapaEducacenso;

    /**
      * @var string
      */
    public $nomeEscola;

    /**
      * @var string
      */
    public $tipoMediacaoDidaticoPedagogico;

    /**
      * @var string
      */
    public $possuiServidor;

    /**
      * @var string
      */
    public $possuiServidorDocente;

    /**
      * @var string
      */
    public $possuiServidorLibras;

    /**
      * @var string
      */
    public $possuiServidorLibrasOuAuxiliarEad;

    /**
      * @var string
      */
    public $possuiServidorDiferenteLibrasOuAuxiliarEad;

    /**
      * @var string
      */
    public $possuiAlunoNecessitandoTradutor;

    /**
      * @var string
      */
    public $possuiServidorNecessitandoTradutor;

    /**
      * @var string
      */
    public $localFuncionamentoDiferenciado;

    /**
      * @var array
      */
    public $localFuncionamento;

    /**
      * @var string
      */
    public $modalidadeCurso;

    /**
      * @var string
      */
    public $codCursoProfissional;

    /**
      * @return bool
      */
    public function horarioFuncionamentoValido()
    {
        if ($this->horaInicial >= $this->horaFinal) {
            return false;
        }
        $horasInicial = explode(':', $this->horaInicial)[0];
        $horasFinal = explode(':', $this->horaFinal)[0];
        $minutosInicial = explode(':', $this->horaInicial)[1];
        $minutosFinal = explode(':', $this->horaFinal)[1];

        return $this->validaHoras($horasInicial) && $this->validaHoras($horasFinal) && $this->validaMinutos($minutosInicial) && $this->validaMinutos($minutosFinal);
    }

    /**
      * @return bool
      */
    private function validaHoras($horas)
    {
        return strlen($horas) == 2 && $horas >= '00' && $horas <= '23';
    }

    /**
      * @return bool
      */
    private function validaMinutos($minutos)
    {
        return strlen($minutos) == 2 && $minutos <= '55' && ((int) $minutos % 5) == 0;
    }

    public function getLocalFuncionamentoDescriptiveValue()
    {
        $descriptiveValues = LocalFuncionamento::getDescriptiveValues();

        $descriptiveValues = array_filter($descriptiveValues, function($key) {
            return in_array($key, $this->localFuncionamento);
        }, ARRAY_FILTER_USE_KEY);

        return implode(', ', $descriptiveValues);
    }

    public function getModalidadeCursoDescriptiveValue()
    {
        $descriptiveValues = ModalidadeCurso::getDescriptiveValues();

        return $descriptiveValues[$this->modalidadeCurso] ?? null;
    }

    public function getLocalFuncionamentoDiferenciadoDescription()
    {
        $locaisFuncionamentoDiferenciado = App_Model_LocalFuncionamentoDiferenciado::getInstance()->getEnums();

        return $locaisFuncionamentoDiferenciado[$this->localFuncionamentoDiferenciado] ?? '';
    }

    public function getTipoMediacaoValidaParaModalidadeCurso()
    {
        $tiposMediacao = App_Model_TipoMediacaoDidaticoPedagogico::getInstance()->getEnums();

        switch ($this->modalidadeCurso) {
            case ModalidadeCurso::ENSINO_REGULAR:
                return "{$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL]} ou {$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA]}";
                break;
            case ModalidadeCurso::EDUCACAO_ESPECIAL:
                return "{$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL]} ou {$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL]}";
                break;
            case ModalidadeCurso::EJA:
                return "{$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL]}, {$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL]} ou {$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA]}";
                break;
            case ModalidadeCurso::EDUCACAO_PROFISSIONAL:
                return "{$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL]} ou {$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA]}";
                break;
        }
    }

    public function getForbiddenDisciplines()
    {
        switch ($this->etapaEducacenso) {
            case 14:
            case 15:
            case 16:
            case 17:
            case 18:
            case 69:
                return [1,2,4,17,25,29];
                break;
            case 19:
            case 20:
            case 21:
            case 41:
            case 70:
                return [17,25,28];
                break;
            case 23:
            case 22:
            case 56:
            case 72:
                return [17,25];
                break;
            case 73:
                return [25];
                break;
            case 25:
            case 26:
            case 27:
            case 28:
            case 29:
            case 71:
                return [5,17,25,28];
                break;
            case 30:
            case 31:
            case 32:
            case 33:
            case 34:
            case 74:
            case 67:
                return [5,25,28];
                break;
            case 35:
            case 36:
            case 37:
            case 38:
                return [17,28];
                break;
            case 39:
            case 40:
            case 64:
            case 68:
                return [1,2,3,4,5,6,7,8,9,10,11,12,13,14,16,23,25,26,27,28,29,30,31,99];
                break;
            default:
                return [];
                break;
        }
    }
}
