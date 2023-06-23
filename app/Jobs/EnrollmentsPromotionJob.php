<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PromocaoApiController;

class EnrollmentsPromotionJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private int $user,
        private int $enrollmentId,
        private string $databaseConnection,
        private bool $updateScore = false
    ) {
    }

    public function handle(DatabaseManager $manager, PromocaoApiController $promocaoApiController): void
    {
        $manager->setDefaultConnection($this->databaseConnection);
        @$promocaoApiController->processEnrollmentsPromotion(
            userId: $this->user,
            enrollmentsId: $this->enrollmentId,
            updateScore: $this->updateScore
        );
    }
}
