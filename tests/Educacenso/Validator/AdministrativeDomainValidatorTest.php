<?php

use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola as Dep;
use iEducar\Modules\Educacenso\Model\EsferaAdministrativa as Esfera;
use iEducar\Modules\Educacenso\Validator\AdministrativeDomainValidator;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->validator = function (int $domain, int $dependence, int $city = 4205407): AdministrativeDomainValidator {
        return new AdministrativeDomainValidator($domain, $dependence, $city);
    };
});

test('dependencia federal aceita apenas federal e federal setec', function () {
    $this->assertTrue(($this->validator)(Esfera::FEDERAL, Dep::FEDERAL)->isValid());
    $this->assertTrue(($this->validator)(Esfera::FEDERAL_SETEC, Dep::FEDERAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::ESTADUAL, Dep::FEDERAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::MUNICIPAL, Dep::FEDERAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::ESTADUAL_E_MUNICIPAL, Dep::FEDERAL)->isValid());
});

test('dependencia estadual aceita apenas estadual', function () {
    $this->assertTrue(($this->validator)(Esfera::ESTADUAL, Dep::ESTADUAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::FEDERAL, Dep::ESTADUAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::FEDERAL_SETEC, Dep::ESTADUAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::MUNICIPAL, Dep::ESTADUAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::ESTADUAL_E_MUNICIPAL, Dep::ESTADUAL)->isValid());
});

test('dependencia municipal aceita estadual municipal e estadual e municipal', function () {
    $this->assertTrue(($this->validator)(Esfera::ESTADUAL, Dep::MUNICIPAL)->isValid());
    $this->assertTrue(($this->validator)(Esfera::MUNICIPAL, Dep::MUNICIPAL)->isValid());
    $this->assertTrue(($this->validator)(Esfera::ESTADUAL_E_MUNICIPAL, Dep::MUNICIPAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::FEDERAL, Dep::MUNICIPAL)->isValid());
    $this->assertFalse(($this->validator)(Esfera::FEDERAL_SETEC, Dep::MUNICIPAL)->isValid());
});

test('dependencia privada aceita estadual municipal e estadual e municipal', function () {
    $this->assertTrue(($this->validator)(Esfera::ESTADUAL, Dep::PRIVADA)->isValid());
    $this->assertTrue(($this->validator)(Esfera::MUNICIPAL, Dep::PRIVADA)->isValid());
    $this->assertTrue(($this->validator)(Esfera::ESTADUAL_E_MUNICIPAL, Dep::PRIVADA)->isValid());
    $this->assertFalse(($this->validator)(Esfera::FEDERAL, Dep::PRIVADA)->isValid());
    $this->assertFalse(($this->validator)(Esfera::FEDERAL_SETEC, Dep::PRIVADA)->isValid());
});

test('dependencia privada em brasilia aceita apenas estadual', function () {
    $this->assertTrue(($this->validator)(Esfera::ESTADUAL, Dep::PRIVADA, 5300108)->isValid());
    $this->assertFalse(($this->validator)(Esfera::MUNICIPAL, Dep::PRIVADA, 5300108)->isValid());
    $this->assertFalse(($this->validator)(Esfera::ESTADUAL_E_MUNICIPAL, Dep::PRIVADA, 5300108)->isValid());
});

test('esfera vazia e sempre valida', function () {
    $this->assertTrue(($this->validator)(0, Dep::FEDERAL)->isValid());
});

test('valor legado cinco e invalido em qualquer dependencia', function () {
    $this->assertFalse(($this->validator)(5, Dep::FEDERAL)->isValid());
    $this->assertFalse(($this->validator)(5, Dep::ESTADUAL)->isValid());
    $this->assertFalse(($this->validator)(5, Dep::MUNICIPAL)->isValid());
    $this->assertFalse(($this->validator)(5, Dep::PRIVADA)->isValid());
});
