<?php

namespace Tests\Browser;

use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;

class LegacyCheckAuthenticationTest extends DuskTestCase
{
    public function testShouldRedirectIfNotAuthenticated()
    {
        $this->browse(function (Browser $browser) {
            $route = '/enrollment/update-enrollments-status';

            $browser->visit($route);

            $browser->on(new LoginPage());
        });
    }

    public function testSeePageIfAuthenticated()
    {
        $this->browse(function (Browser $browser) {
            $route = '/enrollment/update-enrollments-status';

            $browser->loginLegacy();
            $browser->visit($route);

            $content = $browser->driver->getPageSource();

            $assert = Str::contains($content, 'Alterar situação de matrículas');
            $this->assertTrue($assert);
        });
    }
}
