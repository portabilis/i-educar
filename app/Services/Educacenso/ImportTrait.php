<?php

namespace App\Services\Educacenso;

use App\Models\EducacensoImport;
use Illuminate\Http\UploadedFile;

trait ImportTrait
{
    /**
     * Processa o arquivo de importação do censo
     *
     * @param UploadedFile $file
     */
    public function handleFile(UploadedFile $file)
    {
        $this->validateFile($file);

        $splitFileService = new SplitFileService($file);
        $schools = $splitFileService->getSplitedSchools();

        foreach ($schools as $school) {
            $this->createImportProcess($school);
        }
    }

    /**
     * Cria o processo de importação de uma escola
     *
     * @param $school
     * @param $year
     */
    public function createImportProcess($school)
    {
        $import = new EducacensoImport();
        $import->year = $this->getYear();
        $import->school = $this->getSchoolNameByFile($school);
        $import->user_id = $this->user->id;
        $import->finished = false;
        $import->save();
    }

}
