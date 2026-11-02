<?php

uses(Avaliacao_Service_ParecerDescritivoCommon::class);

beforeEach(function () {
    $this->configuraRegra(['parecerDescritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ETAPA_GERAL]);

    $this->getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim = function () {
        return new Avaliacao_Model_ParecerDescritivoGeral([
            'parecer' => 'Ok.',
        ]);
    };

    $this->getTestAdicionaParecerNoBoletim = function () {
        return new Avaliacao_Model_ParecerDescritivoGeral([
            'parecer' => 'N/D.',
        ]);
    };

    $this->getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
                'etapa' => 2,
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
                'etapa' => 3,
            ]),
        ];
    };

    $this->getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 1,
                'parecer' => 'N/D.',
                'etapa' => 1,
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 1,
                'parecer' => 'N/D.',
                'etapa' => 2,
            ]),
        ];
    };

    $this->getTestSalvarPareceresNoBoletimInstanciasDePareceres = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'etapa' => 1,
                'parecer' => 'N/D.',
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'etapa' => 2,
                'parecer' => 'N/D.',
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'etapa' => 3,
                'parecer' => 'N/D.',
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'etapa' => 4,
                'parecer' => 'N/D.',
            ]),
        ];
    };

    $this->getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
                'etapa' => 4,
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'parecer' => 'N/D.',
            ]),
        ];
    };

    $this->getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 1,
                'parecer' => 'N/D.',
                'etapa' => 2,
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 2,
                'parecer' => 'N/D.',
                'etapa' => 2,
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 3,
                'parecer' => 'N/D.',
                'etapa' => 3,
            ]),
            new Avaliacao_Model_ParecerDescritivoGeral([
                'id' => 4,
                'parecer' => 'N/D.',
                'etapa' => 4,
            ]),
        ];
    };

    $this->testAdicionaParecerNoBoletimVerificaValidadores = function (Avaliacao_Model_ParecerDescritivoAbstract $parecer) {
        $this->markTestSkipped();

        $this->assertEquals(1, $parecer->etapa);
        $this->assertEquals('N/D.', $parecer->parecer);

        $validators = $parecer->getValidatorCollection();

        $this->assertEquals($this->_getEtapasPossiveisParecer(), $validators['etapa']->getOption('choices'));
        $this->assertFalse(isset($validators['componenteCurricular']));
    };
});

Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests();
