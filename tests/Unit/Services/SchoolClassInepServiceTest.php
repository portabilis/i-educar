<?php

use App\Models\SchoolClassInep;
use App\Services\SchoolClass\SchoolClassService;
use App\Services\SchoolClassInepService;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\SchoolClassInepFactory;
use iEducar\Modules\SchoolClass\Period;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->assertDatabaseRecordCount = function (int $expectedCount, int $codTurma, ?string $codigoInep = null): void {
        $query = SchoolClassInep::where('cod_turma', $codTurma);

        if ($codigoInep !== null) {
            $query->where('cod_turma_inep', $codigoInep);
        }

        $count = $query->count();
        $this->assertEquals($expectedCount, $count);
    };

    $this->schoolClassService = $this->createMock(SchoolClassService::class);
    $this->service = new SchoolClassInepService($this->schoolClassService);
});

test('delete remove registro por turma e turno', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::MORNING,
    ]);

    $this->service->delete($schoolClass->cod_turma, Period::MORNING);

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::MORNING,
    ]);
});

test('delete com turno null remove registros com turno null', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => null,
    ]);

    $this->service->delete($schoolClass->cod_turma, null);

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => null,
    ]);
});

test('delete nao remove outros turnos', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::MORNING,
    ]);

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::AFTERNOON,
    ]);

    $this->service->delete($schoolClass->cod_turma, Period::MORNING);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::AFTERNOON,
    ]);

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::MORNING,
    ]);
});

test('store cria novo registro sem conflito', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    // Salva primeiro registro
    $this->service->store($schoolClass->cod_turma, '12345678', Period::MORNING);

    // Salva segundo registro com código INEP diferente
    $result = $this->service->store($schoolClass->cod_turma, '87654321', Period::AFTERNOON);

    $this->assertInstanceOf(SchoolClassInep::class, $result);
    $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
    $this->assertEquals('87654321', $result->cod_turma_inep);
    $this->assertEquals(Period::AFTERNOON, $result->turma_turno_id);

    // Verifica que existem dois registros
    ($this->assertDatabaseRecordCount)(2, $schoolClass->cod_turma);
});

test('store atualiza registro existente quando encontra pelos parametros', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $inicial = SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => Period::MORNING,
    ]);

    // Chama store com mesmos parâmetros de busca (cod_turma, turma_turno_id) mas código INEP diferente
    $result = $this->service->store($schoolClass->cod_turma, '87654321', Period::MORNING);

    // Deve ser o mesmo registro (atualizado)
    $this->assertEquals($inicial->id, $result->id);
    $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
    $this->assertEquals('87654321', $result->cod_turma_inep); // Código INEP foi atualizado
    $this->assertEquals(Period::MORNING, $result->turma_turno_id);

    ($this->assertDatabaseRecordCount)(1, $schoolClass->cod_turma);
});

test('store resolve conflito turma nao parcial para parcial', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    // Primeiro salva como não parcial (turma_turno_id = null)
    $this->service->store($schoolClass->cod_turma, '12345678', null);

    // Depois tenta salvar como parcial com mesmo código INEP
    $result = $this->service->store($schoolClass->cod_turma, '12345678', Period::MORNING);

    $this->assertInstanceOf(SchoolClassInep::class, $result);
    $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
    $this->assertEquals('12345678', $result->cod_turma_inep);
    $this->assertEquals(Period::MORNING, $result->turma_turno_id);

    ($this->assertDatabaseRecordCount)(1, $schoolClass->cod_turma, '12345678');
});

test('store resolve conflito turma parcial para nao parcial', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    // Primeiro salva como parcial
    $this->service->store($schoolClass->cod_turma, '12345678', Period::MORNING);

    // Depois tenta salvar como não parcial com mesmo código INEP
    $result = $this->service->store($schoolClass->cod_turma, '12345678', null);

    $this->assertInstanceOf(SchoolClassInep::class, $result);
    $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
    $this->assertEquals('12345678', $result->cod_turma_inep);
    $this->assertNull($result->turma_turno_id);

    ($this->assertDatabaseRecordCount)(1, $schoolClass->cod_turma, '12345678');
});

