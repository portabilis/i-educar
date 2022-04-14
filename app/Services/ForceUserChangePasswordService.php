<?php

namespace App\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Config\Repository;

class ForceUserChangePasswordService
{
    public $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    public function execute(Authenticatable $user)
    {
        $expirationPeriod = $this->config->get('legacy.app.user_accounts.default_password_expiration_period');

        if (empty($expirationPeriod)) {
            return;
        }

        if ($user->isAdmin()) {
            return;
        }

        $daysGone = $user->getDaysSinceLastPasswordUpdated();

        if ($daysGone >= $expirationPeriod) {
            $user->employee->force_reset_password = true;
            $user->employee->save();
        }
    }
}
