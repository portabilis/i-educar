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
     * @param string $route
     *
     * @return boolean
     */
    private function createTestFile(string $class, string $route): bool
    {
        $replaces = [
            'BaseTestName' => $class,
            'ProjectRoute' => $route
        ];

        $stub = file_get_contents(
            resource_path('stubs/base-test.php')
        );

        $stub = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $stub
        );

        $filename = base_path('tests/Pages/Intranet/' . $class . '.php');

        if (file_exists($filename)) {
            return false;
        }

        return file_put_contents($filename, $stub) !== false;
    }

    /**
     * @description Executa o comando.
     *
     * @return int
     */
    public function handle()
    {
        $allViews = $this->getAllViews();

        $allViewsFiltered = $this->excludeRouters($allViews);

        $routersInfo = $this->processRoutersInformation($allViewsFiltered);

        foreach ($routersInfo as $router) {
            $created = $this->createTestFile($router['className'], $router['route']);

            if ($created === false) {
                $this->warn(
                    "Teste {$router['className']} criado para a rota {$router['route']} já criado ou houve falha"
                );
                continue;
            }

            $this->info("Teste {$router['className']} criado para a rota {$router['route']}");
        }

        $this->info('Testes criados com sucesso!');
    }

    /**
     * @description Busca todas os arquivos da pasta intranet para gerar os testes de rota
     *
     * @return array
     */
    private function getAllViews(): array
    {
        $files = glob(__DIR__ . '/../../../ieducar/intranet/*.php');
        $filtered = $this->filterOnlineHttpView($files);

        return $this->cleanStringUrl($filtered);
    }

    /**
     * @param array $files
     *
     * @return array
     */
    private function filterOnlineHttpView(array $files)
    {
        $filtered = [];
        foreach ($files as $file) {
            if (preg_match('/^((?!xml).)*.php/', $file)) {
                $filtered[] = $file;
            }
        }

        return $filtered;
    }

    /**
     * @description  Pega o nome do arquivo/rota
     *
     * @param array $data
     *
     * @return array
     */
    private function cleanStringUrl(array $data)
    {
        $cleanRouter = [];
        foreach ($data as $item) {
            $array = explode('/', $item);
            $cleanRouter[] = end($array);
        }

        return $cleanRouter;
    }

    /**
     * @description Faz a montagem da informações para processamento
     *
     * @param array $allViews
     *
     * @return array
     */
    private function processRoutersInformation(array $allViews)
    {
        $routersInformation = [];
        foreach ($allViews as $view) {
            $className = $this->processClassName($view);

            $routersInformation[] = [
                'className' => $className . 'Test',
                'route' => '/intranet/' . $view,
            ];
        }

        return $routersInformation;
    }

    /**
     * @param $view
     *
     * @return string|string[]
     */
    private function processClassName($view)
    {
        $view = str_replace(['.php', '.inc', '.ajax'], '', $view);

        return str_replace(' ', '', ucwords(str_replace('_', ' ', $view)));
    }

    /**
     * @description Remove rota/arquivo da lista rotas que vão ser geradas
     *
     * @param array $allViews
     *
     * @return array
     */
    private function excludeRouters(array $allViews): array
    {
        $excludeRoutersList = $this->excludeRoutersList();
        foreach ($allViews as $key => $view) {
            if (in_array($view, $excludeRoutersList, true)) {
                unset($allViews[$key]);
            }
        }

        return array_values($allViews);
    }

    /**
     * Lista com rotas/arquivos para não processar
     *
     * @return string[]
     */
    private function excludeRoutersList(): array
    {
        return [
            'S3.php',
            's3_config.php',
            'upload.php',
            'file_check.php',
            'file_check_just_pdf.php',
        ];
    }
}
