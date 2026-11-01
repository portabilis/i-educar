<?php

uses(Avaliacao_Service_ParecerDescritivoCommon::class);

beforeEach(function () {
    $this->configuraRegra(['parecerDescritivo' => RegraAvaliacao_Model_TipoParecerDescritivo::ANUAL_COMPONENTE]);

    $this->getTestInstanciaDeParecerERegistradaApenasUmaVezNoBoletim = function () {
        return new Avaliacao_Model_ParecerDescritivoComponente([
            'componenteCurricular' => 1,
            'parecer' => 'Ok.',
        ]);
    };

    $this->getTestAdicionaParecerNoBoletim = function () {
        return new Avaliacao_Model_ParecerDescritivoComponente([
            'componenteCurricular' => 1,
            'parecer' => 'N/D.',
        ]);
    };

    $this->getTestSalvarPareceresNoBoletimInstanciasDePareceres = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 2,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 3,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 4,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
        ];
    };

    $this->getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstancias = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 2,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
        ];
    };

    $this->getTestSalvarPareceresNoBoletimComEtapasJaLancadasInstanciasJaLancadas = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'id' => 1,
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
        ];
    };

    $this->getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstancias = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
            new Avaliacao_Model_ParecerDescritivoComponente([
                'componenteCurricular' => 2,
                'parecer' => 'N/D.',
            ]),
        ];
    };

    $this->getTestSalvasPareceresAtualizandoEtapaDaUltimaInstanciaAdicionadaNoBoletimComEtapasLancadasInstanciasLancadas = function () {
        return [
            new Avaliacao_Model_ParecerDescritivoComponente([
                'id' => 1,
                'componenteCurricular' => 1,
                'parecer' => 'N/D.',
                'etapa' => 'An',
            ]),
        ];
    };

    $this->testAdicionaParecerNoBoletimVerificaValidadores = function (Avaliacao_Model_ParecerDescritivoAbstract $parecer) {
        $this->markTestSkipped();
        $this->assertEquals(1, $parecer->get('componenteCurricular'));
        $this->assertEquals('An', $parecer->etapa);
        $this->assertEquals('N/D.', $parecer->parecer);

        $validators = $parecer->getValidatorCollection();

        $this->assertEquals($this->_getEtapasPossiveisParecer(), $validators['etapa']->getOption('choices'));

        $this->assertEquals(
            $this->_getComponentesCursados(),
            array_values($validators['componenteCurricular']->getOption('choices'))
        );
    };
});

Avaliacao_Service_ParecerDescritivoCommon::registerParecerDescritivoTests();

test('salvas pareceres atualizando etapa da ultima instancia adicionada no boletim com etapas lancadas (teste do próprio arquivo)', function () {
    $this->markTestSkipped();
});
