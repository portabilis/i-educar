<?php

namespace App\Listeners;

use App\Services\TransferRegistrationDataService;

class AcceptTransferRequestListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $transfer = TransferRegistrationDataService::getTransfer($event->registration);

        $this->acceptTransferRequest($transfer, $event->registration);
    }

    private function acceptTransferRequest($transfer, $newRegistration)
    {
        $transfer->update([
            'ref_cod_matricula_entrada' => $newRegistration->cod_matricula
        ]);
    }
}
