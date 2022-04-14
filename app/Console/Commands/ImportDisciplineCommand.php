<?php

namespace App\Console\Commands;

use App\Exports\DisciplineExport;
use App\Imports\DisciplineImport;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class ImportDisciplineCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:discipline {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import disciplines';

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

        $importer = new DisciplineImport();

        $importer->withOutput($this->output)->import($filename);

        $filesystem->delete($filename);

        // Importa as disciplinas para o banco de dados e após, faz a
        // exportação dos IDs gerados para as disciplinas.

        $export = $importer->getCollection()->map(function ($item) {
            $row = $item->get('row');
            $discipline = $item->get('discipline');

            $row['discipline_id'] = $discipline->getKey();

            return $row;
        });

        $exporter = new DisciplineExport($export);

        $exporter->store($filename, 'local');
    }
}
