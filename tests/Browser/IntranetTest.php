<?php

namespace Tests\Browser\Intranet;

use Illuminate\Support\Str;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class IntranetTest extends DuskTestCase
{
    /**
     * Return all routes for intranet
     *
     * @return array
     */
    private function getIntranetFiles()
    {
        return file(__DIR__ . '/legacy/intranet.txt', FILE_IGNORE_NEW_LINES);
    }

    /**
     * Test all intranet routes.
     *
     * @return void
     *
     * @throws \Throwable
     */
    public function testIntranetRoutes()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginLegacy();

            foreach ($this->getIntranetFiles() as $file) {
                $browser->visit('/intranet/' . $file);

                $title = $browser->driver->getTitle();

                $assert = Str::contains($title, 'i-Educar');

                $this->assertTrue(
                    $assert, "The file [/intranet/{$file}] returned [{$title}] instead [i-Educar] in the title."
                );
            }
        });
    }
}
