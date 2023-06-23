<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Support\Database\Connections;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NotificationsDelete extends Command
{
    use Connections;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exclui todas as notificacções existentes a mais de 06 (seis) meses';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach ($this->getConnections() as $connection) {
            DB::setDefaultConnection($connection);
            Notification::whereDate('created_at', '<', now()->subMonths(6))->delete();
        }
    }
}
