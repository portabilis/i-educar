<?php

namespace App\Contracts;

use App\Models\LegacyRegistration;

interface CopyRegistrationData
{
    /**
     * @param LegacyRegistration $newRegistration
     * @param LegacyRegistration $oldRegistration
     *
     * @return void
     */
    public function copy(
        LegacyRegistration $newRegistration,
        LegacyRegistration $oldRegistration
    );
}
