<?php

use App\Models\File;
use App\Models\FileRelation;
use App\Services\FileService;
use App\Services\UrlPresigner;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class);

test('get files returns empty collection when relation is missing', function () {
    $files = (new FileService(new UrlPresigner))->getFiles(null);

    $this->assertInstanceOf(Collection::class, $files);
    $this->assertCount(0, $files);
});

test('get files by type returns empty collection when relation is missing', function () {
    $files = (new FileService(new UrlPresigner))->getFiles(null, FileRelation::TYPE_MEDICAL_REPORT);

    $this->assertInstanceOf(Collection::class, $files);
    $this->assertCount(0, $files);
});

test('envia o arquivo para a pasta da base e registra a relacao', function () {
    config(['filesystems.default' => 'local', 'legacy.app.database.dbname' => 'base_teste']);
    Storage::fake('local');
    $upload = UploadedFile::fake()->create('laudo.pdf', 10);

    (new FileService(new UrlPresigner))->createFile($upload, 'aluno', 7);

    $file = File::query()->where('original_name', 'laudo.pdf')->firstOrFail();

    Storage::disk('local')->assertExists($upload->hashName('base_teste'));
    $this->assertSame('pdf', $file->extension);
    $this->assertTrue(FileRelation::query()->where('file_id', $file->getKey())->where('relation_type', 'aluno')->where('relation_id', 7)->exists());
});

test('desfaz o registro quando a relacao nao pode ser gravada', function () {
    (new FileService(new UrlPresigner))->saveFile('https://arquivos.test/a.pdf', 1, 'sem-relacao.pdf', 'pdf', 'aluno', 'não é número');

    $this->assertFalse(File::query()->where('original_name', 'sem-relacao.pdf')->exists());
});

test('apaga os arquivos do disco local e do s3 e ignora os que falham', function () {
    config(['legacy.app.database.dbname' => 'base_teste']);
    Storage::fake('local');
    Storage::fake('s3');
    Storage::disk('local')->put('base_teste/local.pdf', 'x');
    Storage::disk('s3')->put('base_teste/remoto.pdf', 'x');

    $service = new FileService(new UrlPresigner);
    $service->saveFile('https://ieducar.test/storage/base_teste/local.pdf', 1, 'local.pdf', 'pdf', 'aluno', 1);
    $service->saveFile('https://bucket.test/base_teste/remoto.pdf', 1, 'remoto.pdf', 'pdf', 'aluno', 1);
    $local = File::query()->where('original_name', 'local.pdf')->value('id');
    $remote = File::query()->where('original_name', 'remoto.pdf')->value('id');

    config(['filesystems.default' => 'local']);
    $service->deleteFiles([$local]);
    config(['filesystems.default' => 's3']);
    $service->deleteFiles([$remote]);

    Storage::disk('local')->assertMissing('base_teste/local.pdf');
    Storage::disk('s3')->assertMissing('base_teste/remoto.pdf');
    $this->assertFalse(File::query()->whereKey([$local, $remote])->exists());
    $this->assertFalse(FileRelation::query()->whereIn('file_id', [$local, $remote])->exists());
});

test('mantem o registro quando o disco falha ao apagar', function () {
    $service = new FileService(new UrlPresigner);
    $service->saveFile('https://bucket.test/base_teste/preso.pdf', 1, 'preso.pdf', 'pdf', 'aluno', 1);
    $id = File::query()->where('original_name', 'preso.pdf')->value('id');
    Storage::shouldReceive('delete')->once()->andThrow(new RuntimeException('disco indisponível'));

    $service->deleteFiles([$id]);

    $this->assertTrue(File::query()->whereKey($id)->exists());
});
