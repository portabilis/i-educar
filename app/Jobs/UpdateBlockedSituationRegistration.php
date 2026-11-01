<?php

namespace App\Jobs;

use App\Models\LegacyRegistration;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class UpdateBlockedSituationRegistration implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private LegacyRegistration $registration
    ) {}

    public function handle(): void
    {
        $this->registration->update([
            'bloquear_troca_de_situacao' => false,
        ]);
    }
}
