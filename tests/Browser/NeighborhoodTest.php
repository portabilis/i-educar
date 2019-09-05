<?php

namespace Tests\Browser;

use Tests\Browser\Login\LoginAsAdmin;
use Tests\Browser\Pages\Neighborhood\ListingPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class NeighborhoodTest extends DuskTestCase
{
    use LoginAsAdmin;

    /**
     * Test neighborhood listing.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testNeighborhoodListing()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(new ListingPage());
        });
    }
}
