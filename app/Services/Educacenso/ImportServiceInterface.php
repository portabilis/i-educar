<?php

namespace App\Services\Educacenso;

use Illuminate\Http\UploadedFile;

interface ImportServiceInterface
{
    /**
     * Retorna o ano a que o service se refere
     *
     * @return int
     */
    public function getYear();

    /**
     * Retorna o nome da escola a partir da string do arquivo de importação
     *
     * @param $school
     * @return string
     */
    public function getSchoolNameByFile($school);

    /**
     * Verifica se o arquivo está de acordo com as regras do ano
     *
     * @param UploadedFile $file
     */
    public function validateFile(UploadedFile $file);

    /**
     * Faz a importação dos dados a partir da string do arquivo do censo
     *
     * @param array $importString
     * @return void
     */
    public function import($importString);
}
