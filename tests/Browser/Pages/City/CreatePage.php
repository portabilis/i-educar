<?php

namespace Tests\Browser\Pages\City;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page;

class CreatePage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/intranet/public_municipio_cad.php';
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
            ->assertSee('Cadastrar município');
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
            '@input-ibge' => '[name=cod_ibge]',
            '@button-save' => '#btn_enviar',
        ];
    }
}
