<?php

namespace Tests\Unit\Educacenso\Validator;

use App\Models\Educacenso\Registro00;
use iEducar\Modules\Educacenso\Model\MantenedoraDaEscolaPrivada;
use iEducar\Modules\Educacenso\Model\Regulamentacao;
use iEducar\Modules\Educacenso\Validator\CnpjMantenedoraPrivada;
use Tests\TestCase;

class CnpjMantenedoraPrivadaTest extends TestCase
{
    public function testCnpjMantenedoraPreenchido()
    {
        $registro = $this->getFakeRegistro();
        $registro->cnpjMantenedoraPrincipal = '123456';
        $validator = new CnpjMantenedoraPrivada($registro);

        $this->assertTrue($validator->isValid());
    }

    public function testCnpjVazioMantenedoraSistemaS()
    {
        $registro = $this->getFakeRegistro();
        $registro->mantenedoraEscolaPrivada = MantenedoraDaEscolaPrivada::SISTEMA_S;
        $validator = new CnpjMantenedoraPrivada($registro);

        $this->assertTrue($validator->isValid());
    }

    public function testCnpjVazioMantenedoraInstituicaoRegulamentacaoNao()
    {
        $registro = $this->getFakeRegistro();
        $registro->mantenedoraEscolaPrivada = MantenedoraDaEscolaPrivada::INSTITUICOES_SIM_FINS_LUCRATIVOS;
        $registro->regulamentacao = Regulamentacao::NAO;
        $validator = new CnpjMantenedoraPrivada($registro);

        $this->assertTrue($validator->isValid());
    }

    public function testCnpjVazioMantenedoraInstituicaoRegulamentacaoSim()
    {
        $registro = $this->getFakeRegistro();
        $registro->mantenedoraEscolaPrivada = MantenedoraDaEscolaPrivada::INSTITUICOES_SIM_FINS_LUCRATIVOS;
        $registro->regulamentacao = Regulamentacao::SIM;
        $validator = new CnpjMantenedoraPrivada($registro);

        $this->assertFalse($validator->isValid());
        $this->assertStringContainsString('Instituição sem fins lucrativos', $validator->getMessage());
    }

    private function getFakeRegistro()
    {
        return new Registro00();
    }
}
