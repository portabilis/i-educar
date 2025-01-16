<?php

namespace App\Console\Commands;

use App\Support\Database\Connections;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueryAllCsvCommand extends Command
{
    use Connections;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:all-csv {--no-database=*} {--file=} {--output=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a query in all databases connections';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $header = [];
        $data = [];
        $file = file_get_contents($this->getFile());

        $excludedDatabases = $this->option('no-database');

        foreach ($this->getConnections() as $connection) {
            if (in_array($connection, $excludedDatabases)) {
                continue;
            }

            try {
                $connectionData = DB::connection($connection)->select($file);

                if (!empty($connectionData) && empty($header)) {
                    $header = array_merge(['conexao'], array_keys((array) $connectionData[0]));
                }
                $data[$connection] = $connectionData;
            } catch (Exception) {
                continue;
            }
        }

        $this->makeCsv($header, $data);
    }

    public function makeCsv($header, $data)
    {
        $file = fopen($this->getFileOutput(), 'w');
        fputcsv($file, $header);

        foreach ($data as $connection => $lines) {
            foreach ($lines as $line) {
                fputcsv($file, array_merge([$connection], (array) $line));
            }
        }

        fclose($file);
    }

    public function getFileOutput()
    {
        return $this->option('output') ?: storage_path('result.csv');
    }

    private function getFile()
    {
        return $this->option('file') ?: storage_path('query.sql');
    }
}
