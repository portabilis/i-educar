<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
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
