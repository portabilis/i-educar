<?php

namespace App\Console\Commands;

use App\Exports\DisciplineExport;
use App\Imports\DisciplineImport;
use Illuminate\Console\Command;

class ImportDisciplineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:discipline {filename} {output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import disciplines';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $importer = new DisciplineImport();

        $importer->withOutput($this->output)->import($this->argument('filename'));

        $exporter = new DisciplineExport($importer->getCollection());

        $exporter->store($this->argument('output'));
    }
}
