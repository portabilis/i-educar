<?php

use App\Models\LegacyInstitution;
use App\Models\LegacySchoolHistory;
use App\Models\LegacySchoolHistoryDiscipline;
use App\Services\GlobalAverageService;
use App\Services\SchoolHistoryService;
use Database\Factories\LegacySchoolHistoryFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\PersonHasPlaceFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->user = LegacyUserFactory::new()->admin()->create();
    $this->scenario = EnrollmentScenario::for($this->user, 2030);
    $this->scenario->evaluationRule();
    $this->registration = $this->scenario->enrolled('Ana Histórico', $this->scenario->schoolClass())->registration;

    $this->average = Mockery::mock(GlobalAverageService::class);
    $this->service = new SchoolHistoryService($this->average);
});

test('a transferência gera o histórico com as disciplinas da série quando a instituição pede', function () {
    $this->scenario->discipline('Matemática');
    $this->scenario->discipline('Educação Física');

    $this->service->gerarHistoricoTransferencia($this->registration->getKey(), $this->user->getKey());

    expect(LegacySchoolHistory::query()->where('ref_cod_matricula', $this->registration->getKey())->exists())->toBeFalse();

    LegacyInstitution::query()->whereKey($this->scenario->institution)->update(['gerar_historico_transferencia' => true]);
    // A cidade e a UF da escola vêm do endereço dela.
    PersonHasPlaceFactory::new()->create(['person_id' => $this->scenario->school->ref_idpes]);
    // O histórico aponta para a grade de 8 (1) ou de 9 anos (2), que a instalação cadastra.
    DB::table('pmieducar.historico_grade_curso')->insertOrIgnore([
        ['id' => 1, 'descricao_etapa' => 'Série', 'created_at' => now(), 'quantidade_etapas' => 8],
        ['id' => 2, 'descricao_etapa' => 'Ano', 'created_at' => now(), 'quantidade_etapas' => 9],
    ]);

    $this->service->gerarHistoricoTransferencia($this->registration->getKey(), $this->user->getKey());

    $history = LegacySchoolHistory::query()->where('ref_cod_matricula', $this->registration->getKey())->firstOrFail();

    expect($history->aprovado)->toBe(4)
        ->and($history->ano)->toBe(2030)
        ->and($history->escola)->not->toBeEmpty()
        ->and($history->escola_cidade)->not->toBeEmpty()
        ->and(LegacySchoolHistoryDiscipline::query()->where('historico_escolar_id', $history->getKey())->orderBy('sequencial')->pluck('nm_disciplina')->all())->toBe(['MATEMATICA', 'EDUCACAO FISICA']);
});

test('a média geral entra no histórico arredondada pela regra da matrícula', function () {
    $this->average->shouldReceive('getGlobalAverage')->once()->andReturn(7.5);
    $history = LegacySchoolHistoryFactory::new()->create([
        'ref_cod_aluno' => $this->registration->ref_cod_aluno,
        'ref_cod_matricula' => $this->registration,
        'sequencial' => 1,
    ]);

    $this->service->insertGlobalAverage($this->registration->ref_cod_aluno, 1, 9);

    $discipline = LegacySchoolHistoryDiscipline::query()->where('historico_escolar_id', $history->getKey())->where('sequencial', 9)->firstOrFail();

    expect($discipline->nm_disciplina)->toBe('Média Geral')
        ->and($discipline->nota)->toBe('7.5');
});

test('sem histórico ou sem matrícula no histórico não há média para gravar', function () {
    $this->average->shouldNotReceive('getGlobalAverage');
    LegacySchoolHistoryFactory::new()->create(['ref_cod_aluno' => $this->registration->ref_cod_aluno, 'ref_cod_matricula' => null, 'sequencial' => 2]);

    $this->service->insertGlobalAverage($this->registration->ref_cod_aluno, 1, 9);
    $this->service->insertGlobalAverage($this->registration->ref_cod_aluno, 2, 9);

    expect(LegacySchoolHistoryDiscipline::query()->where('ref_ref_cod_aluno', $this->registration->ref_cod_aluno)->exists())->toBeFalse();
});

test('o nome da escola vem do cadastro; sem ele, a busca cai no modelo de escola complementar, que não existe mais', function () {
    expect($this->service->getSchoolName($this->scenario->school->getKey()))->toBe($this->scenario->school->name)
        ->and(fn () => $this->service->getSchoolName(0))->toThrow(Error::class, 'LegacyComplementSchool');
});
