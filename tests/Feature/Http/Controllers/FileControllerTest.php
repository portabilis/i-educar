<?php

use App\Services\FileService;
use Database\Factories\FileFactory;
use Database\Factories\LegacyUserFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

beforeEach(function () {
    $this->actingAs(LegacyUserFactory::new()->admin()->create());
});

test('abrir um arquivo redireciona para o endereço dele', function () {
    $file = FileFactory::new()->create(['url' => 'https://exemplo.test/storage/laudo.pdf']);

    $this->get(route('files.show', $file))->assertRedirect('https://exemplo.test/storage/laudo.pdf');
});

test('o envio aceita só imagens, pdf e documentos de até 2MB', function () {
    $this->postJson(route('file-upload'), ['file' => UploadedFile::fake()->create('planilha.csv', 10, 'text/csv')])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['file' => 'Deve ser enviado um arquivo do tipo jpg, png, jpeg ou pdf.']);

    $this->postJson(route('file-upload'), ['file' => UploadedFile::fake()->create('grande.pdf', 3000, 'application/pdf')])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['file' => 'Não são permitidos arquivos com mais de 2MB.']);
});

test('o envio guarda o arquivo e devolve o endereço, o tamanho, a extensão e o nome', function () {
    Storage::fake(config('filesystems.default'));

    $response = $this->postJson(route('file-upload'), ['file' => UploadedFile::fake()->create('laudo.pdf', 10, 'application/pdf')])
        ->assertOk()
        ->assertJsonStructure(['file_url', 'file_size', 'file_extension', 'file_original_name'])
        ->assertJson(['file_extension' => 'pdf', 'file_original_name' => 'laudo.pdf']);

    expect($response->json('file_url'))->toContain('storage/');
});

test('um erro ao guardar o arquivo volta como erro', function () {
    $this->mock(FileService::class)->shouldReceive('upload')->andThrow(new RuntimeException('disco cheio'));

    $this->postJson(route('file-upload'), ['file' => UploadedFile::fake()->create('laudo.pdf', 10, 'application/pdf')])
        ->assertOk()
        ->assertExactJson(['error' => 'disco cheio']);
});
