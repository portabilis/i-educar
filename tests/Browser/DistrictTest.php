<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\Browser\Pages\District\CreatePage;
use Tests\Browser\Pages\District\DetailPage;
use Tests\Browser\Pages\District\ListingPage;
use Tests\Browser\Pages\District\UpdatePage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class DistrictTest extends DuskTestCase
{
    use WithFaker;

    /**
     * Test district flow.
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
            $city = 4418; // Içara
            $districtName = $this->faker->name;
            $districtNameAfterUpdate = $this->faker->name;

            $browser->loginLegacy();

            $browser->visit(new ListingPage())
                ->press(' Novo ');

            $browser->on(new CreatePage())
                ->select('@select-country', $country)
                ->waitUsing(10, 1000, function () use ($browser) {
                    return $browser->resolver->findOrFail('[name=sigla_uf]')->isEnabled();
                })
                ->select('@select-state', $state)
                ->waitUsing(10, 1000, function () use ($browser) {
                    return $browser->resolver->findOrFail('[name=idmun]')->isEnabled();
                })
                ->select('@select-city', $city)
                ->type('@input-name', $districtName)
                ->press('@button-save');

            $browser->on(new ListingPage())
                ->type('@input-name', $districtName)
                ->press('Buscar');

            $browser->on(new ListingPage());

            $districtId = $browser->resolver->findByText($districtName, 'a')->getAttribute('data-id');

            $browser->clickLink($districtName);

            $browser->on(new DetailPage($districtId))
                ->press(' Editar ');

            $browser->on(new UpdatePage($districtId))
                ->type('@input-name', $districtNameAfterUpdate)
                ->press('@button-save');

            $browser->on(new ListingPage())
                ->type('@input-name', $districtNameAfterUpdate)
                ->press('Buscar')
                ->clickLink($districtNameAfterUpdate);

            $browser->on(new DetailPage($districtId));
        });
    }

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
            $browser->loginLegacy();
            $browser->visit(new ListingPage());
        });
    }
}
