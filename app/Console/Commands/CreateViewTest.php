<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateViewTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para criar os testes das views';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
            'BaseTestName' => 'TVA',
            'ProjectRoute' => '/asdaonda.php'
        ];

        $stub = file_get_contents(
            resource_path('stubs/base-test.php')
        );

        $stub = str_replace(
            array_keys($replaces), array_values($replaces), $stub
        );

        dd($stub);

        $filename = base_path('tests/Browser/Routes/' . $path . '/' . $class . '.php');

        file_put_contents($filename, $stub);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->getAllView();

        $this->createTestFile('', '', '');

        $this->info('Testes criados com sucesso!');
    }

    private function getAllView()
    {
        $temp_files = glob(__DIR__ .'/../../../ieducar/intranet/*.php');
    }
}
