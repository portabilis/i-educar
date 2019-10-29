<?php

namespace App\Listeners;

use App\Services\TransferRegistrationDataService;

class MeetsTransferRequestListener
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

        $transfer = $service->getTransfer();

        $this->meetsTransferRequest($transfer, $event->registration);
    }

    private function meetsTransferRequest($transfer, $newRegistration)
    {
        $transfer->update([
            'ref_cod_matricula_entrada' => $newRegistration->cod_matricula
        ]);
    }
}
