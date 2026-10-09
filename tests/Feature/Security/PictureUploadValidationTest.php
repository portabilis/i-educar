<?php

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->validate = function (UploadedFile $file): bool {
        return (new PictureController([
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'tmp_name' => $file->getPathname(),
        ]))->validatePicture();
    };

    require_once base_path('ieducar/intranet/include/image_check.php');
});

test('aceita imagem valida', function () {
    $this->assertTrue(($this->validate)(UploadedFile::fake()->image('foto.png')));
});

test('recusa html com extensao de imagem', function () {
    $file = UploadedFile::fake()->createWithContent('foto.png', '<html><body>conteúdo</body></html>');

    $this->assertFalse(($this->validate)($file));
});

test('recusa svg com extensao de imagem', function () {
    $file = UploadedFile::fake()->createWithContent('foto.jpg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

    $this->assertFalse(($this->validate)($file));
});
