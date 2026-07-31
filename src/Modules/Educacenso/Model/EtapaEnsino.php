<?php

namespace iEducar\Modules\Educacenso\Model;

use App_Model_TipoMediacaoDidaticoPedagogico;

/**
 * Etapas de ensino habilitadas para cada combinação de mediação didático-pedagógica,
 * tipo de turma e etapa agregada (com a Formação Geral Básica diferenciando o Ensino
 * Médio). Retorna null quando a combinação não habilita nenhuma etapa de ensino.
 */
class EtapaEnsino
{
    public const ETAPAS_ENSINO_MEDIO_COM_FORMACAO_GERAL_BASICA = [25, 26, 27, 28, 29];

    public const ETAPAS_ENSINO_FUNDAMENTAL = [14, 15, 16, 17, 18, 19, 20, 21, 41];

    public const ETAPAS_NORMAL_MAGISTERIO = [35, 36, 37, 38];

    public const ETAPAS_CURSO_TECNICO_FIC = [39, 40, 64, 68, 75];

    /**
     * Etapas da educação infantil e dos anos iniciais do ensino fundamental.
     */
    public const ETAPAS_EDUCACAO_INFANTIL_E_ANOS_INICIAIS = [1, 2, 3, 14, 15, 16, 17, 18];

    /**
     * Etapas que habilitam a carga horária integralizada do aluno (registro 60).
     */
    public const ETAPAS_PERMITEM_CARGA_HORARIA_INTEGRALIZADA = [39, 40, 67, 68, 73, 74, 75];

    /**
     * Etapas que habilitam a carga horária total da turma (registro 20).
     */
    public const ETAPAS_CARGA_HORARIA_TURMA = [39, 40, 64, 67, 68, 73, 74, 75];

    /**
     * Etapas cujo mínimo vem do código do curso informado, não de valor fixo.
     */
    public const ETAPAS_CARGA_HORARIA_POR_CURSO = [39, 40, 64];

    /**
     * Carga horária mínima por etapa, quando o valor é fixo. As etapas de
     * ETAPAS_CARGA_HORARIA_POR_CURSO usam o mínimo do curso e não entram aqui.
     */
    public const CARGA_HORARIA_MINIMA_FIXA_POR_ETAPA = [
        67 => 1200,
        68 => 160,
        73 => 760,
        74 => 2400,
        75 => 160,
    ];

    /**
     * Carga horária mínima da qualificação profissional técnica.
     */
    public const CARGA_HORARIA_MINIMA_QUALIFICACAO = 160;

    /**
     * Carga horária mínima do curso técnico com formação geral básica.
     */
    public const CARGA_HORARIA_MINIMA_TECNICO_FGB = 3000;

