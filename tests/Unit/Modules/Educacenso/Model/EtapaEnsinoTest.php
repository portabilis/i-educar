<?php

namespace Tests\Unit\Modules\Educacenso\Model;

use App_Model_TipoMediacaoDidaticoPedagogico as Mediacao;
use iEducar\Modules\Educacenso\Model\EtapaAgregada;
use iEducar\Modules\Educacenso\Model\EtapaEnsino;
use PHPUnit\Framework\TestCase;

class EtapaEnsinoTest extends TestCase
{
    public function test_etapas_permitidas_por_combinacao(): void
    {
        $presencial = Mediacao::PRESENCIAL;
        $semipresencial = Mediacao::SEMIPRESENCIAL;
        $ead = Mediacao::EDUCACAO_A_DISTANCIA;

        // [descrição => [mediação, curricular, com atividade complementar, etapa agregada, FGB, esperado]]
        $casos = [
            'Presencial curricular Educação Infantil' => [$presencial, true, false, EtapaAgregada::EDUCACAO_INFANTIL, false, [1, 2, 3]],
            'Presencial curricular Ensino Fundamental' => [$presencial, true, false, EtapaAgregada::ENSINO_FUNDAMENTAL, false, [14, 15, 16, 17, 18, 19, 20, 21, 41]],
            'Presencial curricular Multi' => [$presencial, true, false, EtapaAgregada::MULTI_CORRECAO_FLUXO, false, [22, 23, 56]],
            'Presencial curricular Ensino Médio com FGB' => [$presencial, true, false, EtapaAgregada::ENSINO_MEDIO, true, [25, 26, 27, 28, 29]],
            'Presencial curricular Ensino Médio sem FGB' => [$presencial, true, false, EtapaAgregada::ENSINO_MEDIO, false, null],
            'Presencial curricular Normal/Magistério com FGB' => [$presencial, true, false, EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO, true, [35, 36, 37, 38]],
            'Presencial curricular EJA' => [$presencial, true, false, EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, false, [69, 70, 72, 71, 74, 73, 67]],
            'Presencial curricular Curso Técnico e FIC' => [$presencial, true, false, EtapaAgregada::CURSO_TECNICO_FIC, false, [39, 40, 64, 68, 75]],

            'Presencial cur+ativ Ensino Fundamental' => [$presencial, false, true, EtapaAgregada::ENSINO_FUNDAMENTAL, false, [14, 15, 16, 17, 18, 19, 20, 21, 41]],
            'Presencial cur+ativ Multi' => [$presencial, false, true, EtapaAgregada::MULTI_CORRECAO_FLUXO, false, [22, 23]],
            'Presencial cur+ativ Ensino Médio com FGB' => [$presencial, false, true, EtapaAgregada::ENSINO_MEDIO, true, [25, 26, 27, 28, 29]],
            'Presencial cur+ativ Normal/Magistério com FGB' => [$presencial, false, true, EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO, true, [35, 36, 37, 38]],

            'Semipresencial curricular EJA' => [$semipresencial, true, false, EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, false, [69, 70, 71, 72]],

            'EAD curricular Ensino Médio sem FGB' => [$ead, true, false, EtapaAgregada::ENSINO_MEDIO, false, null],
            'EAD curricular EJA' => [$ead, true, false, EtapaAgregada::EDUCACAO_JOVENS_ADULTOS, false, [71, 74, 67]],
            'EAD curricular Curso Técnico e FIC' => [$ead, true, false, EtapaAgregada::CURSO_TECNICO_FIC, false, [39, 40, 64, 68, 75]],

            // Combinações sem etapa habilitada (regra geral / gate de mediação)
            'EAD curricular Educação Infantil' => [$ead, true, false, EtapaAgregada::EDUCACAO_INFANTIL, false, null],
            'Semipresencial curricular Ensino Fundamental' => [$semipresencial, true, false, EtapaAgregada::ENSINO_FUNDAMENTAL, false, null],
            'Semipresencial cur+ativ' => [$semipresencial, false, true, EtapaAgregada::ENSINO_FUNDAMENTAL, false, null],
            'EAD cur+ativ' => [$ead, false, true, EtapaAgregada::ENSINO_FUNDAMENTAL, false, null],

            // Gate de FGB: Ensino Médio e Normal/Magistério sem FGB não habilitam etapa
            'Presencial curricular Normal/Magistério sem FGB' => [$presencial, true, false, EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO, false, null],
            'Presencial cur+ativ Ensino Médio sem FGB' => [$presencial, false, true, EtapaAgregada::ENSINO_MEDIO, false, null],
            'Presencial cur+ativ Normal/Magistério sem FGB' => [$presencial, false, true, EtapaAgregada::ENSINO_MEDIO_NORMAL_MAGISTERIO, false, null],
        ];

        foreach ($casos as $descricao => [$mediacao, $curricular, $comAtividadeComplementar, $etapaAgregada, $temFormacaoGeralBasica, $esperado]) {
            $resultado = EtapaEnsino::getEtapasPermitidas(
                $mediacao,
                $curricular,
                $comAtividadeComplementar,
                $etapaAgregada,
                $temFormacaoGeralBasica
            );

            $this->assertSame($esperado, $resultado, "Combinação: {$descricao}");
        }
    }

    public function test_descrever_opcoes_usa_ou_antes_da_ultima(): void
    {
        $this->assertSame('72', EtapaEnsino::descreverOpcoes([72]));
        $this->assertSame('22 ou 23', EtapaEnsino::descreverOpcoes([22, 23]));
        $this->assertSame('69, 70, 71 ou 72', EtapaEnsino::descreverOpcoes([69, 70, 71, 72]));
    }

    public function test_descrever_combinacao_nomeia_os_tres_campos(): void
    {
        $curricular = EtapaEnsino::descreverCombinacao(Mediacao::SEMIPRESENCIAL, false, EtapaAgregada::EDUCACAO_JOVENS_ADULTOS);
        $this->assertSame(
            'Tipo de mediação didático-pedagógica é: Semipresencial, o campo: Tipo de turma é: Curricular (etapa de ensino) e o campo: Etapa agregada é: 306 (Educação de Jovens e Adultos)',
            $curricular
        );

        $comAtividadeComplementar = EtapaEnsino::descreverCombinacao(Mediacao::PRESENCIAL, true, EtapaAgregada::MULTI_CORRECAO_FLUXO);
        $this->assertStringContainsString('Tipo de turma é: Curricular (etapa de ensino) com Atividade Complementar', $comAtividadeComplementar);
        $this->assertStringContainsString('Etapa agregada é: 303 (Multi e correção de fluxo)', $comAtividadeComplementar);
    }

    public function test_descrever_combinacao_para_analise_segue_padrao_da_analise(): void
    {
        $combinacao = EtapaEnsino::descreverCombinacaoParaAnalise(Mediacao::SEMIPRESENCIAL, false, EtapaAgregada::EDUCACAO_JOVENS_ADULTOS);
        $this->assertSame(
            'tipo de mediação didático-pedagógica Semipresencial, tipo de turma Curricular (etapa de ensino) e etapa agregada Educação de Jovens e Adultos',
            $combinacao
        );

        $comAtividadeComplementar = EtapaEnsino::descreverCombinacaoParaAnalise(Mediacao::PRESENCIAL, true, EtapaAgregada::MULTI_CORRECAO_FLUXO);
        $this->assertStringContainsString('tipo de turma Curricular (etapa de ensino) com Atividade Complementar', $comAtividadeComplementar);
        $this->assertStringContainsString('etapa agregada Multi e correção de fluxo', $comAtividadeComplementar);
    }
}
