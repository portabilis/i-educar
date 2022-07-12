<?php

namespace App\Models\Educacenso;

use App\Models\LegacySchoolClass;
use App\Services\Educacenso\Version2019\Registro20Import;
use App_Model_LocalFuncionamentoDiferenciado;
use App_Model_TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;

class Registro20 implements RegistroEducacenso
{
    /**
     * @var string
     */
    public $codTurma;

    /**
     * @var string
     */
    public $codigoEscolaInep;

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
    public $estruturaCurricular;

    /**
     * @var array
     */
    public $atividadesComplementares;

    /**
     * @var string
     */
    public $etapaEducacenso;

    /**
     * @var array
     */
    public $formasOrganizacaoTurma;

    /**
     * @var array
     */
    public $unidadesCurriculares;

    /**
     * @var array
     */
    public $unidadesCurricularesSemDocenteVinculado;

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
     * @var array
     */
    public $disciplinasEducacensoComDocentes;

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
     * @var Collection
     */
    public $componentes;

    /**
     * @var string
     */
    public $codCursoProfissional;

    /**
     * @var string
     */
    public $anoTurma;

    public $inepTurma;

    public $horaInicialMinuto;

    public $horaFinalMinuto;

    public $diaSemanaDomingo;

    public $diaSemanaSegunda;

    public $diaSemanaTerca;

    public $diaSemanaQuarta;

    public $diaSemanaQuinta;

    public $diaSemanaSexta;

    public $diaSemanaSabado;

    public $tipoAtendimentoEscolarizacao;

    public $tipoAtendimentoAtividadeComplementar;

    public $tipoAtendimentoAee;

    public $tipoAtividadeComplementar1;

    public $tipoAtividadeComplementar2;

    public $tipoAtividadeComplementar3;

    public $tipoAtividadeComplementar4;

    public $tipoAtividadeComplementar5;

    public $tipoAtividadeComplementar6;

    /**
     * @param $arrayColumns
     */
    public function hydrateModel($arrayColumns)
    {
        array_unshift($arrayColumns, null);
        unset($arrayColumns[0]);

        $this->codigoEscolaInep = $arrayColumns[2];
        $this->codTurma = $arrayColumns[3];
        $this->inepTurma = $arrayColumns[4];
        $this->nomeTurma = $arrayColumns[5];
        $this->tipoMediacaoDidaticoPedagogico = $arrayColumns[6];
        $this->horaInicial = $arrayColumns[7];
        $this->horaInicialMinuto = $arrayColumns[8];
        $this->horaFinal = $arrayColumns[9];
        $this->horaFinalMinuto = $arrayColumns[10];
        $this->diaSemanaDomingo = $arrayColumns[11];
        $this->diaSemanaSegunda = $arrayColumns[12];
        $this->diaSemanaTerca = $arrayColumns[13];
        $this->diaSemanaQuarta = $arrayColumns[14];
        $this->diaSemanaQuinta = $arrayColumns[15];
        $this->diaSemanaSexta = $arrayColumns[16];
        $this->diaSemanaSabado = $arrayColumns[17];
        $this->tipoAtendimentoEscolarizacao = $arrayColumns[18];
        $this->tipoAtendimentoAtividadeComplementar = $arrayColumns[19];
        $this->tipoAtendimentoAee = $arrayColumns[20];
        $this->tipoAtividadeComplementar1 = $arrayColumns[21];
        $this->tipoAtividadeComplementar2 = $arrayColumns[22];
        $this->tipoAtividadeComplementar3 = $arrayColumns[23];
        $this->tipoAtividadeComplementar4 = $arrayColumns[24];
        $this->tipoAtividadeComplementar5 = $arrayColumns[25];
        $this->tipoAtividadeComplementar6 = $arrayColumns[26];
        $this->localFuncionamentoDiferenciado = $arrayColumns[27];
        $this->modalidadeCurso = $arrayColumns[28];
        $this->etapaEducacenso = $arrayColumns[29];
        $this->codCurso = $arrayColumns[30];
        $this->componentes = $this->getComponentesByImportFile(array_slice($arrayColumns, 30, 26));
    }

