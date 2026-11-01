<?php

use Database\Factories\LegacyRegistrationFactory;
use Database\Factories\LegacyUserFactory;
use Database\Factories\LogUnificationFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// As telas legadas rodam sem avisos do PHP; o include do arquivo ausente só avisa.
function semAvisosNoLegado(Closure $callback): mixed
{
    $level = error_reporting(0);

    try {
        return $callback();
    } finally {
        error_reporting($level);
    }
}

function arquivoPhpTemporario(string $codigo): string
{
    $arquivo = tempnam(sys_get_temp_dir(), 'php') . '.php';
    file_put_contents($arquivo, "<?php\n{$codigo}\n");

    return $arquivo;
}

test('os enums do App devolvem sempre a mesma instância', function (string $enum) {
    expect($enum::getInstance())->toBe($enum::getInstance())->toBeInstanceOf($enum);
})->with(['ColunaMovimentoGeral', 'App_Model_NivelAcesso', 'App_Model_NivelTipoUsuario', 'App_Model_SimNao', 'App_Model_TipoDiaMotivo']);

test('muda a situação da matrícula pela classe legada', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);

    $storage = new ReflectionProperty(CoreExt_Entity::class, '_classStorage');
    $classes = $storage->getValue();
    unset($classes['clspmieducarmatricula']);
    $storage->setValue(null, $classes);

    App_Model_Matricula::setNovaSituacao($registration->getKey(), App_Model_MatriculaSituacao::REPROVADO);

    expect($registration->fresh()->aprovado)->toBe(App_Model_MatriculaSituacao::REPROVADO);
});

test('a unificação apaga os vínculos duplicados, guarda os dados antigos e recusa parâmetros inválidos', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $log = LogUnificationFactory::new()->create();
    DB::unprepared('create table public.cobertura_unificacao (pessoa_id int)');
    DB::table('public.cobertura_unificacao')->insert([['pessoa_id' => 11], ['pessoa_id' => 12]]);

    $unificacao = fn (array $chaves, $unificador = 10, $duplicados = [11, 12], $pessoa = null) => new class($unificador, $duplicados, $pessoa ?? $user->getKey(), new clsBanco, $log->getKey(), $chaves) extends App_Unificacao_Base
    {
        public function __construct($unificador, $duplicados, $pessoa, $db, $log, array $chaves)
        {
            parent::__construct($unificador, $duplicados, $pessoa, $db, $log);
            $this->chavesDeletarDuplicados = $chaves;
        }
    };

    $unificacao([['tabela' => 'public.cobertura_unificacao', 'coluna' => 'pessoa_id']])->unifica();

    expect(DB::table('public.cobertura_unificacao')->count())->toBe(0)
        ->and(DB::table('log_unification_old_data')->where('unification_id', $log->getKey())->count())->toBe(2)
        ->and(fn () => $unificacao([['tabela' => 'public.tabela_que_nao_existe', 'coluna' => 'pessoa_id']])->unifica())->toThrow(Exception::class, 'Erro ao deletar registros duplicados.')
        ->and(fn () => $unificacao([], 'x')->unifica())->toThrow(CoreExt_Exception::class, 'Parâmetro 1 deve ser o código unificador')
        ->and(fn () => $unificacao([], 10, [])->unifica())->toThrow(CoreExt_Exception::class, 'Parâmetro 2 deve ser um array de códigos duplicados')
        ->and(fn () => $unificacao([], 10, [11], 'x')->unifica())->toThrow(CoreExt_Exception::class, 'Parâmetro 3 deve ser um inteiro')
        ->and(fn () => App_Unificacao_Aluno::unifica('x', [1], 1, new clsBanco, 1))->toThrow(CoreExt_Exception::class, 'codAlunoPrincipal')
        ->and(fn () => App_Unificacao_Aluno::unifica(1, [], 1, new clsBanco, 1))->toThrow(CoreExt_Exception::class, 'codAlunos')
        ->and(fn () => App_Unificacao_Aluno::unifica(1, [2], 'x', new clsBanco, 1))->toThrow(CoreExt_Exception::class, 'codPessoa');
});

