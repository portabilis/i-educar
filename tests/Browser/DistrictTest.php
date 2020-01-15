<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Browser\Login\LoginAsAdmin;
use Tests\Browser\Pages\District\ListingPage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class DistrictTest extends DuskTestCase
{
    use LoginAsAdmin, WithFaker;

    /**
     * Test district listing.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testDistrictListing()
    {
        $this->browse(function (Browser $browser) {
            $browser->login()
                ->visit(new ListingPage());
        });
    }
}
