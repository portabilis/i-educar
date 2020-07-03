<?php

namespace App\Console\Commands;

use App\Menu;
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
    protected $signature = 'query:all {--no-database=*}';

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
        $file = file_get_contents(storage_path('query.sql'));

        $excludedDatabases = $this->option('no-database');

        foreach ($this->getConnections() as $connection) {
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
}
