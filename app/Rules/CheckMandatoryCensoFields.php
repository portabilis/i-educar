<?php

namespace App\Rules;

use App\Models\LegacyCourse;
use App\Models\LegacyInstitution;
use App\Models\LegacySchool;
use App_Model_LocalFuncionamentoDiferenciado;
use App_Model_TipoMediacaoDidaticoPedagogico;
use iEducar\Modules\Educacenso\Model\EtapaAgregada;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use Illuminate\Contracts\Validation\Rule;

class CheckMandatoryCensoFields implements Rule
{
    public string $message = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $params
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
            if (!$this->validaCampoOrganizacaoCurricularDaTurma($params)) {
                return false;
            }
            if (!$this->validaCampoFormasOrganizacaoTurma($params)) {
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
        return (new LegacyInstitution)::query()
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
        $organizacaoCurricular = $this->getOrganizacaoCurricularValues($params);

        if (empty($params->etapa_educacenso) &&
            is_array($organizacaoCurricular) &&
            in_array(OrganizacaoCurricular::FORMACAO_GERAL_BASICA, $organizacaoCurricular)) {
            $this->message = 'O campo <b>"Etapa de ensino"</b> deve ser obrigatório quando o campo "Organização Curricular da Turma" for preenchido com "Formação geral básica"';

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
            !in_array((int) $params->etapa_educacenso, [25, 26, 27, 28, 29, 35, 36, 37, 38, 39, 40, 64, 70, 71], true)) {
            $this->message = 'Quando o campo: Tipo de mediação didático-pedagógica é: Educação a Distância, o campo: Etapa de ensino deve ser uma das seguintes opções: 25, 26, 27, 28, 29, 35, 36, 37, 38, 39, 40, 70, 71, ou 64';

            return false;
        }

        $localDeFuncionamentoData = [
            App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO,
            App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL,
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
        $tipoAtendimento = $this->getTipoAtendimentoValues($params);

        if (is_array($tipoAtendimento) && in_array(TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR, $tipoAtendimento)
            && empty($params->atividades_complementares)
        ) {
            $this->message = 'Campo atividades complementares é obrigatório.';

            return false;
        }

        return true;
    }

    protected function validaCampoTipoAtendimento($params)
    {
        $tipoAtendimento = $this->getTipoAtendimentoValues($params);

        if (is_array($tipoAtendimento) && !in_array(TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO, $tipoAtendimento) && in_array(
            $params->tipo_mediacao_didatico_pedagogico,
            [
                App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA,
            ]
        )) {
            $this->message = 'O campo: Tipo de Turma deve ser: Curricular (etapa de ensino) quando o campo: Tipo de mediação didático-pedagógica for: Educação a Distância.';

            return false;
        }

        $course = LegacyCourse::find($params->ref_cod_curso);
        if (is_array($tipoAtendimento) && in_array(TipoAtendimentoTurma::ATIVIDADE_COMPLEMENTAR, $tipoAtendimento) && (int) $course->modalidade_curso === ModalidadeCurso::EJA) {
            $this->message = 'Quando a modalidade do curso é: <b>Educação de Jovens e Adultos (EJA)</b>, o campo <b>Tipo de turma</b> não pode ser <b>Atividade complementar</b>';

            return false;
        }

        return true;
    }

    protected function validaCampoLocalFuncionamentoDiferenciado($params)
    {
        $school = LegacySchool::find($params->ref_ref_cod_escola);
        $localFuncionamentoEscola = $school->local_funcionamento;
        if (is_string($localFuncionamentoEscola)) {
            $localFuncionamentoEscola = explode(',', str_replace(['{', '}'], '', $localFuncionamentoEscola));
        }

        $localFuncionamentoEscola = (array) $localFuncionamentoEscola;

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

    public function validaCampoOrganizacaoCurricularDaTurma(mixed $params)
    {
        $organizacaoCurricular = $this->getOrganizacaoCurricularValues($params);

        if (!empty($organizacaoCurricular)) {
            $etapasAgregadaPermitidas = [EtapaAgregada::ENSINO_MEDIO, EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO];

            if (!in_array((int) $params->etapa_agregada, $etapasAgregadaPermitidas)) {
                if (in_array(OrganizacaoCurricular::FORMACAO_GERAL_BASICA, $organizacaoCurricular)) {
                    $this->message = 'A opção "Formação geral básica" só pode ser preenchida quando o campo 25 (Etapa agregada) for preenchido com 304 ou 305.';

                    return false;
                }

                if (in_array(OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO, $organizacaoCurricular)) {
                    $this->message = 'A opção "Itinerário formativo de aprofundamento" só pode ser preenchida quando o campo 25 (Etapa agregada) for preenchido com 304 ou 305.';

                    return false;
                }

                if (in_array(OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL, $organizacaoCurricular)) {
                    $this->message = 'A opção "Itinerário de formação técnica e profissional" só pode ser preenchida quando o campo 25 (Etapa agregada) for preenchido com 304 ou 305.';

                    return false;
                }
            }
        }

        $etapaEnsinoCanContainsWithEnsinoMedioEFormacaoGeralBasica = [25, 26, 27, 28, 29];
        if (is_array($organizacaoCurricular) &&
            in_array(OrganizacaoCurricular::FORMACAO_GERAL_BASICA, $organizacaoCurricular) &&
            $params->etapa_agregada &&
            ((int) $params->etapa_agregada === EtapaAgregada::ENSINO_MEDIO) &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, $etapaEnsinoCanContainsWithEnsinoMedioEFormacaoGeralBasica)) {
            $this->message = 'Quando o campo: <b>Organização Curricular da Turma</b> for preenchido com: <b>Formação geral básica</b> e o campo: <b>Etapa agregada</b> for preenchido com: <b>Ensino Médio</b> deve ser uma das seguintes opções: 25, 26, 27, 28 ou 29';

            return false;
        }

        $etapaEnsinoCanContainsWithEnsinoMedioEFormacaoGeralBasica = [35, 36, 37, 38];
        if (is_array($organizacaoCurricular) &&
            in_array(OrganizacaoCurricular::FORMACAO_GERAL_BASICA, $organizacaoCurricular) &&
            $params->etapa_agregada &&
            ((int) $params->etapa_agregada === EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO) &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, $etapaEnsinoCanContainsWithEnsinoMedioEFormacaoGeralBasica)) {
            $this->message = 'Quando o campo: <b>Organização Curricular da Turma</b> for preenchido com: <b>Formação geral básica</b> e o campo: <b>Etapa agregada</b> for preenchido com: <b>Ensino Médio - Normal/ Magistério</b> deve ser uma das seguintes opções: 35, 36, 37 ou 38';

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
        ];

        $validOptionCorrelationForEtapaEnsino = [
            FormaOrganizacaoTurma::SERIE_ANO => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 35, 36, 37, 38, 39, 40, 64, 56, 69, 70, 71, 72,
            ],
            FormaOrganizacaoTurma::SEMESTRAL => [
                25, 26, 27, 28, 29, 35, 36, 37, 38, 39, 40, 64, 69, 70, 71, 72,
            ],
            FormaOrganizacaoTurma::CICLOS => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 56, 25, 26, 27, 28, 29, 35, 36, 37, 38,
            ],
            FormaOrganizacaoTurma::NAO_SERIADO => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 35, 36, 37, 38, 39, 40, 64, 56, 69, 70, 71, 72,
            ],
            FormaOrganizacaoTurma::MODULES => [
                14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 41, 25, 26, 27, 28, 29, 35, 36, 37, 38, 39, 40, 64, 56, 69, 70, 71, 72,
            ],
        ];

        if (isset($params->formas_organizacao_turma) &&
            isset($params->etapa_educacenso) &&
            !in_array((int) $params->etapa_educacenso, $validOptionCorrelationForEtapaEnsino[(int) $params->formas_organizacao_turma], true)
        ) {
            $todasEtapasEducacenso = loadJson(__DIR__ . '/../../ieducar/intranet/educacenso_json/etapas_ensino.json');
            $this->message = "Não é possível selecionar a opção: <b>{$validOption[(int) $params->formas_organizacao_turma]}</b>, no campo: <b>Formas de organização da turma</b> quando o campo: Etapa for: {$todasEtapasEducacenso[$params->etapa_educacenso]}.";

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

    private function getOrganizacaoCurricularValues(mixed $params): ?array
    {
        return transformStringFromDBInArray(string: $params->organizacao_curricular);
    }

    private function getTipoAtendimentoValues(mixed $params): ?array
    {
        return transformStringFromDBInArray(string: $params->tipo_atendimento);
    }
}
