<?php

namespace App\Services\Exemption;

use App\Events\ActiveLookingChanged;
use App\Models\LegacyActiveLooking;
use App\Models\LegacyRegistration;
use App\Rules\CanStoreActiveLooking;

class ActiveLookingService
{
    public function store(LegacyActiveLooking $activeLooking, LegacyRegistration $registration)
    {
        validator(
            ['active_looking' => [
                'registration' => $registration,
                'active_looking' => $activeLooking,
            ],
            ],
            ['active_looking' => new CanStoreActiveLooking]
        )->validate();
        $activeLooking->save();

        event(new ActiveLookingChanged(
            activeLooking: $activeLooking,
            action: $activeLooking->wasRecentlyCreated ? ActiveLookingChanged::ACTION_CREATED : ActiveLookingChanged::ACTION_UPDATED
        ));

        return $activeLooking;
    }

    public function delete(LegacyActiveLooking $activeLooking)
    {
        $activeLooking->delete();
    }
}
