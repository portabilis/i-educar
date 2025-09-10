<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyIndividualPicture;
use Tests\EloquentTestCase;

class LegacyIndividualPictureTest extends EloquentTestCase
{
    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyIndividualPicture::class;
    }

    public function test_attributes()
    {
        $this->assertEquals($this->model->caminho, $this->model->url);

        $this->model->url = 'http://www.example.com';

        $this->assertEquals('http://www.example.com', $this->model->url);
    }
}
