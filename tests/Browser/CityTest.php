<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Browser\Login\LoginAsAdmin;
use Tests\Browser\Pages\City\ListingPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class CityTest extends DuskTestCase
{
    use LoginAsAdmin, WithFaker;

    /**
     * Test city listing.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testCityListing()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(new ListingPage());
        });
    }
}
