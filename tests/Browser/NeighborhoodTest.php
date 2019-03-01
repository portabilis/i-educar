<?php

namespace Tests\Browser;

use Tests\Browser\Pages\Neighborhood\ListingPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class NeighborhoodTest extends DuskTestCase
{
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
            $browser->loginLegacy();

            $browser->visit(new ListingPage());
        });
    }
}
