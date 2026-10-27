<?php

use App\Models\LegacyPerson;
use App\Models\LegacyUser;
use App\Models\Phone;
use Tests\ViewModelTestCase;

uses(ViewModelTestCase::class);

beforeEach(fn () => $this->viewModel(Phone::class, relations: [
    'person' => LegacyPerson::class,
    'createdBy' => LegacyUser::class,
    'updatedBy' => LegacyUser::class,
]));

ViewModelTestCase::registerModelTests();

test('get formatted number attribute', function () {
    $areaCode = $this->model->area_code;
    $number = $this->model->number;

    $number = preg_replace('/(\d{4,5})(\d{4})/', '$1-$2', $number);
    $expect = "({$areaCode}) {$number}";

    $this->assertEquals($expect, $this->model->formattedNumber);
});
