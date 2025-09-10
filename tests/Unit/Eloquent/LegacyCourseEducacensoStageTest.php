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

    public function test_get_ids_by_course()
    {
        $return = $this->model->getIdsByCourse($this->model->curso_id);
        $this->assertIsArray($return);
        $this->assertCount(1, $return);
    }
}
