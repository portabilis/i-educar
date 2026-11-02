<?php

use App\Services\BackupUrlPresigner;
use App\Services\UrlPresigner;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config(['filesystems.disks.s3.bucket' => 'arquivos', 'filesystems.disks.s3.key' => 'chave', 'filesystems.disks.s3.secret' => 'segredo']);
    Storage::fake('s3');
    Storage::disk('s3')->buildTemporaryUrlsUsing(
        fn ($path, $expiration, $options) => 'https://assinado.test/' . $path . '?' . http_build_query($options)
    );
});

test('no disco local devolve a própria URL e no s3 assina a chave do arquivo', function () {
    config(['filesystems.default' => 'local']);

    expect((new UrlPresigner)->getPresignedUrl('https://ieducar.test/storage/a.pdf'))->toBe('https://ieducar.test/storage/a.pdf');

    config(['filesystems.default' => 's3']);

    expect((new UrlPresigner)->getPresignedUrl('https://s3.test/arquivos/base/laudo%2520m%C3%A9dico.pdf', 'laudo médico.pdf'))
        ->toBe('https://assinado.test/base/laudo médico.pdf?' . http_build_query(['ResponseContentDisposition' => 'inline; filename="laudo%20m%C3%A9dico.pdf"']))
        ->and((new UrlPresigner)->getPresignedUrl('https://arquivos.s3.test/base/a.pdf'))->toBe('https://assinado.test/base/a.pdf?')
        ->and((new UrlPresigner)->getPresignedUrl('https://s3.test/'))->toBe('');
});

test('recusa disco sem assinatura implementada', function () {
    config(['filesystems.default' => 'ftp', 'filesystems.cloud' => 'ftp']);

    expect(fn () => (new UrlPresigner)->getPresignedUrl('x'))->toThrow(Exception::class, 'not implemented for cloud filesystem: ftp')
        ->and(fn () => (new BackupUrlPresigner)->getPresignedUrl('x'))->toThrow(Exception::class, 'not implemented for cloud filesystem: ftp');
});

test('o backup na nuvem s3 é assinado pelo assinador de backups', function () {
    config(['filesystems.cloud' => 's3']);

    expect((new BackupUrlPresigner)->getPresignedUrl('https://s3-sa-east-1.amazonaws.com/backups/escola/backup.backup'))
        ->toStartWith('https://backups.s3.sa-east-1.amazonaws.com/escola/backup.backup?');
});
