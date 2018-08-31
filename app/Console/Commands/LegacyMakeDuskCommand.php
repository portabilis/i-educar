<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class LegacyMakeDuskCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:make:dusk {route}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Dusk Test for legacy code';

    /**
     * Return the class name.
     *
     * @param string $path
     * @param string $route
     *
     * @return string
     */
    private function getClassName($path, $route)
    {
        $class = trim($route, '/');

        $class = Str::before($class, '?');
        $class = Str::before($class, '.php');
        $class = Str::substr($class, Str::length($path) + 1);
        $class = Str::studly($class) . 'RouteTest';
        $class = str_replace('/', '', $class);

        return $class;
    }

    /**
     * Return the class path.
     *
     * @param string $route
     *
     * @return string
     */
    private function getClassPath($route)
    {
        $path = trim($route, '/');

        if (Str::startsWith($path, 'intranet')) {
            $path = 'Intranet';
        } elseif (Str::startsWith($path, 'modules')) {
            $path = 'Modules';
        } elseif (Str::startsWith($path, 'module')) {
            $path = 'Module';
        }

        return $path;
    }

    /**
     * Create the test file.
     *
     * @param string $class
     * @param string $path
     * @param string $route
     *
     * @return void
     */
    private function createTestFile($class, $path, $route)
    {
        $replaces = [
            'LegacyDuskClass' => $class,
            'LegacyDuskRoute' => $route,
            'LegacyDuskPath' => $path,
        ];

        $stub = file_get_contents(
            resource_path('stubs/legacy-dusk.php')
        );

        $stub = str_replace(
            array_keys($replaces), array_values($replaces), $stub
        );

        $filename = base_path('tests/Browser/Routes/' . $path . '/' . $class . '.php');

        file_put_contents($filename, $stub);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $route = $this->argument('route');
        $path = $this->getClassPath($route);
        $class = $this->getClassName($path, $route);

        $this->createTestFile($class, $path, $route);
    }
}
