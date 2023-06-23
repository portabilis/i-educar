<?php

namespace Tests\Unit\App\Models;

use App\Models\LegacyBenefit;
use App\Models\LegacyIndividual;
use App\Models\LegacyPerson;
use App\Models\LegacyRegistration;
use App\Models\LegacyStudent;
use App\Models\StudentInep;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\LegacyStudentFactory;
use Tests\EloquentTestCase;

class LegacyStudentTest extends EloquentTestCase
{
    /**
     * @var array
     */
    protected $relations = [
        'individual' => LegacyIndividual::class,
        'person' => LegacyPerson::class,
        'registrations' => LegacyRegistration::class,
        'inep' => StudentInep::class,
        'benefits' => LegacyBenefit::class,
    ];

    protected function getEloquentModelName(): string
    {
        return LegacyStudent::class;
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->tipo_responsavel, $this->model->guardianType);
        $this->assertEquals($this->model->inep ? $this->model->inep->number : null, $this->model->inepNumber);
        $this->assertEquals($this->model->aluno_estado_id, $this->model->stateRegistrationId);
    }

    public function testGetGuardianName(): void
    {
        $individual = LegacyIndividualFactory::new()->father()->mother()->guardian()->create();
        $model = LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual,
        ]);

        $join = $model->individual->mother->name . ', ' . $model->individual->father->name;
        $expected = match ($model->guardianType) {
            'm' => $model->individual->mother->name,
            'p' => $model->individual->father->name,
            'r' => $model->individual->responsible->name,
            'a' => strlen($join) < 3 ? null : $join,
            default => null
        };
        $this->assertEquals($expected, $model->getGuardianName());
    }

    public function testGetGuardianCpf(): void
    {
        $join = ($this->model->individual->mother->individual->cpf ?? 'não informado') . ', ' . ($this->individual->model->father->individual->cpf ?? 'não informado');
        $expected = match ($this->model->guardianType) {
            'm' => $this->individual->mother->individual->cpf ?? 'não informado',
            'p' => $this->individual->father->individual->cpf ?? 'não informado',
            'r' => $this->individual->responsible->individual->cpf ?? 'não informado',
            'a' => strlen($join) < 3 ? null : $join,
            default => null
        };
        $this->assertEquals($expected, $this->model->getGuardianCpf());
    }

    public function testScopeActive(): void
    {
        LegacyStudentFactory::new()->create(['ativo' => 0]);
        $found = $this->instanceNewEloquentModel()->active()->get();
        $this->assertCount(1, $found);
    }

    public function testScopeMale(): void
    {
        LegacyIndividual::query()->update([
            'sexo' => null,
        ]);

        $individual = LegacyIndividualFactory::new()->create(['sexo' => 'M']);
        LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual,
        ]);
        $found = $this->instanceNewEloquentModel()->male()->get();
        $this->assertCount(1, $found);
    }

    public function testScopeFemale(): void
    {
        LegacyIndividual::query()->update([
            'sexo' => null,
        ]);

        $individual = LegacyIndividualFactory::new()->create(['sexo' => 'F']);
        $student2 = LegacyStudentFactory::new()->create([
            'ref_idpes' => $individual,
        ]);
        $found = $this->instanceNewEloquentModel()->female()->whereIn('cod_aluno', [
            $student2->cod_aluno,
            $this->model->cod_aluno,
        ])->get();
        $this->assertCount(1, $found);
    }
}
