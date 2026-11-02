<?php

use App\Models\Educacenso\Registro00;
use iEducar\Modules\Educacenso\ExportRule\SituacaoFuncionamento;
use iEducar\Modules\Educacenso\Model\SituacaoFuncionamento as SituacaoFuncionamentoModel;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->getFakeRegistro = function () {
        $registro = new Registro00;
        $registro->inicioAnoLetivo = 'Data Fake';
        $registro->fimAnoLetivo = 'Data Fake';
        $registro->unidadeVinculada = 'Unidade Fake';

        return $registro;
    };
});

test('campos null with situacao funcionamento diferente de em atividade', function () {
    $registro = ($this->getFakeRegistro)();

    $registro->situacaoFuncionamento = SituacaoFuncionamentoModel::EXTINTA;
    /** @var Registro00 $registro */
    $registro = SituacaoFuncionamento::handle($registro);
    $this->assertNull($registro->inicioAnoLetivo);
    $this->assertNull($registro->fimAnoLetivo);
    $this->assertNull($registro->unidadeVinculada);
});

test('campos null with situacao funcionamento em atividade', function () {
    $registro = ($this->getFakeRegistro)();

    $registro->situacaoFuncionamento = SituacaoFuncionamentoModel::EM_ATIVIDADE;
    /** @var Registro00 $registro */
    $registro = SituacaoFuncionamento::handle($registro);
    $this->assertNotNull($registro->inicioAnoLetivo);
    $this->assertNotNull($registro->fimAnoLetivo);
    $this->assertNotNull($registro->unidadeVinculada);
});
