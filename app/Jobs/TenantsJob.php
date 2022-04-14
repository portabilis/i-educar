<?php

namespace App\Jobs;

use App\Support\Database\Connections;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TenantsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Connections;

    /**
     * @var int
     */
    public $timeout = 600;

    /**
     * @var string
     */
    private $jobString;

    /**
     * Create a new job instance.
     *
     * @param Dispatchable $job
     */
    public function __construct($jobString)
    {
        $this->jobString = $jobString;
    }

    /**
     * Executa o job passado por parâmetro para todas as conexões configuradas
     *
     * @return void
     */
    public function handle()
    {
        $connections = $this->getConnections();

        foreach ($connections as $connection) {
            $this->jobString::dispatch($connection);
        }
    }
}
