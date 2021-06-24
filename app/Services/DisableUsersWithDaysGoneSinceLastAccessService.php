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
    public function execute(Authenticatable $user){
        $expirationPeriod = $this->config->get('legacy.app.user_accounts.default_password_expiration_period');

        if (empty($expirationPeriod) === false) {
            $daysGone = $user->getDaysSinceLastAccessOrEnabledUserDate();
            if ($daysGone >= $expirationPeriod) {
                $user->disable();
            }
        }
    }
}
