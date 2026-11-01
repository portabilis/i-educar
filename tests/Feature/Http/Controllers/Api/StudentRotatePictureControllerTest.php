<?php

use Database\Factories\LegacyIndividualPictureFactory;
use Database\Factories\LegacyStudentFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());

    $this->student = LegacyStudentFactory::new()->create();
    $this->picture = LegacyIndividualPictureFactory::new()->create([
        'idpes' => $this->student->ref_idpes,
        'caminho' => 'https://arquivos.test/fotos/original.png',
    ]);

    // Uma imagem real de 4x2, para conferir a rotação pelas dimensões.
    $this->source = tempnam(sys_get_temp_dir(), 'foto');
    imagepng(imagecreatetruecolor(4, 2), $this->source);

    $this->rotate = fn (array $payload) => $this->postJson("/api/students/{$this->student->getKey()}/rotate-picture", $payload);
    $this->stored = config('legacy.app.database.dbname') . '/original.png';
});

afterEach(function () {
    @unlink($this->source);
});

test('gira a foto, grava no storage com o nome original e devolve a nova URL', function () {
    Storage::fake();

    ($this->rotate)(['url' => $this->source])
        ->assertOk()
        ->assertExactJson(['url' => Storage::url($this->stored)]);

    Storage::assertExists($this->stored);

    $rotated = Image::make(Storage::get($this->stored));

    expect($rotated->width())->toBe(2)
        ->and($rotated->height())->toBe(4)
        ->and($this->picture->refresh()->url)->toBe(Storage::url($this->stored));
});

test('aceita outro ângulo', function () {
    Storage::fake();

    ($this->rotate)(['url' => $this->source, 'angle' => 180])->assertOk();

    $rotated = Image::make(Storage::get($this->stored));

    expect($rotated->width())->toBe(4)
        ->and($rotated->height())->toBe(2);
});

test('avisa quando a imagem não pode ser lida', function () {
    Storage::fake();

    ($this->rotate)(['url' => '/nao/existe.png'])
        ->assertStatus(422)
        ->assertJson(['message' => 'Ocorreu um erro no servidor ao girar e enviar foto. Tente novamente.']);

    Storage::assertMissing($this->stored);

    expect($this->picture->refresh()->url)->toBe('https://arquivos.test/fotos/original.png');
});

test('avisa quando o storage não grava', function () {
    Storage::shouldReceive('put')->once()->andReturnFalse();

    ($this->rotate)(['url' => $this->source])
        ->assertStatus(422)
        ->assertJson(['message' => 'Ocorreu um erro no servidor ao girar e enviar foto. Tente novamente.']);

    expect($this->picture->refresh()->url)->toBe('https://arquivos.test/fotos/original.png');
});
