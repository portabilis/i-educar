<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourseEducacensoStage;
use Tests\EloquentTestCase;

class LegacyCourseEducacensoStageTest extends EloquentTestCase
{
    private LegacyCourseEducacensoStage $courseEducacensoStage;

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCourseEducacensoStage::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->courseEducacensoStage = $this->createNewModel();
    }

    /** @test  */
    public function getIdsByCourse()
    {
        $return = $this->courseEducacensoStage->getIdsByCourse($this->courseEducacensoStage->curso_id);
        $this->assertIsArray($return);
        $this->assertCount(1, $return);
    }
}