test('store resolve conflito mudanca entre turnos', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    // Primeiro salva no turno matutino
    $this->service->store($schoolClass->cod_turma, '12345678', Period::MORNING);

    // Depois tenta salvar no turno vespertino com mesmo código INEP
    $result = $this->service->store($schoolClass->cod_turma, '12345678', Period::AFTERNOON);

    $this->assertInstanceOf(SchoolClassInep::class, $result);
    $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
    $this->assertEquals('12345678', $result->cod_turma_inep);
    $this->assertEquals(Period::AFTERNOON, $result->turma_turno_id);

    ($this->assertDatabaseRecordCount)(1, $schoolClass->cod_turma, '12345678');
});

test('store resolve conflito com multiplos registros', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    // Primeiro cria registro não-parcial
    $this->service->store($schoolClass->cod_turma, '12345678', null);

    // Depois cria registro parcial (vai limpar o anterior)
    $this->service->store($schoolClass->cod_turma, '12345678', Period::MORNING);

    // Agora tenta salvar em outro turno - deve limpar o anterior e criar novo
    $result = $this->service->store($schoolClass->cod_turma, '12345678', Period::AFTERNOON);

    $this->assertInstanceOf(SchoolClassInep::class, $result);
    $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
    $this->assertEquals('12345678', $result->cod_turma_inep);
    $this->assertEquals(Period::AFTERNOON, $result->turma_turno_id);

    ($this->assertDatabaseRecordCount)(1, $schoolClass->cod_turma, '12345678');
});

test('save sem ineps parciais turno integral sem enturmacoes parciais', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $this->schoolClassService
        ->expects($this->once())
        ->method('hasStudentsPartials')
        ->with($schoolClass->cod_turma)
        ->willReturn(false);

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: null,
        codigoInepEducacensoVespertino: null,
        turnoId: Period::FULLTIME
    );

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => null,
    ]);
});

test('save sem ineps parciais turno integral com enturmacoes parciais', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $this->schoolClassService
        ->expects($this->once())
        ->method('hasStudentsPartials')
        ->with($schoolClass->cod_turma)
        ->willReturn(true);

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: null,
        codigoInepEducacensoVespertino: null,
        turnoId: Period::FULLTIME
    );

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => Period::FULLTIME,
    ]);
});

test('save com ineps parciais sempre tem valor no turno', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $this->schoolClassService
        ->expects($this->once())
        ->method('hasStudentsPartials')
        ->with($schoolClass->cod_turma)
        ->willReturn(true);

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: '87654321',
        codigoInepEducacensoVespertino: '11223344',
        turnoId: Period::FULLTIME
    );

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => Period::FULLTIME,
    ]);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '87654321',
        'turma_turno_id' => Period::MORNING,
    ]);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '11223344',
        'turma_turno_id' => Period::AFTERNOON,
    ]);
});

test('save remove inep quando codigo nulo', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => null,
    ]);

    $this->schoolClassService
        ->expects($this->never())
        ->method('hasStudentsPartials');

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: null, // código INEP nulo
        codigoInepEducacensoMatutino: null,
        codigoInepEducacensoVespertino: null,
        turnoId: Period::FULLTIME
    );

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => null,
    ]);
});

test('save remove ineps parciais quando codigos nulos', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::MORNING,
    ]);

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::AFTERNOON,
    ]);

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678', // mantém integral
        codigoInepEducacensoMatutino: null, // remove matutino
        codigoInepEducacensoVespertino: null, // remove vespertino
        turnoId: Period::FULLTIME
    );

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::MORNING,
    ]);

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::AFTERNOON,
    ]);
});

test('save turnos especiais sempre turno null', function ($turnoId) {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $this->schoolClassService
        ->expects($this->never())
        ->method('hasStudentsPartials');

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: null,
        codigoInepEducacensoVespertino: null,
        turnoId: $turnoId
    );

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => null,
    ]);
})->with([
    'turno_matutino' => [Period::MORNING],
    'turno_vespertino' => [Period::AFTERNOON],
    'turno_noturno' => [Period::NIGTH],
]);

test('save cenario completo com todos ineps', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $this->schoolClassService
        ->expects($this->once())
        ->method('hasStudentsPartials')
        ->with($schoolClass->cod_turma)
        ->willReturn(true);

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: '87654321',
        codigoInepEducacensoVespertino: '11223344',
        turnoId: Period::FULLTIME
    );

    $registros = SchoolClassInep::where('cod_turma', $schoolClass->cod_turma)->get();
    $this->assertCount(3, $registros);

    $turnos = $registros->pluck('turma_turno_id')->sort()->values();
    $this->assertEquals([Period::MORNING, Period::AFTERNOON, Period::FULLTIME], $turnos->toArray());
});

