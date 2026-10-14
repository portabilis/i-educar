<?php

use App\Http\Middleware\LoadSettings;
use App\Models\LegacyGeneralConfiguration;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->handle = function () {
        (new LoadSettings)->handle(Request::create('/intranet/index.php'), function () {});
    };

    Cache::flush();
});

test('general configuration cache is cleared when configuration is saved', function () {
    LegacyGeneralConfigurationFactory::new()->create();

    LegacyGeneralConfiguration::query()->get()->each->update(['ieducar_entity_name' => 'Antes']);

    ($this->handle)();
    $this->assertEquals('Antes', Config::get('legacy.config.ieducar_entity_name'));

    LegacyGeneralConfiguration::query()->get()->each->update(['ieducar_entity_name' => 'Depois']);

    ($this->handle)();
    $this->assertEquals('Depois', Config::get('legacy.config.ieducar_entity_name'));
});
