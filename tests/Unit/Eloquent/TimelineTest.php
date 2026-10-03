<?php

namespace Tests\Unit\Eloquent;

use App\Models\LegacyIndividual;
use App\Models\Timeline;
use Database\Factories\LegacyIndividualFactory;
use Database\Factories\TimelineFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TimelineTest extends TestCase
{
    use DatabaseTransactions;

    public function test_payload_is_stored_as_array(): void
    {
        $timeline = TimelineFactory::new()->create()->fresh();

        $this->assertIsArray($timeline->payload);
        $this->assertArrayHasKey('user', $timeline->payload);
    }

    public function test_has_no_updated_at(): void
    {
        $timeline = TimelineFactory::new()->create();

        $this->assertNotNull($timeline->created_at);
        $this->assertNull(Timeline::UPDATED_AT);
        $this->assertArrayNotHasKey('updated_at', $timeline->getAttributes());
    }

    public function test_belongs_to_individual_through_morph(): void
    {
        $individual = LegacyIndividualFactory::new()->create();

        $timeline = TimelineFactory::new()->create([
            'model_id' => $individual->getKey(),
            'model_type' => LegacyIndividual::class,
        ]);

        $this->assertInstanceOf(LegacyIndividual::class, $timeline->model);
        $this->assertEquals($individual->getKey(), $timeline->model->getKey());
    }

    public function test_individual_has_many_timelines(): void
    {
        $individual = LegacyIndividualFactory::new()->create();

        TimelineFactory::new()->count(2)->create([
            'model_id' => $individual->getKey(),
            'model_type' => LegacyIndividual::class,
        ]);

        $this->assertCount(2, $individual->timelines);
    }
}
