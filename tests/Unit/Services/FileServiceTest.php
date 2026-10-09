<?php

namespace Tests\Unit\Services;

use App\Models\File;
use App\Models\FileRelation;
use App\Services\FileService;
use App\Services\UrlPresigner;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_get_files_returns_empty_collection_when_relation_is_missing()
    {
        $files = (new FileService(new UrlPresigner))->getFiles(null);

        $this->assertInstanceOf(Collection::class, $files);
        $this->assertCount(0, $files);
    }

    public function test_get_files_by_type_returns_empty_collection_when_relation_is_missing()
    {
        $files = (new FileService(new UrlPresigner))->getFiles(null, FileRelation::TYPE_MEDICAL_REPORT);

        $this->assertInstanceOf(Collection::class, $files);
        $this->assertCount(0, $files);
    }

    public function test_envia_o_arquivo_para_a_pasta_da_base_e_registra_a_relacao()
    {
        config(['filesystems.default' => 'local', 'legacy.app.database.dbname' => 'base_teste']);
        Storage::fake('local');
        $upload = UploadedFile::fake()->create('laudo.pdf', 10);

        (new FileService(new UrlPresigner))->createFile($upload, 'aluno', 7);

        $file = File::query()->where('original_name', 'laudo.pdf')->firstOrFail();

        Storage::disk('local')->assertExists($upload->hashName('base_teste'));
        $this->assertSame('pdf', $file->extension);
        $this->assertTrue(FileRelation::query()->where('file_id', $file->getKey())->where('relation_type', 'aluno')->where('relation_id', 7)->exists());
    }

    public function test_desfaz_o_registro_quando_a_relacao_nao_pode_ser_gravada()
    {
        (new FileService(new UrlPresigner))->saveFile('https://arquivos.test/a.pdf', 1, 'sem-relacao.pdf', 'pdf', 'aluno', 'não é número');

        $this->assertFalse(File::query()->where('original_name', 'sem-relacao.pdf')->exists());
    }

    public function test_apaga_os_arquivos_do_disco_local_e_do_s3_e_ignora_os_que_falham()
    {
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
    }

    public function test_mantem_o_registro_quando_o_disco_falha_ao_apagar()
    {
        $service = new FileService(new UrlPresigner);
        $service->saveFile('https://bucket.test/base_teste/preso.pdf', 1, 'preso.pdf', 'pdf', 'aluno', 1);
        $id = File::query()->where('original_name', 'preso.pdf')->value('id');
        Storage::shouldReceive('delete')->once()->andThrow(new \RuntimeException('disco indisponível'));

        $service->deleteFiles([$id]);

        $this->assertTrue(File::query()->whereKey($id)->exists());
    }
}
