<?php

namespace App\Services\Exemption;

use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use App\Rules\CanStoreActiveLooking;

class ActiveLookingService
{
    public function store(LegacyActiveLooking $activeLooking, LegacyRegistration $registration)
    {
        validator(
            ['active_looking' =>
                [
                    'registration' => $registration,
                    'active_looking' => $activeLooking,
                ]
            ],
            ['active_looking' => new CanStoreActiveLooking()]
        )->validate();

        $activeLooking->save();
    }

    public function delete(LegacyActiveLooking $activeLooking)
    {
        $activeLooking->delete();
    }
}
