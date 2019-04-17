<?php

namespace Tests\Browser\Pages\State;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class UpdatePage extends Page
{
    /**
     * @var int
     */
    private $stateAbbreviation;

    /**
     * DetailPage constructor.
     *
     * @param string $stateAbbreviation
     */
    public function __construct($stateAbbreviation)
    {
        $this->stateAbbreviation = $stateAbbreviation;
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
            return '/intranet/public_uf_cad.php';
        }

        return '/intranet/public_uf_cad.php?sigla_uf=' . $this->stateAbbreviation;
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
            ->assertSee('Editar UF');
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
            '@input-abbreviation' => '[name=sigla_uf]',
            '@input-name' => '[name=nome]',
            '@input-ibge' => '[name=cod_ibge]',
            '@button-save' => '#btn_enviar',
        ];
    }
}
