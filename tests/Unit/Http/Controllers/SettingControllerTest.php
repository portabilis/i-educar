<?php

use App\Events\SystemSettingsUpdatedEvent;
use App\Http\Controllers\SettingController;
use Database\Factories\SettingFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('save inputs dispatches system settings updated event', function () {
    Event::fake();

    $setting = SettingFactory::new()->create();

    $request = Request::create('/configuracoes/configuracoes-de-sistema', 'POST', [
        $setting->id => 'novo valor',
    ]);
    app(SettingController::class)->saveInputs($request);

    Event::assertDispatched(SystemSettingsUpdatedEvent::class);
});
