<?php

use App\Models\Educacenso\Registro00;
use iEducar\Modules\Educacenso\Model\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\Regulamentacao;
use iEducar\Modules\Educacenso\Validator\CnpjMantenedoraPrivada;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->getFakeRegistro = function () {
        return new Registro00;
    };
});

test('cnpj mantenedora preenchido', function () {
    $registro = ($this->getFakeRegistro)();
    $registro->cnpjMantenedoraPrincipal = '123456';
    $validator = new CnpjMantenedoraPrivada($registro);

    $this->assertTrue($validator->isValid());
});

test('cnpj vazio mantenedora sistema s', function () {
    $registro = ($this->getFakeRegistro)();
    $registro->mantenedoraEscolaPrivada = MantenedoraDaEscolaPrivada::SISTEMA_S;
    $validator = new CnpjMantenedoraPrivada($registro);

    $this->assertTrue($validator->isValid());
});

test('cnpj vazio mantenedora instituicao regulamentacao nao', function () {
    $registro = ($this->getFakeRegistro)();
    $registro->mantenedoraEscolaPrivada = MantenedoraDaEscolaPrivada::INSTITUICOES_SIM_FINS_LUCRATIVOS;
    $registro->regulamentacao = Regulamentacao::NAO;
    $validator = new CnpjMantenedoraPrivada($registro);

    $this->assertTrue($validator->isValid());
});

test('cnpj vazio mantenedora instituicao regulamentacao sim', function () {
    $registro = ($this->getFakeRegistro)();
    $registro->mantenedoraEscolaPrivada = MantenedoraDaEscolaPrivada::INSTITUICOES_SIM_FINS_LUCRATIVOS;
    $registro->regulamentacao = Regulamentacao::SIM;
    $validator = new CnpjMantenedoraPrivada($registro);

    $this->assertFalse($validator->isValid());
    $this->assertStringContainsString('Instituição sem fins lucrativos', $validator->getMessage());
});
