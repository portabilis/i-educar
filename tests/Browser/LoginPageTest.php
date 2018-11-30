<?php

namespace Tests\Browser;

use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class LoginPageTest extends DuskTestCase
{
    /**
     * Test if login page is shown.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testSeeLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->on(new LoginPage());
        });
    }
}
