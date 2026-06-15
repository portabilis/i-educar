<?php

namespace Tests\Unit\Http\Controllers;

use App\Events\SystemSettingsUpdatedEvent;
use App\Http\Controllers\SettingController;
use Database\Factories\SettingFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SettingControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_save_inputs_dispatches_system_settings_updated_event()
    {
        Event::fake();

        $setting = SettingFactory::new()->create();

        $request = Request::create('/configuracoes/configuracoes-de-sistema', 'POST', [
            $setting->id => 'novo valor',
        ]);
        app(SettingController::class)->saveInputs($request);

        Event::assertDispatched(SystemSettingsUpdatedEvent::class);
    }
}
