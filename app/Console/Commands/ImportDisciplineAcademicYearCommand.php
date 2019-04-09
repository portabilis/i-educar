<?php

namespace App\Console\Commands;

use App\Imports\DisciplineAcademicImport;
use Illuminate\Console\Command;

class ImportDisciplineAcademicYearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:discipline-academic-year {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Discipline Academic Year';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $importer = new DisciplineAcademicImport();

        $importer->withOutput($this->output)->import($this->argument('filename'));
    }
}
