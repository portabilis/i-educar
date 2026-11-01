<?php

use Database\Factories\LegacySchoolFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

class ApiDeTeste extends ApiCoreController
{
    protected $_processoAp = 999;

    public function chama(string $metodo, ...$argumentos)
    {
        return $this->$metodo(...$argumentos);
    }

    public function Gerar() {}

    public function resposta(): array
    {
        return $this->response;
    }

    public function mensagens(): array
    {
        return array_column($this->messenger->getMsgs(), 'msg');
    }

    protected function defaultSearchOptions()
    {
        return ['namespace' => 'pmieducar', 'table' => 'escola', 'idAttr' => 'cod_escola', 'labelAttr' => 'cod_escola::varchar', 'selectFields' => [], 'sqlParams' => []];
    }
}

beforeEach(function () {
    $_GET = ['oper' => 'get', 'resource' => 'escola'];

    // A validação da área usa a lista de instituições do IedFinder, que outros
    // testes trocam por dublês no repositório estático.
    $storage = new ReflectionProperty(CoreExt_Entity::class, '_classStorage');
    $classes = $storage->getValue();
    unset($classes['clspmieducarinstituicao']);
    $storage->setValue(null, $classes);
});

afterEach(function () {
    $_GET = [];
});

test('sem permissão no processo não exclui, não cadastra e não habilita', function () {
    $this->actingAs(LegacyUserFactory::new()->institutional()->create());
    $school = LegacySchoolFactory::new()->create();
    $_GET['id'] = $school->getKey();
    $api = new ApiDeTeste;

    expect($api->chama('canChange'))->toBeFalse()
        ->and($api->chama('canDelete'))->toBeFalse()
        ->and($api->chama('canEnable'))->toBeFalse()
        ->and($api->chama('validatesAuthorizationToChange'))->toBeFalse()
        ->and($api->mensagens())->toContain("Usuário sem permissão para excluir 'escola'.", "Usuário sem permissão para cadastrar 'escola'.")
        ->and($api->chama('getNivelAcesso'))->toBe(App_Model_NivelTipoUsuario::INSTITUCIONAL);
});

test('o administrador exclui e habilita o recurso que existe', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $_GET['id'] = LegacySchoolFactory::new()->create()->getKey();
    $api = new ApiDeTeste;

    expect($api->chama('canDelete'))->toBeTrue()
        ->and($api->chama('canEnable'))->toBeTrue()
        ->and($api->chama('validatesAccessToSchool', 0))->toBeTrue();
});

test('o usuário de escola não acessa outra escola', function () {
    $mine = LegacySchoolFactory::new()->create();
    $this->actingAs(LegacyUserFactory::new()->school()->withSchool($mine)->create());
    $api = new ApiDeTeste;

    expect($api->chama('validatesAccessToSchool', $mine->getKey()))->toBeTrue()
        ->and($api->chama('validatesAccessToSchool', LegacySchoolFactory::new()->create()->getKey()))->toBeFalse()
        ->and($api->mensagens())->toContain("Usuário sem permissão para acessar 'escola' desta escola.");
});

test('avisa a operação não implementada, exige a lista de operações e o Gerar das filhas', function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $api = new ApiDeTeste;
    $api->chama('notImplementedOperationError');

    expect($api->mensagens())->toBe(["Operação 'get' não implementada para o recurso 'escola'"])
        ->and(fn () => $api->chama('getAvailableOperationsForResources'))->toThrow(CoreExt_Exception::class)
        ->and(fn () => (new class extends ApiCoreController {})->Gerar())->toThrow(CoreExt_Exception::class, "The method 'Gerar' must be overwritten!");
});

test('resposta que não vira JSON é trocada por uma mensagem de erro', function () {
    $api = new ApiDeTeste;
    $api->chama('appendResponse', 'infinito', INF);

    ob_start();
    $api->chama('prepareResponse');
    $output = ob_get_clean();

    expect(json_decode($output, true))->toBe(['msgs' => [['msg' => 'Erro inesperado no servidor. Por favor, tente novamente.', 'type' => 'error']]]);
});

test('confere a existência de vários registros de uma vez', function () {
    $schools = LegacySchoolFactory::new()->count(2)->create()->modelKeys();
    $api = new ApiDeTeste;

    expect($api->chama('validatesExistenceOf', 'escola', $schools))->toBeTrue()
        ->and($api->chama('validatesExistenceOf', 'escola', [$schools[0], 0]))->toBeFalse();
});

test('busca, cria, salva e recusa entidades pelo data mapper', function () {
    $user = LegacyUserFactory::new()->admin()->create();
    $this->actingAs($user);
    $api = new ApiDeTeste;
    $mapper = new Educacenso_Model_IesDataMapper;

    expect($api->chama('tryGetEntityOf', $mapper, null))->toBeNull()
        ->and($api->chama('getOrCreateEntityOf', $mapper, 'abc'))->toBeInstanceOf(Educacenso_Model_Ies::class);

    $api->chama('saveEntity', $mapper, $api->chama('createEntityOf', $mapper, ['nome' => '']));

    expect(implode(PHP_EOL, $api->mensagens()))->toContain('Erro ao salvar o recurso educacenso ies: ');

    $api->chama('saveEntity', $mapper, $api->chama('createEntityOf', $mapper, [
        'ies' => 909090, 'nome' => 'Faculdade de Teste', 'dependenciaAdministrativa' => 1, 'tipoInstituicao' => 1, 'uf' => 'SC', 'user' => $user->getKey(), 'created_at' => now()->toDateTimeString(),
    ]));

    $id = DB::table('modules.educacenso_ies')->where('ies_id', 909090)->value('id');

    expect($id)->not->toBeNull()
        ->and($api->chama('getOrCreateEntityOf', $mapper, $id)->nome)->toBe('Faculdade de Teste');
});

test('escapa texto para o banco e busca por número', function () {
    $school = LegacySchoolFactory::new()->create();
    $api = new ApiDeTeste;

    expect($api->chama('safeStringForDb', "D'Ávila"))->toBe("D\\'Ávila")
        ->and($api->chama('sqlsForNumericSearch'))->toContain("where cod_escola::varchar like $1||'%'")
        ->and($api->chama('loadResourcesBySearchQuery', (string) $school->getKey()))->toHaveKey($school->getKey());
});
