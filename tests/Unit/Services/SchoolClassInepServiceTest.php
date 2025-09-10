<?php

namespace Tests\Unit\Services;

use App\Models\SchoolClassInep;
use App\Services\SchoolClass\SchoolClassService;
use App\Services\SchoolClassInepService;
use Database\Factories\LegacySchoolClassFactory;
use Database\Factories\SchoolClassInepFactory;
use iEducar\Modules\SchoolClass\Period;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SchoolClassInepServiceTest extends TestCase
{
    use DatabaseTransactions;

    private SchoolClassInepService $service;

    private SchoolClassService $schoolClassService;

    private const INEP_CODE_1 = '12345678';

    private const INEP_CODE_2 = '87654321';

    private const INEP_CODE_3 = '11223344';

    protected function setUp(): void
    {
        parent::setUp();

        $this->schoolClassService = $this->createMock(SchoolClassService::class);
        $this->service = new SchoolClassInepService($this->schoolClassService);
    }

    public function test_delete_remove_registro_por_turma_e_turno()
    {
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
    }

    public function test_delete_com_turno_null_remove_registros_com_turno_null()
    {
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
    }

    public function test_delete_nao_remove_outros_turnos()
    {
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
    }

    public function test_store_cria_novo_registro_sem_conflito()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        // Salva primeiro registro
        $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, Period::MORNING);

        // Salva segundo registro com código INEP diferente
        $result = $this->service->store($schoolClass->cod_turma, self::INEP_CODE_2, Period::AFTERNOON);

        $this->assertInstanceOf(SchoolClassInep::class, $result);
        $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
        $this->assertEquals(self::INEP_CODE_2, $result->cod_turma_inep);
        $this->assertEquals(Period::AFTERNOON, $result->turma_turno_id);

        // Verifica que existem dois registros
        $this->assertDatabaseRecordCount(2, $schoolClass->cod_turma);
    }

    public function test_store_atualiza_registro_existente_quando_encontra_pelos_parametros()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $inicial = SchoolClassInepFactory::new()->create([
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
            'turma_turno_id' => Period::MORNING,
        ]);

        // Chama store com mesmos parâmetros de busca (cod_turma, turma_turno_id) mas código INEP diferente
        $result = $this->service->store($schoolClass->cod_turma, self::INEP_CODE_2, Period::MORNING);

        // Deve ser o mesmo registro (atualizado)
        $this->assertEquals($inicial->id, $result->id);
        $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
        $this->assertEquals(self::INEP_CODE_2, $result->cod_turma_inep); // Código INEP foi atualizado
        $this->assertEquals(Period::MORNING, $result->turma_turno_id);

        $this->assertDatabaseRecordCount(1, $schoolClass->cod_turma);
    }

    public function test_store_resolve_conflito_turma_nao_parcial_para_parcial()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        // Primeiro salva como não parcial (turma_turno_id = null)
        $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, null);

        // Depois tenta salvar como parcial com mesmo código INEP
        $result = $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, Period::MORNING);

        $this->assertInstanceOf(SchoolClassInep::class, $result);
        $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
        $this->assertEquals(self::INEP_CODE_1, $result->cod_turma_inep);
        $this->assertEquals(Period::MORNING, $result->turma_turno_id);

        $this->assertDatabaseRecordCount(1, $schoolClass->cod_turma, self::INEP_CODE_1);
    }

    public function test_store_resolve_conflito_turma_parcial_para_nao_parcial()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        // Primeiro salva como parcial
        $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, Period::MORNING);

        // Depois tenta salvar como não parcial com mesmo código INEP
        $result = $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, null);

        $this->assertInstanceOf(SchoolClassInep::class, $result);
        $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
        $this->assertEquals(self::INEP_CODE_1, $result->cod_turma_inep);
        $this->assertNull($result->turma_turno_id);

        $this->assertDatabaseRecordCount(1, $schoolClass->cod_turma, self::INEP_CODE_1);
    }

    public function test_store_resolve_conflito_mudanca_entre_turnos()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        // Primeiro salva no turno matutino
        $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, Period::MORNING);

        // Depois tenta salvar no turno vespertino com mesmo código INEP
        $result = $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, Period::AFTERNOON);

        $this->assertInstanceOf(SchoolClassInep::class, $result);
        $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
        $this->assertEquals(self::INEP_CODE_1, $result->cod_turma_inep);
        $this->assertEquals(Period::AFTERNOON, $result->turma_turno_id);

        $this->assertDatabaseRecordCount(1, $schoolClass->cod_turma, self::INEP_CODE_1);
    }

    public function test_store_resolve_conflito_com_multiplos_registros()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        // Primeiro cria registro não-parcial
        $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, null);

        // Depois cria registro parcial (vai limpar o anterior)
        $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, Period::MORNING);

        // Agora tenta salvar em outro turno - deve limpar o anterior e criar novo
        $result = $this->service->store($schoolClass->cod_turma, self::INEP_CODE_1, Period::AFTERNOON);

        $this->assertInstanceOf(SchoolClassInep::class, $result);
        $this->assertEquals($schoolClass->cod_turma, $result->cod_turma);
        $this->assertEquals(self::INEP_CODE_1, $result->cod_turma_inep);
        $this->assertEquals(Period::AFTERNOON, $result->turma_turno_id);

        $this->assertDatabaseRecordCount(1, $schoolClass->cod_turma, self::INEP_CODE_1);
    }

    public function test_save_sem_ineps_parciais_turno_integral_sem_enturmacoes_parciais()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $this->schoolClassService
            ->expects($this->once())
            ->method('hasStudentsPartials')
            ->with($schoolClass->cod_turma)
            ->willReturn(false);

        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: null,
            codigoInepEducacensoVespertino: null,
            turnoId: Period::FULLTIME
        );

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
            'turma_turno_id' => null,
        ]);
    }

    public function test_save_sem_ineps_parciais_turno_integral_com_enturmacoes_parciais()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $this->schoolClassService
            ->expects($this->once())
            ->method('hasStudentsPartials')
            ->with($schoolClass->cod_turma)
            ->willReturn(true);

        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: null,
            codigoInepEducacensoVespertino: null,
            turnoId: Period::FULLTIME
        );

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
            'turma_turno_id' => Period::FULLTIME,
        ]);
    }

    public function test_save_com_ineps_parciais_sempre_tem_valor_no_turno()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $this->schoolClassService
            ->expects($this->once())
            ->method('hasStudentsPartials')
            ->with($schoolClass->cod_turma)
            ->willReturn(true);

        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: self::INEP_CODE_2,
            codigoInepEducacensoVespertino: self::INEP_CODE_3,
            turnoId: Period::FULLTIME
        );

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
            'turma_turno_id' => Period::FULLTIME,
        ]);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_2,
            'turma_turno_id' => Period::MORNING,
        ]);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_3,
            'turma_turno_id' => Period::AFTERNOON,
        ]);
    }

    public function test_save_remove_inep_quando_codigo_nulo()
    {
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
    }

    public function test_save_remove_ineps_parciais_quando_codigos_nulos()
    {
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
            codigoInepEducacenso: self::INEP_CODE_1, // mantém integral
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
    }

    #[DataProvider('turnosSemProcessamentoProvider')]
    public function test_save_turnos_especiais_sempre_turno_null($turnoId)
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $this->schoolClassService
            ->expects($this->never())
            ->method('hasStudentsPartials');

        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: null,
            codigoInepEducacensoVespertino: null,
            turnoId: $turnoId
        );

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
            'turma_turno_id' => null,
        ]);
    }

    public static function turnosSemProcessamentoProvider()
    {
        return [
            'turno_matutino' => [Period::MORNING],
            'turno_vespertino' => [Period::AFTERNOON],
            'turno_noturno' => [Period::NIGTH],
        ];
    }

    public function test_save_cenario_completo_com_todos_ineps()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $this->schoolClassService
            ->expects($this->once())
            ->method('hasStudentsPartials')
            ->with($schoolClass->cod_turma)
            ->willReturn(true);

        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: self::INEP_CODE_2,
            codigoInepEducacensoVespertino: self::INEP_CODE_3,
            turnoId: Period::FULLTIME
        );

        $registros = SchoolClassInep::where('cod_turma', $schoolClass->cod_turma)->get();
        $this->assertCount(3, $registros);

        $turnos = $registros->pluck('turma_turno_id')->sort()->values();
        $this->assertEquals([Period::MORNING, Period::AFTERNOON, Period::FULLTIME], $turnos->toArray());
    }

    public function test_save_apenas_ineps_parciais_sem_principal()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        $this->schoolClassService
            ->expects($this->never())
            ->method('hasStudentsPartials');

        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: null, // sem INEP principal
            codigoInepEducacensoMatutino: self::INEP_CODE_2,
            codigoInepEducacensoVespertino: self::INEP_CODE_3,
            turnoId: Period::FULLTIME
        );

        $registros = SchoolClassInep::where('cod_turma', $schoolClass->cod_turma)->get();
        $this->assertCount(2, $registros);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_2,
            'turma_turno_id' => Period::MORNING,
        ]);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_3,
            'turma_turno_id' => Period::AFTERNOON,
        ]);
    }

    public function test_save_remove_todos_quando_todos_codigos_nulos()
    {
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
    }

    public function test_save_mantem_apenas_um_inep_parcial()
    {
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
            codigoInepEducacensoMatutino: self::INEP_CODE_2, // mantém apenas matutino
            codigoInepEducacensoVespertino: null, // remove vespertino
            turnoId: Period::FULLTIME
        );

        $registros = SchoolClassInep::where('cod_turma', $schoolClass->cod_turma)->get();
        $this->assertCount(1, $registros);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_2,
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
    }

    public function test_save_cenario_mudanca_turno_integral_matutino_integral()
    {
        $schoolClass = LegacySchoolClassFactory::new()->create();

        // 1. Editar uma turma com código INEP inicial
        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: null,
            codigoInepEducacensoVespertino: null,
            turnoId: Period::MORNING
        );

        // Verifica estado inicial - turno matutino com INEP não-parcial
        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
            'turma_turno_id' => null,
        ]);
        $this->assertDatabaseRecordCount(1, $schoolClass->cod_turma);

        // 2. Alterar o turno para Integral e informar turnos parciais para alguns alunos
        $this->schoolClassService
            ->expects($this->exactly(2))
            ->method('hasStudentsPartials')
            ->with($schoolClass->cod_turma)
            ->willReturnOnConsecutiveCalls(true, false);

        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: self::INEP_CODE_2,
            codigoInepEducacensoVespertino: self::INEP_CODE_3,
            turnoId: Period::FULLTIME
        );

        // Verifica que agora tem 3 registros (integral + parciais)
        $this->assertDatabaseRecordCount(3, $schoolClass->cod_turma);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
            'turma_turno_id' => Period::FULLTIME,
        ]);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_2,
            'turma_turno_id' => Period::MORNING,
        ]);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_3,
            'turma_turno_id' => Period::AFTERNOON,
        ]);

        // 3. Mudar a turma para turno matutino e salvar novamente
        $this->service->save(
            codTurma: $schoolClass->cod_turma,
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: null,
            codigoInepEducacensoVespertino: null,
            turnoId: Period::MORNING
        );

        // Verifica que agora tem apenas 1 registro (não-parcial)
        $this->assertDatabaseRecordCount(1, $schoolClass->cod_turma);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
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
            codigoInepEducacenso: self::INEP_CODE_1,
            codigoInepEducacensoMatutino: null,
            codigoInepEducacensoVespertino: null,
            turnoId: Period::FULLTIME
        );

        // Verifica que continua com 1 registro (não-parcial, mesmo sendo integral)
        $this->assertDatabaseRecordCount(1, $schoolClass->cod_turma);

        $this->assertDatabaseHas('modules.educacenso_cod_turma', [
            'cod_turma' => $schoolClass->cod_turma,
            'cod_turma_inep' => self::INEP_CODE_1,
            'turma_turno_id' => null, // null porque não tem enturmações parciais
        ]);
    }

    private function assertDatabaseRecordCount(int $expectedCount, int $codTurma, ?string $codigoInep = null): void
    {
        $query = SchoolClassInep::where('cod_turma', $codTurma);

        if ($codigoInep !== null) {
            $query->where('cod_turma_inep', $codigoInep);
        }

        $count = $query->count();
        $this->assertEquals($expectedCount, $count);
    }
}
