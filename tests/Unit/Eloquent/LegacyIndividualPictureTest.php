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

    /** @test */
    public function getUrlAttribute()
    {
        $this->assertEquals($this->model->caminho, $this->model->url);
    }

    /** @test */
    public function setUrlAttribute()
    {
        $this->model->url = 'http://www.example.com';

        $this->assertEquals('http://www.example.com', $this->model->url);
    }
}
