<?php

namespace Tests\Unit\Http\Middleware;

use App\Events\SystemSettingsUpdatedEvent;
use App\Http\Middleware\LoadSettings;
use App\Setting;
use Database\Factories\SettingFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoadSettingsTest extends TestCase
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

    /**
     * @return void
     */
    public function test_middleware()
    {
        SettingFactory::new()->create([
            'key' => 'load.settings.test',
            'value' => 'Middleware for Test',
            'type' => Setting::TYPE_STRING,
        ]);

        $request = Request::create('/intranet/index.php');

        $middleware = new LoadSettings;

        $middleware->handle($request, function () {});

        $this->assertEquals('Middleware for Test', Config::get('load.settings.test'));
    }

    public function test_settings_are_cached_between_requests()
    {
        SettingFactory::new()->create([
            'key' => 'load.settings.cached',
            'value' => 'valor',
            'type' => Setting::TYPE_STRING,
        ]);

        $this->handle();

        DB::enableQueryLog();
        DB::flushQueryLog();

        $this->handle();

        $queries = collect(DB::getQueryLog())->pluck('query');

        $this->assertFalse($queries->contains(fn ($query) => str_contains($query, 'from "settings"')));
        $this->assertFalse($queries->contains(fn ($query) => str_contains($query, 'configuracoes_gerais')));
    }

    public function test_settings_cache_is_cleared_when_system_settings_are_updated()
    {
        $setting = SettingFactory::new()->create([
            'key' => 'load.settings.invalidation',
            'value' => 'antigo',
            'type' => Setting::TYPE_STRING,
        ]);

        $this->handle();
        $this->assertEquals('antigo', Config::get('load.settings.invalidation'));

        Setting::where('id', $setting->getKey())->update(['value' => 'novo']);
        SystemSettingsUpdatedEvent::dispatch();

        $this->handle();

        $this->assertEquals('novo', Config::get('load.settings.invalidation'));
    }
}
