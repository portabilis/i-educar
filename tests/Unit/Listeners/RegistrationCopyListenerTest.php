<?php

namespace Tests\Unit\Listeners;

use App\Events\RegistrationCopyEvent;
use App\Listeners\RegistrationCopyListener;
use App\Models\LegacyRegistration;
use App\Services\RegistrationDataService;
use Mockery;
use Tests\TestCase;

class RegistrationCopyListenerTest extends TestCase
{
    public function test_copies_the_old_registration_data_into_the_new_one(): void
    {
        $new = new LegacyRegistration;
        $old = new LegacyRegistration;

        $service = Mockery::mock(RegistrationDataService::class);
        $service->shouldReceive('copy')->once()->with($new, $old);

        (new RegistrationCopyListener($service))->handle(new RegistrationCopyEvent($new, $old));
    }
}
