<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyMakeTestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:make:tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all tests for legacy code';

    /**
     * Return all routes.
     *
     * @return array
     */
    private function getRoutes()
    {
        $files = $this->getRoutesFromFile();
        $routes = $this->getRoutesFromDatabase();

        return array_unique(array_merge($routes, $files));
    }

    /**
     * Parse route and return real legacy route.
     *
     * @param string $route
     *
     * @return string
     */
    private function parseRoute($route)
    {
        if (Str::contains($route, 'module'))  {
            return $route;
        }

        return '/intranet/' . $route;
    }

    /**
     * Return all routes for intranet path.
     *
     * @return array
     */
    private function getRoutesFromFile()
    {
        return file(base_path('tests/Browser/legacy/intranet.txt'), FILE_IGNORE_NEW_LINES);
    }

    /**
     * Return all routes from database.
     *
     * @return array
     */
    private function getRoutesFromDatabase()
    {
        $result = DB::select("SELECT link FROM menus WHERE link <> ''");

        return array_map(function ($value) {
            return $value->arquivo;
        }, $result);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ($this->getRoutes() as $route) {
            $route = $this->parseRoute($route);

            $this->call('legacy:make:dusk', [
                'route' => $route
            ]);
        }
    }
}
