<?php

namespace App\Services\Educacenso;

use App\Models\Educacenso\RegistroEducacenso;
use App\User;
use Illuminate\Http\UploadedFile;

abstract class ImportService
{
    const DELIMITER = '|';

    /**
     * Faz a importação dos dados a partir da string do arquivo do censo
     *
     * @param array $importString
     * @param $user
     * @return void
     */
    public function import($importString, $user)
    {
        foreach ($importString as $line) {
            $this->importLine($line, $user);
        }
    }

    /**
     * Importa uma linha
     *
     * @param string $line
     * @param $user
     */
    private function importLine($line, $user)
    {
        $lineId = $this->getLineId($line);

        $class = $this->getRegistroById($lineId);
        $line = preg_replace( "/\r|\n/", '', utf8_decode($line));
        $arrayColumns = explode(self::DELIMITER, $line);

        if (!$class) {
            return;
        }

        $model = $class::getModel($arrayColumns);

        $class->import($model, $this->getYear(), $user);
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
    abstract public function getRegistroById($lineId);

    /**
     * Retorna o ano a que o service se refere
     *
     * @return int
     */
    abstract public function getYear();

    /**
     * Retorna o nome da escola a partir da string do arquivo de importação
     *
     * @param $school
     * @return string
     */
    abstract public function getSchoolNameByFile($school);

    /**
     * Verifica se o arquivo está de acordo com as regras do ano
     *
     * @param UploadedFile $file
     */
    abstract public function validateFile(UploadedFile $file);
}
