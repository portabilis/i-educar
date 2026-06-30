<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Support\Database\Connections;
use Illuminate\Console\Command;

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
     */
    public function handle(): int
    {
        $this->eachConnection(fn () => Notification::whereDate('created_at', '<', now()->subMonths(6))->delete());

        return self::SUCCESS;
    }
}