test('a referência a um enum carrega a classe do arquivo e devolve nulo para valor fora dele', function () {
    $enum = 'CoberturaEnumDeTeste' . uniqid();
    $arquivo = arquivoPhpTemporario("class {$enum} extends CoreExt_Enum { protected \$_data = [1 => 'Um']; public static function getInstance() { return self::_getInstance(__CLASS__); } }");
    $entity = new class extends CoreExt_Entity
    {
        protected $_data = ['tipo' => null];

        public function getDefaultValidatorCollection()
        {
            return [];
        }
    };

    $entity->setReference('tipo', ['value' => 99, 'class' => $enum, 'file' => $arquivo]);

    expect($entity->tipo)->toBeNull();

    $classe = 'CoberturaClasseDeStorage' . uniqid();
    $outro = arquivoPhpTemporario("class {$classe} {}");

    expect(CoreExt_Entity::addClassToStorage($classe, null, $outro))->toBeInstanceOf($classe);
});

test('as mensagens de validação escolhem singular ou plural pelo valor', function () {
    $validador = new class extends CoreExt_Validate_Abstract
    {
        protected function _getDefaultOptions()
        {
            return ['mensagem' => ['singular' => 'O valor @value é inválido.', 'plural' => 'Os valores @value são inválidos.']];
        }

        protected function _validate($value)
        {
            return true;
        }

        public function mensagem($value)
        {
            return $this->_getErrorMessage('mensagem', ['@value' => $value]);
        }
    };

    $validador->isValid('a');

    expect($validador->mensagem(['a', 'b']))->toBe('Os valores a, b são inválidos.')
        ->and($validador->mensagem(['a']))->toBe('O valor a é inválido.')
        ->and($validador->getValue())->toBe('a')
        ->and(fn () => $validador->mensagem(3))->toThrow(TypeError::class)
        ->and(fn () => $validador->mensagem('a'))->toThrow(TypeError::class)
        ->and((new CoreExt_Validate_Numeric)->isValid('1,5'))->toBeTrue();
});

test('a página expõe os erros da entidade, aceita a URL de cancelar em texto e se despacha', function () {
    $entity = new Educacenso_Model_Ies(['nome' => '']);
    $entity->isValid();
    $page = new class extends Core_Controller_Page_Abstract {};
    $page->setEntity($entity);
    $page->setOptions(['url_cancelar' => 'educar_index.php']);
    $page->configurarBotoes();

    expect($page->hasError('nome'))->toBeTrue()
        ->and($page->getError('nome'))->not->toBeEmpty()
        ->and($page->getErrors())->toHaveKey('nome')
        ->and($page->url_cancelar)->toBe('educar_index.php')
        ->and($page->dispatch())->toBe($page);
});

test('o armazenamento de sessão padrão, a sessão e a mensageria', function () {
    $storage = new CoreExt_Session_Storage_Default;
    $storage->start();
    $storage->regenerate();
    $storage->destroy();
    $storage->shutdown();

    $session = new CoreExt_Session(['sessionstorage' => $storage]);
    $session->chave = 'valor';

    $origem = new Portabilis_Messenger;
    $origem->append('Atenção', 'notice');
    $destino = new Portabilis_Messenger;
    $destino->merge($origem);

    expect(isset($session->chave))->toBeTrue()
        ->and(CoreExt_Session_Storage_Default::getSessionId())->toBeNull()
        ->and(CoreExt_Session_Storage_Default::isStarted())->toBeFalse()
        ->and($destino->getMsgs())->toBe([['msg' => 'Atenção', 'type' => 'notice']]);
});

