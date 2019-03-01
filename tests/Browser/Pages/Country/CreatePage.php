<?php

namespace Tests\Browser\Pages\Country;

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
        return '/intranet/public_pais_cad.php';
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
            ->assertSee('Cadastrar país')
            ->assertSee('Nome')
            ->assertSee('Código INEP');
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
            '@input-ibge' => '[name=cod_ibge]',
            '@button-save' => '#btn_enviar',
        ];
    }
}
