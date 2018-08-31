<?php

namespace Tests\Browser\Routes\LegacyDuskPath;

use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\RouteTestCase;

class LegacyDuskClass extends RouteTestCase
{
    /**
     * Route.
     *
     * @var string
     */
    const ROUTE = 'LegacyDuskRoute';

    /**
     * Test route.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testRoute()
    {
        $this->browse(function (Browser $browser) {
            $route = self::ROUTE;

            $browser->loginLegacy();
            $browser->visit($route);

            $title = $browser->driver->getTitle();

            $assert = Str::contains($title, 'i-Educar');

            $this->assertTrue(
                $assert, "The route [{$route}] returned [{$title}] instead [i-Educar] in the title."
            );
        });
    }
}
