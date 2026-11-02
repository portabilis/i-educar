<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

class IesDataMapperDeTeste extends Educacenso_Model_IesDataMapper
{
    protected $_notPersistable = ['uf'];

    public function chama(string $metodo, ...$argumentos)
    {
        return $this->$metodo(...$argumentos);
    }
}

beforeEach(function () {
    $user = LegacyUserFactory::new()->admin()->create();

    foreach ([505050 => 'Faculdade A', 505051 => 'Faculdade B'] as $ies => $nome) {
        DB::table('modules.educacenso_ies')->insert(['ies_id' => $ies, 'nome' => $nome, 'dependencia_administrativa_id' => 1, 'tipo_instituicao_id' => 1, 'uf' => 'SC', 'user_id' => $user->getKey(), 'created_at' => now()]);
    }

    CoreExt_DataMapper::resetDefaultDbAdapter();
    $this->mapper = new IesDataMapperDeTeste;
});

test('sem adapter padrão o data mapper cria a conexão legada', function () {
    expect($this->mapper->getDbAdapter())->toBeInstanceOf(clsBanco::class);
});

test('busca por consulta preparada, com condições soltas, com sinal de comparação ou com parâmetro', function () {
    $nomes = fn (array $entities) => collect($entities)->pluck('nome')->sort()->values()->all();

    expect($nomes($this->mapper->findAllUsingPreparedQuery(['nome'], ['ies_id >= $1', 'ies_id <= $2'], [505050, 505051])))->toBe(['Faculdade A', 'Faculdade B'])
        ->and($nomes($this->mapper->findAllUsingPreparedQuery([], ['ies' => '$1'], [505051], [], false)))->toBe(['Faculdade B'])
        ->and($nomes($this->mapper->findAll(['nome'], ['ies' => '> 505050'])))->toContain('Faculdade B')->not->toContain('Faculdade A');
});

test('não monta UPDATE sem campos para gravar, descarta os não persistíveis e ignora atributos desconhecidos', function () {
    $orgao = new class extends Educacenso_Model_OrgaoRegionalDataMapper
    {
        protected $_notPersistable = ['id'];
    };
    $sql = (fn () => $this->_getUpdateStatment(new Educacenso_Model_OrgaoRegional(['sigla_uf' => 'SC', 'codigo' => '1'])))->call($orgao);

    $entity = $this->mapper->chama('_mapData', ['nome' => 'Faculdade C', 'coluna_inexistente' => 1], new Educacenso_Model_Ies);

    expect($sql)->toBe('')
        ->and($this->mapper->chama('_cleanData', ['nome' => 'Faculdade C', 'uf' => 'SC']))->toBe(['nome' => 'Faculdade C'])
        ->and($entity->nome)->toBe('Faculdade C');
});
