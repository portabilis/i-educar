<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyBenefit;
use App\Models\LegacyStudent;
use App\Models\LegacyStudentBenefit;
use App\Models\LegacyUser;
use Database\Factories\LegacyBenefitFactory;
use Database\Factories\LegacyStudentFactory;
use Tests\EloquentTestCase;

class LegacyBenefitTest extends EloquentTestCase
{
    public $relations = [
        'deletedByUser' => LegacyUser::class,
        'createdByUser' => LegacyUser::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyBenefit::class;
    }

    /** @test  */
    public function students()
    {
        $benefit = LegacyBenefitFactory::new()->create();
        $student = LegacyStudentFactory::new()->create();

        LegacyStudentBenefit::create([
            'aluno_id' => $student->cod_aluno,
            'aluno_beneficio_id' => $benefit->id,
        ]);

        $this->assertCount(1, $benefit->students);
        $this->assertInstanceOf(LegacyStudent::class, $benefit->students->first());
    }
}
