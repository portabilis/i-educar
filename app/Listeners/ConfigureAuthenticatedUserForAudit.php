<?php

namespace App\Listeners;

use Illuminate\Database\Connection;

class ConfigureAuthenticatedUserForAudit
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Set context data for audit log.
     *
     * @param int    $id
     * @param string $name
     *
     * @return void
     */
    private function setContext($id, $name)
    {
        $pdo = $this->connection->getPdo();

        $enabled = config('audit.enabled', true) ? 'true' : 'false';

        $context = json_encode([
            'user_id' => $id,
            'user_name' => $name,
        ]);

        $pdo->exec("SET \"audit.enabled\" = {$enabled};");
        $pdo->exec("SET \"audit.context\" = '{$context}';");
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle($event)
    {
        $this->setContext($event->user->id, $event->user->name);
    }
}
