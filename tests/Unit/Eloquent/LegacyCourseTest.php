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

    private LegacyCourse $course;

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCourse::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->course = $this->createNewModel();
    }

    /** @test  */
    public function getDescriptionAttribute()
    {
        $this->assertEquals($this->course->getDescriptionAttribute(), $this->course->descricao);
    }

    /** @test  */
    public function getStepsAttribute()
    {
        $this->assertEquals($this->course->getStepsAttribute(), $this->course->qtd_etapas);
    }

    /** @test  */
    public function getNameAttribute()
    {
        $this->assertEquals($this->course->name, $this->course->nm_curso);
    }

    /** @test  */
    public function getIsStandardCalendarAttribute()
    {
        $this->assertEquals($this->course->getIsStandardCalendarAttribute(), $this->course->padrao_ano_escolar);
    }
}
