<?php

use App\Contracts\Output;
use iEducar\Support\Output\NullOutput;
use Tests\TestCase;

uses(TestCase::class);

test('null output implement interface out put', function () {
    $this->assertInstanceOf(Output::class, new NullOutput);
});

test('method progress advance will return void', function () {
    $this->assertNull((new NullOutput)->progressAdvance());
});

test('method info will return void', function () {
    $this->assertNull((new NullOutput)->info('message'));
});

test('method progress start will return void', function () {
    $this->assertNull((new NullOutput)->progressStart('max'));
});

test('method progress finish will return void', function () {
    $this->assertNull((new NullOutput)->progressFinish());
});
