<?php

use App\Services\SchoolGradeDisciplineService;
use Database\Factories\LegacyDisciplineDependenceFactory;
use Database\Factories\LegacyDisciplineFactory;
use Database\Factories\LegacySchoolGradeDisciplineFactory;
use Database\Factories\LegacySchoolGradeFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * O que o serviço dos componentes da série na escola faz e a tela de série da
 * escola não alcança: as etapas específicas, a regravação com valores
 * próprios, as consultas de apoio, as exclusões e as recusas sem escola,
 * série ou componente.
 */
uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->discipline = function (): int {
        return LegacyDisciplineFactory::new()->create()->getKey();
    };

    $this->linked = function (array $attributes = []): int {
        return LegacySchoolGradeDisciplineFactory::new()->create($attributes + [
            'ref_ref_cod_escola' => $this->school,
            'ref_ref_cod_serie' => $this->grade,
        ])->ref_cod_disciplina;
    };

    $this->row = function (int $component): ?object {
        return DB::table('pmieducar.escola_serie_disciplina')
            ->where('ref_ref_cod_escola', $this->school)
            ->where('ref_ref_cod_serie', $this->grade)
            ->where('ref_cod_disciplina', $component)
            ->first();
    };

    $this->service = new SchoolGradeDisciplineService;

    LegacyUserFactory::new()->admin()->create();

    $link = LegacySchoolGradeFactory::new()->create();

    $this->school = $link->ref_cod_escola;
    $this->grade = $link->ref_cod_serie;
});

test('cadastra com etapas especificas', function () {
    $component = ($this->discipline)();

    $created = $this->service->store(
        gradeId: $this->grade,
        schoolId: $this->school,
        disciplineId: $component,
        workload: 60,
        specificStages: 1,
        usedStages: '1,3',
        academicYears: [2030],
    );

    $row = ($this->row)($component);

    $this->assertTrue($created);
    $this->assertSame(1, $row->ativo);
    $this->assertEquals(60, $row->carga_horaria);
    $this->assertSame(1, $row->etapas_especificas);
    $this->assertSame('1,3', $row->etapas_utilizadas);
    $this->assertSame('{2030}', $row->anos_letivos);
});

test('regrava o componente vinculado com valores proprios', function () {
    $component = ($this->linked)(['etapas_especificas' => 1, 'etapas_utilizadas' => '1,3']);

    $edited = $this->service->store(
        gradeId: $this->grade,
        schoolId: $this->school,
        disciplineId: $component,
        active: 0,
        workload: 90,
        specificStages: 1,
        usedStages: '2,4',
        academicYears: [2030, 2031],
        absenceHour: 0.75,
        weeklyClasses: 4,
    );

    $row = ($this->row)($component);

    $this->assertTrue($edited);
    $this->assertSame(0, $row->ativo);
    $this->assertEquals(90, $row->carga_horaria);
    $this->assertEquals(0.75, $row->hora_falta);
    $this->assertEquals(4, $row->aulas_por_semana);
    $this->assertSame('2,4', $row->etapas_utilizadas);
    $this->assertSame('{2030,2031}', $row->anos_letivos);
});

test('sem etapas especificas a regravacao limpa as etapas utilizadas', function () {
    $component = ($this->linked)(['etapas_especificas' => 1, 'etapas_utilizadas' => '1,3']);

    $this->assertTrue($this->service->store($this->grade, $this->school, $component, academicYears: null));

    $row = ($this->row)($component);

    $this->assertSame(0, $row->etapas_especificas);
    $this->assertNull($row->etapas_utilizadas);
    $this->assertSame('{' . now()->year . '}', $row->anos_letivos);
});

test('nao grava sem escola serie ou componente que exista', function () {
    $component = ($this->discipline)();

    $this->assertFalse($this->service->store('abc', $this->school, $component));
    $this->assertFalse($this->service->store($this->grade, null, $component));
    $this->assertFalse($this->service->store($this->grade, $this->school, 'abc'));
    $this->assertFalse($this->service->store($this->grade, $this->school, 999999999));
    $this->assertNull(($this->row)($component));
});

test('lista os componentes vinculados', function () {
    $component = ($this->linked)();
    ($this->linked)();

    $this->assertEqualsCanonicalizing(
        [$component],
        $this->service->getAllDisciplines($this->school, $this->grade)->where('ref_cod_disciplina', $component)->pluck('ref_cod_disciplina')->all()
    );
    $this->assertCount(2, $this->service->getAllDisciplines($this->school, $this->grade));
    $this->assertContains($component, $this->service->getDisciplines($this->school, $this->grade)->pluck('id')->all());
});

