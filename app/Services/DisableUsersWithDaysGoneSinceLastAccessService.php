<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;

class DisableUsersWithDaysGoneSinceLastAccessService
{
    public $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function execute(Authenticatable $user)
    {
        $expirationPeriod = $this->config->get('legacy.app.user_accounts.max_days_without_login_to_disable_user');

        if (empty($expirationPeriod) === false && $user->isAdmin() === false) {
            $daysGone = $user->getDaysSinceLastAccessOrEnabledUserDate();
            if ($daysGone >= $expirationPeriod) {
                $user->disable();
            }
        }
    }
}
