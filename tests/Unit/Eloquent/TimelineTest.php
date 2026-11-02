<?php

use App\Models\LegacyIndividual;
use App\Models\Timeline;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\TimelineFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('payload is stored as array', function () {
    $timeline = TimelineFactory::new()->create()->fresh();

    $this->assertIsArray($timeline->payload);
    $this->assertArrayHasKey('user', $timeline->payload);
});

test('has no updated at', function () {
    $timeline = TimelineFactory::new()->create();

    $this->assertNotNull($timeline->created_at);
    $this->assertNull(Timeline::UPDATED_AT);
    $this->assertArrayNotHasKey('updated_at', $timeline->getAttributes());
});

test('belongs to individual through morph', function () {
    $individual = LegacyIndividualFactory::new()->create();

    $timeline = TimelineFactory::new()->create([
        'model_id' => $individual->getKey(),
        'model_type' => LegacyIndividual::class,
    ]);

    $this->assertInstanceOf(LegacyIndividual::class, $timeline->model);
    $this->assertEquals($individual->getKey(), $timeline->model->getKey());
});

test('individual has many timelines', function () {
    $individual = LegacyIndividualFactory::new()->create();

    TimelineFactory::new()->count(2)->create([
        'model_id' => $individual->getKey(),
        'model_type' => LegacyIndividual::class,
    ]);

    $this->assertCount(2, $individual->timelines);
});
