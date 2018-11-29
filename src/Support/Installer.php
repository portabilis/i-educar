<?php

namespace iEducar\Support;

class Installer
{
    static public $extensions = [
        'bcmath',
        'curl',
        'dom',
        'fileinfo',
        'json',
        'libxml',
        'mbstring',
        'openssl',
        'PDO',
        'pgsql',
        'Phar',
        'SimpleXML',
        'tokenizer',
        'xml',
        'xmlwriter',
        'zip',
        'pcre',
    ];

    static public function checkExtensions(): bool
    {
        foreach (self::$extensions as $ext) {
            if (extension_loaded($ext)) {
                continue;
            }

            return false;
        }

        return true;
    }

    static public function getExtensionsReport(): array
    {
        $report = [];

        foreach (self::$extensions as $ext) {
            $loaded = false;

            if (extension_loaded($ext)) {
                $loaded = true;
            }

            $report[$ext] = $loaded;
        }

        return $report;
    }

    static public function checkWritablePaths(array $paths): bool
    {
        foreach ($paths as $path) {
            if (is_writable($path)) {
                continue;
            }

            return false;
        }

        return true;
    }

    static public function getWritablePathsReport(array $paths): array
    {
        $report = [];

        foreach ($paths as $path) {
            $writable = false;

            if (is_writable($path)) {
                $writable = true;
            }

            $report[$path] = $writable;
        }

        return $report;
    }

    static public function checkDatabaseConnection(): bool
    {
        try {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s',
                getenv('DB_HOST'),
                getenv('DB_PORT'),
                getenv('DB_DATABASE'),
                getenv('DB_USERNAME'),
                getenv('DB_PASSWORD')
            );

            $conn = new \PDO($dsn);

            if ($conn) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    static public function exec(string $rootPath, string $command, int $time): int
    {
        chdir($rootPath);

        $tmpFile = 'install-' . $time . '.tmp';

        exec('touch ./storage/framework/cache/' . $tmpFile);

        $command = './scripts/install_helper "' . $command
            . '" ./storage/framework/cache/' . $tmpFile
            . ' > /dev/null 2>&1 & echo $!;';

        return (int) exec($command);
    }

    static public function consult(string $rootPath,int $pid, int $time): int
    {
        chdir($rootPath);

        $tmpFile = 'install-' . $time . '.tmp';
        $status = posix_getpgid($pid);

        if ((int) $status > 0) {
            return -1; // rodando
        } else {
            sleep(1);

            $status = file_get_contents('./storage/framework/cache/' . $tmpFile);

            if ($status == '') {
                return 1; // erro
            }

            return (int) $status; // 0 é sucesso
        }

        return 1; // erro
    }
}
