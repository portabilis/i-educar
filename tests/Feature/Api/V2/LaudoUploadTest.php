<?php

use App\Models\File;
use App\Models\FileRelation;
use App\Models\LegacyStudent;
use App\Services\FileService;
use Database\Factories\LegacyStudentFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Api\V2\ConfiguresInstitutionToken;
use Tests\TestCase;

uses(DatabaseTransactions::class, TestCase::class, ConfiguresInstitutionToken::class);

beforeEach(function () {
    Storage::fake();
});

test('rejects request without token', function () {
    $this->institutionWithToken();

    $this->postJson('/api/v2/aluno-laudo-upload', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('rejects request with invalid token', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'wrong-token'])
        ->postJson('/api/v2/aluno-laudo-upload', [])
        ->assertStatus(401)
        ->assertExactJson(['message' => 'Unauthorized']);
});

test('requires student', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/aluno-laudo-upload', [])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['aluno_id']);
});

test('rejects student that does not exist', function () {
    $this->institutionWithToken();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/aluno-laudo-upload', [
            'aluno_id' => 99999999,
            'file' => UploadedFile::fake()->create('laudo.pdf', 100, 'application/pdf'),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['aluno_id']);
});

test('rejects file with invalid type', function () {
    $this->institutionWithToken();
    $student = LegacyStudentFactory::new()->create();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/aluno-laudo-upload', [
            'aluno_id' => $student->getKey(),
            'file' => UploadedFile::fake()->create('laudo.txt', 100, 'text/plain'),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'file' => 'Deve ser enviado um arquivo do tipo jpg, png, jpeg ou pdf.',
        ]);
});

test('rejects file larger than 2MB', function () {
    $this->institutionWithToken();
    $student = LegacyStudentFactory::new()->create();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/aluno-laudo-upload', [
            'aluno_id' => $student->getKey(),
            'file' => UploadedFile::fake()->create('laudo.pdf', 2001, 'application/pdf'),
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'file' => 'Não são permitidos arquivos com mais de 2MB.',
        ]);
});

test('rejects request without file', function () {
    $this->institutionWithToken();
    $student = LegacyStudentFactory::new()->create();

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/aluno-laudo-upload', [
            'aluno_id' => $student->getKey(),
        ])
        ->assertStatus(422)
        ->assertExactJson(['message' => 'O arquivo enviado é inválido.']);

    $this->assertDatabaseCount(FileRelation::class, 0);
});

test('stores medical report for the student', function () {
    $this->institutionWithToken();
    $student = LegacyStudentFactory::new()->create();
    $file = UploadedFile::fake()->create('laudo.pdf', 100, 'application/pdf');

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/aluno-laudo-upload', [
            'aluno_id' => $student->getKey(),
            'file' => $file,
        ])
        ->assertOk()
        ->assertExactJson(['message' => 'Laudo salvo com sucesso.']);

    Storage::assertExists($file->hashName(config('legacy.app.database.dbname')));

    $storedFile = File::query()->where('original_name', 'laudo.pdf')->firstOrFail();

    expect($storedFile)
        ->extension->toBe('pdf')
        ->size->toEqual($file->getSize());

    $this->assertDatabaseHas(FileRelation::class, [
        'relation_type' => LegacyStudent::class,
        'relation_id' => $student->getKey(),
        'file_id' => $storedFile->getKey(),
        'type' => FileRelation::TYPE_MEDICAL_REPORT,
    ]);
});

test('returns error when upload fails', function () {
    $this->institutionWithToken();
    $student = LegacyStudentFactory::new()->create();

    $this->mock(FileService::class)
        ->shouldReceive('upload')
        ->once()
        ->andThrow(new RuntimeException('Storage unavailable'));

    $this->withHeaders(['token' => 'valid-token'])
        ->postJson('/api/v2/aluno-laudo-upload', [
            'aluno_id' => $student->getKey(),
            'file' => UploadedFile::fake()->create('laudo.pdf', 100, 'application/pdf'),
        ])
        ->assertStatus(500)
        ->assertExactJson(['message' => 'Não foi possível salvar o laudo para o aluno informado.']);

    $this->assertDatabaseCount(FileRelation::class, 0);
});
