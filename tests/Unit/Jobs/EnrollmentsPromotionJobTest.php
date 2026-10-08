<?php

namespace Tests\Unit\Jobs;

use App\Jobs\EnrollmentsPromotionJob;
use Illuminate\Database\DatabaseManager;
use Mockery;
use PromocaoApiController;
use Tests\TestCase;

class EnrollmentsPromotionJobTest extends TestCase
{
    public function test_switches_the_connection_and_promotes_the_enrollment(): void
    {
        $manager = Mockery::mock(DatabaseManager::class);
        $manager->shouldReceive('setDefaultConnection')->once()->with('escola-alfa');

        $controller = Mockery::mock(PromocaoApiController::class);
        $controller->shouldReceive('processEnrollmentsPromotion')->once()->with(7, 42, true);

        (new EnrollmentsPromotionJob(7, 42, 'escola-alfa', true))->handle($manager, $controller);
    }
}
