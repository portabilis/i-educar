<?php

namespace App\Console\Commands;

use App\Imports\SchoolGradeDisciplineImport;
use Illuminate\Console\Command;

class ImportSchoolGradeDisciplineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:school-grade-discipline {filename} {school?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import school grade discipline';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $importer = new SchoolGradeDisciplineImport(
            $this->argument('school')
        );

        $importer->withOutput($this->output)->import($this->argument('filename'));
    }
}
