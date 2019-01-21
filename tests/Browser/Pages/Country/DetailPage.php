<?php

namespace Tests\Browser\Pages\Country;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class DetailPage extends Page
{
    /**
     * @var int
     */
    private $countryId;

    /**
     * DetailPage constructor.
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
            return '/intranet/public_pais_det.php';
        }

        return '/intranet/public_pais_det.php?idpais=' . $this->countryId;
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
            ->assertSee('Detalhe do país')
            ->assertSee('Pais - Detalhe');
    }
}
