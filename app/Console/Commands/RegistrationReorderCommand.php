<?php

namespace App\Console\Commands;

use App\Models\LegacyRegistration;
use App\Services\EnrollmentService;
use Illuminate\Console\Command;

class RegistrationReorderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registration:reorder {registration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reorder all registration enrollments';

    /**
     * Execute the console command.
     *
     * @param EnrollmentService $service
     *
     * @return mixed
     */
    public function handle(EnrollmentService $service)
    {
        /** @var LegacyRegistration $registration */
        $registration = LegacyRegistration::query()->findOrFail(
            $id = $this->argument('registration')
        );

        $service->reorder($registration);

        $this->info("Enrollments reordered to registration: {$id}");
    }
}
