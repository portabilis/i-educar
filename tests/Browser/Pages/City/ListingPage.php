<?php

namespace Tests\Browser\Pages\City;

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
        return '/intranet/public_municipio_lst.php';
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
            ->assertSee('Listagem de municípios')
            ->assertSee('Pais')
            ->assertSee('Estado')
            ->assertSee('Nome')
            ->assertSee('Município - Listagem');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@select-country' => '[name=idpais]',
            '@select-state' => '[name=sigla_uf]',
            '@input-name' => '[name=nome]',
            '@button-search' => '#botao_busca',
        ];
    }
}
