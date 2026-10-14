<?php

use App\Models\Country;
use Database\Factories\CountryFactory;
use iEducar\Legacy\InteractWithDatabase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

// Tela legada de países, guardando o redirecionamento em vez de enviá-lo.
function telaDePaises(): object
{
    return new class
    {
        use InteractWithDatabase {
            model as modeloPadrao;
            index as indicePadrao;
        }

        public $mensagem;

        public ?string $redirecionou = null;

        public function model()
        {
            return Country::class;
        }

        public function index()
        {
            return 'public_pais_lst.php';
        }

        public function simpleRedirect($url)
        {
            $this->redirecionou = $url;
        }
    };
}

test('sem modelo e sem página de listagem a tela avisa o que falta', function () {
    $screen = telaDePaises();

    expect(fn () => $screen->modeloPadrao())->toThrow(Exception::class, 'Missing model name.')
        ->and(fn () => $screen->indicePadrao())->toThrow(Exception::class, 'Missing index page name.');
});

test('cadastro e edição que falham ficam na tela com a mensagem', function () {
    $screen = telaDePaises();
    $country = CountryFactory::new()->create();
    Country::saving(fn () => throw new RuntimeException('banco indisponível'));

    expect($screen->create(['name' => 'Terra']))->toBeFalse()
        ->and($screen->mensagem)->toBe('Cadastro não realizado.<br>')
        ->and($screen->update($country->getKey(), ['name' => 'Outra']))->toBeFalse()
        ->and($screen->mensagem)->toBe('Edição não realizada.<br>')
        ->and($screen->redirecionou)->toBeNull();
});
