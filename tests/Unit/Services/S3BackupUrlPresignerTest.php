<?php

use App\Services\S3BackupUrlPresigner;
use Tests\TestCase;

uses(TestCase::class);

test('assina por dez minutos o backup no bucket e na região da URL', function () {
    config(['filesystems.disks.s3.key' => 'chave', 'filesystems.disks.s3.secret' => 'segredo']);

    $url = (new S3BackupUrlPresigner)->getPresignedUrl('https://s3-sa-east-1.amazonaws.com/backups/escola/backup%2520de%2520hoje.backup');

    expect($url)->toStartWith('https://backups.s3.sa-east-1.amazonaws.com/escola/backup%20de%20hoje.backup?')
        ->toContain('X-Amz-Expires=600')
        ->toContain('X-Amz-Credential=chave')
        ->toContain('X-Amz-Signature=');
});
