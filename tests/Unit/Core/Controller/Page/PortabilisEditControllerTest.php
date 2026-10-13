<?php

use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

class TelaPortabilisDeTeste extends Portabilis_Controller_Page_EditController
{
    protected $_dataMapper = 'Educacenso_Model_IesDataMapper';

    public mixed $resultado = null;

    public bool $podeSalvar = true;

    public ?string $erro = null;

    protected function canSave()
    {
        return $this->podeSalvar;
    }

    protected function save()
    {
        if ($this->erro) {
            $this->messenger()->append($this->erro, 'error');
        }

        if ($this->resultado instanceof Throwable) {
            throw $this->resultado;
        }

        return $this->resultado;
    }

    public function chama(string $metodo, ...$argumentos)
    {
        return $this->$metodo(...$argumentos);
    }
}

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
    $_GET = [];
    Portabilis_View_Helper_Application::forgetLoadedAssets();
});

afterEach(function () {
    $_GET = [];
});

test('carrega os estilos e scripts do frontend, sem o de recursos no modo compatível', function () {
    expect((new TelaPortabilisDeTeste)->getAppendedOutput())
        ->toContain('/vendor/legacy/Portabilis/Assets/Stylesheets/Frontend.css')
        ->toContain('/vendor/legacy/Portabilis/Assets/Javascripts/Frontend/Resource.js');

    Portabilis_View_Helper_Application::forgetLoadedAssets();

    $compatible = new class extends TelaPortabilisDeTeste
    {
        protected $backwardCompatibility = true;
    };

    expect($compatible->getAppendedOutput())->not->toContain('Frontend/Resource.js');
});

test('salvar avisa o sucesso, respeita o erro informado e trata exceção e retorno inválido', function () {
    $tela = new TelaPortabilisDeTeste;

    expect($tela->chama('_save'))->toBeTrue()
        ->and($tela->chama('flashMessage'))->toContain('Alterações gravadas com sucesso.');

    $tela = new TelaPortabilisDeTeste;
    $tela->erro = 'Campo inválido';

    expect($tela->chama('_save'))->toBeFalse();

    foreach ([new RuntimeException('falhou'), 'talvez'] as $resultado) {
        $tela = new TelaPortabilisDeTeste;
        $tela->resultado = $resultado;

        expect($tela->chama('_save'))->toBeFalse()
            ->and($tela->chama('flashMessage'))->toContain('Erro ao gravar alterações, por favor, tente novamente.');
    }

    $tela = new TelaPortabilisDeTeste;
    $tela->podeSalvar = false;

    expect($tela->chama('_save'))->toBeFalse();
});

test('com erros na entidade não mostra a mensagem da gravação', function () {
    $tela = new TelaPortabilisDeTeste;
    $entity = (new Educacenso_Model_IesDataMapper)->createNewEntityInstance(['nome' => '']);
    $entity->isValid();
    $tela->setEntity($entity);

    expect($tela->chama('flashMessage'))->toBe('');
});

test('ao editar carrega o registro do id', function () {
    $_GET['id'] = 0;

    expect((new TelaPortabilisDeTeste)->chama('_save'))->toBeTrue();
});

test('as filhas definem o Gerar e o save, e a tela oferece validador, consulta e data mappers', function () {
    $tela = new class extends Portabilis_Controller_Page_EditController
    {
        protected $_dataMapper = 'Educacenso_Model_IesDataMapper';

        public function chama(string $metodo, ...$argumentos)
        {
            return $this->$metodo(...$argumentos);
        }
    };

    expect(fn () => $tela->Gerar())->toThrow(Exception::class, "The method 'Gerar' must be overwritten!")
        ->and(fn () => $tela->chama('save'))->toThrow(Exception::class, "The method 'save' must be overwritten!")
        ->and($tela->chama('canSave'))->toBeTrue()
        ->and($tela->chama('validator'))->toBeInstanceOf(Portabilis_Validator::class)
        ->and($tela->chama('mergeOptions', ['a' => 1], ['a' => 2, 'b' => 3]))->toBe(['a' => 1, 'b' => 3])
        ->and($tela->chama('fetchPreparedQuery', 'SELECT 1 AS um', ['return_only' => 'first-field']))->toBe(1)
        ->and($tela->chama('getDataMapperFor', 'educacenso', 'ies'))->toBeInstanceOf(Educacenso_Model_IesDataMapper::class);
});

test('carrega o estilo e o script da tela quando existem', function () {
    $root = sys_get_temp_dir() . '/raiz-portabilis';
    @mkdir("{$root}/vendor/legacy/Escola/Assets/Stylesheets", 0777, true);
    @mkdir("{$root}/vendor/legacy/Escola/Assets/Javascripts", 0777, true);
    touch("{$root}/vendor/legacy/Escola/Assets/Stylesheets/Edit.css");
    touch("{$root}/vendor/legacy/Escola/Assets/Javascripts/Edit.js");
    $_SERVER['DOCUMENT_ROOT'] = $root;

    $tela = new TelaPortabilisDeTeste;
    $tela->chama('loadResourceAssets', new class
    {
        public function getControllerName()
        {
            return 'escola';
        }

        public function getActionName()
        {
            return 'edit';
        }
    });

    expect($tela->getAppendedOutput())
        ->toContain('/vendor/legacy/Escola/Assets/Stylesheets/Edit.css')
        ->toContain('/vendor/legacy/Escola/Assets/Javascripts/Edit.js');
});
