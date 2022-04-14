<?php

namespace App\Console\Commands;

use App\Imports\SchoolGradeImport;
use Illuminate\Console\Command;

class ImportSchoolGradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:school-grade {filename} {school?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import school grade';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $importer = new SchoolGradeImport(
            $this->argument('school')
        );

        $importer->withOutput($this->output)->import($this->argument('filename'));
    }
}
