<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyEducationLevel;
use App\Models\LegacyEducationType;
use App\Models\LegacyGrade;
use App\Models\LegacyInstitution;
use Tests\EloquentTestCase;

class LegacyCourseTest extends EloquentTestCase
{
    protected $relations = [
        'grades' => LegacyGrade::class,
        'educationType' => LegacyEducationType::class,
        'educationLevel' => LegacyEducationLevel::class,
        'institution' => LegacyInstitution::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCourse::class;
    }

    /** @test */
    public function attributes()
    {
        $this->assertEquals($this->model->id, $this->model->cod_curso);
        $this->assertEquals($this->model->description, $this->model->descricao);
        $this->assertEquals($this->model->steps, $this->model->qtd_etapas);
        $expected = $this->model->nm_curso . ' (' . $this->model->description . ')';
        $this->assertEquals($expected, $this->model->name);

        $this->model->description = null;
        $this->assertEquals($this->model->name, $this->model->nm_curso);
        $this->assertEquals($this->model->isStandardCalendar, $this->model->padrao_ano_escolar);
    }
}
