<?php

use App\Events\RegistrationCopyEvent;
use App\Listeners\RegistrationCopyListener;
use App\Models\LegacyRegistration;
use App\Services\RegistrationDataService;
use Tests\TestCase;

uses(TestCase::class);

test('copies the old registration data into the new one', function () {
    $new = new LegacyRegistration;
    $old = new LegacyRegistration;

    $service = Mockery::mock(RegistrationDataService::class);
    $service->shouldReceive('copy')->once()->with($new, $old);

    (new RegistrationCopyListener($service))->handle(new RegistrationCopyEvent($new, $old));
});
