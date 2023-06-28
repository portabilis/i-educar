<?php

namespace Tests\Unit\View;

use App\Models\LegacyPerson;
use App\Models\LegacyUser;
use App\Models\Phone;
use Tests\ViewTestCase;

class PhoneTest extends ViewTestCase
{
    protected $relations = [
        'person' => LegacyPerson::class,
        'createdBy' => LegacyUser::class,
        'updatedBy' => LegacyUser::class,
    ];

    protected function getViewModelName(): string
    {
        return Phone::class;
    }

    public function testGetFormattedNumberAttribute()
    {
        $areaCode = $this->model->area_code;
        $number = $this->model->number;

        $number = preg_replace('/(\d{4,5})(\d{4})/', '$1-$2', $number);
        $expect = "({$areaCode}) {$number}";

        $this->assertEquals($expect, $this->model->formattedNumber);
    }
}
