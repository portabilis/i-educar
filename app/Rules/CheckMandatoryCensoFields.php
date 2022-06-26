<?php

namespace App\Rules;

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\School;
use App_Model_LocalFuncionamentoDiferenciado;
use App_Model_TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\Model\EstruturaCurricular;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Illuminate\Contracts\Validation\Rule;

class CheckMandatoryCensoFields implements Rule
{
    public const ETAPAS_ENSINO_REGULAR = [
        1,
        2,
        3,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        25,
        26,
        27,
        28,
        29,
        35,
        36,
        37,
        38,
        41,
        56
    ];

    public const ETAPAS_ESPECIAL_SUBSTITUTIVAS = [
        1,
        2,
        3,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        25,
        26,
        27,
        28,
        29,
        30,
        31,
        32,
        33,
        34,
        35,
        36,
        37,
        38,
        41,
        56,
        39,
        40,
        69,
        70,
        71,
        72,
        73,
        74,
        64,
        67,
        68
    ];

    public string $message = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $params
     *
     * @return bool
     */
    public function passes($attribute, $params)
    {
        if ($this->validarCamposObrigatoriosCenso($params->ref_cod_instituicao)) {
            if (!$this->validaCamposHorario($params)) {
                return false;
            }
            if (!$this->validaEtapaEducacenso($params)) {
                return false;
            }
            if (!$this->validaCampoAtividadesComplementares($params)) {
                return false;
            }
            if (!$this->validaCampoEstruturaCurricular($params)) {
                return false;
            }
            if (!$this->validaCampoEtapaEnsino($params['tipo_atendimento'])) {
                return false;
            }
            if (!$this->validaCampoFormasOrganizacaoTurma($params)) {
                return false;
            }
            if (!$this->validaCampoUnidadeCurricular($params)) {
                return false;
            }
            if (!$this->validaCampoTipoAtendimento($params)) {
                return false;
            }
            if (!$this->validaCampoLocalFuncionamentoDiferenciado($params)) {
                return false;
            }
        }

        return true;
    }

    protected function validarCamposObrigatoriosCenso($refCodInstituicao)
    {
        return (new LegacyInstitution())::query()
            ->find(['cod_instituicao' => $refCodInstituicao])
            ->first()
            ->isMandatoryCensoFields();
    }

    protected function validaCamposHorario($params)
    {
        if ($params->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL) {
            if (empty($params->hora_inicial)) {
                $this->message = 'O campo hora inicial é obrigatório';

                return false;
            }
            if (empty($params->hora_final)) {
                $this->message = 'O campo hora final é obrigatório';

                return false;
            }
            if (empty($params->hora_inicio_intervalo)) {
                $this->message = 'O campo hora início intervalo é obrigatório';

                return false;
            }
            if (empty($params->hora_fim_intervalo)) {
                $this->message = 'O campo hora fim intervalo é obrigatório';

                return false;
            }
            if (empty($params->dias_semana)) {
                $this->message = 'O campo dias da semana é obrigatório';

                return false;
            }
        }

        return true;
    }

    private function validaEtapaEducacenso($params)
    {
        $course = LegacyCourse::find($params->ref_cod_curso);
        $estruturaCurricular = $this->getEstruturaCurricularValues($params);

        if (empty($params->etapa_educacenso) &&
            is_array($estruturaCurricular) &&
            (in_array(1, $estruturaCurricular, true) || in_array(3, $estruturaCurricular, true))) {
            $this->message = 'O campo <b>"Etapa de ensino"</b> deve ser obrigatório quando o campo "Estrutura curricular" for preenchido com "Formação geral básica" ou "Não se aplica"';

            return false;
        }

        if ((int)$course->modalidade_curso === ModalidadeCurso::ENSINO_REGULAR &&
            isset($params->etapa_educacenso) &&
            !in_array((int)$params->etapa_educacenso, self::ETAPAS_ENSINO_REGULAR)) {
            $this->message = 'Quando a modalidade do curso é: Ensino regular, o campo: Etapa de ensino deve ser uma das seguintes opções:'
                . implode(',', self::ETAPAS_ENSINO_REGULAR) . '.';

            return false;
        }

        if ((int)$course->modalidade_curso === ModalidadeCurso::EDUCACAO_ESPECIAL &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, self::ETAPAS_ESPECIAL_SUBSTITUTIVAS)) {
            $this->message = 'Quando a modalidade do curso é: Educação especial, o campo: Etapa de ensino deve ser uma das seguintes opções:'
                . implode(',', self::ETAPAS_ESPECIAL_SUBSTITUTIVAS) . '.';

            return false;
        }

