<?php

namespace Tests\Unit\Observers;

use App\Http\Middleware\LoadSettings;
use App\Models\LegacyGeneralConfiguration;
use Database\Factories\LegacyGeneralConfigurationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LegacyGeneralConfigurationObserverTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    private function handle(): void
    {
        (new LoadSettings)->handle(Request::create('/intranet/index.php'), function () {});
    }

    public function test_general_configuration_cache_is_cleared_when_configuration_is_saved()
    {
        LegacyGeneralConfigurationFactory::new()->create();

        LegacyGeneralConfiguration::query()->get()->each->update(['ieducar_entity_name' => 'Antes']);

        $this->handle();
        $this->assertEquals('Antes', Config::get('legacy.config.ieducar_entity_name'));

        LegacyGeneralConfiguration::query()->get()->each->update(['ieducar_entity_name' => 'Depois']);

        $this->handle();
        $this->assertEquals('Depois', Config::get('legacy.config.ieducar_entity_name'));
    }
}
