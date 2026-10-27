<?php

use App\Models\Educacenso\Registro00;
use iEducar\Modules\Educacenso\ExportRule\DependenciaAdministrativa;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->getFakeRegistro = function () {
        $registro = new Registro00;
        $registro->mantenedoraEscolaPrivada = 'Mantenedora Fake';
        $registro->mantenedoraEmpresa = 'Empresa Fake';
        $registro->cnpjEscolaPrivada = 'Cpnj Fake';

        return $registro;
    };
});

test('campos null with dependencia administrativa diferente de privada', function () {
    $registro = ($this->getFakeRegistro)();

    $registro->dependenciaAdministrativa = DependenciaAdministrativaEscola::MUNICIPAL;
    /** @var Registro00 $registro */
    $registro = DependenciaAdministrativa::handle($registro);
    $this->assertNull($registro->mantenedoraEscolaPrivada);
    $this->assertNull($registro->mantenedoraEmpresa);
    $this->assertNull($registro->cnpjEscolaPrivada);
});

test('campos not null dependencia administrativa diferente privada', function () {
    $registro = ($this->getFakeRegistro)();

    $registro->dependenciaAdministrativa = DependenciaAdministrativaEscola::PRIVADA;
    /** @var Registro00 $registro */
    $registro = DependenciaAdministrativa::handle($registro);
    $this->assertNotNull($registro->mantenedoraEscolaPrivada);
    $this->assertNotNull($registro->mantenedoraEmpresa);
    $this->assertNotNull($registro->cnpjEscolaPrivada);
});