        if ((int)$course->modalidade_curso === ModalidadeCurso::EJA &&
            isset($params->etapa_educacenso) &&
            !in_array($params->etapa_educacenso, [69, 70, 71, 72])) {
            $this->message = 'Quando a modalidade do curso é: Educação de Jovens e Adultos (EJA), o campo: Etapa de ensino deve ser uma das seguintes opções: 69, 70, 71 ou 72.';

            return false;
        }

        if ($course->modalidade_curso == 4 &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, [30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67, 68])) {
            $this->message = 'Quando a modalidade do curso é: Educação Profissional, o campo: Etapa de ensino deve ser uma das seguintes opções: 30, 31, 32, 33, 34, 39, 40, 73, 74, 64, 67 ou 68.';

            return false;
        }

        if ($params->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL &&
            isset($params->etapa_educacenso) &&
            !in_array($params->etapa_educacenso, [69, 70, 71, 72])) {
            $this->message = 'Quando o campo: Tipo de mediação didático-pedagógica é: Semipresencial, o campo: Etapa de ensino deve ser uma das seguintes opções: 69, 70, 71 ou 72.';

            return false;
        }

        if ($params->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, [25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 70, 71, 73, 74, 67, 68], true)) {
            $this->message = 'Quando o campo: Tipo de mediação didático-pedagógica é: Educação a Distância, o campo: Etapa de ensino deve ser uma das seguintes opções: 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 70, 71, 73, 74, 67 ou 68';

            return false;
        }

        $localDeFuncionamentoData = [
            App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO,
            App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL
        ];

        if (in_array($params->local_funcionamento_diferenciado, $localDeFuncionamentoData) &&
            isset($params->etapa_educacenso) &&
            in_array($params->etapa_educacenso, [1, 2, 3, 56])) {
            $nomeOpcao = (App_Model_LocalFuncionamentoDiferenciado::getInstance()->getEnums())[$params->local_funcionamento_diferenciado];
            $this->message = "Quando o campo: Local de funcionamento diferenciado é: {$nomeOpcao}, o campo: Etapa de ensino não pode ser nenhuma das seguintes opções: 1, 2, 3 ou 56.";

            return false;
        }

        return true;
    }

    protected function validaCampoAtividadesComplementares($params)
    {
        if ($params->tipo_atendimento == TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR
            && empty($params->atividades_complementares)
        ) {
            $this->message = 'Campo atividades complementares é obrigatório.';

            return false;
        }

        return true;
    }

    protected function validaCampoEtapaEnsino($tipoAtendimento)
    {
        if (!empty($tipoAtendimento) && !in_array($tipoAtendimento, [-1, 4, 5])) {
            $this->message = 'Campo etapa de ensino é obrigatório';

            return false;
        }

        return true;
    }

    protected function validaCampoTipoAtendimento($params)
    {
        if ($params->tipo_atendimento != TipoAtendimentoTurma::ESCOLARIZACAO && in_array(
            $params->tipo_mediacao_didatico_pedagogico,
            [
                App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL,
                App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA
            ]
        )) {
            $this->message = 'O campo: Tipo de atendimento deve ser: Escolarização quando o campo: Tipo de mediação didático-pedagógica for: Semipresencial ou Educação a Distância.';

            return false;
        }

        $course = LegacyCourse::find($params->ref_cod_curso);
        if ((int)$params->tipo_atendimento === TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR && (int) $course->modalidade_curso === ModalidadeCurso::EJA) {
            $this->message = 'Quando a modalidade do curso é: <b>Educação de Jovens e Adultos (EJA)</b>, o campo <b>Tipo de atendimento</b> não pode ser <b>Atividade complementar</b>';

            return false;
        }

        return true;
    }

    protected function validaCampoLocalFuncionamentoDiferenciado($params)
    {
        $school = School::find($params->ref_ref_cod_escola);
        $localFuncionamentoEscola = $school->local_funcionamento;
        if (is_string($localFuncionamentoEscola)) {
            $localFuncionamentoEscola = explode(',', str_replace(['{', '}'], '', $localFuncionamentoEscola));
        }

        $localFuncionamentoEscola = (array)$localFuncionamentoEscola;

        if (!in_array(
            9,
            $localFuncionamentoEscola
        ) && $params->local_funcionamento_diferenciado == App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO) {
            $this->message = 'Não é possível selecionar a opção: Unidade de atendimento socioeducativo quando o local de funcionamento da escola não for: Unidade de atendimento socioeducativo.';

            return false;
        }

        if (!in_array(
            10,
            $localFuncionamentoEscola
        ) && $params->local_funcionamento_diferenciado == App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL) {
            $this->message = 'Não é possível selecionar a opção: Unidade prisional quando o local de funcionamento da escola não for: Unidade prisional.';

            return false;
        }

        return true;
    }

    public function validaCampoEstruturaCurricular(mixed $params)
    {
        $estruturaCurricular = $this->getEstruturaCurricularValues($params);

        if (is_array($estruturaCurricular) && in_array(2, $estruturaCurricular, true) && count($estruturaCurricular) === 1) {
            $params->etapa_educacenso = null;
        }

        if ($params->tipo_atendimento == TipoAtendimentoTurma::ESCOLARIZACAO && empty($estruturaCurricular)) {
            $this->message = 'Campo "Estrutura Curricular" é obrigatório quando o campo tipo de atentimento é "Escolarização".';

            return false;
        }

        if (is_array($estruturaCurricular) && count($estruturaCurricular) > 1 && in_array(3, $estruturaCurricular, true)) {
            $this->message = 'Não é possível informar mais de uma opção no campo: <b>Estrutura curricular</b>, quando a opção: <b>Não se aplica</b> estiver selecionada';

            return false;
        }

        if (
            is_array($estruturaCurricular) &&
            !in_array(EstruturaCurricular::FORMACAO_GERAL_BASICA, $estruturaCurricular, true) &&
            $params->tipo_mediacao_didatico_pedagogico == App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL
        ) {
            $this->message = 'Quando o campo: <b>Tipo de mediação didático-pedagógica</b> é: <b>Semipresencial</b>, o campo: <b>Estrutura curricular</b> deve ter a opção <b>Formação geral básica</b> informada.';

            return false;
        }

        $etapaEnsinoCanNotContainsWithFormacaoGeralBasica = [1, 2, 3, 39, 40, 64, 68];
        if (is_array($estruturaCurricular) &&
            in_array(1, $estruturaCurricular, true) &&
            isset($params->etapa_educacenso) &&
            in_array((int) $params->etapa_educacenso, $etapaEnsinoCanNotContainsWithFormacaoGeralBasica)) {
            $this->message = 'Quando o campo: <b>Estrutura curricular</b> for preenchido com: <b>Formação geral básica</b>, o campo: <b>Etapa de ensino</b> deve ser uma das seguintes opções: 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 41, 69, 70, 71, 72, 56, 73, 74 ou 67';

            return false;
        }

        $etapaEnsinoCanNotContainsWithItinerarioFormativo = [1, 2, 3, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 39, 40, 41, 56, 64, 68, 69, 70, 72, 73];
        if (is_array($estruturaCurricular) &&
            in_array(2, $estruturaCurricular, true) &&
            isset($params->etapa_educacenso) &&
            in_array((int) $params->etapa_educacenso, $etapaEnsinoCanNotContainsWithItinerarioFormativo)) {
            $this->message = 'Quando o campo: <b>Estrutura curricular</b> for preenchido com: <b>Itinerário formativo</b>, o campo: <b>Etapa de ensino</b> deve ser uma das seguintes opções: 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 71, 74, 67';

            return false;
        }

        $etapaEnsinoCanNotContainsWithNaoSeAplica = [14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 56, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 69, 70, 72, 71, 73, 67, 74];
        if (is_array($estruturaCurricular) &&
            in_array(3, $estruturaCurricular, true) &&
            isset($params->etapa_educacenso) &&
            in_array((int) $params->etapa_educacenso, $etapaEnsinoCanNotContainsWithNaoSeAplica)) {
            $this->message = 'Quando o campo: <b>Estrutura curricular</b> for preenchido com: <b>Não se aplica</b>, o campo: <b>Etapa de ensino</b> deve ser uma das seguintes opções: 1, 2, 3, 24, 39, 40, 64, 68';

            return false;
        }

        return true;
    }

    private function validaCampoFormasOrganizacaoTurma(mixed $params)
    {
        $validOption = [
            1 => 'Série/ano (séries anuais)',
            2 => 'Períodos semestrais',
            3 => 'Ciclo(s)',
            4 => 'Grupos não seriados com base na idade ou competência',
            5 => 'Módulos',
            6 => 'Alternância regular de períodos de estudos'
        ];

        $validOptionCorrelationForEtapaEnsino = [
            FormaOrganizacaoTurma::SERIE_ANO => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 64, 69, 70, 71, 72, 73, 74, 67
            ],
            FormaOrganizacaoTurma::SEMESTRAL => [
                25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 69, 70, 71, 72, 73, 74, 67, 68
            ],
            FormaOrganizacaoTurma::CICLOS => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 56
            ],
            FormaOrganizacaoTurma::NAO_SERIADO => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 64, 69, 70, 71, 72, 73, 74, 67, 68
            ],
            FormaOrganizacaoTurma::MODULES => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 56, 64, 69, 70, 71, 72, 73, 74, 67,68
            ],
            FormaOrganizacaoTurma::ALTERNANCIA_REGULAR => [
                19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 64, 69, 70, 71, 72, 73, 74, 67, 68
            ]
        ];

        if (isset($params->formas_organizacao_turma) &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, $validOptionCorrelationForEtapaEnsino[(int)$params->formas_organizacao_turma], true)
        ) {
            $todasEtapasEducacenso = loadJson(__DIR__ . '/../../ieducar/intranet/educacenso_json/etapas_ensino.json');
            $this->message = "Não é possível selecionar a opção: <b>{$validOption[(int)$params->formas_organizacao_turma]}</b>, no campo: <b>Formas de organização da turma</b> quando o campo: Etapa de ensino for: {$todasEtapasEducacenso[$params->etapa_educacenso]}.";

            return false;
        }

        return true;
    }

    private function validaCampoUnidadeCurricular(mixed $params): bool
    {
        $estruturaCurricular = $this->getEstruturaCurricularValues($params);

        if (empty($estruturaCurricular)) {
            return true;
        }

        if (empty($params->unidade_curricular) && !in_array(2, $estruturaCurricular, true)) {
            return true;
        }

        if (empty($params->unidade_curricular) && in_array(2, $estruturaCurricular, true)) {
            $this->message = 'Campo: <b>Unidade curricular</b> é obrigatório quando o campo: <b>Estrutura Curricular contém: Itinerário formativo</b>';

            return false;
        }

        if (!empty($params->unidade_curricular) && !in_array(2, $estruturaCurricular, true)) {
            $this->message = 'Campo: <b>Unidade curricular</b> não pode ser preenchido quando o campo: <b>Estrutura Curricular não contém: Itinerário formativo</b>';

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }

    private function getEstruturaCurricularValues(mixed $params): ?array
    {
        if ($params->estrutura_curricular === null) {
            return null;
        }

        return array_map(
            'intval',
            explode(',', str_replace(['{', '}'], '', $params->estrutura_curricular))
                ?: []
        );
    }
}
