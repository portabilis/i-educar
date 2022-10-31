<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyCourseEducacensoStage;
use Tests\EloquentTestCase;

class LegacyCourseEducacensoStageTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyCourseEducacensoStage::class;
    }

    /** @test  */
    public function getIdsByCourse()
    {
        $return = $this->model->getIdsByCourse($this->model->curso_id);
        $this->assertIsArray($return);
        $this->assertCount(1, $return);
    }
}