test('save apenas ineps parciais sem principal', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    $this->schoolClassService
        ->expects($this->never())
        ->method('hasStudentsPartials');

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: null, // sem INEP principal
        codigoInepEducacensoMatutino: '87654321',
        codigoInepEducacensoVespertino: '11223344',
        turnoId: Period::FULLTIME
    );

    $registros = SchoolClassInep::where('cod_turma', $schoolClass->cod_turma)->get();
    $this->assertCount(2, $registros);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '87654321',
        'turma_turno_id' => Period::MORNING,
    ]);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '11223344',
        'turma_turno_id' => Period::AFTERNOON,
    ]);
});

test('save remove todos quando todos codigos nulos', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => null,
    ]);

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::MORNING,
    ]);

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::AFTERNOON,
    ]);

    $this->schoolClassService
        ->expects($this->never())
        ->method('hasStudentsPartials');

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: null, // remove principal
        codigoInepEducacensoMatutino: null, // remove matutino
        codigoInepEducacensoVespertino: null, // remove vespertino
        turnoId: Period::FULLTIME
    );

    $registros = SchoolClassInep::where('cod_turma', $schoolClass->cod_turma)->get();
    $this->assertCount(0, $registros);
});

test('save mantem apenas um inep parcial', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => null,
    ]);

    SchoolClassInepFactory::new()->create([
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::AFTERNOON,
    ]);

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: null, // remove principal
        codigoInepEducacensoMatutino: '87654321', // mantém apenas matutino
        codigoInepEducacensoVespertino: null, // remove vespertino
        turnoId: Period::FULLTIME
    );

    $registros = SchoolClassInep::where('cod_turma', $schoolClass->cod_turma)->get();
    $this->assertCount(1, $registros);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '87654321',
        'turma_turno_id' => Period::MORNING,
    ]);

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => null,
    ]);

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::AFTERNOON,
    ]);
});

test('save cenario mudanca turno integral matutino integral', function () {
    $schoolClass = LegacySchoolClassFactory::new()->create();

    // 1. Editar uma turma com código INEP inicial
    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: null,
        codigoInepEducacensoVespertino: null,
        turnoId: Period::MORNING
    );

    // Verifica estado inicial - turno matutino com INEP não-parcial
    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => null,
    ]);
    ($this->assertDatabaseRecordCount)(1, $schoolClass->cod_turma);

    // 2. Alterar o turno para Integral e informar turnos parciais para alguns alunos
    $this->schoolClassService
        ->expects($this->exactly(2))
        ->method('hasStudentsPartials')
        ->with($schoolClass->cod_turma)
        ->willReturnOnConsecutiveCalls(true, false);

    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: '87654321',
        codigoInepEducacensoVespertino: '11223344',
        turnoId: Period::FULLTIME
    );

    // Verifica que agora tem 3 registros (integral + parciais)
    ($this->assertDatabaseRecordCount)(3, $schoolClass->cod_turma);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => Period::FULLTIME,
    ]);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '87654321',
        'turma_turno_id' => Period::MORNING,
    ]);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '11223344',
        'turma_turno_id' => Period::AFTERNOON,
    ]);

    // 3. Mudar a turma para turno matutino e salvar novamente
    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: null,
        codigoInepEducacensoVespertino: null,
        turnoId: Period::MORNING
    );

    // Verifica que agora tem apenas 1 registro (não-parcial)
    ($this->assertDatabaseRecordCount)(1, $schoolClass->cod_turma);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => null,
    ]);

    // Verifica que os registros parciais foram removidos
    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::MORNING,
    ]);

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::AFTERNOON,
    ]);

    $this->assertDatabaseMissing('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'turma_turno_id' => Period::FULLTIME,
    ]);

    // 4. Alterar o turno da turma para integral novamente
    $this->service->save(
        codTurma: $schoolClass->cod_turma,
        codigoInepEducacenso: '12345678',
        codigoInepEducacensoMatutino: null,
        codigoInepEducacensoVespertino: null,
        turnoId: Period::FULLTIME
    );

    // Verifica que continua com 1 registro (não-parcial, mesmo sendo integral)
    ($this->assertDatabaseRecordCount)(1, $schoolClass->cod_turma);

    $this->assertDatabaseHas('modules.educacenso_cod_turma', [
        'cod_turma' => $schoolClass->cod_turma,
        'cod_turma_inep' => '12345678',
        'turma_turno_id' => null, // null porque não tem enturmações parciais
    ]);
});
