<?php

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    require_once base_path('ieducar/intranet/include/file_check.php');
    require_once base_path('ieducar/intranet/include/file_check_just_pdf.php');
    require_once base_path('ieducar/intranet/include/image_check.php');

    config(['legacy.app.database.dbname' => 'base_teste']);
    $this->tmp = tempnam(sys_get_temp_dir(), 'upload');
    // Um PNG de 1x1 pixel, para a checagem de conteúdo das fotos.
    file_put_contents($this->tmp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='));
});

afterEach(fn () => @unlink($this->tmp));

test('os verificadores de arquivo validam nome, extensão e tamanho', function (string $class, string $ok, string $wrongType) {
    $file = fn (string $name, int $size = 10) => new $class(['name' => $name, 'size' => $size, 'tmp_name' => $this->tmp]);

    expect($file($ok)->validateFile())->toBeTrue()
        ->and(tap($file(''), fn ($check) => $check->validateFile())->getErrorMessage())->toBe('Selecione um arquivo.')
        ->and(tap($file('planilha.exe'), fn ($check) => $check->validateFile())->getErrorMessage())->toBe($wrongType)
        ->and(tap($file($ok, 3 * 1024 * 1024), fn ($check) => $check->validateFile())->getErrorMessage())->toEndWith('ão permitidos arquivos com mais de 2MB.')
        ->and($file('semextensao')->getExtension('semextensao'))->toBe('')
        ->and((new $class([], 10, ['txt']))->suportedExtensions)->toBe(['txt']);
})->with([
    'qualquer documento' => ['FileController', 'laudo.pdf', 'Deve ser enviado um arquivo do tipo jpg, png ou pdf.'],
    'só pdf' => ['FileControllerPdf', 'laudo.pdf', 'Deve ser enviado um arquivo do tipo pdf.'],
]);

test('os verificadores enviam o arquivo para a pasta da base e avisam quando o disco falha', function (string $class, string $method) {
    Storage::fake();
    $check = new $class(['name' => 'foto.png', 'size' => 10, 'tmp_name' => $this->tmp]);

    expect($check->{$method}())->toContain('base_teste/');

    Storage::shouldReceive('put')->andReturnFalse();

    expect($check->{$method}())->toBe('')
        ->and($check->getErrorMessage())->toBe('Ocorreu um erro no servidor ao enviar foto. Tente novamente.');
})->with([
    ['FileController', 'sendFile'],
    ['FileControllerPdf', 'sendFile'],
    ['PictureController', 'sendPicture'],
]);

test('a foto precisa ter conteúdo de imagem, extensão aceita e tamanho permitido', function () {
    $picture = fn (array $file) => new PictureController($file + ['tmp_name' => $this->tmp, 'size' => 10]);

    expect($picture(['name' => 'foto.png'])->validatePicture())->toBeTrue()
        ->and(tap($picture(['name' => '']), fn ($check) => $check->validatePicture())->getErrorMessage())->toBe('Selecione uma imagem.')
        ->and(tap($picture(['name' => 'foto.png', 'size' => 3 * 1024 * 1024]), fn ($check) => $check->validatePicture())->getErrorMessage())->toBe('O cadastro não pode ser realizado, a foto possui um tamanho maior do que o permitido.')
        ->and(tap($picture(['name' => 'foto.png', 'tmp_name' => '']), fn ($check) => $check->validatePicture())->getErrorMessage())->toBe('O cadastro não pode ser realizado, a foto possui um formato diferente daqueles permitidos.')
        ->and($picture(['name' => 'x'])->getExtension('semextensao'))->toBe('')
        ->and((new PictureController([], 10, 20, 30, ['png']))->maxWidth)->toBe(10);
});
