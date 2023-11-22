<?php

namespace App\Listeners;

use App\Events\RegistrationCopyEvent;
use App\Exceptions\Transfer\MissingDescriptiveOpinionType;
use App\Exceptions\Transfer\StagesAreNotSame;
use App\Services\RegistrationDataService;
use App\Services\TransferRegistrationDataService;

class RegistrationCopyListener
{
    /**
     * @var TransferRegistrationDataService
     */
    protected $service;

    public function __construct(RegistrationDataService $service)
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
    public function handle(RegistrationCopyEvent $event)
    {
        $this->service->copy($event->newRegistration, $event->oldRegistration);
    }
}
