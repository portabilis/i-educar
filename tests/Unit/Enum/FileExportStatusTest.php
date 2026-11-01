<?php

use App\Models\Enums\FileExportStatus;

test('cada status da exportação de documentos tem um texto', function () {
    expect(FileExportStatus::WAITING->name())->toBe('Aguardando a exportação do arquivo ser finalizada')
        ->and(FileExportStatus::ERROR->name())->toBe('O arquivo não pode ser exportado')
        ->and(FileExportStatus::SUCCESS->name())->toBe('Fazer download');
});