test('lista os anos letivos por escola e componente', function () {
    $component = ($this->linked)(['anos_letivos' => '{2030,2031}']);
    $otherLink = LegacySchoolGradeFactory::new()->create(['ref_cod_serie' => $this->grade]);
    $other = LegacySchoolGradeDisciplineFactory::new()->create([
        'ref_ref_cod_escola' => $otherLink->ref_cod_escola,
        'ref_ref_cod_serie' => $this->grade,
        'anos_letivos' => '{2032}',
    ]);

    $years = $this->service->getAcademicYearsByGrade($this->grade, [$this->school, $other->ref_ref_cod_escola, 'abc']);

    $this->assertSame([2030, 2031], $years["{$this->school}-{$component}"]);
    $this->assertSame([2032], $years["{$other->ref_ref_cod_escola}-{$other->ref_cod_disciplina}"]);
    $this->assertTrue($this->service->getAcademicYearsByGrade('abc', [$this->school])->isEmpty());
    $this->assertTrue($this->service->getAcademicYearsByGrade($this->grade, ['abc'])->isEmpty());
});

test('exclui um componente ou todos da escola', function () {
    $component = ($this->linked)();
    $other = ($this->linked)();

    $this->assertTrue($this->service->delete($this->grade, $this->school, $component));
    $this->assertNull(($this->row)($component));
    $this->assertNotNull(($this->row)($other));

    $this->assertTrue($this->service->deleteAll($this->grade, [$this->school, 'abc']));
    $this->assertNull(($this->row)($other));
});

test('nao exclui sem escola serie ou componente que exista', function () {
    $component = ($this->linked)();

    $this->assertFalse($this->service->delete('abc', $this->school, $component));
    $this->assertFalse($this->service->delete($this->grade, $this->school, 999999999));
    $this->assertFalse($this->service->deleteAll('abc', [$this->school]));
    $this->assertFalse($this->service->deleteAll($this->grade, ['abc']));
    $this->assertNotNull(($this->row)($component));
});

test('exclui os componentes vinculados que nao estao na lista', function () {
    $kept = ($this->linked)();
    $left = ($this->linked)();

    $this->assertTrue($this->service->deleteNotIn($this->grade, $this->school, [$kept, 'abc']));
    $this->assertNotNull(($this->row)($kept));
    $this->assertNull(($this->row)($left));
});

test('sem lista ou sem escola e serie nao exclui os que ficaram fora', function () {
    $component = ($this->linked)();

    $this->assertFalse($this->service->deleteNotIn('abc', $this->school, [$component]));
    $this->assertFalse($this->service->deleteNotIn($this->grade, $this->school, null));
    $this->assertFalse($this->service->deleteNotIn($this->grade, $this->school, ['abc']));
    $this->assertNotNull(($this->row)($component));
});

test('acusa dependencia dentro ou fora da lista', function () {
    $withDependence = ($this->linked)();
    $without = ($this->linked)();

    LegacyDisciplineDependenceFactory::new()->create([
        'ref_cod_escola' => $this->school,
        'ref_cod_serie' => $this->grade,
        'ref_cod_disciplina' => $withDependence,
    ]);

    // Na exclusão, importa a dependência dos componentes da lista.
    $this->assertTrue($this->service->hasDependency($this->grade, $this->school, [$withDependence], deleting: true));
    $this->assertFalse($this->service->hasDependency($this->grade, $this->school, [$without], deleting: true));

    // Na edição, importa a dependência dos que ficaram fora da lista.
    $this->assertTrue($this->service->hasDependency($this->grade, $this->school, [$without]));
    $this->assertFalse($this->service->hasDependency($this->grade, $this->school, [$withDependence]));

    $this->assertFalse($this->service->hasDependency('abc', $this->school, [$withDependence], deleting: true));
});

test('lista os componentes da escola no ano com ou sem a serie', function () {
    $component = ($this->linked)(['anos_letivos' => '{2030}']);
    $otherYear = ($this->linked)(['anos_letivos' => '{2031}']);
    $otherLink = LegacySchoolGradeFactory::new()->create(['ref_cod_escola' => $this->school]);
    $otherGrade = LegacySchoolGradeDisciplineFactory::new()->create([
        'ref_ref_cod_escola' => $this->school,
        'ref_ref_cod_serie' => $otherLink->ref_cod_serie,
        'anos_letivos' => '{2030}',
    ])->ref_cod_disciplina;

    $bySchool = $this->service->getDisciplinesForYear($this->school, 2030)->pluck('id')->all();
    $byGrade = $this->service->getDisciplinesForYear($this->school, 2030, $this->grade)->pluck('id')->all();

    $this->assertEqualsCanonicalizing([$component, $otherGrade], $bySchool);
    $this->assertSame([$component], $byGrade);
    $this->assertNotContains($otherYear, $bySchool);
});
