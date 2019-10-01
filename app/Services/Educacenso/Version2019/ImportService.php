<?php

namespace App\Services\Educacenso\Version2019;

use App\Services\Educacenso\ImportServiceInterface;
use App\User;
use Illuminate\Http\UploadedFile;

class ImportService implements ImportServiceInterface
{
    /**
     * Retorna o ano a que o service se refere
     *
     * @return int
     */
    public function getYear()
    {
        return 2019;
    }

    /**
     * Retorna o nome da escola a partir da string do arquivo de importação
     *
     * @param $school
     * @return string
     */
    public function getSchoolNameByFile($school)
    {
        $columns = explode('|', $school[0]);

        return $columns[9];
    }

    /**
     * Verifica se o arquivo está de acordo com as regras do ano
     *
     * todo: Implementar validação do arquivo
     * @param UploadedFile $file
     */
    public function validateFile(UploadedFile $file)
    {

    }
}
