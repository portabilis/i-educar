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
     * Etapas que, além do itinerário de formação técnica e profissional (IFTP),
     * habilitam o preenchimento da carga horária total da turma (registro 20).
     */
    public const ETAPAS_PERMITEM_CARGA_HORARIA = [39, 40, 67, 68, 73, 75];

    /**
     * Carga horária total mínima (em horas) por etapa, conforme o Anexo 8 do
     * layout do Censo.
     */
    public const CARGA_HORARIA_MINIMA_POR_ETAPA = [
        39 => 100,
        40 => 100,
        67 => 1200,
        68 => 160,
        73 => 760,
        75 => 160,
    ];

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
