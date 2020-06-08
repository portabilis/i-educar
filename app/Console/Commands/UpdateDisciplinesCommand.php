<?php

namespace App\Console\Commands;

use App\Services\Discipline\MoveDisciplineDataService;
use iEducar\Support\Output\CommandOutput;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class UpdateDisciplinesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:disciplines {filename} {--copier=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move os dados de um componente curricular para outro';

    /**
     * Execute the console command.
     *
     * @param Filesystem $filesystem
     *
     * @return mixed
     */
    public function handle(Filesystem $filesystem)
    {
        $filename = $this->argument('filename');

        $output = new CommandOutput($this->output);
        $service = new MoveDisciplineDataService($output);
        $this->setCopiers($service);

        DB::beginTransaction();
        Excel::import($service, $filename);
        DB::commit();
    }

    private function setCopiers(MoveDisciplineDataService $service)
    {
        $copiers = $this->option('copier');
        if (empty($copiers)) {
            $service->setDefaultCopiers();
            return;
        }

        foreach ($copiers as $copier) {
            $service->setMoveDataService(new $copier);
        }
    }
}
