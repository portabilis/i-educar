<?php

namespace App\Services\Educacenso\Version2019;

use App\Services\Educacenso\ImportServiceInterface;
use App\Services\Educacenso\RegistroImportInterface;
use App\User;
use Illuminate\Http\UploadedFile;

class ImportService implements ImportServiceInterface
{
    const DELIMITER = '|';

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
        $columns = explode(self::DELIMITER, $school[0]);

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

    /**
     * Faz a importação dos dados a partir da string do arquivo do censo
     *
     * @param array $importString
     * @return void
     */
    public function import($importString)
    {
        foreach ($importString as $line) {
            $this->importLine($line);
        }
    }

    /**
     * Importa uma linha
     *
     * @param string $line
     */
    private function importLine($line)
    {
        $lineId = $this->getLineId($line);

        $this->getRegistroById($lineId)::import($line);
    }

    /**
     * Retorna o ID da linha (registro)
     *
     * @param $line
     * @return string
     */
    private function getLineId($line)
    {
        $arrayLine = explode(self::DELIMITER, $line);

        return $arrayLine[0];
    }

    /**
     * Retorna a classe responsável por importar o registro da linha
     *
     * @param $lineId
     * @return RegistroImportInterface
     */
    private function getRegistroById($lineId)
    {
        $arrayRegistros = [
            '00' => Registro00Import::class
        ];

        return $arrayRegistros[$lineId];
    }
}