test('controladores, despachante, visão e utilitários legados nas bordas', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $consulta = new class extends ConsultaBaseController
    {
        public function chama(string $metodo, ...$argumentos)
        {
            return $this->$metodo(...$argumentos);
        }
    };
    $_GET = ['oper' => 'get', 'resource' => 'outra'];

    try {
        $consulta->Gerar();
    } finally {
        $_GET = [];
    }

    $front = CoreExt_Controller_Front::getInstance();
    $estrategia = fn (int $tipo) => (function () use ($tipo) {
        $this->setOptions(['controller_type' => $tipo]);

        return $this->_getControllerStrategy();
    })->call($front);

    $visao = new class extends CoreExt_View_Abstract {};
    $visao->setContents('conteúdo');

    expect($consulta->chama('canGetAlunos'))->toBeTrue()
        ->and($consulta->chama('getData'))->toBe([])
        ->and($consulta->chama('getMethodName', 'alunos_ativos'))->toBe('getDataAlunosAtivos')
        ->and($estrategia(1))->toBeInstanceOf(CoreExt_Controller_Dispatcher_Strategy_FrontStrategy::class)
        ->and(fn () => $estrategia(9))->toThrow(Exception::class, 'Parâmetro de controlador incorreto')
        ->and((string) $visao)->toBe('conteúdo')
        ->and(fn () => semAvisosNoLegado(fn () => (new Portabilis_DataMapper_Utils)->getDataMapperFor('pacote', 'inexistente')))->toThrow(CoreExt_Exception::class)
        ->and(fn () => Portabilis_Utils_Float::limitDecimal('abc'))->toThrow(Exception::class, 'Value must be numeric!')
        ->and(fn () => Portabilis_Utils_User::hasNivelAcesso('QUALQUER'))->toThrow(CoreExt_Exception::class)
        ->and(Portabilis_Utils_Validation::validatesCpf('123'))->toBeFalse()
        ->and(SafeJson::decode('{"a":1}', true))->toBe(['a' => 1]);

    $estrategia(2);
});

test('telas de visualização e listagem, redirecionamento com código desconhecido e caminho vazio do despachante', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $registration = LegacyRegistrationFactory::new()->create(['aprovado' => 3]);
    $storage = new ReflectionProperty(CoreExt_Entity::class, '_classStorage');
    $classes = $storage->getValue();
    unset($classes['clspmieducarmatricula']);
    $storage->setValue(null, $classes);

    App_Model_Matricula::atualizaMatricula($registration->getKey(), auth()->id(), false);

    $visualizacao = new class extends Core_Controller_Page_ViewController
    {
        protected $_dataMapper = 'Educacenso_Model_IesDataMapper';

        protected $_titulo = 'IES';

        public function getEntry()
        {
            throw new Exception('Registro não encontrado');
        }
    };

    $listagem = new class extends Core_Controller_Page_ListController
    {
        protected $_dataMapper = 'Educacenso_Model_IesDataMapper';
    };

    $consulta = new class extends ConsultaBaseController
    {
        protected function canGetAlunos()
        {
            return false;
        }

        public function alunos()
        {
            return $this->getAlunos();
        }
    };

    $_SERVER['REQUEST_URI'] = '';
    $despachante = new CoreExt_Controller_Dispatcher_Standard;

    try {
        expect(semAvisosNoLegado(fn () => $visualizacao->Gerar()))->toBeFalse()
            ->and($registration->fresh()->aprovado)->toBe(App_Model_MatriculaSituacao::REPROVADO)
            ->and($listagem->getEntries())->toBeArray()
            ->and($consulta->alunos())->toBeNull()
            ->and($despachante->getControllerName())->toBe($despachante->getOption('controller_default_name'))
            ->and(fn () => $consulta->simpleRedirect('/destino', 999))->toThrow(HttpResponseException::class);
    } finally {
        unset($_SERVER['REQUEST_URI']);
    }
});

test('a listagem Portabilis carrega o estilo e o script da tela quando existem', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $root = sys_get_temp_dir() . '/raiz-listagem';
    @mkdir("{$root}/vendor/legacy/Escola/Assets/Stylesheets", 0777, true);
    @mkdir("{$root}/vendor/legacy/Escola/Assets/Javascripts", 0777, true);
    touch("{$root}/vendor/legacy/Escola/Assets/Stylesheets/Index.css");
    touch("{$root}/vendor/legacy/Escola/Assets/Javascripts/Index.js");
    $_SERVER['DOCUMENT_ROOT'] = $root;
    Portabilis_View_Helper_Application::forgetLoadedAssets();

    $listagem = new class extends Portabilis_Controller_Page_ListController
    {
        protected $_dataMapper = 'Educacenso_Model_IesDataMapper';

        public function assets($dispatcher)
        {
            $this->loadResourceAssets($dispatcher);
        }
    };
    $listagem->assets(new class
    {
        public function getControllerName()
        {
            return 'escola';
        }

        public function getActionName()
        {
            return 'index';
        }
    });

    expect($listagem->getAppendedOutput())
        ->toContain('/vendor/legacy/Escola/Assets/Stylesheets/Index.css')
        ->toContain('/vendor/legacy/Escola/Assets/Javascripts/Index.js');
});
