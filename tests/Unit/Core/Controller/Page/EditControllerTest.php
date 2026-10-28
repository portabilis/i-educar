<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

class CadastroDeIesController extends Core_Controller_Page_EditController
{
    protected $_dataMapper = 'Educacenso_Model_IesDataMapper';

    protected $_processoAp = 999;

    protected $_saveOption = true;

    protected $_deleteOption = true;

    protected $_formMap = [
        'nome' => ['label' => 'Nome da IES', 'help' => '', 'entity' => 'nome'],
        'uf' => ['label' => 'UF', 'help' => '', 'entity' => 'estado'],
        'ies' => ['label' => 'Código', 'help' => '', 'entity' => 'ies'],
        'dependenciaAdministrativa' => ['label' => 'Dependência', 'help' => '', 'entity' => 'dependenciaAdministrativa'],
        'tipoInstituicao' => ['label' => 'Tipo', 'help' => '', 'entity' => 'tipoInstituicao'],
        'user' => ['label' => 'Usuário', 'help' => '', 'entity' => 'user'],
        'created_at' => ['label' => 'Criação', 'help' => '', 'entity' => 'created_at'],
    ];

    public function __construct(array $options = [])
    {
        parent::__construct();
        $this->setOptions($options + ['new_success' => 'view', 'edit_success' => 'view', 'delete_success' => 'index']);
        $this->setDispatcher(new class extends CoreExt_Controller_Dispatcher_Standard
        {
            public function getControllerName()
            {
                return 'Ies';
            }
        });
    }

    public function chama(string $metodo, ...$argumentos)
    {
        return $this->$metodo(...$argumentos);
    }
}

function iesDoCadastro(array $attributes = []): int
{
    return DB::table('modules.educacenso_ies')->insertGetId($attributes + [
        'ies_id' => 808080, 'nome' => 'Faculdade Antiga', 'dependencia_administrativa_id' => 1, 'tipo_instituicao_id' => 1, 'uf' => 'SC', 'user_id' => auth()->id(), 'created_at' => now(),
    ]);
}

// Os redirecionamentos legados leem posições que podem não existir; as telas
// rodam sem avisos do PHP.
function redirecionamentoDoCadastro(Closure $acao): ?string
{
    $level = error_reporting(0);

    try {
        $acao();
    } catch (HttpResponseException $exception) {
        return $exception->getResponse()->getTargetUrl();
    } finally {
        error_reporting($level);
    }

    return null;
}

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $_GET = [];
    $_POST = [];
});

afterEach(function () {
    $_GET = [];
    $_POST = [];
});

test('o mapa do formulário dá o nome da entidade e o rótulo pelo campo ou pela entidade', function () {
    $controller = new CadastroDeIesController;

    expect($controller->chama('_getEntity', 'uf'))->toBe('estado')
        ->and($controller->chama('_getEntityLabel', 'estado'))->toBe('UF')
        ->and($controller->chama('_getEntityLabel', 'nome'))->toBe('Nome da IES')
        ->and($controller->getValidators())->toBe([]);
});

test('sem id começa um registro novo, que salva e volta para a tela de visualização', function () {
    $controller = new CadastroDeIesController(['new_success_params' => ['aba' => 'dados']]);

    expect($controller->Inicializar())->toBe('Novo');

    $_POST = ['nome' => 'Faculdade Nova', 'ies' => 707070, 'dependenciaAdministrativa' => 1, 'tipoInstituicao' => 1, 'uf' => 'SC', 'user' => auth()->id(), 'created_at' => now()->toDateTimeString()];

    expect(redirecionamentoDoCadastro(fn () => $controller->Novo()))->toEndWith('Ies/view?aba=dados')
        ->and(DB::table('modules.educacenso_ies')->where('ies_id', 707070)->value('nome'))->toBe('Faculdade Nova');

    // Sem os campos que a tabela exige, o salvamento falha e a tela não redireciona.
    // A falha aborta a transação do teste, então fica por último.
    $_POST = ['nome' => 'Faculdade Sem Dados', 'ignorado' => 'x'];

    expect($controller->Novo())->toBeFalse()
        ->and(session('legacy'))->toBe('Erro no preenchimento do formulário. ');
});

test('com id edita o registro, confere as permissões e volta para ele', function () {
    $id = iesDoCadastro();
    $_GET['id'] = $id;
    $controller = new CadastroDeIesController;

    expect($controller->Inicializar())->toBe('Editar')
        ->and($controller->fexcluir)->toBeTrue();

    $_POST = ['nome' => 'Faculdade Renomeada'];

    expect(redirecionamentoDoCadastro(fn () => $controller->Editar()))->toEndWith("Ies/view?id={$id}")
        ->and(DB::table('modules.educacenso_ies')->where('id', $id)->value('nome'))->toBe('Faculdade Renomeada');

    $controller = new CadastroDeIesController(['edit_success_params' => ['aba' => 'dados']]);

    expect(redirecionamentoDoCadastro(fn () => $controller->Editar()))->toEndWith('Ies/view?aba=dados');

    $_POST = ['nome' => null];

    expect((new CadastroDeIesController)->Editar())->toBeFalse();
});

test('a exclusão apaga e volta para a lista, com os parâmetros configurados', function () {
    $_GET['id'] = iesDoCadastro();
    $controller = new CadastroDeIesController(['delete_success_params' => ['busca' => 'S']]);
    $controller->tipoacao = 'Excluir';

    expect($controller->Formular())->toBe('Editar')
        ->and(redirecionamentoDoCadastro(fn () => $controller->Excluir()))->toEndWith('Ies/index?busca=S')
        ->and(DB::table('modules.educacenso_ies')->where('id', $_GET['id'])->exists())->toBeFalse();

    unset($_GET['id']);

    expect((new CadastroDeIesController)->Excluir())->toBeFalse();
});

test('o id que não existe não abre a edição, e a tela só de leitura não salva', function () {
    $_GET['id'] = 0;
    $controller = new CadastroDeIesController;

    expect($controller->chama('_initEditar'))->toBeFalse()
        ->and(session('legacy'))->toBeInstanceOf(Exception::class);

    $readOnly = new class extends CadastroDeIesController
    {
        protected $_onlyRead = true;
    };

    expect($readOnly->chama('_save'))->toBeFalse();
});
