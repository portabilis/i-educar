<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Browser\Login\LoginAsAdmin;
use Tests\Browser\Pages\City\CreatePage;
use Tests\Browser\Pages\City\DetailPage;
use Tests\Browser\Pages\City\ListingPage;
use Tests\Browser\Pages\City\UpdatePage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class CityTest extends DuskTestCase
{
    use LoginAsAdmin, WithFaker;

    /**
     * Test city flow.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testFlowForCityPages()
    {
        $this->browse(function (Browser $browser) {
            $country = 45; // Brasil
            $state = 'SC'; // Santa Catarina
            $cityName = $this->faker->city;
            $cityNameAfterUpdate = $this->faker->city;

            $browser->login();

            $browser->visit(new ListingPage())
                ->press(' Novo ');

            $browser->on(new CreatePage())
                ->select('@select-country', $country)
                ->waitUsing(10, 1000, function () use ($browser) {
                    return $browser->resolver->findOrFail('[name=sigla_uf]')->isEnabled();
                })
                ->select('@select-state', $state)
                ->type('@input-name', $cityName)
                ->press('@button-save');

            $browser->on(new ListingPage())
                ->type('@input-name', $cityName)
                ->press('Buscar');

            $browser->on(new ListingPage());

            $cityId = $browser->resolver->findByText($cityName, 'a')->getAttribute('data-id');

            $browser->clickLink($cityName);

            $browser->on(new DetailPage($cityId))
                ->press(' Editar ');

            $browser->on(new UpdatePage($cityId))
                ->type('@input-name', $cityNameAfterUpdate)
                ->press('@button-save');

            $browser->on(new ListingPage())
                ->type('@input-name', $cityNameAfterUpdate)
                ->press('Buscar')
                ->clickLink($cityNameAfterUpdate);

            $browser->on(new DetailPage($cityId));
        });
    }

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
            $browser->login();
            $browser->visit(new ListingPage());
        });
    }
}
