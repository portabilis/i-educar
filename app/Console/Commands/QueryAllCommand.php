<?php

namespace App\Console\Commands;

use App\Menu;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class QueryAllCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Count duplicated scores';

    /**
     * @return array
     */
    private function getConnections()
    {
        $connections = config('database.connections');

        return array_diff(array_keys($connections), ['sqlite', 'mysql', 'pgsql', 'sqlsrv', 'bussolastaging']);
    }

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

        foreach ($this->getConnections() as $connection) {
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
