<?php

use App\Models\Educacenso\Registro60;
use iEducar\Modules\Educacenso\Model\PaisResidencia;
use iEducar\Modules\Educacenso\Model\PoderPublicoTransporte;
use iEducar\Modules\Educacenso\Model\TipoAtendimentoTurma;
use iEducar\Modules\Educacenso\Model\TipoMediacaoDidaticoPedagogico;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->aluno = function (array $attributes): Registro60 {
        $aluno = new Registro60;

        foreach ($attributes as $key => $value) {
            $aluno->$key = $value;
        }

        return $aluno;
    };
});

test('transporte publico e exigido de quem mora no brasil e estuda presencialmente em turma curricular', function () {
    $aluno = ($this->aluno)([
        'tipoAtendimentoTurma' => [TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO],
        'tipoMediacaoTurma' => TipoMediacaoDidaticoPedagogico::PRESENCIAL,
        'paisResidenciaAluno' => PaisResidencia::BRASIL,
    ]);

    $this->assertTrue($aluno->transportePublicoRequired());

    $aluno->paisResidenciaAluno = PaisResidencia::ARGENTINA;

    $this->assertFalse($aluno->transportePublicoRequired());
});

test('o veiculo e exigido do transporte municipal ou estadual', function () {
    $this->assertTrue(($this->aluno)(['transportePublico' => PoderPublicoTransporte::MUNICIPAL])->veiculoTransporteEscolarRequired());
    $this->assertFalse(($this->aluno)(['transportePublico' => null])->veiculoTransporteEscolarRequired());
});

test('reconhece turma de atividade complementar ou aee', function () {
    $this->assertTrue(($this->aluno)(['tipoAtendimentoTurma' => [TipoAtendimentoTurma::AEE]])->isAtividadeComplementarOrAee());
    $this->assertFalse(($this->aluno)(['tipoAtendimentoTurma' => [TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO]])->isAtividadeComplementarOrAee());
});

test('escolarizacao em outro espaco e exigida na turma presencial fora de local diferenciado', function () {
    $aluno = ($this->aluno)([
        'tipoAtendimentoTurma' => [TipoAtendimentoTurma::CURRICULAR_ETAPA_ENSINO],
        'tipoMediacaoTurma' => TipoMediacaoDidaticoPedagogico::PRESENCIAL,
        'localFuncionamentoDiferenciadoTurma' => App_Model_LocalFuncionamentoDiferenciado::NAO_ESTA,
    ]);

    $this->assertTrue($aluno->recebeEscolarizacaoOutroEspacoIsRequired());
});

test('itinerario nao preenchido ate marcar um tipo e uma composicao', function () {
    $aluno = new Registro60;

    $this->assertTrue($aluno->tipoItinerarioNaoPreenchido());
    $this->assertTrue($aluno->composicaoItinerarioNaoPreenchido());

    $aluno->tipoItinerarioIntegrado = 1;
    $aluno->composicaoItinerarioFormacaoTecnica = 1;

    $this->assertFalse($aluno->tipoItinerarioNaoPreenchido());
    $this->assertFalse($aluno->composicaoItinerarioNaoPreenchido());
});
