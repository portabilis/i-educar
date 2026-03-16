<?php

namespace App\Jobs;

use App\Models\LegacyRegistration;
use App\Services\RegistrationService;
use App\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateRegistrationDateJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private array $registrationIds;

    private string $newDateRegistration;

    private ?string $newDateEnrollment;

    private bool $ignoreRelocation;

    private string $databaseConnection;

    private User $user;

    public int $timeout = 90;

    public function __construct(
        array $registrationIds,
        string $newDateRegistration,
        ?string $newDateEnrollment,
        bool $ignoreRelocation,
        string $databaseConnection,
        User $user
    ) {
        $this->registrationIds = $registrationIds;
        $this->newDateRegistration = $newDateRegistration;
        $this->newDateEnrollment = $newDateEnrollment;
        $this->ignoreRelocation = $ignoreRelocation;
        $this->databaseConnection = $databaseConnection;
        $this->user = $user;
    }

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        DB::setDefaultConnection($this->databaseConnection);
        DB::beginTransaction();

        $registrations = LegacyRegistration::whereIn('cod_matricula', $this->registrationIds)->get();
        $registrationService = new RegistrationService($this->user);

        $newDateRegistration = \DateTime::createFromFormat('Y-m-d', $this->newDateRegistration);
        $newDateEnrollment = $this->newDateEnrollment
            ? \DateTime::createFromFormat('Y-m-d', $this->newDateEnrollment)
            : null;

        foreach ($registrations as $registration) {
            $registrationService->updateRegistrationDate($registration, $newDateRegistration);

            if ($newDateEnrollment) {
                $registrationService->updateEnrollmentsDate($registration, $newDateEnrollment, $this->ignoreRelocation);
            }
        }

        DB::commit();
    }

    public function tags()
    {
        return [
            $this->databaseConnection,
            'update-registration-date',
        ];
    }
}
