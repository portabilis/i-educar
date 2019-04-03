<?php

namespace Tests\Browser\Pages\State;

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
        return '/intranet/public_uf_lst.php';
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
            ->assertSee('Listagem de UFs')
            ->assertSee('Pais')
            ->assertSee('Sigla Uf')
            ->assertSee('Nome')
            ->assertSee('Uf - Listagem');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@input-name' => '[name=nome]',
        ];
    }
}
