<?php

namespace Tests\Browser\Pages\District;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Page;

class UpdatePage extends Page
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
            return '/intranet/public_distrito_cad.php';
        }

        return '/intranet/public_distrito_cad.php?iddis=' . $this->districtId;
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
            ->assertSee('Editar distrito');
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
            '@button-save' => '#btn_enviar',
        ];
    }
}
