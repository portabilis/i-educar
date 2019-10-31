<?php

namespace App\Jobs;

use App\Models\EducacensoImport as EducacensoImportModel;
use App\Services\Educacenso\ImportServiceFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class EducacensoImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var EducacensoImportModel
     */
    private $educacensoImport;

    /**
     * @var array
     */
    private $importArray;

    /**
     * @var string
     */
    private $databaseConnection;

    /**
     * Create a new job instance.
     *
     * @param EducacensoImportModel $educacensoImport
     * @param $importArray
     * @param string $databaseConnection
     */
    public function __construct(EducacensoImportModel $educacensoImport, $importArray, $databaseConnection)
    {
        $this->educacensoImport = $educacensoImport;
        $this->importArray = $importArray;
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::setDefaultConnection($this->databaseConnection);
        DB::beginTransaction();

        $importService = ImportServiceFactory::createImportService($this->educacensoImport->year);
        $importService->import($this->importArray, $this->educacensoImport->user);

        $educacensoImport = $this->educacensoImport;
        $educacensoImport->finished = true;
        $educacensoImport->save();

        DB::commit();
    }
}
