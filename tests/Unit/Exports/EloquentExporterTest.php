<?php

use App\Models\Exporter\Enrollment;
use App\Models\Exporter\Student;
use App\Models\Exporter\Teacher;
use Database\Factories\Exporter\ExportFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('monta a consulta da exportação com as relações pedidas e conta os registros', function () {
    $relatives = ['cpf', 'phone', 'religion', 'race', 'birthplace', 'marital_status', 'person_schooling_degree'];
    $fields = ['registration_id', 'transport.tipo_transporte', 'transport.veiculo_transporte_escolar'];
    foreach (['mother', 'father', 'guardian'] as $relative) {
        foreach ($relatives as $column) {
            $fields[] = "{$relative}.{$column}";
        }
    }

    $export = ExportFactory::new()->create(['model' => Enrollment::class, 'fields' => $fields, 'filters' => [
        ['column' => 'exporter_student.year', 'operator' => '=', 'value' => 2030],
        ['column' => 'exporter_student.school_id', 'operator' => 'in', 'value' => [1, 2]],
    ]]);

    $exporter = $export->getExporter();

    expect($exporter->query())
        ->toContain('"fisica" as "mf"', '"fisica" as "ff"', '"fisica" as "gf"', '"aluno" as "aluno"', 'tipo_transporte', 'exporter_student.year = 2030', 'exporter_student.school_id in (1, 2)')
        ->and($exporter->getExportCount())->toBe(0);
});

test('os filtros de escola por lista de matrículas viram contenção de array, uma por escola', function () {
    $export = ExportFactory::new()->create(['model' => Student::class, 'fields' => ['student_id'], 'filters' => [
        ['column' => 'exporter_student_grouped_registration.school_filter_id', 'operator' => '@>', 'value' => [1, 2]],
        ['column' => 'exporter_student_grouped_registration.school_filter_id', 'operator' => '@>', 'value' => 3],
    ]]);

    expect($export->getExportQuery()->toSql())
        ->toContain("(exporter_student_grouped_registration.school_filter_id @> ('{1}') or exporter_student_grouped_registration.school_filter_id @> ('{2}'))", "school_filter_id @> ('{3}')");
});

test('monta a consulta dos professores com as alocações', function () {
    $export = ExportFactory::new()->create(['model' => Teacher::class, 'fields' => ['id', 'allocations.funcao_exercida', 'allocations.tipo_vinculo'], 'filename' => 'professores.csv']);

    expect($export->getExportQuery()->toSql())
        ->toContain('"professor_turma" as "allocations"', 'Função Exercida', 'Tipo Vínculo');
});
