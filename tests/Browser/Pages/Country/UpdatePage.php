<?php

namespace Tests\Browser\Pages\Country;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class UpdatePage extends Page
{
    /**
     * @var int
     */
    private $countryId;

    /**
     * UpdatePage constructor.
     *
     * @param int $countryId
     */
    public function __construct($countryId)
    {
        $this->countryId = $countryId;
    }

    /**
     * Get the URL for the page.
     *
     * @param bool $onlyPath
     *
     * @return string
     */
    public function url($onlyPath = false)
    {
        if ($onlyPath) {
            return '/intranet/public_pais_cad.php';
        }

        return '/intranet/public_pais_cad.php?idpais=' . $this->countryId;
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param  Browser  $browser
     * @return void
     */
    public function assert(Browser $browser)
    {
        $browser->assertPathIs($this->url(true))
            ->assertSee('Editar país')
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
