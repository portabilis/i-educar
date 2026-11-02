<?php

use App\Http\Controllers\Controller;
use Tests\TestCase;

uses(TestCase::class);

// O título da página vem da primeira propriedade de título que o controller tiver.
function tituloDaPagina(string $property): ?string
{
    $controller = new class extends Controller {};
    $controller->{$property} = "Título por {$property}";

    return Closure::bind(fn () => $this->getPageTitle(), $controller, Controller::class)();
}

test('o título da página aceita os nomes de propriedade do legado', function (string $property) {
    expect(tituloDaPagina($property))->toBe("Título por {$property}");
})->with(['title', '_title', 'titulo', '_titulo']);
