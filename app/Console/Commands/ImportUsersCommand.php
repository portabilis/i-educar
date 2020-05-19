<?php

namespace App\Console\Commands;

use App\Services\Discipline\MoveDisciplineDataService;
use App\Services\ImportUsersService;
use iEducar\Support\Output\CommandOutput;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ImportUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users {filename} {--multi-tenant} {--force-reset-password} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa usuários a partir de um CSV';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $filename = $this->argument('filename');

        $output = new CommandOutput($this->output);
        $service = new ImportUsersService($output, $this->option('multi-tenant'), $this->option('force-reset-password'));

        Excel::import($service, $filename);
    }
}
