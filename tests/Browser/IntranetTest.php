<?php

namespace Tests\Browser\Intranet;

use Illuminate\Support\Facades\DB;
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
    private function getIntranetRoutes()
    {
        $files = $this->getRoutesFromFile();
        $routes = $this->getRoutesFromDatabase();

        return array_unique(array_merge($routes, $files));
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

            foreach ($this->getIntranetRoutes() as $route) {
                $route = $this->parseRoute($route);
                $browser->visit($route);
                $title = $browser->driver->getTitle();

                $assert = Str::contains($title, 'i-Educar');

                $this->assertTrue(
                    $assert, "The file [{$route}] returned [{$title}] instead [i-Educar] in the title."
                );
            }
        });
    }

    private function parseRoute($route)
    {
        if (Str::contains($route, 'module'))  {
            return $route;
        }

        return '/intranet/' . $route;
    }

    private function getRoutesFromFile()
    {
        return file(__DIR__ . '/legacy/intranet.txt', FILE_IGNORE_NEW_LINES);
    }

    private function getRoutesFromDatabase()
    {
        $result = DB::select("SELECT arquivo FROM portal.menu_submenu WHERE arquivo <> ''");

        return array_map(function ($value) {
            return $value->arquivo;
        }, $result);
    }
}
