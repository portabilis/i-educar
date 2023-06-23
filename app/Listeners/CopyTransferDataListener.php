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

    public function __construct(TransferRegistrationDataService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     *
     * @throws MissingDescriptiveOpinionType
     * @throws StagesAreNotSame
     */
    public function handle($event)
    {
        $this->service->transferData($event->registration);
    }
}
