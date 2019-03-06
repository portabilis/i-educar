<?php

namespace Tests\Browser\Pages\Neighborhood;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class ListingPage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/intranet/public_bairro_lst.php';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url())
            ->assertSee('Listagem de bairros')
            ->assertSee('Pais')
            ->assertSee('Estado')
            ->assertSee('Município')
            ->assertSee('Distrito')
            ->assertSee('Nome')
            ->assertSee('Bairro - Listagem');
    }
}
