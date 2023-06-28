<?php

namespace App\Contracts;

use App\Models\LegacyRegistration;

interface CopyRegistrationData
{
    /**
     * @return void
     */
    public function copy(
        LegacyRegistration $newRegistration,
        LegacyRegistration $oldRegistration
    );
}
