<?php

use App\Services\ReleasePeriodService;
use Database\Factories\LegacySchoolClassStageFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\ReleasePeriodDateFactory;
use Database\Factories\ReleasePeriodFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Request\Support\EnrollmentScenario;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->scenario = EnrollmentScenario::for(LegacyUserFactory::new()->admin()->create(), now()->year);
    $this->scenario->course->update(['padrao_ano_escolar' => 0]);
    $this->schoolClass = $this->scenario->schoolClass();
    $this->stageType = DB::table('pmieducar.modulo')->where('ref_cod_instituicao', $this->scenario->institution)->value('cod_modulo');
    foreach ([1, 2] as $sequence) {
        LegacySchoolClassStageFactory::new()->create(['ref_cod_turma' => $this->schoolClass, 'sequencial' => $sequence, 'ref_cod_modulo' => $this->stageType]);
    }

    $this->service = new ReleasePeriodService;
    $this->canPost = fn ($stage) => $this->service->canPostNow($this->scenario->school->getKey(), $this->schoolClass->getKey(), $stage, now()->year);
    $this->period = function (int $stage, string $start, string $end) {
        $period = ReleasePeriodFactory::new()->create(['year' => now()->year, 'stage' => $stage, 'stage_type_id' => $this->stageType]);
        $period->schools()->attach($this->scenario->school->getKey());
        ReleasePeriodDateFactory::new()->create(['release_period_id' => $period, 'start_date' => $start, 'end_date' => $end]);
    };
});

test('sem período cadastrado, pode lançar; com período, só dentro das datas', function () {
    expect(($this->canPost)(1))->toBeTrue();

    ($this->period)(1, now()->subDay()->toDateString(), now()->addDay()->toDateString());
    ($this->period)(2, now()->addDays(5)->toDateString(), now()->addDays(10)->toDateString());

    expect(($this->canPost)(1))->toBeTrue()
        ->and(($this->canPost)(2))->toBeFalse()
        ->and(($this->canPost)('Rc'))->toBeFalse()
        ->and(($this->canPost)('An'))->toBeFalse();
});

test('a turma precisa ter etapas', function () {
    $other = $this->scenario->schoolClass();

    expect(fn () => $this->service->canPostNow($this->scenario->school->getKey(), $other->getKey(), 1, now()->year))
        ->toThrow(Exception::class, 'Não foi possível identificar as etapas da turma');
});
