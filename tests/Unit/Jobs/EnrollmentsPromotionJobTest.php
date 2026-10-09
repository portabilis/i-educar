<?php

use App\Jobs\EnrollmentsPromotionJob;
use Illuminate\Database\DatabaseManager;
use Tests\TestCase;

uses(TestCase::class);

test('switches the connection and promotes the enrollment', function () {
    $manager = Mockery::mock(DatabaseManager::class);
    $manager->shouldReceive('setDefaultConnection')->once()->with('escola-alfa');

    $controller = Mockery::mock(PromocaoApiController::class);
    $controller->shouldReceive('processEnrollmentsPromotion')->once()->with(7, 42, true);

    (new EnrollmentsPromotionJob(7, 42, 'escola-alfa', true))->handle($manager, $controller);
});
