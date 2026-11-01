<?php

use Tests\TestCase;

uses(TestCase::class);

function arquivoIni(string $conteudo): string
{
    $arquivo = tempnam(sys_get_temp_dir(), 'ini');
    file_put_contents($arquivo, $conteudo);

    return $arquivo;
}

beforeEach(function () {
    $this->ini = arquivoIni(<<<'INI'
        [production]
        app.name = "i-Educar"
        app.database.dbname = ieducar
        app.database.port = 5432
        report.logo = logo.png

        [testing : production]
        app.database.dbname = testing
        cache.driver = array
        INI);
});

afterEach(function () {
    @unlink($this->ini);
});

test('lê a seção de produção com os namespaces separados por ponto', function () {
    $config = new CoreExt_Config_Ini($this->ini);

    expect($config->app->database->dbname)->toBe('ieducar')
        ->and($config->app->name)->toBe('i-Educar')
        ->and($config->naoExiste)->toBeNull()
        ->and(count($config))->toBe(2)
        ->and(array_keys($config->toArray()))->toBe(['app', 'report']);

    $keys = [];

    foreach ($config as $key => $value) {
        $keys[] = $key;
    }

    expect($keys)->toBe(['app', 'report']);
});

test('troca para a seção que herda da produção, mantendo o que ela não redefine', function () {
    $config = new CoreExt_Config_Ini($this->ini);
    $config->changeEnviroment('testing');

    expect($config->hasEnviromentSection('testing'))->toBeTrue()
        ->and($config->hasEnviromentSection('staging'))->toBeFalse()
        ->and($config->app->database->dbname)->toBe('testing')
        ->and($config->app->database->port)->toBe('5432')
        ->and($config->report->logo)->toBe('logo.png')
        ->and($config->cache->driver)->toBe('array');
});

test('as configurações do tenant viram chaves do legacy, ou as de produção quando ele não existe', function () {
    $config = new CoreExt_Config_Ini($this->ini);

    expect($config->getSettings('testing'))->toMatchArray(['legacy.app.database.dbname' => 'testing', 'legacy.report.logo' => 'logo.png'])
        ->and($config->getSettings('outro'))->toMatchArray(['legacy.app.database.dbname' => 'ieducar']);
});

test('recusa arquivo com erro de sintaxe, herança dupla e herança de seção que não existe', function (string $conteudo, string $message) {
    $arquivo = arquivoIni($conteudo);

    try {
        expect(fn () => new CoreExt_Config_Ini($arquivo))->toThrow(Exception::class, $message);
    } finally {
        unlink($arquivo);
    }
})->with([
    'sintaxe' => ["[production]\nnome = \"aberto", 'Arquivo ini com problemas de sintaxe.'],
    'herança dupla' => ["[a]\nx = 1\n[b]\ny = 2\n[c : a : b]\nz = 3", 'Não é possível herdar mais que uma seção.'],
    'seção inexistente' => ["[production : base]\nx = 1", 'Não foi possível estender production, seção base não existe'],
]);

test('o valor padrão só vale quando o primeiro é nulo, e não pode ser nulo', function () {
    $config = new CoreExt_Config(['a' => 1]);

    expect($config->get('valor', 'padrão'))->toBe('valor')
        ->and($config->get(null, 'padrão'))->toBe('padrão')
        ->and(fn () => $config->get(null))->toThrow(Exception::class, 'O segundo parâmetro deve conter algum valor não nulo.');
});
