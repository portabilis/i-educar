<?php

namespace App\Models\Educacenso;

use App\Models\LegacySchoolClass;
use App_Model_LocalFuncionamentoDiferenciado;
use App_Model_TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
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
    public $organizacaoCurricular;

    /**
     * @var array
     */
    public $atividadesComplementares;

    /**
     * @var string
     */
    public $etapaEducacenso;

    public $etapaAgregada;

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

    public $classeComLinguaBrasileiraSinais;

    public $classeEspecial;

    public $formacaoAlternancia;

    public $outrasUnidadesCurricularesObrigatorias;

    public $turmaTurnoId;

    public $areaItinerario;

    public $tipoCursoIntinerario;

    public $codCursoProfissionalIntinerario;

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
                return "{$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL]} ou {$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA]}";

                break;
            case ModalidadeCurso::EDUCACAO_PROFISSIONAL:
                return "{$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL]}, {$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL]} ou {$tiposMediacao[App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA]}";

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
    public function curricularEtapaDeEnsino()
    {
        return in_array(TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO, $this->tipoAtendimento);
    }

    /**
     * @return bool
     */
    public function atividadeComplementar()
    {
        return in_array(TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR, $this->tipoAtendimento);
    }

    /**
     * @return bool
     */
    public function atendimentoEducacionalEspecializado()
    {
        return in_array(TipoAtendimentoTurma::AEE, $this->tipoAtendimento);
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
            $idTurma = $this->codTurma;
            if (str_contains($idTurma, '-')) {
                $idTurma = explode('-', $idTurma)[0];
            }

            $this->componentes = LegacySchoolClass::find($idTurma)->getDisciplines();
        }

        return $this->componentes;
    }

    /**
     * @return bool
     */
    public function presencial()
    {
        return $this->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL;
    }

    /**
     * @return bool
     */
    public function educacaoDistancia()
    {
        return $this->tipoMediacaoDidaticoPedagogico == App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA;
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

    public function itinerarioFormativoAprofundamento()
    {
        return in_array(OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO, $this->organizacaoCurricular);
    }

    public function formacaoGeralBasica()
    {
        return in_array(OrganizacaoCurricular::FORMACAO_GERAL_BASICA, $this->organizacaoCurricular);
    }

    public function itinerarioFormacaoTecnicaProfissional()
    {
        return in_array(OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL, $this->organizacaoCurricular);
    }

    public function possuiOrganizacaoCurricular()
    {
        return $this->itinerarioFormacaoTecnicaProfissional() ||
            $this->itinerarioFormativoAprofundamento() ||
            $this->formacaoGeralBasica();
    }

    public function requereFormasOrganizacaoTurma()
    {
        return $this->curricularEtapaDeEnsino() && !in_array($this->etapaEducacenso, [1, 2, 3, 24]);
    }

    public function requereEtapaEducacenso()
    {
        return in_array($this->organizacaoCurricular, [
            OrganizacaoCurricular::FORMACAO_GERAL_BASICA,
        ]);
    }
}