    /**
     * @return bool
     */
    public function horarioFuncionamentoValido()
    {
        if ($this->horaInicial >= $this->horaFinal) {
            return false;
        }
        $horaInicial = explode(':', $this->horaInicial)[0];
        $horaFinal = explode(':', $this->horaFinal)[0];
        $minutoInicial = explode(':', $this->horaInicial)[1];
        $minutoFinal = explode(':', $this->horaFinal)[1];

        return $this->validaHoras($horaInicial) && $this->validaHoras($horaFinal) && $this->validaMinutos($minutoInicial) && $this->validaMinutos($minutoFinal);
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

        $descriptiveValues = array_filter($descriptiveValues, function ($key) {
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
                return [1, 2, 4, 17, 25, 29];

                break;
            case 19:
            case 20:
            case 21:
            case 41:
            case 70:
                return [17, 25, 28];

                break;
            case 23:
            case 22:
            case 56:
            case 72:
                return [17, 25];

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
                return [5, 17, 25, 28];

                break;
            case 30:
            case 31:
            case 32:
            case 33:
            case 34:
            case 74:
            case 67:
                return [5, 25, 28];

                break;
            case 35:
            case 36:
            case 37:
            case 38:
                return [17, 28];

                break;
            case 39:
            case 40:
            case 64:
            case 68:
                return [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 16, 23, 25, 26, 27, 28, 29, 30, 31, 99];

                break;
            default:
                return [];

                break;
        }
    }

    /**
     * @return bool
     */
    public function escolarizacao()
    {
        return $this->tipoAtendimento == TipoAtendimentoTurma::ESCOLARIZACAO;
    }

    /**
     * @return bool
     */
    public function atividadeComplementar()
    {
        return $this->tipoAtendimento == TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR;
    }

    /**
     * @return bool
     */
    public function atendimentoEducacionalEspecializado()
    {
        return $this->tipoAtendimento == TipoAtendimentoTurma::AEE;
    }

    /**
     * @return array
     */
    public function componentesCodigosEducacenso()
    {
        $componentes = $this->componentes();
        $componentes = $componentes->map(function ($componente) {
            return $componente->codigo_educacenso;
        })->toArray();

        return array_unique($componentes);
    }

    /**
     * @return array
     */
    public function componentesIds()
    {
        $componentes = $this->componentes();
        $componentes = $componentes->map(function ($componente) {
            return $componente->id;
        })->toArray();

        return array_unique($componentes);
    }

    /**
     * @return Collection
     */
    public function componentes()
    {
        if (!isset($this->componentes)) {
            $this->componentes = LegacySchoolClass::find($this->codTurma)->getDisciplines();
        }

        return $this->componentes;
    }

    /**
     * @return boolean
     */
    public function presencial()
    {
        return $this->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL;
    }

    /**
     * @return boolean
     */
    public function educacaoDistancia()
    {
        return $this->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA;
    }

    private function getComponentesByImportFile($componentesImportacao)
    {
        $arrayComponentes = array_keys(Registro20Import::getComponentes());

        $componentesExistentes = [];
        foreach ($componentesImportacao as $key => $value) {
            if ($value != '1') {
                continue;
            }

            $componentesExistentes[] = $arrayComponentes[$key];
        }

        return $componentesExistentes;
    }

    public function etapaEducacensoDescritiva()
    {
        $etapasEducacenso = loadJson('educacenso_json/etapas_ensino.json');

        return $etapasEducacenso[$this->etapaEducacenso];
    }

    public function unidadesCurricularesSemDocenteVinculado()
    {
        $unidadesCurriculares = UnidadesCurriculares::getDescriptiveValues();
        $unidadesSemDocente = [];

        foreach ($this->unidadesCurricularesSemDocenteVinculado as $unidadeCurricular) {
            $unidadesSemDocente[$unidadeCurricular] = $unidadesCurriculares[$unidadeCurricular];
        }

        return $unidadesSemDocente;
    }

    public function formaOrganizacaoTurmaDescritiva()
    {
        $descriptiveValues = FormaOrganizacaoTurma::getDescriptiveValues();

        return $descriptiveValues[$this->formasOrganizacaoTurma];
    }

    public function itinerarioFormativo()
    {
        return in_array(EstruturaCurricular::ITINERARIO_FORMATIVO, $this->estruturaCurricular);
    }

    public function formacaoGeralBasica()
    {
        return in_array(EstruturaCurricular::FORMACAO_GERAL_BASICA, $this->estruturaCurricular);
    }

    public function estruturaCurricularNaoSeAplica()
    {
        return in_array(EstruturaCurricular::NAO_SE_APLICA, $this->estruturaCurricular);
    }

    public function requereFormasOrganizacaoTurma()
    {
        return $this->escolarizacao() && !in_array($this->etapaEducacenso, [1, 2, 3, 24]);
    }

    public function requereEtapaEducacenso()
    {
        return in_array($this->estruturaCurricular, [
            EstruturaCurricular::FORMACAO_GERAL_BASICA,
            EstruturaCurricular::NAO_SE_APLICA
        ]);
    }
}
