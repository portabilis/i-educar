<?php

namespace App\Console\Commands;

use App\Support\Database\Connections;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueryAllCommand extends Command
{
    use Connections;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:all {--no-database=*} {--database=*} {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a query in all databases connections';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $array = [];
        $data = [];
        $file = file_get_contents($this->getFile());

        $excludedDatabases = $this->option('no-database');
        $allowedDatabases = $this->option('database');

        $connections = $this->getConnections();

        if (count($allowedDatabases)) {
            $connections = array_intersect($connections, $allowedDatabases);
        }

        foreach ($connections as $connection) {
            if (in_array($connection, $excludedDatabases)) {
                continue;
            }

            try {
                $data = (array) DB::connection($connection)->selectOne($file);
            } catch (Exception $exception) {
                continue;
            }

            if (isset($data)) {
                $array[] = array_merge([$connection], $data);
            }
        }

        $header = array_keys($data);
        array_unshift($header, 'connection');

        $this->table($header, $array);
    }

    private function getFile()
    {
        return $this->option('file') ?: storage_path('query.sql');
    }
}
