<?php

namespace Tests\Browser\Pages\State;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class DetailPage extends Page
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
            return '/intranet/public_uf_det.php';
        }

        return '/intranet/public_uf_det.php?sigla_uf=' . $this->stateAbbreviation;
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
            ->assertSee('Detalhe da UF')
            ->assertSee('Uf - Detalhe');
    }
}
