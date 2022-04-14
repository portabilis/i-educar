<?php

namespace App\Jobs;

use App\Jobs\Concerns\ShouldNotificate;
use App\Services\Exemption\BatchExemptionService;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class BatchExemptionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use ShouldNotificate;

    /**
     * @var array
     */
    private $importArray;

    /**
     * @var string
     */
    private $databaseConnection;

    /**
     * @var BatchExemptionService
     */
    private $batchExemptionService;

    /**
     * @var User
     */
    private $user;

    /**
     * @var int
     */
    public $timeout = 600;

    public function __construct(BatchExemptionService $batchExemptionService, $databaseConnection, User $user)
    {
        $this->batchExemptionService = $batchExemptionService;
        $this->databaseConnection = $databaseConnection;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @throws Throwable
     *
     * @return void
     */
    public function handle()
    {
        DB::setDefaultConnection($this->databaseConnection);
        DB::beginTransaction();

        try {
            $this->batchExemptionService->handle();
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->notificateError();

            throw $exception;
        }

        $this->notificateSuccess();
        DB::commit();
    }

    public function tags()
    {
        return [
            $this->databaseConnection,
            'batch-exemption'
        ];
    }

    public function getSuccessMessage()
    {
        return 'O processo de Dispensa em lote foi finalizado.';
    }

    public function getErrorMessage()
    {
        return 'Não foi possível finalizar o processo de dispensa em lote.';
    }

    public function getNotificationUrl()
    {
        return '/consulta-dispensas';
    }

    public function getUser()
    {
        return $this->user;
    }
}
