<?php

namespace Tests\Browser;

use App\State;
use Tests\Browser\Login\LoginAsAdmin;
use Tests\Browser\Pages\State\CreatePage;
use Tests\Browser\Pages\State\DetailPage;
use Tests\Browser\Pages\State\ListingPage;
use Tests\Browser\Pages\State\UpdatePage;
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
    public function testFlowForStatePages()
    {
        $this->browse(function (Browser $browser) {
            $state = factory(State::class)->make([
                'country_id' => 1, // FIXME makes dynamic in the future, now is Tonga (1)
            ]);
            $stateAfterUpdate = factory(State::class)->make([
                'country_id' => 1, // FIXME makes dynamic in the future, now is Tonga (1)
            ]);

            $browser->login();

            $browser->visit(new ListingPage())
                ->press(' Novo ');

            $browser->on(new CreatePage())
                ->select('@select-country', $state->country_id)
                ->type('@input-abbreviation', $state->abbreviation)
                ->type('@input-name', $state->name)
                ->type('@input-ibge', $state->ibge)
                ->press('@button-save');

            $browser->on(new ListingPage())
                ->type('@input-name', $state->name)
                ->press('Buscar');

            $browser->on(new ListingPage());

            $stateAbbreviation = $browser->resolver->findByText($state->name, 'a')->getAttribute('data-id');

            $browser->clickLink($state->name);

            $browser->on(new DetailPage($stateAbbreviation))
                ->press(' Editar ');

            $browser->on(new UpdatePage($stateAbbreviation));
        });
    }
}
