<?php

use App\Models\Educacenso\Registro20;
use Database\Factories\LegacySchoolClassFactory;
use iEducar\Modules\Educacenso\Model\FormaOrganizacaoTurma;
use iEducar\Modules\Educacenso\Model\LocalFuncionamento;
use iEducar\Modules\Educacenso\Model\ModalidadeCurso;
use iEducar\Modules\Educacenso\Model\OrganizacaoCurricular;
use iEducar\Modules\Educacenso\Model\UnidadesCurriculares;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('horario de funcionamento aceita so horas e minutos validos', function () {
    $registro = new Registro20;

    foreach ([['08:00', '12:00', true], ['12:00', '08:00', false], ['08:00', '24:00', false], ['08:03', '12:00', false]] as [$inicio, $fim, $valido]) {
        $registro->horaInicial = $inicio;
        $registro->horaFinal = $fim;

        $this->assertSame($valido, $registro->horarioFuncionamentoValido(), "{$inicio} às {$fim}");
    }
});

test('descreve local modalidade local diferenciado etapa unidades e organizacao', function () {
    $registro = new Registro20;
    $registro->localFuncionamento = [LocalFuncionamento::PREDIO_ESCOLAR, LocalFuncionamento::GALPAO];
    $registro->modalidadeCurso = ModalidadeCurso::EJA;
    $registro->localFuncionamentoDiferenciado = 999;
    $registro->etapaEducacenso = 14;
    $registro->unidadesCurricularesSemDocenteVinculado = [UnidadesCurriculares::LIBRAS];
    $registro->formasOrganizacaoTurma = FormaOrganizacaoTurma::SERIE_ANO;

    // A descrição da etapa vem de um JSON lido com caminho relativo à intranet.
    $cwd = getcwd();
    chdir(base_path('ieducar/intranet'));

    try {
        $etapa = $registro->etapaEducacensoDescritiva();
    } finally {
        chdir($cwd);
    }

    $this->assertStringContainsString(', ', $registro->getLocalFuncionamentoDescriptiveValue());
    $this->assertSame(ModalidadeCurso::getDescriptiveValues()[ModalidadeCurso::EJA], $registro->getModalidadeCursoDescriptiveValue());
    $this->assertSame('', $registro->getLocalFuncionamentoDiferenciadoDescription());
    $this->assertNotEmpty($etapa);
    $this->assertSame([UnidadesCurriculares::LIBRAS => UnidadesCurriculares::getDescriptiveValues()[UnidadesCurriculares::LIBRAS]], $registro->unidadesCurricularesSemDocenteVinculado());
    $this->assertSame(FormaOrganizacaoTurma::getDescriptiveValues()[FormaOrganizacaoTurma::SERIE_ANO], $registro->formaOrganizacaoTurmaDescritiva());
});

test('lista as mediacoes validas para cada modalidade', function (int $modalidade, string $mediacoes) {
    $registro = new Registro20;
    $registro->modalidadeCurso = $modalidade;

    $this->assertSame($mediacoes, $registro->getTipoMediacaoValidaParaModalidadeCurso());
})->with([
    'ensino regular' => [ModalidadeCurso::ENSINO_REGULAR, 'Presencial ou Educação a distância'],
    'educação especial' => [ModalidadeCurso::EDUCACAO_ESPECIAL, 'Presencial ou Semipresencial'],
    'EJA' => [ModalidadeCurso::EJA, 'Presencial ou Educação a distância'],
    'educação profissional' => [ModalidadeCurso::EDUCACAO_PROFISSIONAL, 'Presencial, Semipresencial ou Educação a distância'],
]);

test('sem modalidade nao ha mediacao valida', function () {
    $registro = new Registro20;
    $registro->modalidadeCurso = null;

    $this->assertNull($registro->getTipoMediacaoValidaParaModalidadeCurso());
});

test('lista os componentes proibidos em cada etapa', function (int $etapa, array $proibidos) {
    $registro = new Registro20;
    $registro->etapaEducacenso = $etapa;

    $this->assertSame($proibidos, $registro->getForbiddenDisciplines());
})->with([
    'anos iniciais' => [14, [1, 2, 4, 17, 25, 29]],
    'anos finais' => [19, [17, 25, 28]],
    'multietapa' => [22, [17, 25]],
    'EJA fundamental e médio' => [73, [25]],
    'ensino médio' => [25, [5, 17, 25, 28]],
    'ensino médio integrado' => [30, [5, 25, 28]],
    'normal/magistério' => [35, [17, 28]],
    'curso técnico' => [39, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 16, 23, 25, 26, 27, 28, 29, 30, 31, 99]],
]);

test('tipo de turma mediacao e organizacao curricular', function () {
    $registro = new Registro20;
    $registro->tipoAtendimento = null;
    $registro->tipoMediacaoDidaticoPedagogico = App_Model_TipoMediacaoDidaticoPedagogico::EDUCACAO_A_DISTANCIA;
    $registro->organizacaoCurricular = [];

    $this->assertNull($registro->tipoTurma());
    $this->assertTrue($registro->educacaoDistancia());
    $this->assertFalse($registro->possuiOrganizacaoCurricular());
    $this->assertFalse($registro->requereEtapaEducacenso());

    $registro->organizacaoCurricular = [OrganizacaoCurricular::FORMACAO_GERAL_BASICA];

    $this->assertTrue($registro->possuiOrganizacaoCurricular());
});

test('os componentes da turma ignoram o sufixo do codigo', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $registro = new Registro20;
    $registro->codTurma = $schoolClass->getKey() . '-1';

    $this->assertCount($schoolClass->getDisciplines()->count(), $registro->componentes());
});
