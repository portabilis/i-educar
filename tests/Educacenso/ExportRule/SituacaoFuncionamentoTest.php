<?php

namespace Tests\Educacenso\ExportRule;

use App\Models\Educacenso\Registro00;
use iEducar\Modules\Educacenso\ExportRule\SituacaoFuncionamento;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento as SituacaoFuncionamentoModel;
use Tests\TestCase;

class SituacaoFuncionamentoTest extends TestCase
{
    public function test_campos_null_with_situacao_funcionamento_diferente_de_em_atividade()
    {
        $registro = $this->getFakeRegistro();

        $registro->situacaoFuncionamento = SituacaoFuncionamentoModel::EXTINTA;
        /** @var Registro00 $registro */
        $registro = SituacaoFuncionamento::handle($registro);
        $this->assertNull($registro->inicioAnoLetivo);
        $this->assertNull($registro->fimAnoLetivo);
        $this->assertNull($registro->unidadeVinculada);
    }

    public function test_campos_null_with_situacao_funcionamento_em_atividade()
    {
        $registro = $this->getFakeRegistro();

        $registro->situacaoFuncionamento = SituacaoFuncionamentoModel::EM_ATIVIDADE;
        /** @var Registro00 $registro */
        $registro = SituacaoFuncionamento::handle($registro);
        $this->assertNotNull($registro->inicioAnoLetivo);
        $this->assertNotNull($registro->fimAnoLetivo);
        $this->assertNotNull($registro->unidadeVinculada);
    }

    private function getFakeRegistro()
    {
        $registro = new Registro00;
        $registro->inicioAnoLetivo = 'Data Fake';
        $registro->fimAnoLetivo = 'Data Fake';
        $registro->unidadeVinculada = 'Unidade Fake';

        return $registro;
    }
}
