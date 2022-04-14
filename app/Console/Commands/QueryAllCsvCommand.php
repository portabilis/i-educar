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
        $array = [];
        $data = [];
        $file = file_get_contents($this->getFile());

        $excludedDatabases = $this->option('no-database');

        foreach ($this->getConnections() as $connection) {
            if (in_array($connection, $excludedDatabases)) {
                continue;
            }

            try {
                $data[$connection] = DB::connection($connection)->select($file);
            } catch (Exception $exception) {
                continue;
            }
        }

        if (isset($data[$connection][0])) {
            $header = array_keys((array) $data[$connection][0]);
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
