<?php

namespace App\Listeners;

use App\Services\TransferRegistrationDataService;

class AcceptTransferRequestListener
{
    /**
     * @var TransferRegistrationDataService
     */
    private $service;

    /**
     * @param TransferRegistrationDataService $service
     */
    public function __construct(TransferRegistrationDataService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $transfer = $this->service->getTransfer($event->registration);

        if (empty($transfer)) {
            return;
        }

        $this->acceptTransferRequest($transfer, $event->registration);
    }

    /**
     * @param $transfer
     * @param $newRegistration
     */
    private function acceptTransferRequest($transfer, $newRegistration)
    {
        $transfer->update([
            'ref_cod_matricula_entrada' => $newRegistration->cod_matricula
        ]);
    }
}
