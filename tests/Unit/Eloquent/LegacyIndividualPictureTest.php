<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyIndividualPicture;
use Tests\EloquentTestCase;

class LegacyIndividualPictureTest extends EloquentTestCase
{
    private LegacyIndividualPicture $picture;

    /**
     * @return string
     */
    protected function getEloquentModelName()
    {
        return LegacyIndividualPicture::class;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->picture = $this->createNewModel();
    }

    /** @test */
    public function getUrlAttribute()
    {
        $this->assertEquals($this->picture->getUrlAttribute(), $this->picture->url);
    }

    /** @test */
    public function setUrlAttribute()
    {
        $this->picture->setUrlAttribute('http://www.example.com');

        $this->assertEquals('http://www.example.com', $this->picture->getUrlAttribute());
        $this->assertEquals('http://www.example.com', $this->picture->url);
    }
}
