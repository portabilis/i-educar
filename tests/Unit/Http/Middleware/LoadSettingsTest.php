<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\LoadSettings;
use App\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LoadSettingsTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @return void
     */
    public function testMiddleware()
    {
        factory(Setting::class)->create([
            'key' => 'load.settings.test',
            'value' => 'Middleware for Test',
            'type' => Setting::TYPE_STRING,
        ]);

        $request = Request::create('/intranet/index.php');

        $middleware = new LoadSettings();

        $middleware->handle($request, function () {});

        $this->assertEquals('Middleware for Test', Config::get('load.settings.test'));
    }
}
