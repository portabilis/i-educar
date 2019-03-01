<?php

namespace Tests\Browser\Pages\District;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class CreatePage extends Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/intranet/public_distrito_cad.php';
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
            ->assertSee('Cadastrar distrito');
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
            '@select-city' => '[name=idmun]',
            '@input-name' => '[name=nome]',
            '@input-ibge' => '[name=cod_ibge]',
            '@button-save' => '#btn_enviar',
        ];
    }
}
