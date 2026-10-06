<?php

namespace Tests\Feature\Security;

use Illuminate\Http\UploadedFile;
use PictureController;
use Tests\TestCase;

/**
 * CVE-2026-2064: a foto era aceita apenas pela extensão do nome do arquivo,
 * mas gravada com a extensão deduzida do conteúdo, o que permitia publicar
 * HTML ou SVG no storage.
 */
class PictureUploadValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once base_path('ieducar/intranet/include/image_check.php');
    }

    private function validate(UploadedFile $file): bool
    {
        return (new PictureController([
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'tmp_name' => $file->getPathname(),
        ]))->validatePicture();
    }

    public function test_aceita_imagem_valida()
    {
        $this->assertTrue($this->validate(UploadedFile::fake()->image('foto.png')));
    }

    public function test_recusa_html_com_extensao_de_imagem()
    {
        $file = UploadedFile::fake()->createWithContent('foto.png', '<html><body>conteúdo</body></html>');

        $this->assertFalse($this->validate($file));
    }

    public function test_recusa_svg_com_extensao_de_imagem()
    {
        $file = UploadedFile::fake()->createWithContent('foto.jpg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $this->assertFalse($this->validate($file));
    }
}
