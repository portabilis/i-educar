<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DatabaseRestoreCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:restore {database} {filename}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore a database using a backup file';

    /**
     * Return the database host.
     *
     * @return string
     */
    private function getHost()
    {
        return env('DB_HOST');
    }

    /**
     * Return the database port.
     *
     * @return string
     */
    private function getPort()
    {
        return env('DB_PORT');
    }

    /**
     * Return the database user.
     *
     * @return string
     */
    private function getUser()
    {
        return env('DB_USERNAME');
    }

    /**
     * Drop old database if exists and create it again.
     *
     * @param string $database
     *
     * @return void
     */
    private function dropAndCreateDatabase($database)
    {
        $definition = 'echo "drop database if exists %s; create database %s;" | psql -h %s -p %s -U %s';

        $command = sprintf(
            $definition,
            $database,
            $database,
            $this->getHost(),
            $this->getPort(),
            $this->getUser()
        );

        passthru($command);
    }

    /**
     * Restore the database using the backup file.
     *
     * @param string $database
     * @param string $filename
     *
     * @return void
     */
    private function restoreDatabaseUsingBackupFile($database, $filename)
    {
        $definition = 'pg_restore --host=%s --port=%s --username=%s --dbname=%s %s --no-privileges --no-owner';

        $command = sprintf(
            $definition,
            $this->getHost(),
            $this->getPort(),
            $this->getUser(),
            $database,
            $filename
        );

        passthru($command);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $database = $this->argument('database');
        $filename = $this->argument('filename');

        $this->dropAndCreateDatabase($database);
        $this->restoreDatabaseUsingBackupFile($database, $filename);
    }
}
