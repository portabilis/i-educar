<?php

namespace Tests\Browser\Pages\City;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class DetailPage extends Page
{
    /**
     * @var int
     */
    private $cityId;

    /**
     * DetailPage constructor.
     *
     * @param int $cityId
     */
    public function __construct($cityId)
    {
        $this->cityId = $cityId;
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
            return '/intranet/public_municipio_det.php';
        }

        return '/intranet/public_municipio_det.php?idmun=' . $this->cityId;
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
            ->assertSee('Detalhe do município')
            ->assertSee('Município - Detalhe');
    }
}
