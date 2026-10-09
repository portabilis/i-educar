<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

class RelatorioDoController extends Portabilis_Report_ReportCore
{
    public $conteudo = '';

    public function templateName()
    {
        return 'relatorio-do-controller';
    }

    public function requiredArgs()
    {
        $this->addRequiredArg('ano');
    }

    public function dumps($options = [])
    {
        return $this->conteudo;
    }
}

class RelatorioDeTesteController extends Portabilis_Controller_ReportCoreController
{
    public $formulario = false;

    public function __construct(public RelatorioDoController $relatorio)
    {
        parent::__construct();
    }

    public function form()
    {
        $this->formulario = true;
    }

    public function report()
    {
        return $this->relatorio;
    }

    public function beforeValidation()
    {
        $this->report->addArg('ano', $_POST['ano'] ?? null);
    }

    public function assets($dispatcher)
    {
        $this->loadResourceAssets($dispatcher);
    }
}

function controllerDeRelatorio(): RelatorioDeTesteController
{
    return new RelatorioDeTesteController(new RelatorioDoController);
}

function gerarRelatorio(RelatorioDeTesteController $controller): string
{
    ob_start();
    $controller->Gerar();

    return ob_get_clean();
}

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $_POST = [];
    $_GET = [];
});

afterEach(function () {
    $_POST = [];
    $_GET = [];
});

test('sem usuário logado volta para o login', function () {
    auth()->logout();

    expect(fn () => controllerDeRelatorio())->toThrow(HttpResponseException::class);
});

test('sem envio mostra o formulário com o botão de exibir', function () {
    $controller = controllerDeRelatorio();

    gerarRelatorio($controller);

    expect($controller->formulario)->toBeTrue()
        ->and($controller->nome_url_sucesso)->toBe('Exibir')
        ->and($controller->acao_enviar)->toBe('printReport()');
});

test('sem os argumentos obrigatórios avisa o que falta', function () {
    $_POST = ['ano' => ''];

    expect(gerarRelatorio(controllerDeRelatorio()))->toContain('Informe um valor no campo "ano"');
});

test('relatório vazio vira erro, com detalhes para o administrador e aviso ao rastreador de erros', function () {
    Mail::fake();
    config(['legacy.modules.error.track' => true, 'legacy.modules.error.tracker_name' => 'EMAIL', 'legacy.report.show_error_details' => false]);
    $_GET['print_report_with_get'] = 1;
    $_POST = ['ano' => 2030];

    expect(gerarRelatorio(controllerDeRelatorio()))->toContain('Detalhes: No report result to render!');

    $this->actingAs(LegacyUserFactory::new()->institutional()->create());
    config(['legacy.modules.error.track' => false]);

    expect(gerarRelatorio(controllerDeRelatorio()))->toContain('Visualização dos detalhes sobre o erro desativada.');
});

test('os cabeçalhos do PDF e os estilos e scripts da tela que existirem', function () {
    Portabilis_View_Helper_Application::forgetLoadedAssets();
    $controller = controllerDeRelatorio();
    $controller->headers('PDF');

    $root = sys_get_temp_dir() . '/raiz-relatorio';
    @mkdir("{$root}/vendor/legacy/Relatorio/Assets/Stylesheets", 0777, true);
    @mkdir("{$root}/vendor/legacy/Relatorio/Assets/Javascripts", 0777, true);
    touch("{$root}/vendor/legacy/Relatorio/Assets/Stylesheets/Boletim.css");
    touch("{$root}/vendor/legacy/Relatorio/Assets/Javascripts/Boletim.js");
    $_SERVER['DOCUMENT_ROOT'] = $root;

    $controller->assets(new class
    {
        public function getControllerName()
        {
            return 'relatorio';
        }

        public function getActionName()
        {
            return 'boletim';
        }
    });

    expect($controller->getAppendedOutput())
        ->toContain('/vendor/legacy/Relatorio/Assets/Stylesheets/Boletim.css')
        ->toContain('/vendor/legacy/Relatorio/Assets/Javascripts/Boletim.js');
});

test('a tela de relatório precisa definir o formulário e a preparação dos argumentos', function () {
    $controller = new class extends Portabilis_Controller_ReportCoreController {};

    expect(fn () => $controller->form())->toThrow(Exception::class, "The method 'form' must be overridden!")
        ->and(fn () => $controller->beforeValidation())->toThrow(Exception::class, "The method 'beforeValidation' must be overridden!");
});

class RelatorioSemModelo extends RelatorioDoController
{
    public function templateName()
    {
        throw new RuntimeException('Modelo do relatório não encontrado');
    }
}

test('quando o nome do modelo falha, o rastreador usa a classe do relatório e o erro ainda aparece', function () {
    Mail::fake();
    config(['legacy.modules.error.track' => true, 'legacy.modules.error.tracker_name' => 'EMAIL', 'legacy.report.show_error_details' => true]);
    $_GET['print_report_with_get'] = 1;
    $_POST = ['ano' => 2030];

    // O nome do modelo é lido de novo só para identificar o relatório no
    // rastreador; se ele falhar ali, o erro original continua sendo exibido.
    expect(gerarRelatorio(new RelatorioDeTesteController(new RelatorioSemModelo)))->toContain('Detalhes: No report result to render!');
});
