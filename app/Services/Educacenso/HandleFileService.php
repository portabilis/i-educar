<?php

namespace App\Services\Educacenso;

use App\Exceptions\Educacenso\InvalidFileYear;
use App\Jobs\EducacensoImportJob;
use App\Models\EducacensoImport;
use App\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class HandleFileService
{
    /**
     * @var ImportService
     */
    private $yearImportService;

    /**
     * @var User
     */
    private $user;

    /**
     * @var EducacensoImportJob[]
     */
    private $jobs;

    /**
     * @param ImportService $yearImportService
     * @param User          $user
     */
    public function __construct(ImportService $yearImportService, User $user)
    {
        $this->yearImportService = $yearImportService;
        $this->user = $user;
    }

    /**
     * Processa o arquivo de importação do censo
     *
     * @param UploadedFile $file
     */
    public function handleFile(UploadedFile $file)
    {
        $splitFileService = new SplitFileService($file);
        $schools = $splitFileService->getSplitedSchools();

        $this->validateFile($schools->current());

        foreach ($schools as $school) {
            $this->createImportProcess($school);
        }

        $this->dispatchJobs();
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
        $import->year = $this->yearImportService->getYear();
        $import->school = utf8_encode($this->yearImportService->getSchoolNameByFile($school));
        $import->user_id = $this->user->id;
        $import->registration_date = $this->yearImportService->registrationDate;
        $import->finished = false;
        $import->save();

        $school = array_map('utf8_encode', $school);

        $this->jobs[] = new EducacensoImportJob($import, $school, DB::getDefaultConnection(), $this->yearImportService->registrationDate);
    }

    private function dispatchJobs()
    {
        $firstJob = $this->jobs[0];
        unset($this->jobs[0]);

        $firstJob->chain($this->jobs);

        app(Dispatcher::class)->dispatch($firstJob);
    }

    private function validateFile($school)
    {
        $serviceYear = $this->yearImportService->getYear();
        $line = explode($this->yearImportService::DELIMITER, $school[0]);

        $fileYear = \DateTime::createFromFormat('d/m/Y', $line[3])->format('Y');

        if ($serviceYear != $fileYear) {
            throw new InvalidFileYear($fileYear, $serviceYear);
        }
    }
}
