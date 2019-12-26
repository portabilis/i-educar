<?php

namespace App\Services\Educacenso;

use Generator;
use Illuminate\Http\UploadedFile;

class SplitFileService
{
    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @param UploadedFile $file
     */
    public function __construct(UploadedFile $file)
    {
        $this->file = $file;
    }

    /**
     * Retorna um generator contendo as escolas do arquivo
     *
     * @return Generator
     */
    public function getSplitedSchools()
    {
        $lines = $this->readFile();

        $school = [];
        foreach ($lines as $key => $line) {
            if ($this->isNewSchoolLine($line)) {
                if (count($school)) {
                    yield $school;
                }

                $school = [];
            }

            $school[] = $line;
        }

        if (count($school)) {
            yield $school;
        }
    }

    /**
     * @return Generator
     */
    private function readFile()
    {
        $handle = fopen($this->file, 'r');

        while (($line = fgets($handle)) !== false) {
            yield $line;
        }
    }

    /**
     * @param string $line
     * @return bool
     */
    private function isNewSchoolLine($line)
    {
        return starts_with($line, '00|');
    }
}
