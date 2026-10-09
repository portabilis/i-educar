<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyEnrollment;
use App\Models\LegacyPeriod;
use App\Models\LegacyRegistration;
use App\Models\LegacySchoolClass;
use App\Models\LegacyUser;
use App\Models\RegistrationStatus;
use Database\Factories\LegacyEnrollmentFactory;
use Database\Factories\LegacyPeriodFactory;
use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacySchoolClassFactory;
use Database\Seeders\DefaultRelatorioSituacaoMatriculaTableSeeder;
use Tests\EloquentTestCase;

class LegacyEnrollmentTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'registration' => LegacyRegistration::class,
        'schoolClass' => LegacySchoolClass::class,
        'period' => LegacyPeriod::class,
        'createdBy' => LegacyUser::class,
        'updatedBy' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyEnrollment::class;
    }

    public function test_attributes()
    {
        $this->assertEquals($this->model->ref_cod_matricula, $this->model->registration_id);
        $this->assertEquals($this->model->registration->student->person->nome, $this->model->student_name);
        $this->assertEquals($this->model->data_enturmacao, $this->model->date);
        $this->assertEquals($this->model->date_departed, $this->model->data_exclusao);
        $this->assertEquals($this->model->ref_cod_turma, $this->model->school_class_id);
        $this->assertEquals($this->model->registration->student->cod_aluno, $this->model->getStudentId());
    }

    public function test_filters_by_activity_and_by_situation(): void
    {
        $registration = LegacyRegistrationFactory::new()->create(['dependencia' => false]);
        $active = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);
        $transferred = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 0, 'transferido' => true]);
        $dropped = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 0]);

        $ids = fn ($query) => $query->whereRegistration($registration->getKey())->orderBy('id')->pluck('id')->all();

        $this->assertSame([$transferred->id, $dropped->id], $ids(LegacyEnrollment::query()->notActive()));
        $this->assertSame([$active->id], $ids(LegacyEnrollment::query()->activeBySituation(RegistrationStatus::ONGOING)));
        $this->assertSame([$active->id, $transferred->id], $ids(LegacyEnrollment::query()->activeBySituation(RegistrationStatus::TRANSFERRED)));
        $this->assertSame([$active->id, $transferred->id], $ids(LegacyEnrollment::query()->activeBySituation(null)));
    }

    public function test_filters_by_period_of_the_enrollment_or_of_the_class(): void
    {
        $period = LegacyPeriodFactory::new()->create();
        $other = LegacyPeriodFactory::new()->create();
        $registration = LegacyRegistrationFactory::new()->create();
        $own = LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'turno_id' => $period]);
        $fromClass = LegacyEnrollmentFactory::new()->create([
            'ref_cod_matricula' => $registration,
            'turno_id' => null,
            'ref_cod_turma' => LegacySchoolClassFactory::new()->create(['turma_turno_id' => $period]),
        ]);
        LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'turno_id' => $other]);

        $found = LegacyEnrollment::query()->whereRegistration($registration->getKey())->wherePeriod($period->getKey())->orderBy('id')->pluck('id')->all();

        $this->assertSame([$own->id, $fromClass->id], $found);
    }

    public function test_joins_the_situation_views(): void
    {
        $this->seed(DefaultRelatorioSituacaoMatriculaTableSeeder::class);

        $registration = LegacyRegistrationFactory::new()->create(['aprovado' => RegistrationStatus::APPROVED, 'ativo' => 1]);
        LegacyEnrollmentFactory::new()->create(['ref_cod_matricula' => $registration, 'ativo' => 1]);

        $simple = LegacyEnrollment::query()->whereRegistration($registration->getKey())->addJoinViewSituacao(RegistrationStatus::APPROVED)->get();
        $reports = LegacyEnrollment::query()->whereRegistration($registration->getKey())->addJoinViewSituacaoRelatorios(RegistrationStatus::APPROVED)->get();

        $this->assertCount(1, $simple);
        $this->assertNotEmpty($simple->first()->texto_situacao);
        $this->assertCount(1, $reports);
        $this->assertNotEmpty($reports->first()->texto_situacao);
    }
}
