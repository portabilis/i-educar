<?php

namespace App\Listeners;

use App\Events\ReportIssued;
use App\Models\ReportsCount;

class ReportIssuedListener
{
    public function handle(ReportIssued $event): void
    {
        ReportsCount::updateOrCreate([
            'render' => $event->render,
            'template' => $event->template,
            'success' => $event->success,
            'date' => now(),
            'authenticated' => $event->authenticate,
        ]);
    }
}
