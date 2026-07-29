<?php

namespace iEducar\Modules\Educacenso\Model;

use App_Model_LocalFuncionamentoDiferenciado;

/**
 * Faixas de idade permitidas pelo layout do Censo Escolar. A idade considerada é a
 * mesma que o INEP usa na importação: o ano do Censo menos o ano de nascimento (o
 * mês e o dia são ignorados).
 */
class FaixaEtariaEducacenso
{
    /**
     * Idade [mínima, máxima] permitida por etapa de ensino da turma (registro 60).
     * São exatamente as 30 etapas com faixa de idade definida no layout do Censo. As
     * etapas sem faixa definida (como creche unificada, multietapa, correção de fluxo
     * e curso técnico misto) não entram na validação.
     */
    public const POR_ETAPA = [
        1 => [0, 6],
        2 => [3, 9],
        14 => [4, 50],
        15 => [5, 50],
        16 => [5, 50],
        17 => [6, 50],
        18 => [7, 50],
        19 => [8, 50],
        20 => [9, 50],
        21 => [10, 50],
        41 => [12, 50],
        25 => [12, 58],
        26 => [12, 58],
        27 => [12, 58],
        28 => [12, 58],
        29 => [12, 58],
        35 => [12, 58],
        36 => [12, 58],
        37 => [12, 58],
        38 => [12, 58],
        69 => [12, 93],
        70 => [12, 93],
        71 => [15, 93],
        74 => [15, 94],
        67 => [15, 94],
        73 => [12, 94],
        39 => [13, 58],
        40 => [13, 75],
        68 => [12, 94],
        75 => [12, 94],
    ];

    /**
     * Idade [mínima, máxima] permitida ao gestor (registro 40).
     */
    public const FAIXA_GESTOR = [18, 95];

    /**
     * Idade [mínima, máxima] permitida ao profissional escolar em sala de aula (registro 50).
     */
    public const FAIXA_PROFISSIONAL = [14, 95];

    /**
     * Idade [mínima, máxima] permitida ao itinerário formativo sem formação geral básica.
     */
    public const FAIXA_ITINERARIO_SEM_FORMACAO_GERAL_BASICA = [12, 94];

    /**
     * Idade [mínima, máxima] permitida na unidade de atendimento socioeducativo.
     */
    public const FAIXA_SOCIOEDUCATIVO = [12, 94];

    /**
     * Idade [mínima, máxima] permitida na unidade prisional.
     */
    public const FAIXA_PRISIONAL = [18, 94];

    public const ORIGEM_ETAPA = 'etapa';

    public const ORIGEM_PRISIONAL = 'prisional';

    public const ORIGEM_SOCIOEDUCATIVO = 'socioeducativo';

    public const ORIGEM_ITINERARIO_SEM_FORMACAO_GERAL_BASICA = 'itinerario';

    /**
     * Calcula a idade pela regra do Censo: ano do Censo menos o ano de nascimento.
     * Retorna null quando a data de nascimento não está preenchida.
     */
    public static function idadeNoCenso(int $anoCenso, ?string $dataNascimento): ?int
    {
        if (empty($dataNascimento)) {
            return null;
        }

        return $anoCenso - (int) substr($dataNascimento, 0, 4);
    }

    /**
     * Resolve a regra de idade aplicável ao aluno, com a faixa [mínima, máxima] e a
     * origem dessa faixa (para a mensagem descrever o motivo).
     *
     * Quando a turma tem uma característica diferenciada (unidade prisional, unidade
     * socioeducativa ou itinerário formativo sem formação geral básica), o layout do
     * Censo define uma faixa própria para essa característica, que substitui a faixa
     * da etapa. Sem característica diferenciada, vale a faixa da etapa de ensino.
     *
     * Retorna ['faixa' => [mínima, máxima], 'origem' => self::ORIGEM_*], ou null
     * quando a etapa não tem faixa de idade definida (nesse caso não há o que validar).
     */
    public static function regraDeIdadeDoAluno(?int $etapaTurma, ?array $organizacaoCurricular, ?int $localFuncionamento): ?array
    {
        if ($localFuncionamento === App_Model_LocalFuncionamentoDiferenciado::UNIDADE_PRISIONAL) {
            return ['faixa' => self::FAIXA_PRISIONAL, 'origem' => self::ORIGEM_PRISIONAL];
        }

        if ($localFuncionamento === App_Model_LocalFuncionamentoDiferenciado::UNIDADE_ATENDIMENTO_SOCIOEDUCATIVO) {
            return ['faixa' => self::FAIXA_SOCIOEDUCATIVO, 'origem' => self::ORIGEM_SOCIOEDUCATIVO];
        }

        if (self::ehItinerarioSemFormacaoGeralBasica($organizacaoCurricular)) {
            return [
                'faixa' => self::FAIXA_ITINERARIO_SEM_FORMACAO_GERAL_BASICA,
                'origem' => self::ORIGEM_ITINERARIO_SEM_FORMACAO_GERAL_BASICA,
            ];
        }

        if (isset(self::POR_ETAPA[$etapaTurma])) {
            return ['faixa' => self::POR_ETAPA[$etapaTurma], 'origem' => self::ORIGEM_ETAPA];
        }

        return null;
    }

    /**
     * Indica se a organização curricular da turma é itinerário formativo (aprofundamento
     * ou formação técnica e profissional) sem formação geral básica.
     */
    private static function ehItinerarioSemFormacaoGeralBasica(?array $organizacaoCurricular): bool
    {
        if (empty($organizacaoCurricular)) {
            return false;
        }

        $temItinerario = in_array(OrganizacaoCurricular::ITINERARIO_FORMATIVO_APROFUNDAMENTO, $organizacaoCurricular)
            || in_array(OrganizacaoCurricular::ITINERARIO_FORMACAO_TECNICA_PROFISSIONAL, $organizacaoCurricular);

        $temFormacaoGeralBasica = in_array(OrganizacaoCurricular::FORMACAO_GERAL_BASICA, $organizacaoCurricular);

        return $temItinerario && !$temFormacaoGeralBasica;
    }
}
