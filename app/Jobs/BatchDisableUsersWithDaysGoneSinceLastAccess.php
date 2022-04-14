<?php

namespace App\Jobs;

use App\Services\DisableUsersWithDaysGoneSinceLastAccessService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class BatchDisableUsersWithDaysGoneSinceLastAccess implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $databaseConnection;

    public function __construct($databaseConnection)
    {
        $this->databaseConnection = $databaseConnection;
    }

    public function handle(Repository $config)
    {
        DB::setDefaultConnection($this->databaseConnection);
        $expirationPeriod = $config->get('legacy.app.user_accounts.max_days_without_login_to_disable_user');

        if (empty($expirationPeriod) === false) {
            $this->disableUsersWithDaysGoneSinceLastAccess($expirationPeriod);
        }
    }

    private function disableUsersWithDaysGoneSinceLastAccess($expirationPeriod): void
    {
        $users = (new User())->getActiveUsersNotAdmin();

        foreach ($users as $user) {
            $disableUsersWithDaysGoneSinceLastAccessService = app(DisableUsersWithDaysGoneSinceLastAccessService::class);
            $disableUsersWithDaysGoneSinceLastAccessService->execute($user);
        }
    }
}
