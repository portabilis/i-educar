<?php

use PHPUnit\Framework\Assert;

class StubCadastro extends clsCadastro
{
    public function __construct()
    {
        // intentionally do not call parent constructor to avoid Laravel facades (Auth) being used
        // but copy minimal expected state from superglobals
        $this->tipoacao = $_POST['tipoacao'] ?? '';
    }

    public $novoReturn = false;

    public $editarReturn = false;

    public $excluirReturn = false;

    public $inicializarReturn = '';

    public $formularCalled = false;

    public function Inicializar()
    {
        return $this->inicializarReturn;
    }

    public function Formular()
    {
        $this->formularCalled = true;
    }

    public function Novo()
    {
        return $this->novoReturn;
    }

    public function Editar()
    {
        return $this->editarReturn;
    }

    public function Excluir()
    {
        return $this->excluirReturn;
    }

    // Avoid calling Session facades in tests
    protected function setFlashMessage() {}

    // Helper para ler a mensagem privada
    public function getMensagem()
    {
        $rp = new ReflectionProperty(clsCadastro::class, '_mensagem');
        $rp->setAccessible(true);

        return $rp->getValue($this);
    }
}

beforeEach(function () {
    // limpar superglobais antes de cada teste
    $_POST = [];
    $_GET = [];
    $_FILES = [];
    // garantir que a chave 'excluir' exista para evitar warnings de índice indefinido
    $_GET['excluir'] = $_GET['excluir'] ?? null;
});

afterEach(function () {
    // limpar superglobais após cada teste
    $_POST = [];
    $_GET = [];
    $_FILES = [];
});

it('CT-01: GET sem tipoacao chama Inicializar e Formular', function () {
    $stub = new StubCadastro;
    $stub->inicializarReturn = '';

    $stub->Processar();

    expect($stub->formularCalled)->toBeTrue();
    expect($stub->tipoacao)->toBe('');
});

it('CT-02: Novo sucesso com script_sucesso gera script de fechamento', function () {
    $_POST['tipoacao'] = 'Novo';

    $stub = new StubCadastro;
    $stub->novoReturn = true;
    $stub->script_sucesso = 'alert(1);';

    $stub->Processar();

    expect($stub->sucesso)->toBeTrue();
    expect($stub->script)->toContain('window.close()');
});

it('CT-03: Novo falha sem erros e sem mensagem preexistente define CAD01', function () {
    $_POST['tipoacao'] = 'Novo';

    $stub = new StubCadastro;
    $stub->novoReturn = false;
    $stub->erros = null;
    // garantir que nao ha mensagem preexistente

    $stub->Processar();

    expect($stub->sucesso)->toBeFalse();
    expect($stub->getMensagem())->toBe('Não foi possível inserir a informação. [CAD01]');
});

it('CT-04: Novo sucesso com script_sucesso vazio nao cria script', function () {
    $_POST['tipoacao'] = 'Novo';

    $stub = new StubCadastro;
    $stub->novoReturn = true;
    $stub->script_sucesso = '';

    $stub->Processar();

    expect($stub->sucesso)->toBeTrue();
    // script pode ser null ou vazio; se existir, garantir que não contenha window.close()
    Assert::assertTrue(is_null($stub->script) || $stub->script === '' || strpos($stub->script, 'window.close()') === false);
});

it('CT-05: Novo falha mesmo com script_sucesso preenchido nao cria script', function () {
    $_POST['tipoacao'] = 'Novo';

    $stub = new StubCadastro;
    $stub->novoReturn = false;
    $stub->script_sucesso = 'alert(1);';

    $stub->Processar();

    expect($stub->sucesso)->toBeFalse();
    // script nao deve conter fechamento
    Assert::assertTrue(is_null($stub->script) || $stub->script === '' || strpos($stub->script, 'window.close()') === false);
});

it('CT-06: Novo falha com erros nao sobrescreve mensagem padrao', function () {
    $_POST['tipoacao'] = 'Novo';

    $stub = new StubCadastro;
    $stub->novoReturn = false;
    $stub->erros = ['algum erro'];
    // garantir que nao ha mensagem preexistente

    $stub->Processar();

    expect($stub->sucesso)->toBeFalse();
    // Espera que _mensagem nao tenha sido preenchida com CAD01
    expect($stub->getMensagem())->toBeNull();
});

it('CT-07: Novo falha e mensagem preexistente nao e sobrescrita', function () {
    $_POST['tipoacao'] = 'Novo';

    $stub = new StubCadastro;
    $stub->novoReturn = false;
    $stub->erros = null;
    // setar mensagem preexistente diretamente no atributo privado via Reflection para evitar facades
    $rp = new ReflectionProperty(clsCadastro::class, '_mensagem');
    $rp->setAccessible(true);
    $rp->setValue($stub, 'preexistente');

    $stub->Processar();

    expect($stub->sucesso)->toBeFalse();
    expect($stub->getMensagem())->toBe('preexistente');
});
