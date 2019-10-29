<?php

namespace App\Listeners;

use App\Services\TransferRegistrationDataService;

class CopyTransferDataListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $service = new TransferRegistrationDataService($event->registration);

        $service->transferData();
    }
}
