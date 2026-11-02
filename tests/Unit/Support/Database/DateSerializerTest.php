<?php

use App\Support\Database\DateSerializer;
use Carbon\Carbon;
use Tests\TestCase;

uses(TestCase::class, DateSerializer::class);

test('trait', function () {
    $expect = '2022-01-01 23:01:00';

    $this->assertEquals($expect, $this->serializeDate(Carbon::parse($expect)));
});
