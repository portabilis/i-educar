<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
     * Return the database list filename.
     *
     * @return string
     */
    private function getDatabaseList()
    {
        return storage_path('db.list');
    }

    /**
     * Generate a database list from backup file.
     *
     * @param string $filename
     *
     * @return void
     */
    private function createDatabaseList($filename)
    {
        $definition = 'pg_restore -l %s > %s';

        $command = sprintf(
            $definition,
            $filename,
            $this->getDatabaseList()
        );

        passthru($command);
    }

    /**
     * Remove lines that contains table data imports.
     *
     * @param array $tables
     *
     * @return void
     */
    private function removeTableDataFromDatabaseList(array $tables)
    {
        $definition = 'sed -i \'/TABLE DATA %s/d\' %s';

        if (Str::contains(PHP_OS, 'Darwin')) {
            $definition = 'sed -i \'\' \'/TABLE DATA %s/d\' %s';
        }

        foreach ($tables as $table) {
            $table = str_replace('.', ' ', $table);

            $command = sprintf(
                $definition,
                $table,
                $this->getDatabaseList(),
                $this->getDatabaseList()
            );

            passthru($command);
        }
    }

    /**
     * Return audit tables.
     *
     * @return array
     */
    private function getAuditTables()
    {
        return [
            'modules.auditoria',
            'modules.auditoria_geral',
        ];
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
        $definition = 'pg_restore -L %s --host=%s --port=%s --username=%s --dbname=%s %s';

        $command = sprintf(
            $definition,
            $this->getDatabaseList(),
            $this->getHost(),
            $this->getPort(),
            $this->getUser(),
            $database,
            $filename
        );

        passthru($command);
    }

    /**
     * Alter search path for database. Use legacy i-Educar configuration.
     *
     * @param string $database
     *
     * @return void
     */
    private function alterSearchPathInDatabase($database)
    {
        $definition = 'echo "ALTER DATABASE %s SET search_path = \"\$user\", public, portal, cadastro, historico, pmieducar, urbano, modules;" | psql -h %s -p %s -U %s';

        $command = sprintf(
            $definition,
            $database,
            $this->getHost(),
            $this->getPort(),
            $this->getUser()
        );

        passthru($command);
    }

    /**
     * Delete the database list created.
     *
     * @return void
     */
    private function deleteDatabaseList()
    {
        if (file_exists($this->getDatabaseList())) {
            unlink($this->getDatabaseList());
        }
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

        $this->createDatabaseList($filename);
        $this->removeTableDataFromDatabaseList($this->getAuditTables());
        $this->dropAndCreateDatabase($database);
        $this->restoreDatabaseUsingBackupFile($database, $filename);
        $this->alterSearchPathInDatabase($database);
        $this->deleteDatabaseList();
    }
}
