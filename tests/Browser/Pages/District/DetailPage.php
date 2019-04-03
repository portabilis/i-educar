<?php

namespace Tests\Browser\Pages\District;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class DetailPage extends Page
{
    /**
     * @var int
     */
    private $districtId;

    /**
     * DetailPage constructor.
     *
     * @param int $districtId
     */
    public function __construct($districtId)
    {
        $this->districtId = $districtId;
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
            return '/intranet/public_distrito_det.php';
        }

        return '/intranet/public_distrito_det.php?iddis=' . $this->districtId;
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
            ->assertSee('Detalhe do distrito')
            ->assertSee('Distrito - Detalhe');
    }

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [
            '@element' => '#selector',
        ];
    }
}
