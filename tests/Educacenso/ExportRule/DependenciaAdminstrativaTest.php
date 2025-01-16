<?php

namespace Tests\Educacenso\ExportRule;

use App\Models\Educacenso\Registro00;
use iEducar\Modules\Educacenso\ExportRule\DependenciaAdministrativa;
use iEducar\Modules\Educacenso\Model\DependenciaAdministrativaEscola;
use Tests\TestCase;

class DependenciaAdminstrativaTest extends TestCase
{
    public function test_campos_null_with_dependencia_administrativa_diferente_de_privada()
    {
        $registro = $this->getFakeRegistro();

        $registro->dependenciaAdministrativa = DependenciaAdministrativaEscola::MUNICIPAL;
        /** @var Registro00 $registro */
        $registro = DependenciaAdministrativa::handle($registro);
        $this->assertNull($registro->mantenedoraEscolaPrivada);
        $this->assertNull($registro->mantenedoraEmpresa);
        $this->assertNull($registro->cnpjEscolaPrivada);
    }

    public function test_campos_not_null_dependencia_administrativa_diferente_privada()
    {
        $registro = $this->getFakeRegistro();

        $registro->dependenciaAdministrativa = DependenciaAdministrativaEscola::PRIVADA;
        /** @var Registro00 $registro */
        $registro = DependenciaAdministrativa::handle($registro);
        $this->assertNotNull($registro->mantenedoraEscolaPrivada);
        $this->assertNotNull($registro->mantenedoraEmpresa);
        $this->assertNotNull($registro->cnpjEscolaPrivada);
    }

    private function getFakeRegistro()
    {
        $registro = new Registro00;
        $registro->mantenedoraEscolaPrivada = 'Mantenedora Fake';
        $registro->mantenedoraEmpresa = 'Empresa Fake';
        $registro->cnpjEscolaPrivada = 'Cpnj Fake';

        return $registro;
    }
}
