<?php

namespace Tests\Browser;

use Tests\Browser\Pages\LoginPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Throwable;

class LoginPageTest extends DuskTestCase
{
    /**
     * Test if login page is shown.
     *
     * @return void
     *
     * @throws Throwable
     */
    public function testSeeLoginPage()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->on(new LoginPage());
        });
    }

    /**
     * @return void
     *
     * @throws Throwable
     */
    public function testErrorLogin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->on(new LoginPage())
                ->type('login', 'admin')
                ->type('password', 'wrong-password')
                ->press('Entrar');

            $browser->on(new LoginPage())
                ->assertSee(trans('auth.failed'));
        });
    }
}
