<?php

namespace App\Jobs;

use App\Models\Exporter\Export;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class UpdateUrlExport implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private Export $export,
        private string $url
    ) {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->export->update(['url' => $this->url]);
    }
}