    public static function getEtapasPermitidas(
        ?int $mediacao,
        bool $curricular,
        bool $curricularComAtividadeComplementar,
        ?int $etapaAgregada,
        bool $temFormacaoGeralBasica
    ): ?array {
        if ($mediacao === App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA && $curricular) {
            if ($etapaAgregada === EtapaAgregada::ENSINO_MEDIO) {
                return $temFormacaoGeralBasica ? self::ETAPAS_ENSINO_MEDIO_COM_FORMACAO_GERAL_BASICA : null;
            }
            if ($etapaAgregada === EtapaAgregada::EDUCACAO_JOVENS_ADULTOS) {
                return [71, 74, 67];
            }
            if ($etapaAgregada === EtapaAgregada::CURSO_TECNICO_FIC) {
                return self::ETAPAS_CURSO_TECNICO_FIC;
            }

            return null;
        }

        if ($mediacao === App_Model_TipoMediacaoDidaticoPedagogico::SEMIPRESENCIAL && $curricular) {
            if ($etapaAgregada === EtapaAgregada::EDUCACAO_JOVENS_ADULTOS) {
                return [69, 70, 71, 72];
            }

            return null;
        }

        if ($mediacao === App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL && $curricular) {
            if ($etapaAgregada === EtapaAgregada::EDUCACAO_INFANTIL) {
                return [1, 2, 3];
            }
            if ($etapaAgregada === EtapaAgregada::ENSINO_FUNDAMENTAL) {
                return self::ETAPAS_ENSINO_FUNDAMENTAL;
            }
            if ($etapaAgregada === EtapaAgregada::MULTI_CORRECAO_FLUXO) {
                return [22, 23, 56];
            }
            if ($etapaAgregada === EtapaAgregada::ENSINO_MEDIO) {
                return $temFormacaoGeralBasica ? self::ETAPAS_ENSINO_MEDIO_COM_FORMACAO_GERAL_BASICA : null;
            }
            if ($etapaAgregada === EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO) {
                return $temFormacaoGeralBasica ? self::ETAPAS_NORMAL_MAGISTERIO : null;
            }
            if ($etapaAgregada === EtapaAgregada::EDUCACAO_JOVENS_ADULTOS) {
                return [69, 70, 72, 71, 74, 73, 67];
            }
            if ($etapaAgregada === EtapaAgregada::CURSO_TECNICO_FIC) {
                return self::ETAPAS_CURSO_TECNICO_FIC;
            }

            return null;
        }

        if ($mediacao === App_Model_TipoMediacaoDidaticoPedagogico::PRESENCIAL && $curricularComAtividadeComplementar) {
            if ($etapaAgregada === EtapaAgregada::ENSINO_FUNDAMENTAL) {
                return self::ETAPAS_ENSINO_FUNDAMENTAL;
            }
            if ($etapaAgregada === EtapaAgregada::MULTI_CORRECAO_FLUXO) {
                return [22, 23];
            }
            if ($etapaAgregada === EtapaAgregada::ENSINO_MEDIO) {
                return $temFormacaoGeralBasica ? self::ETAPAS_ENSINO_MEDIO_COM_FORMACAO_GERAL_BASICA : null;
            }
            if ($etapaAgregada === EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO) {
                return $temFormacaoGeralBasica ? self::ETAPAS_NORMAL_MAGISTERIO : null;
            }

            return null;
        }

        return null;
    }

    public static function descreverCombinacao(?int $mediacao, bool $comAtividadeComplementar, ?int $etapaAgregada): string
    {
        [$mediacaoDescrita, $tipoTurma, $agregadaDescrita] = self::descricoesDosCampos($mediacao, $comAtividadeComplementar, $etapaAgregada);

        return "Tipo de mediação didático-pedagógica é: {$mediacaoDescrita}, o campo: Tipo de turma é: {$tipoTurma} e o campo: Etapa agregada é: {$etapaAgregada} ({$agregadaDescrita})";
    }

    public static function descreverCombinacaoParaAnalise(?int $mediacao, bool $comAtividadeComplementar, ?int $etapaAgregada): string
    {
        [$mediacaoDescrita, $tipoTurma, $agregadaDescrita] = self::descricoesDosCampos($mediacao, $comAtividadeComplementar, $etapaAgregada);

        return "tipo de mediação didático-pedagógica {$mediacaoDescrita}, tipo de turma {$tipoTurma} e etapa agregada {$agregadaDescrita}";
    }

    private static function descricoesDosCampos(?int $mediacao, bool $comAtividadeComplementar, ?int $etapaAgregada): array
    {
        return [
            App_Model_TipoMediacaoDidaticoPedagogico::getInstance()->getEnums()[$mediacao] ?? '',
            TipoAtendimentoTurma::getDescriptiveValues()[
                $comAtividadeComplementar
                    ? TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO_COM_ATIVIDADE_COMPLEMENTAR
                    : TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO
            ],
            EtapaAgregada::getDescriptiveValues()[$etapaAgregada] ?? '',
        ];
    }

    public static function descreverOpcoes(array $opcoes): string
    {
        if (count($opcoes) === 1) {
            return (string) $opcoes[0];
        }

        $ultima = array_pop($opcoes);

        return implode(', ', $opcoes) . ' ou ' . $ultima;
    }
}
