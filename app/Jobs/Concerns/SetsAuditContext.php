<?php

namespace App\Jobs\Concerns;

use Illuminate\Support\Facades\Context;
use Illuminate\Support\Facades\DB;

trait SetsAuditContext
{
    protected function setAuditContextFromLaravelContext(): void
    {
        if (!config('audit.enabled', true) || !Context::has('audit_user_id')) {
            return;
        }

        $context = json_encode([
            'user_id' => Context::get('audit_user_id'),
            'user_name' => Context::get('audit_user_name'),
            'origin' => Context::get('audit_origin', 'queue'),
        ], JSON_HEX_APOS | JSON_HEX_QUOT);

        $pdo = DB::connection()->getPdo();
        $pdo->exec("SET \"audit.context\" = '{$context}';");
    }
}
