<?php

namespace Tests\Unit\Services\Educacenso;

use App\Services\Educacenso\SplitFileService;
use Generator;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SplitFileServiceTest extends TestCase
{
    /**
     * Instancia o service que faz o split do arquivo
     *
     * @param $fileName
     * @return SplitFileService
     */
    public function getService($fileName)
    {
        $file = new UploadedFile(base_path('tests/Unit/assets/Educacenso/' . $fileName), $fileName);
        return new SplitFileService($file);
    }

    /**
     * O método getSplitedSchools deve retornar um Generator com as escolas contidas no arquivo
     */
    public function testNumberOfScools()
    {
        $service = $this->getService('oneschool');
        $result = $service->getSplitedSchools();
        $size = $this->countGenerator($result);

        $this->assertEquals(1, $size);

        $service = $this->getService('threeschools');
        $result = $service->getSplitedSchools();
        $size = $this->countGenerator($result);

        $this->assertEquals(3, $size);
    }

    /**
     * Se um arquivo vazio for passado, deverá retornar um Generator vazio
     */
    public function testEmptyFileShouldRetursEmpty()
    {
        $service = new SplitFileService(UploadedFile::fake()->create('fakefile'));
        $result = $service->getSplitedSchools();
        $size = $this->countGenerator($result);

        $this->assertEquals(0, $size);
    }

    /**
     * Retorna o tamanho de um generator
     *
     * @param Generator $generator
     * @return int
     */
    private function countGenerator(Generator $generator)
    {
        $count = 0;
        foreach ($generator as $key => $value) {
            $count++;
        }

        return $count;
    }
}
