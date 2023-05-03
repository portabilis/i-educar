<?php

namespace App\Services\Educacenso;

use App\Exceptions\Educacenso\InvalidFileDate;
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
        $import->school = mb_convert_encoding($this->yearImportService->getSchoolNameByFile($school), 'UTF-8');
        $import->user_id = $this->user->id;
        $import->registration_date = $this->yearImportService->registrationDate;
        $import->finished = false;
        $import->save();

        array_walk_recursive($school, static fn (&$item) => $item = mb_convert_encoding($item, 'HTML-ENTITIES', 'UTF-8'));

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

        if (is_bool($line[3])) {
            throw new InvalidFileDate();
        }

        $fileYear = \DateTime::createFromFormat('d/m/Y', $line[3])->format('Y');

        if ($serviceYear != $fileYear) {
            throw new InvalidFileYear($fileYear, $serviceYear);
        }
    }
}
