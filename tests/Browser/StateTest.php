<?php

namespace Tests\Browser;

use App\State;
use Tests\Browser\Login\LoginAsAdmin;
use Tests\Browser\Pages\State\ListingPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class StateTest extends DuskTestCase
{
    use LoginAsAdmin;

    /**
     * Test state listing.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testStateListing()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(new ListingPage());
        });
    }
}
