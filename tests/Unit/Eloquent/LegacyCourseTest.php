<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourse;
use App\Models\LegacyGrade;
use App\Models\LegacyQualification;
use Tests\EloquentTestCase;

class LegacyCourseTest extends EloquentTestCase
{
    protected $relations = [
        'grades' => LegacyGrade::class,
        'qualifications' => LegacyQualification::class,
    ];

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCourse::class;
    }

    /** @test  */
    public function getIdAttribute()
    {
        $this->assertEquals($this->model->id, $this->model->cod_curso);
    }

    /** @test  */
    public function getDescriptionAttribute()
    {
        $this->assertEquals($this->model->description, $this->model->descricao);
    }

    /** @test  */
    public function getStepsAttribute()
    {
        $this->assertEquals($this->model->steps, $this->model->qtd_etapas);
    }

    /** @test  */
    public function getNameAttribute()
    {
        $expected = $this->model->nm_curso . ' (' . $this->model->description . ')';
        $this->assertEquals($expected, $this->model->name);

        $this->model->description = null;
        $this->assertEquals($this->model->name, $this->model->nm_curso);
    }

    /** @test  */
    public function getIsStandardCalendarAttribute()
    {
        $this->assertEquals($this->model->isStandardCalendar, $this->model->padrao_ano_escolar);
    }
}
