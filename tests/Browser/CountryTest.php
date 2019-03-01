<?php

namespace Tests\Browser;

use App\Country;
use Tests\Browser\Pages\Country\CreatePage;
use Tests\Browser\Pages\Country\DetailPage;
use Tests\Browser\Pages\Country\ListingPage;
use Tests\Browser\Pages\Country\UpdatePage;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class CountryTest extends DuskTestCase
{
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
            $browser->loginLegacy()
                ->visit(new ListingPage());
        });
    }

    /**
     * Test create a country.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testCreateCountry()
    {
        $country = factory(Country::class)->make();

        $this->browse(function (Browser $browser) use ($country) {
            $browser->loginLegacy()
                ->visit(new CreatePage())
                ->type('@input-name', $country->name)
                ->type('@input-ibge', $country->ibge)
                ->press('@button-save')
                ->on(new ListingPage());
        });
    }

    /**
     * Test update a country.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testUpdateCountry()
    {
        $country = factory(Country::class)->make();

        $this->browse(function (Browser $browser) use ($country) {
            $browser->loginLegacy()
                ->visit(new UpdatePage(1)) // FIXME ID should be dynamic in the future
                ->type('@input-name', $country->name)
                ->type('@input-ibge', $country->ibge)
                ->press('@button-save')
                ->on(new ListingPage());
        });
    }

    /**
     * Test country flow.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testFlowForCountryPages()
    {
        $this->browse(function (Browser $browser) {
            $country = factory(Country::class)->make();
            $countryAfterUpdate = factory(Country::class)->make();

            $browser->loginLegacy();

            $browser->visit(new ListingPage())
                ->press(' Novo ');

            $browser->on(new CreatePage())
                ->type('@input-name', $country->name)
                ->type('@input-ibge', $country->ibge)
                ->press('@button-save');

            $browser->on(new ListingPage())
                ->type('@input-name', $country->name)
                ->press('Buscar');

            $browser->on(new ListingPage());

            $countryId = $browser->resolver->findByText($country->name, 'a')->getAttribute('data-id');

            $browser->clickLink($country->name);

            $browser->on(new DetailPage($countryId))
                ->press(' Editar ');

            $browser->on(new UpdatePage($countryId))
                ->type('@input-name', $countryAfterUpdate->name)
                ->type('@input-ibge', $countryAfterUpdate->ibge)
                ->press('@button-save');

            $browser->on(new ListingPage())
                ->type('@input-name', $countryAfterUpdate->name)
                ->press('Buscar')
                ->clickLink($countryAfterUpdate->name);

            $browser->on(new DetailPage($countryId));
        });
    }
}
