<?php

namespace App\Listeners;

use Illuminate\Database\Connection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;

class ConfigureAuthenticatedUserForAudit
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Connection $connection, Request $request)
    {
        $this->connection = $connection;
        $this->request = $request;
    }

    /**
     * Set context data for audit log.
     *
     * @param int    $id
     * @param string $name
     * @return void
     */
    private function setContext($id, $name)
    {
        $pdo = $this->connection->getPdo();

        $enabled = config('audit.enabled', true) ? 'true' : 'false';

        $context = json_encode([
            'user_id' => $id,
            'user_name' => $name,
            'origin' => $this->request->fullUrl(),
            'ip' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ], JSON_HEX_APOS | JSON_HEX_QUOT);

        $pdo->exec("SET \"audit.enabled\" = {$enabled};");
        $pdo->exec("SET \"audit.context\" = '{$context}';");
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle($event)
    {
        $this->setContext($event->user->id, $event->user->name);

        // Propaga para Jobs via Context facade
        Context::add('audit_user_id', $event->user->id);
        Context::add('audit_user_name', $event->user->name);
        Context::add('audit_origin', $this->request->fullUrl());
    }
}
