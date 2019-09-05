<?php

namespace Tests\Browser;

use Tests\Browser\Login\LoginAsAdmin;
use Tests\Browser\Pages\Country\ListingPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class CountryTest extends DuskTestCase
{
    use LoginAsAdmin;

    /**
     * Test country listing.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testCountryListing()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(new ListingPage());
        });
    }
}
