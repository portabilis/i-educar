<?php

namespace App\Listeners;

use App\Exceptions\Transfer\MissingDescriptiveOpinionType;
use App\Exceptions\Transfer\StagesAreNotSame;
use App\Services\TransferRegistrationDataService;

class CopyTransferDataListener
{
    /**
     * @var TransferRegistrationDataService
     */
    protected $service;

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
     * @throws MissingDescriptiveOpinionType
     * @throws StagesAreNotSame
     *
     * @return void
     */
    public function handle($event)
    {
        $this->service->transferData($event->registration);
    }
}
