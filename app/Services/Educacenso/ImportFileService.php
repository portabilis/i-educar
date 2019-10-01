<?php

namespace App\Services\Educacenso;

use App\Jobs\EducacensoImportJob;
use App\Models\EducacensoImport;
use App\User;
use Illuminate\Http\UploadedFile;

class ImportFileService
{
    /**
     * @var ImportServiceInterface
     */
    private $importService;

    /**
     * @var User
     */
    private $user;

    public function __construct(ImportServiceInterface $importService, User $user)
    {
        $this->importService = $importService;
        $this->user = $user;
    }
    /**
     * Processa o arquivo de importação do censo
     *
     * @param UploadedFile $file
     */
    public function handleFile(UploadedFile $file)
    {
        $this->importService->validateFile($file);

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
        $import->year = $this->importService->getYear();
        $import->school = $this->importService->getSchoolNameByFile($school);
        $import->user_id = $this->user->id;
        $import->finished = false;
        $import->save();

        $school = array_map('utf8_encode', $school);

        EducacensoImportJob::dispatch($import, $school);
    }

}
